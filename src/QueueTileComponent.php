<?php

namespace Creacoon\QueueTile;

use Creacoon\QueueTile\JiraStore;
use livewire\Component;

class QueueTileComponent extends Component
{
    public $position;

    public function mount(string $position)
    {
        $this->position = $position;
    }

    public function render()
    {
        return view('dashboard-queue-tile::tile', [
            'refreshIntervalInSeconds' => config('dashboard.tiles.jira.refresh_interval_in_seconds') ?? 60,
            'processedQueueData' => JiraStore::make()->getData(),
        ]);
    }
}
