<?php

namespace Creacoon\JiraQueueServiceTile;
use Spatie\Dashboard\Models\Tile;


class JiraQueueTileStore
{
    private Tile $tile;

    public static function make()
    {
        return new static();
    }

    public function __construct()
    {
        $this->tile = Tile::firstOrCreateForName("JiraQueueServiceTile");
    }

    public function setData(string $key, array $data): self
    {
        $this->tile->putData($key, $data);

        return $this;
    }


    public function getData(string $key): array
    {
        return $this->tile->getData($key) ?? [];
    }
}
