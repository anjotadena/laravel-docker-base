<?php

namespace App\Exceptions;

use App\Traits\JsonResponser;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use JsonResponser;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Exception $e, $request) {
            if (!$request->is('api/*')) {
                return;
            }

            if ($e instanceof NotFoundHttpException) {
                return $this->messageResponse(__('Endpoint not found.'), Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof ValidationException) {
                return $this->errorResponse([
                    'message' => __('Invalid data provided.'),
                    'errors' => $e->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($e instanceof UnauthorizedException) {
                return $this->errorResponse([
                    'message' => $e->getMessage()
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($e instanceof AuthenticationException) {
                return $this->errorResponse([
                    'message' => $e->getMessage()
                ], Response::HTTP_FORBIDDEN);
            }

            // Don't display relevant error when debug is false
            $isAppOnDebug = (bool) config('app.debug', false);

            return $this->messageResponse($isAppOnDebug ? $e->getMessage() : __('Ooop! Something wen\'t wrong. Please contact developer.'), Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}
