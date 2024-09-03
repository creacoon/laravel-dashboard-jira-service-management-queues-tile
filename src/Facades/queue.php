<?php

namespace Creacoon\JiraQueueServiceTile\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Creacoon\JiraQueueServiceTile\queue
 */
class queue extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'queue-tile';
    }
}
