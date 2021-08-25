<?php

namespace Peidgnad\LaravelEthereum\Facades;

use Illuminate\Support\Facades\Facade;

class Utils extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Peidgnad\LaravelEthereum\Helpers\Utils::class;
    }
}
