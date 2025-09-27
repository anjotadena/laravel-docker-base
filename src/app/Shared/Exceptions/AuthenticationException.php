<?php

namespace App\Shared\Exceptions;

class AuthenticationException extends DomainException
{
    public function __construct(
        string $message = 'Authentication failed',
        string $reason = '',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            errorCode: 'AUTHENTICATION_FAILED',
            context: array_merge(['reason' => $reason], $context),
            httpStatusCode: 401,
            shouldReport: false
        );
    }
}
