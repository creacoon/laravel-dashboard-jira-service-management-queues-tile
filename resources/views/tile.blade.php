<x-dashboard-tile :position="$position">
    <div class="p-4 h-full flex flex-col">
        <div class="flex items-center justify-center mb-4">
            <div class="font-medium text-dimmed text-sm uppercase tracking-wide tabular-nums">
                Queue Overview
            </div>
        </div>
        <div class="flex-grow">
            <table class="w-full">
                <thead class="text-left">
                <tr>
                    <th class="font-medium text-white text-sm uppercase tracking-wide tabular-nums">Queue Name</th>
                    <th class="font-medium text-white text-sm uppercase tracking-wide tabular-nums">Open</th>
                    <th class="font-medium text-white text-sm uppercase tracking-wide tabular-nums">In Progress</th>
                    <th class="font-medium text-white text-sm uppercase tracking-wide tabular-nums">Done</th>
                </tr>
                </thead>
                <tbody>
                @foreach($queueValues as $queue)
                    <tr>
                        <td class="font-medium text-dimmed text-xs uppercase tracking-wide tabular-nums">{{ $queue['queue_name'] }}</td>
                        <td class="font-medium text-dimmed text-xs uppercase tracking-wide tabular-nums">{{ $queue['queueStatus']['open'] }}</td>
                        <td class="font-medium text-dimmed text-xs uppercase tracking-wide tabular-nums">{{ $queue['queueStatus']['in_progress'] }}</td>
                        <td class="font-medium text-dimmed text-xs uppercase tracking-wide tabular-nums">{{ $queue['queueStatus']['done'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-tile>
