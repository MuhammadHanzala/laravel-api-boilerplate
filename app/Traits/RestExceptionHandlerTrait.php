<?php
namespace App\Traits;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Laravel\Passport\Exceptions\MissingScopeException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait RestExceptionhandlerTrait
{

    protected function getJsonResponseForException(Request $request, Exception $e)
    {

        switch (true) {
            case $this->isModelNotFoundException($e):
                $retval = $this->modelNotfound();
                break;
            case $this->isOAuthServerException($e):
                $retval = $this->OAuthError();
                break;
            case $this->isAuthenticationException($e):
                $retval = $this->AuthenticationError();
                break;
            case $this->isMethodException($e):
                $retval = $this->methodError();
                break;
            case $this->isNotFoundHttpException($e):
                $retval = $this->routeError();
                break;
            case $this->isMissingScopeException($e):
                $retval = $this->scopeError();
                break;
            default:
                $retval = $this->badRequest($e);
        }

        return $retval;
    }

    protected function jsonResponse(array $payload = null, $statusCode = 404)
    {
        $payload = $payload ?: [];

        return response()->json($payload, $statusCode);
    }

    // Error Responses

    protected function badrequest(Exception $e, $message = "Bad request", $statusCode = 400)
    {
//        return $this->jsonResponse(['error'=>$e->getTrace()],400);
        return $this->jsonResponse(["error" => $message, "code" => "BAD_REQUEST", "debug" => get_class($e) . " " . $e->getMessage()], $statusCode);
    }

    protected function modelNotFound($message = "Record Not Found", $statusCode = 404)
    {
        return $this->jsonResponse(["error" => $message, "code" => "NOT_FOUND"], $statusCode);
    }

    protected function OAuthError($message = "There was an error authenticating your request", $statusCode = 401)
    {
        return $this->jsonResponse(["error" => $message, "code" => "BAD_AUTH"], $statusCode);
    }

    protected function AuthenticationError($message = "There was an error authenticating your request", $statusCode = 401)
    {
        return $this->jsonResponse(["error" => $message, "code" => "BAD_AUTH"], $statusCode);
    }

    protected function methodError($message = "The requested method is not allowed for this request", $statusCode = 400)
    {
        return $this->jsonResponse(["error" => $message, "code" => "BAD_METHOD"], $statusCode);
    }

    protected function routeError($message = "The requested route can not be found on the server", $statusCode = 404)
    {
        return $this->jsonResponse(["error" => $message, "code" => "BAD_ROUTE"], $statusCode);
    }

    protected function scopeError($messsage = "You don't have the right to access this route", $statusCode = 401)
    {
        return $this->jsonResponse(["error" => $messsage, "code" => "SCOPE_NOT_FOUND"], $statusCode);
    }

    // Exception Handlers

    protected function isModelNotFoundException(Exception $e)
    {
        return $e instanceof ModelNotFoundException;
    }

    protected function isOAuthServerException(Exception $e)
    {
        return $e instanceof OAuthServerException;
    }
    protected function isAuthenticationException(Exception $e)
    {
        return $e instanceof AuthenticationException;
    }
    protected function isMethodException(Exception $e)
    {
        return $e instanceof MethodNotAllowedHttpException;
    }
    protected function isNotFoundHttpException(Exception $e)
    {
        return $e instanceof NotFoundHttpException;
    }
    protected function isMissingScopeException(Exception $e)
    {
        return $e instanceof MissingScopeException;
    }

}
