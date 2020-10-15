<?php
namespace Sabirepo\Repository\Facades;

use Illuminate\Support\Facades\Facade;

class Repository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'sabirepo-repository';
    }
}
