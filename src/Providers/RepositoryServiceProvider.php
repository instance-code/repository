<?php
namespace Sabirepo\Repository\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Sabirepo\Repository\Exceptions\RepositoryHandler;
class RepositoryServiceProvider extends ServiceProvider {
    public $commands = [
        \Sabirepo\Repository\Console\Commands\MakeRepository::class
    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // register facades
        $this->app->bind('sabirepo-repository', function() {
            return new \Sabirepo\Repository\RepositoryBase;
        });

        // merge config
        $this->mergeConfigFrom(
        __DIR__ . '/../Config/repository.php', 'repository'
        );

        // binding repository
        foreach (config('repository.bindings') as $key => $value) {
            $this->app->bind($key, $value);
        }

        // register commands
        $this->commands($this->commands);

        // default handler
        if(config('repository.default_handler')) {
            $this->app->singleton(ExceptionHandler::class, RepositoryHandler::class);
        }
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
//            __DIR__ . '/../Stubs/RepositoryServiceProvider.stub' => app_path('Providers/RepositoryServiceProvider.php'),
        ], 'repository');
    }

}
