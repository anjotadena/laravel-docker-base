<?php

namespace App\Shared\Exceptions;

class UnauthorizedException extends DomainException
{
    public function __construct(
        string $message = 'You are not authorized to perform this action.',
        string $action = '',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            errorCode: 'UNAUTHORIZED',
            context: array_merge(['action' => $action], $context),
            httpStatusCode: 403,
            shouldReport: false
        );
    }
}
