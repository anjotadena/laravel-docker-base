<?php

namespace App\Exceptions;

use App\Shared\Exceptions\Contracts\DomainExceptionInterface;
use App\Traits\JsonResponser;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
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
        // Custom reporting for domain exceptions
        $this->reportable(function (Throwable $e) {
            if ($e instanceof DomainExceptionInterface && !$e->shouldBeReported()) {
                return false;
            }
        });

        // Custom rendering for API requests
        $this->renderable(function (Exception $e, $request) {
            if (!$request->is('api/*')) {
                return;
            }

            return $this->handleApiException($e, $request);
        });
    }

    /**
     * Handle exceptions for API requests
     */
    protected function handleApiException(Exception $e, $request): JsonResponse
    {
        // Handle domain exceptions
        if ($e instanceof DomainExceptionInterface) {
            return $this->handleDomainException($e);
        }

        // Handle Laravel's built-in exceptions
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

        // Handle unexpected exceptions
        return $this->handleUnexpectedException($e);
    }

    /**
     * Handle domain exceptions
     */
    protected function handleDomainException(DomainExceptionInterface $e): JsonResponse
    {
        $data = [
            'error' => [
                'code' => $e->getErrorCode(),
                'message' => $e->getMessage(),
            ]
        ];

        // Add context if available
        if (!empty($e->getContext())) {
            $data['error']['context'] = $e->getContext();
        }

        // Add business rule information if applicable
        if (method_exists($e, 'getBusinessRule') && !empty($e->getBusinessRule())) {
            $data['error']['business_rule'] = $e->getBusinessRule();
        }

        // Add validation errors if applicable
        if (method_exists($e, 'getErrors') && !empty($e->getErrors())) {
            $data['error']['errors'] = $e->getErrors();
        }

        return response()->json($data, $e->getHttpStatusCode());
    }

    /**
     * Handle unexpected exceptions
     */
    protected function handleUnexpectedException(Exception $e): JsonResponse
    {
        $isAppOnDebug = (bool) config('app.debug', false);

        $message = $isAppOnDebug
            ? $e->getMessage()
            : __('Something went wrong. Please contact the developer.');

        $data = [
            'error' => [
                'code' => 'INTERNAL_ERROR',
                'message' => $message,
            ]
        ];

        // Add debug information in development
        if ($isAppOnDebug) {
            $data['error']['debug'] = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
        }

        return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
