<?php

namespace Creacoon\QueueTile\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Creacoon\QueueTile\queue
 */
class queue extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'queue-tile';
    }
}
