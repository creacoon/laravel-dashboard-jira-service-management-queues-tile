<?php

namespace Creacoon\QueueTile;
use Spatie\Dashboard\Models\Tile;


class JiraStore
{
    private Tile $tile;

    public static function make()
    {
        return new static();
    }

    public function __construct()
    {
        $this->tile = Tile::firstOrCreateForName("QueueTile");
    }

    public function setData(array $data): self
    {
        $this->tile->putData('QueueInProgressStore', $data);

        return $this;
    }

    public function getData(): array
    {
        return$this->tile->getData('QueueInProgressStore') ?? [];
    }
}
