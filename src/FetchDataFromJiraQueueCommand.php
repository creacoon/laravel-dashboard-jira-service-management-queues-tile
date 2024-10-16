<?php

namespace Creacoon\JiraQueueServiceTile;

use Illuminate\Console\Command;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class FetchDataFromJiraQueueCommand extends Command
{
    protected $signature = 'dashboard:fetch-queue-jira-service-management-data';

    protected $description = 'Fetch queue data using the Jira Service Management API';

    public function handle(): int
    {
        $data = [
            'queues' => [],
        ];

        $this->info('Fetching all queue data');

        $queues = $this->getQueues();

        foreach ($queues as $queue) {
            if (in_array($queue['id'], config('dashboard.tiles.jira_service_queues.visible_queues'))) {
                $queueName = $queue['name'];
                $queueId = $queue['id'];

                $data['queues'][] = [
                    'queue_name' => $queueName,
                    'queue_id' => $queueId,
                    'issue_count' => $this->getQueueIssueCount($queueId),
                ];
            }
        }

        $data['issues_resolved_today'] = $this->getIssuesHandledTodayCount();

        JiraQueueTileServiceManagementStore::make()->setData($data);

        return self::SUCCESS;
    }

    private function getQueues(): array
    {
        $queueResponse = $this->apiClient()
            ->get(config('dashboard.tiles.jira_service_queues.jira_host').'/rest/servicedeskapi/servicedesk/4/queue');

        if ($queueResponse->successful()) {
            $queuesData = $queueResponse->json();

            return $queuesData['values'];
        }

        return [];
    }

    private function getQueueIssueCount(string $queueId): int
    {
        $issuePage = 1;
        $issuesPerPage = 50;
        $issueCount = 0;

        do {
            $this->info("Fetching data for queue {$queueId}, issue page: {$issuePage}");
            $queueRepo = $this->apiClient()
                ->get(config('dashboard.tiles.jira_service_queues.jira_host').
                    "/rest/servicedeskapi/servicedesk/4/queue/{$queueId}/issue", [
                        'start' => ($issuePage - 1) * $issuesPerPage,
                        'limit' => $issuesPerPage,
                    ]);

            if ($queueRepo->successful()) {
                $queueRepoData = $queueRepo->json();
                $issueData = $queueRepoData['values'] ?? [];
                $issueCount = +count($issueData);
            } else {
                $this->error("Failed to fetch issues for queue {$queueId}. Status: {$queueRepo->status()}");
                break;
            }

        } while (count($issueData) === $issuesPerPage);

        return $issueCount;
    }

    private function getIssuesHandledTodayCount(): ?int
    {
        $issuesResponse = $this->apiClient()
            ->get(config('dashboard.tiles.jira_service_queues.jira_host').'/rest/api/3/search', [
                'jql' => config('dashboard.tiles.jira_service_queues.resolved_today_jql'),
                'maxResults' => 1000,
            ]);

        if ($issuesResponse->successful()) {
            $issuesData = $issuesResponse->json();

            return count($issuesData['issues'] ?? []);
        }

        return null;
    }

    private function apiClient(): PendingRequest
    {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic '.base64_encode(config('dashboard.tiles.jira_service_queues.jira_user').':'.
                    config('dashboard.tiles.jira_service_queues.jira_api_token')),
        ]);
    }
}
