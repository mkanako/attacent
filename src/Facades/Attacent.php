<?php

namespace Cc\Attacent\Facades;

use Illuminate\Support\Facades\Facade;

class Attacent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Cc\Attacent\Attacent::class;
    }
}
