<?php

namespace InstanceCode\Repository\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

use InstanceCode\Repository\Helper;

class MakeService extends Command
{
    protected $helper;
    protected $namespace;
    protected $modelNamespace;
    protected $path;
    protected $naming;

    protected $signature = 'make:service {name}';

    /**
     * @var string
     */
    protected $description = 'Use: php artisan make:service {name}';

    public function __construct(Helper $helper)
    {
        parent::__construct();
        $this->helper = $helper;
        $this->namespace = config('repository.service_namespace');
        $this->path = config('repository.service_path');
        $this->naming = config('repository.naming', 'singular');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->arguments();
        $name = ucwords($arguments['name']);
        $dir = $this->naming === 'plural' ? Str::plural($name) : Str::singular($name);
        $this->createBaseIfNotExists();

        // make repo
        $this->createService($dir);
    }

    /**
     * init
     * @return void
     */
    private function createBaseIfNotExists()
    {
        $this->helper->createDirectoryIfNotExists($this->path);
        $this->makeBaseTemplates([
            'BaseService',
        ], $this->path, ['{$NAMESPACE}'], [$this->namespace]);
    }

    /**
     * @param $names: Array | String
     * if $key of $names is string, output file name is $key
     * @return void
     */
    private function makeBaseTemplates($names, $path, array $search = [], array $replace = [])
    {
        $names = is_array($names) ? $names : [$names];
        foreach ($names as $k => $v) {
            $template = $this->helper->getTemplate(
                $v,
                $search,
                $replace
            );

            $newName = is_string($k) ? $k : $v;
            $this->helper->createFile(
                $path . DIRECTORY_SEPARATOR . "{$newName}.php",
                $template
            );
        }
    }

    /**
     * @param $dir
     */
    private function createService($dir)
    {
        $className = Str::singular($dir);
        $path = $this->path . DIRECTORY_SEPARATOR;
        $plural = Str::plural($dir);
        $di = lcfirst($plural);
        $tbl = Str::snake($plural);
        // $this->helper->createDirectoryIfNotExists($this->path . DIRECTORY_SEPARATOR . $dir);
        $this->makeBaseTemplates(
            [
                "{$className}Service" => "Service",
            ],
            $path,
            ['{$NAMESPACE}', '{$REPO_NAME}', '{$ITEM_NAME}', '{$MODEL_NAMESPACE}', '{$DI}', '{$ITEM_TABLE}'],
            [$this->namespace, $dir, $className, $this->modelNamespace, $di, $tbl]
        );
    }
}
