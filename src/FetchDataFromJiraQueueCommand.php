<?php

namespace Creacoon\JiraQueueServiceTile;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
//function for fetching the data
class FetchDataFromJiraQueueCommand extends Command
{
    //You can run this signature in the terminal php artisan fetch:queue-jira-data
    //This is meant for testing and inserting the data
    protected $signature = 'fetch:queue-jira-service-management-data';
    protected $description = 'Fetch queue data using the Jira Service Management API';

    public function handle()
    {
        $basicAuthToken = base64_encode(config('atlassian.jira.auth.basic.username') . ":" . config('atlassian.jira.auth.basic_token.token'));
        $queueValues = [];
        $statusConfig = config('dashboard.tiles.queue_statuses.statuses', []);

        $this->info("Fetching all queue data");

        $queueResponse = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . $basicAuthToken,
        ])->get(config('atlassian.jira.host') . "/rest/servicedeskapi/servicedesk/4/queue");

        if ($queueResponse->successful()) {
            $queuesData = $queueResponse->json();
            $queues = $queuesData['values'] ?? [];

            foreach ($queues as $queue) {
                if (isset($queue['name'], $queue['id'])) {
                    $queueName = $queue['name'];
                    $queueId = $queue['id'];
                    $statuses = array_fill_keys(array_keys($statusConfig), 0);

                    $issuePage = 1;
                    $issuesPerPage = 50;

                    do {
                        $this->info("Fetching data for queue {$queueName}, issue page: {$issuePage}");
                        $queueRepo = Http::withHeaders([
                            'Accept' => 'application/json',
                            'Authorization' => 'Basic ' . $basicAuthToken,
                        ])->get(config('atlassian.jira.host') . "/rest/servicedeskapi/servicedesk/4/queue/{$queueId}/issue", [
                            'start' => ($issuePage - 1) * $issuesPerPage,
                            'limit' => $issuesPerPage,
                        ]);

                        if ($queueRepo->successful()) {
                            $queueRepoData = $queueRepo->json();
                            $issueData = $queueRepoData['values'] ?? [];

                            if (empty($issueData)) {
                                break;
                            }

                            foreach ($issueData as $queueItem) {
                                $statusId = $queueItem['fields']['status']['statusCategory']['id'];

                                foreach ($statusConfig as $statusKey => $configuredStatusId) {
                                    if ($statusId == $configuredStatusId) {
                                        $statuses[$statusKey]++;
                                        break;
                                    }
                                }
                            }

                            $issuePage++;
                        } else {
                            $this->error("Failed to fetch issues for queue {$queueName}. Status: {$queueRepo->status()}");
                            break;
                        }

                    } while (count($issueData) == $issuesPerPage);

                    $issuesResponse = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Authorization' => 'Basic ' . $basicAuthToken,
                    ])->get(config('atlassian.jira.host') . "/rest/api/2/search", [
                        'jql' =>  config('dashboard.tiles.jql_queue_service_management.jql'),
                        'maxResults' => 1000,
                    ]);

                    if ($issuesResponse->successful()) {
                        $issuesData = $issuesResponse->json();
                        $doneIssuesToday = count($issuesData['issues'] ?? []);
                        $this->info("Number of issues done today: {$doneIssuesToday}");


                    } else {
                        $this->error("Failed to fetch issues for queue {$queueName}. Status: {$issuesResponse->status()}");
                    }

                    $queueValues[] = [
                        'queue_name' => $queueName,
                        'queue_id' => $queueId,
                        'queue_status' => $statuses,
                    ];
                }
            }

            dump('Queue Values:', $queueValues);

            $dataKey = 'queue-jira-service-management-data';
            JiraQueueTileServiceManagementStore::make()->setData($dataKey, $queueValues);
        } else {
            $this->error("Failed to fetch queue data. Status: {$queueResponse->status()}");
        }
    }

}

