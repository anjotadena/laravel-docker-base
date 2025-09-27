<?php

namespace App\Domains\Auth\Exceptions;

use App\Shared\Exceptions\BusinessRuleException;

class TokenExpiredException extends BusinessRuleException
{
    public function __construct(
        string $tokenType = 'token',
        array $context = []
    ) {
        parent::__construct(
            message: "The {$tokenType} has expired and is no longer valid.",
            errorCode: 'TOKEN_EXPIRED',
            businessRule: 'Tokens must not be expired',
            context: array_merge(['token_type' => $tokenType], $context),
            httpStatusCode: 401
        );
    }
}
