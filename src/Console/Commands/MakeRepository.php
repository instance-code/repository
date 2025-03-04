<?php

namespace InstanceCode\Repository\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

use InstanceCode\Repository\Helper;

class MakeRepository extends Command
{
    protected $helper;
    protected $namespace;
    protected $modelNamespace;
    protected $modelPath;
    protected $repoPath;
    protected $naming;

    /**
     * @var name: repo named
     * @var --m: disabled auto create model
     */
    protected $signature = 'make:repository {name} {--m}';

    /**
     * @var string
     */
    protected $description = 'Use: php artisan make:repository {name} {--m|model}';

    public function __construct(Helper $helper)
    {
        parent::__construct();
        $this->helper = $helper;
        $this->namespace = config('repository.namespace');
        $this->modelNamespace = config('repository.model.namespace');
        $this->modelPath = config('repository.model.path');
        $this->repoPath = config('repository.path');
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
        $this->makeBaseRepoIfNotExists();

        // make repo
        $this->makeRepo($dir);

        // disabled auto create model
        if (!$this->option('m')) {
            $this->makeBaseModel($dir);
        }
    }

    /**
     * init
     * @return void
     */
    private function makeBaseRepoIfNotExists()
    {
        $this->helper->createDirectoryIfNotExists($this->repoPath);
        $this->makeBaseTemplates([
            'RepositoryInterface',
            'RepositoryAbstract',
        ], $this->repoPath, ['{$NAMESPACE}'], [$this->namespace]);
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
    private function makeRepo($dir)
    {
        $className = Str::singular($dir);
        $path = $this->repoPath . DIRECTORY_SEPARATOR . $dir;
        $plural = Str::plural($dir);
        $di = lcfirst($plural);
        $tbl = Str::snake($plural);
        $this->helper->createDirectoryIfNotExists($this->repoPath . DIRECTORY_SEPARATOR . $dir);
        $this->makeBaseTemplates(
            [
                "{$className}Interface" => "ItemInterface",
                "{$className}Repository" => "ItemRepository",
            ],
            $path,
            ['{$NAMESPACE}', '{$REPO_NAME}', '{$ITEM_NAME}', '{$MODEL_NAMESPACE}', '{$DI}', '{$ITEM_TABLE}'],
            [$this->namespace, $dir, $className, $this->modelNamespace, $di, $tbl]
        );
    }

    /**
     * @param $dir
     */
    private function makeBaseModel($dir)
    {
        $className = Str::singular($dir);
        $oldName = $className === 'User' ? $className : 'Item';
        $this->helper->createDirectoryIfNotExists($this->modelPath);
        $this->makeBaseTemplates(
            [
                $className => $oldName
            ],
            $this->modelPath,
            ['{$ITEM_NAME}', '{$MODEL_NAMESPACE}'],
            [$className, $this->modelNamespace]
        );
    }
}
