<?php

namespace App\Domains\Auth\Exceptions;

use App\Shared\Exceptions\BusinessRuleException;

class EmailNotVerifiedException extends BusinessRuleException
{
    public function __construct(
        string $email,
        array $context = []
    ) {
        parent::__construct(
            message: 'Your email address must be verified before you can continue.',
            errorCode: 'EMAIL_NOT_VERIFIED',
            businessRule: 'User must verify email before authentication',
            context: array_merge(['email' => $email], $context),
            httpStatusCode: 403
        );
    }
}
