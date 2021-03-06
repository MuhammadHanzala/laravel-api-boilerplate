<?php

namespace App\Exceptions;

use App\Traits\RestExceptionhandlerTrait;
use App\Traits\RestTrait;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use \Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    use RestExceptionhandlerTrait;
    use RestTrait;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if (!$this->isApiCall($request)) {
            if ($exception instanceof MethodNotAllowedHttpException) {
                return abort(404);
            }
            if ($exception instanceof \InvalidArgumentException) {
                return abort(404);
            }
            $retval = parent::render($request, $exception);
        } else {
            $retval = $this->getJsonResponseForException($request, $exception);
        }

        return $retval;
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        //return 'hello';
        return redirect('/message');
        //return parent::unauthenticated($request, $exception); // TODO: Change the autogenerated stub
    }
}
