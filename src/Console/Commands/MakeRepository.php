<?php
namespace Sabirepo\Repository\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Sabirepo\Repository\RepositoryBase as Repository;
class MakeRepository extends Command {
    protected $repo;
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

    public function __construct(Repository $repo)
    {
        parent::__construct();
        $this->repo = $repo;
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
        if(!$this->option('m')) {
            $this->makeBaseModel($dir);
        }
    }

    /**
     * init
     * @return void
     */
    private function makeBaseRepoIfNotExists()
    {
        $this->repo->createDirectoryIfNotExists($this->repoPath);
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
    private function makeBaseTemplates($names, $path, array $search = null, array $replace = null)
    {
        $names = is_array($names) ? $names : [$names];
        foreach($names as $k => $v) {
            $template = $this->repo->getTemplate(
                $v,
                $search,
                $replace
            );

            $newName = is_string($k) ? $k : $v;
            $this->repo->createFile(
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
        $di = strtolower(Str::plural($dir));
        $this->repo->createDirectoryIfNotExists($this->repoPath . DIRECTORY_SEPARATOR . $dir);
        $this->makeBaseTemplates([
            "{$className}Interface" => "ItemInterface",
            "{$className}Repository" => "ItemRepository",
            ],
            $path ,
            ['{$NAMESPACE}', '{$REPO_NAME}', '{$ITEM_NAME}', '{$MODEL_NAMESPACE}', '{$DI}', '{$ITEM_TABLE}'],
            [$this->namespace, $dir, $className, $this->modelNamespace, $di, $di]
        );
    }

    /**
     * @param $dir
     */
    private function makeBaseModel($dir)
    {
        $className = Str::singular($dir);
        $oldName = $className === 'User' ? $className : 'Item';
        $this->repo->createDirectoryIfNotExists($this->modelPath);
        $this->makeBaseTemplates([
               $className => $oldName
            ],
            $this->modelPath ,
            ['{$ITEM_NAME}', '{$MODEL_NAMESPACE}'],
            [$className, $this->modelNamespace]
        );
    }
}
