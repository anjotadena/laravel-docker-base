<?php

namespace App\Exceptions;

use App\Traits\JsonResponser;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
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
                
            return $this->messageResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}
