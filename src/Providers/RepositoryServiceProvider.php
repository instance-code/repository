<?php

namespace InstanceCode\Repository\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;

class RepositoryServiceProvider extends ServiceProvider {
    public $commands = [
        \InstanceCode\Repository\Console\Commands\MakeRepository::class,
        \InstanceCode\Repository\Console\Commands\MakeService::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // register facades
        $this->app->bind('instance-code-repository', function() {
            return new \InstanceCode\Repository\Helper;
        });

        // merge config
        $this->mergeConfigFrom(
        __DIR__ . '/../Config/repository.php', 'repository'
        );

        $bindingClass = config('repository.bindingClass');
        if (@class_exists($bindingClass)) {
            $this->app->register($bindingClass);
        }

        //binding repository
        foreach (config('repository.bindings') as $key => $value) {
            $this->app->bind($key, $value);
        }

        // register commands
        $this->commands($this->commands);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // public config
        $this->publishes([
            __DIR__ . '/../Config' => config_path(),
           __DIR__ . '/../Stubs/RepositoryServiceProvider.stub' => app_path('Providers/RepositoryServiceProvider.php'),
        ], 'repository');
    }
}
