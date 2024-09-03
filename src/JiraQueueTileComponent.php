<?php

namespace Creacoon\JiraQueueServiceTile;

use Creacoon\JiraQueueServiceTile\JiraQueueTileStore;
use livewire\Component;

class JiraQueueTileComponent extends Component
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
            'processedQueueData' => JiraQueueTileStore::make()->getData(),
        ]);
    }
}
