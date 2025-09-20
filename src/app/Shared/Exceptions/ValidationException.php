<?php

namespace App\Shared\Exceptions;

class ValidationException extends DomainException
{
    private array $errors;

    public function __construct(
        array $errors,
        string $message = 'Validation failed',
        string $errorCode = 'VALIDATION_ERROR'
    ) {
        parent::__construct($message, $errorCode, ['errors' => $errors]);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
