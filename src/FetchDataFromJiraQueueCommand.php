<?php

namespace Creacoon\JiraQueueServiceTile;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
class FetchDataFromJiraQueueCommand extends Command
{
    protected $signature = 'fetch:queue-jira-data';
    protected $description = 'Fetch queue data using the Jira API';
    public function handle()
    {
        $apiEmail = env('JIRA_USER');
        $apiToken = env('JIRA_API_TOKEN');
        $basicAuthToken = base64_encode("$apiEmail:$apiToken");

        $queueResponse = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . $basicAuthToken,
        ])->get(env('JIRA_HOST')."/rest/servicedeskapi/servicedesk/4/queue");

        $queueValues = [];

        if ($queueResponse->successful()) {
            $queuesData = $queueResponse->json();
            dump('Queues Data:', $queuesData);
            $queues = $queuesData['values'] ?? [];

            foreach ($queues as $queue) {
                if (isset($queue['name'], $queue['id'])) {
                    $queueName = $queue['name'];
                    $queueId = $queue['id'];

                    $queueRepo = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Authorization' => 'Basic ' . $basicAuthToken,
                    ])->get(env('JIRA_HOST')."/rest/servicedeskapi/servicedesk/4/queue/{$queueId}/issue");

                    if ($queueRepo->successful()) {
                        $queueRepoData = $queueRepo->json();
                        $statuses = ['open' => 0, 'in_progress' => 0, 'done' => 0];

                        foreach ($queueRepoData['values'] as $queueItem) {
                            $statusId = $queueItem['fields']['status']['statusCategory']['id'];
                            $now = Carbon::now();


                            if ($statusId == 3 && isset($queueItem['fields']['customfield_10026']['ongoingCycle']['startTime']['iso8601'])) {
                                $doneTime = Carbon::parse($queueItem['fields']['customfield_10026']['ongoingCycle']['startTime']['iso8601']);

                                if ($doneTime->diffInHours($now) <= 24) {
                                    $statuses['done']++;
                                }
                            }elseif ($statusId == 2) {
                                $statuses['open']++;
                            } elseif ($statusId == 4) {
                                $statuses['in_progress']++;
                            }
                        }

                        $queueValues[] = [
                            'queue_name' => $queueName,
                            'queue_id' => $queueId,
                            'queueStatus' => $statuses
                        ];

                        dump('Queue Values:', $queueValues);
                    }
                }
            }
        }
        $dataKey = 'jira_queue_data';
        JiraQueueTileStore::make()->setData($dataKey, $queueValues);
        $this->info('All done!');
    }
}
