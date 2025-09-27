<?php

namespace App\Shared\Exceptions;

class ValidationException extends DomainException
{
    protected array $errors;

    public function __construct(
        array $errors,
        string $message = 'The given data is invalid.',
        array $context = []
    ) {
        $this->errors = $errors;

        parent::__construct(
            message: $message,
            errorCode: 'VALIDATION_FAILED',
            context: array_merge(['errors' => $errors], $context),
            httpStatusCode: 422,
            shouldReport: false
        );
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function toArray(): array
    {
        return [
            'error' => [
                'code' => $this->getErrorCode(),
                'message' => $this->getMessage(),
                'errors' => $this->getErrors(),
                'context' => $this->getContext(),
            ]
        ];
    }
}
