<?php
namespace Sabirepo\Repository\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use Illuminate\Validation\ValidationException;
use Psy\Exception\FatalErrorException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use Sabirepo\Repository\Facades\Repository as Repository;

class RepositoryHandler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $response = [];
        if ($exception instanceof ValidationException) {
            $response['status'] = Response::HTTP_UNPROCESSABLE_ENTITY; //422
            $response['body'] = (object)[];
            $t = (array_map(function($data) {
                return $data[0] ?? "";
            }, $exception->errors()));

            $response['messages'] = array_map(function($data) {
                return $data[0] ?? "";
            }, $exception->errors());
        }

        if ($exception instanceof UnauthorizedHttpException) {
            $response['status'] = Response::HTTP_UNAUTHORIZED;
            $response['body'] = (object)[];
            $response['messages'] = [__('message.unauthorized')];
        }

        if ($exception instanceof FatalErrorException) {
            $response['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response['body'] = (object)[];
            $response['messages'] = [__('message.internal_server_error')];
        }

        if ($exception instanceof QueryException) {
            $response['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response['body'] = (object)[];
            $response['messages'] = [__('message.internal_server_error')];
        }

        return $response ?
                Repository::response($response, $response['status'])
                : parent::render($request, $exception);

    }
}
