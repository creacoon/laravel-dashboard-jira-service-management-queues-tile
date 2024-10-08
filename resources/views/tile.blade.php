<x-dashboard-tile :position="$position">
    <div class="p-4 h-full flex flex-col">
        <div class="flex items-center justify-center mb-4">
            <div class="font-medium text-dimmed text-sm uppercase tracking-wide tabular-nums">
                Queue Overview
            </div>
        </div>
        <div class="flex-grow overflow-y-auto max-h-48">
            <table class="w-full">
                <thead class="text-left border-b border-gray-700">
                <tr>
                    <th class="font-large text-dimmed text-s uppercase tracking-wide tabular-nums border-b border-gray-700 ">Queue Name</th>
                    @foreach(array_keys($queueValues[0]['queue_status']) as $statusKey)
                        <th class="font-large text-dimmed text-s uppercase tracking-wide tabular-nums border-b border-gray-700 ">{{ ucwords(str_replace('_', ' ', $statusKey)) }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($queueValues as $queue)
                    <tr>
                        <td class="font-medium text-dimmed text-s uppercase tracking-wide tabular-nums border-b border-gray-700 "> {{ $queue['queue_name'] }}</td>
                        @foreach($queue['queue_status'] as $statusValue)
                            <td class="font-medium text-dimmed text-s uppercase tracking-wide tabular-nums border-b border-gray-700">{{ $statusValue }}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-tile>
