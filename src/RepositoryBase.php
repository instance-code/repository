<?php
namespace Sabirepo\Repository;

use Illuminate\Support\Facades\File;
use Sabirepo\Repository\Resources\ResponseResource;
class RepositoryBase {
    protected $ds = DIRECTORY_SEPARATOR;
    protected $permissions = 0755;

    /**
     * @param string $path
     * @return string
     */
    public function realPath($path = ''): string
    {
        return preg_replace(
            '/^' . preg_quote(base_path() . $this->ds, $this->ds) . '/',
            '',
            $path
        );
    }

    /**
     * makePath.
     *
     * @return void
     */
    public function makePath($path = '')
    {
        $str = '';
        $sortPath = explode($this->ds, $this->realPath($path));
        foreach($sortPath as $row) {
            $str .= $this->ds . $row;
            $path = base_path() . $str;
            if(!File::exists($path)) {
                File::makeDirectory($path, $this->permissions,true,true);
            }
        }
    }

    /**
     * if not exists Directory, make Directory with permission
     * @var null
     */
    public function createDirectoryIfNotExists($params)
    {
        $params = !is_array($params) ? [$params] : $params;
        foreach($params as $path) {
            $this->makePath($path);
        }
    }

    /**
     * get templates
     * @var string
     */
    public function getTemplate($templateFileName,$search=null,$replace=null)
    {
        $template = File::get(__DIR__ . $this->ds . 'Stubs' . $this->ds . $templateFileName . '.stub');

        return str_replace($search, $replace, $template);
    }

    /**
     * create file
     * @var string
     */
    public function createFile($files,$template)
    {
        $files = !is_array($files) ? [$files] : $files;
        foreach($files as $file) {
            $file = base_path($this->realPath($file));
            if(File::exists($file)) continue;
            File::put($file,$template,false);
        }
    }

     /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function put($path,$tmp)
    {
        File::chmod($path, $this->permissions);
        File::put($path,$tmp);
    }

    /**
     * @param $resource['body']
     * @param $resource['status']
     * @param $resource['message']
     * @param int $status
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function response($resource, $status = 200)
    {
        $status = (int) ($resource['status'] ?? $status);
        return (new ResponseResource($resource))->response()->setStatusCode($status);
    }
}
