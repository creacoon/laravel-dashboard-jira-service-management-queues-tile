<?php

namespace Creacoon\JiraQueueServiceTile\Test;

use Creacoon\JiraQueueServiceTile\JiraQueueTileServiceManagementStore;
use Creacoon\JiraQueueServiceTile\JiraQueueTileServiceProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase;
use Spatie\Dashboard\DashboardServiceProvider;

class FetchDataFromJiraQueueCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            DashboardServiceProvider::class,
            JiraQueueTileServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('dashboard.tiles.jira_service_queues', [
            'jira_host' => 'https://example.atlassian.net',
            'jira_user' => 'dashboard@example.com',
            'jira_api_token' => 'secret',
            'visible_queues' => ['24', '41'],
            'resolved_today_jql' => 'project = CSS AND statusCategory IN (Done)',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        (include __DIR__.'/../vendor/spatie/laravel-dashboard/database/migrations/create_dashboard_tiles_table.php.stub')->up();
    }

    public function test_it_stores_the_issue_count_of_each_visible_queue_from_a_single_queue_request(): void
    {
        Http::fake([
            'example.atlassian.net/rest/servicedeskapi/servicedesk/4/queue*' => Http::response([
                'values' => [
                    ['id' => '24', 'name' => 'ToDo', 'issueCount' => 59],
                    ['id' => '41', 'name' => 'In Progress', 'issueCount' => 33],
                    ['id' => '20', 'name' => 'All open issues', 'issueCount' => 9728],
                ],
            ]),
            'example.atlassian.net/rest/api/3/search/approximate-count' => Http::response(['count' => 4]),
        ]);

        $this->artisan('dashboard:fetch-queue-jira-service-management-data')->assertSuccessful();

        $this->assertSame([
            'queues' => [
                ['queue_name' => 'ToDo', 'queue_id' => '24', 'issue_count' => 59],
                ['queue_name' => 'In Progress', 'queue_id' => '41', 'issue_count' => 33],
            ],
            'issues_resolved_today' => 4,
        ], JiraQueueTileServiceManagementStore::make()->getData('JSMQueueData'));

        Http::assertSentCount(2);
        Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request['includeCount'] === 'true');
    }
}
