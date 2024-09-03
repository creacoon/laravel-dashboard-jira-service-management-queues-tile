<?php

namespace Creacoon\QueueTile;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchDataFromJiraCommand extends Command
{
    protected $signature = 'fetch:queue-jira-data';
    protected $description = 'Fetch queue data using the Jira API';

    public function handle()
    {
        $apiEmail = 'danielsandzand@gmail.com';
        $apiToken = env('JIRA_API_TOKEN');

        $basicAuthToken = base64_encode("$apiEmail:$apiToken");
        $queueResponse = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . $basicAuthToken,
        ])->get("https://creacoon-sandbox-218.atlassian.net/rest/servicedeskapi/servicedesk/4/queue");

        if ($queueResponse->successful()) {
            $queuesData = $queueResponse->json();
            dump('Queues Data:', $queuesData);

            $processedQueueData = [];
            $i = 0;
            foreach ($queuesData['values'] as $queue) {
                $nameArray = isset($queue['name']) ? explode(' ', $queue['name']) : [];

                $initials = '';

                if($nameArray){
                    $initials = substr($nameArray[0], 0, 1).
                        (($nameArray[1] ?? null) ? substr($nameArray[(count($nameArray)-1)], 0, 1) : '');
                }
                $issueResponse = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . $basicAuthToken,
                ])->get($queue['_links']['issues']);

                $issues = $issueResponse->json();

                $firstIssueSummary = $issues['values'][0]['fields']['summary'] ?? 'no summary available';
                $processedQueueData[$i] = [
                    'name' => $queue['name'],
                    'title' => $firstIssueSummary,
                    'asInitials' => $initials,
                ];
                $i++;
            }
            JiraStore::make()->setData($processedQueueData);
            $this->info('All done!');
        } else{
            $this->error('failed to fetch data' . $queueResponse->status());
        }
    }

}


