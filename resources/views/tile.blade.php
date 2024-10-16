<x-dashboard-tile :position="$position" :refresh-interval="$refreshIntervalInSeconds">
    <div class="p-4 h-full flex flex-col">
        <div class="flex items-center justify-center mb-4">
            <div class="font-medium text-dimmed text-sm uppercase tracking-wide tabular-nums">
                Jira Service Queues
            </div>
        </div>
        @if (!empty($data))
        <table class="w-full">
            <thead>
            <tr>
                <th></th>
                <th scope="col" class="px-2 py-2 text-left text-base font-medium text-gray-100 uppercase tracking-wider">Issue Count</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data['queues'] as $queue)
                <tr class="bg-transparent">
                    <td class="text-gray-200 px-2 py-2 whitespace-nowrap">{{ $queue['queue_name'] }}</td>
                    <td class="text-gray-200 px-2 py-2 whitespace-nowrap text-base">{{ $queue['issue_count']}}</td>
                </tr>
            @endforeach
            <tr class="bg-transparent">
                <td class="text-gray-200 px-2 py-2 whitespace-nowrap">Resolved today</td>
                <td class="text-gray-200 px-2 py-2 whitespace-nowrap text-base">{{ $data['issues_resolved_today'] }}</td>
            </tr>
            </tbody>
        </table>
        @else
            <div class="flex items-center justify-center h-full">
                <div class="text-center text-dimmed font-medium text-s">
                    No data found
                </div>
            </div>
        @endif
    </div>
</x-dashboard-tile>
