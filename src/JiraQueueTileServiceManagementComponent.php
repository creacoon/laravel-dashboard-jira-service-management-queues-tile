<?php

namespace Creacoon\JiraQueueServiceTile;

use Creacoon\JiraQueueServiceTile\JiraQueueTileServiceManagementStore;
use livewire\Component;

class JiraQueueTileServiceManagementComponent extends Component
{
    public $position;

    public function mount(string $position)
    {
        $this->position = $position;
    }

    public function render()
    {
        return view('dashboard-jira-queue-tile::tile', [
            'refreshIntervalInSeconds' => config('dashboard.tiles.jira.refresh_interval_in_seconds') ?? 60,
            'data' => JiraQueueTileServiceManagementStore::make()->getData('JSMQueueData'),
        ]);
    }
}
