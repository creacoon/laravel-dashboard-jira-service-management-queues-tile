<x-dashboard-tile :position="$position" :refresh-interval="$refreshIntervalInSeconds">
    <div class="grid grid-rows-auto-1 gap-3 h-full">
        <div class="font-medium text-dimmed text-sm uppercase tracking-wide">
            Jira service queues
        </div>

        @if(empty($data))
            <div class="flex items-center justify-center text-dimmed text-sm">
                No data found
            </div>
        @else
            <div class="flex flex-col gap-2">
                @foreach($data['queues'] as $queue)
                    <div class="flex items-baseline justify-between gap-3 rounded-xl border border-white/5 bg-white/[0.04] px-3 py-2.5 text-base">
                        <span class="truncate font-medium text-default">{{ $queue['queue_name'] }}</span>
                        <span class="font-semibold tabular-nums text-default">{{ $queue['issue_count'] }}</span>
                    </div>
                @endforeach

                <div class="flex items-baseline justify-between gap-3 rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-3 py-2.5 text-base">
                    <span class="font-medium text-default">Resolved today</span>
                    <span class="font-semibold tabular-nums text-emerald-400">{{ $data['issues_resolved_today'] ?? '–' }}</span>
                </div>
            </div>
        @endif
    </div>
</x-dashboard-tile>
