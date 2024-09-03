<x-dashboard-tile :position="$position">
    <div class="grid gap-x-8 gap-y-4 grid-cols-3">

        <div class="font-medium text-dimmed text-sm uppercase tracking-wide tabular-nums"><h2>Open Tickets</h2></div>
        <div class="font-medium text-dimmed text-sm uppercase tracking-wide tabular-nums"><h2>Pending Tickets</h2></div>
        <div class="font-medium text-dimmed text-sm uppercase tracking-wide tabular-nums">
            @foreach($processedQueueData as $queueData)
                <div>
                    <h1>{{ $queueData['name'] }}</h1>
                    <div class="pl-3 pt-1 pb-1 rounded-t-lg flex" style="font-size: 1.4rem; background-color: rgb(0, 82, 204);">
                        <div class="w-5/6"></div>
                    </div>
                    <div class="rounded-b-lg" style="border: 1px solid rgb(0, 82, 204); border-top: 0; color:white;">
                        <div class="pl-3" style="margin: 0 0.05rem 0.05rem 0.05rem">
                            {{ $queueData['title'] }}
                        </div>
                    </div>
                </div>
            @endforeach


        </div>
    </div>
</x-dashboard-tile>
