<?php

namespace App\Shared\Exceptions;

class AuthenticationException extends DomainException
{
    public function __construct(
        string $message = 'Authentication failed',
        string $errorCode = 'AUTH_ERROR'
    ) {
        parent::__construct($message, $errorCode, [], 401);
    }
}
