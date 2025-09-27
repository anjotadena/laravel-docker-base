<?php

namespace App\Domains\Auth\Exceptions;

use App\Shared\Exceptions\AuthenticationException;

class InvalidCredentialsException extends AuthenticationException
{
    public function __construct(
        string $message = 'The provided credentials are invalid.',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            reason: 'invalid_credentials',
            context: $context
        );
    }
}
