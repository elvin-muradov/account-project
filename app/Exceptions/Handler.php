<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Traits\HttpResponses;
use Illuminate\Auth\AuthenticationException as AuthAuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    use HttpResponses;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function render($request, Throwable $e): Response|Throwable|JsonResponse
    {
        return match ($request->expectsJson()) {
            $e instanceof HttpResponseException, $e instanceof QueryException => $this->error(
                message: $e->getMessage(),
                code: 500
            ),
            $e instanceof NotFoundHttpException, $e instanceof RouteNotFoundException => $this->error(
                message: $e->getMessage(),
                code: 404
            ),
            $e instanceof AuthAuthenticationException => $this->error(
                message: $e->getMessage(),
                code: 401
            ),
            $e instanceof InvalidArgumentException => $this->error(
                message: $e->getMessage(),
                code: 23503
            ),

            default => parent::render($request, $e)
        };
    }
}
