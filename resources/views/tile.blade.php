<x-dashboard-tile :position="$position">
    <div class=" p-4 h-full flex flex-col">
        <div class="flex items-center justify-center mb-4">
            <div class="font-medium text-dimmed text-sm uppercase tracking-wide tabular-nums">
                Queue Overview
            </div>
        </div>
        <div class="flex-grow overflow-y-auto  max-h-48">
            <table class="w-full">
                <thead class="text-left border-b border-gray-700" >
                <tr>
                    <th class="font-medium text-white text-l uppercase tracking-wide tabular-nums">Queue Name</th>
                    <th class="font-medium text-white text-l uppercase tracking-wide tabular-nums">Open</th>
                    <th class="font-medium text-white text-l uppercase tracking-wide tabular-nums">In Progress</th>
                    <th class="font-medium text-white text-l uppercase tracking-wide tabular-nums">Done</th>
                </tr>
                </thead>
                <tbody>
                @foreach($queueValues as $queue)
                    <tr>
                        <td class="font-medium text-dimmed text-s uppercase tracking-wide tabular-nums border-b border-gray-700 ">{{ $queue['queue_name'] }}</td>
                        <td class="font-medium text-dimmed text-s uppercase tracking-wide tabular-nums border-b border-gray-700 ">{{ $queue['queue_status']['open_status'] }}</td>
                        <td class="font-medium text-dimmed text-s uppercase tracking-wide tabular-nums border-b border-gray-700">{{ $queue['queue_status']['in_progress'] }}</td>
                        <td class="font-medium text-dimmed text-s uppercase tracking-wide tabular-nums border-b border-gray-700">{{ $queue['queue_status']['done_status'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-tile>
