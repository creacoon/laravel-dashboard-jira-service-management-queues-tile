<?php

namespace Creacoon\JiraQueueServiceTile;
use Spatie\Dashboard\Models\Tile;


class JiraQueueTileServiceManagementStore
{
    private Tile $tile;

    public static function make()
    {
        return new static();
    }

    public function __construct()
    {
        $this->tile = Tile::firstOrCreateForName("JSMQueuesTile");
    }

    public function setData(array $data): self
    {
        $this->tile->putData('JSMQueueData', $data);

        return $this;
    }


    public function getData(string $key): array
    {
        return $this->tile->getData('JSMQueueData') ?? [];
    }
}
