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
        //Here it is encoded so that we can use it for our auth
        $basicAuthToken = base64_encode(config('atlassian.jira.auth.basic.username').":".config('atlassian.jira.auth.basic_token.token'));
        $queueValues = [];
        $page = 1;
        $perPage = 5;
        $queues = [];
        //here we retrieve status data from the dashboard config
        $statusConfig = config('dashboard.tiles.queue_statuses', []);

        do {
            //this row is meant for testing if there are multiple pages
            $this->info("Fetching data for page: {$page}");
            //We retrieve the data from jira queue service by using the basic auth method
            $queueResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $basicAuthToken,
            ])->get(config('atlassian.jira.host')."/rest/servicedeskapi/servicedesk/4/queue", [

                'start' => ($page - 1) * $perPage,
                'limit' => $perPage,
            ]);

            if ($queueResponse->successful()) {
                $queuesData = $queueResponse->json();
                $queues = $queuesData['values'] ?? [];

                foreach ($queues as $queue) {
                    if (isset($queue['name'], $queue['id'])) {
                        $queueName = $queue['name'];
                        $queueId = $queue['id'];

                        $queueRepo = Http::withHeaders([
                            'Accept' => 'application/json',
                            'Authorization' => 'Basic ' . $basicAuthToken,
                        ])->get(config('atlassian.jira.host')."/rest/servicedeskapi/servicedesk/4/queue/{$queueId}/issue");

                        if ($queueRepo->successful()) {
                            $queueRepoData = $queueRepo->json();
                            $statuses = array_fill_keys(array_keys($statusConfig), 0);

                            foreach ($queueRepoData['values'] as $queueItem) {
                                $statusId = $queueItem['fields']['status']['statusCategory']['id'];
                                // Get the current time
                                $now = Carbon::now();
                                //here we loop through the statusConfig array, which maps status keys to their ID's
                                foreach ($statusConfig as $statusKey => $configuredStatusId) {
                                    //Check if the id's match
                                    if ($statusId == $configuredStatusId) {
                                        // Here we look if the difference between doneTime and now is less than 24 hours if it is then add the status to the queue.
                                        if ($statusKey == 'done_status' && isset($queueItem['fields']['customfield_10026']['ongoingCycle']['startTime']['iso8601'])) {
                                            $doneTime = Carbon::parse($queueItem['fields']['customfield_10026']['ongoingCycle']['startTime']['iso8601']);
                                            if ($doneTime->diffInHours($now) <= 24) {
                                                $statuses[$statusKey]++;
                                            }
                                        } else {
                                            $statuses[$statusKey]++;
                                        }
                                        break;
                                    }
                                }
                            }
                            // store all the data in the queueValues array.
                            $queueValues[] = [
                                'queue_name' => $queueName,
                                'queue_id' => $queueId,
                                'queue_status' => $statuses,
                            ];
                            dump('Queue Values:', $queueValues);
                        }
                    }
                }
            }

            $page++;
            //While the data count is the same as the limit of the page keep looping when it reaches the limit create new page
        } while (count($queues) == $perPage);

        $dataKey = 'queue-jira-service-management-data';
        JiraQueueTileServiceManagementStore::make()->setData($dataKey, $queueValues);

        $this->info('All done!');
    }
}
//Retrieve the issues with the status done
//Retrieve the issues that are done d