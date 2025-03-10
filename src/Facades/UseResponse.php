<?php

namespace InstanceCode\Repository\Facades;

use Illuminate\Support\Facades\Facade;

class UseResponse extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'instance-code-repository';
    }
}
