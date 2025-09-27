<?php

namespace App\Domains\User\Exceptions;

use App\Shared\Exceptions\BusinessRuleException;

class EmailAlreadyTakenException extends BusinessRuleException
{
    public function __construct(
        string $email,
        array $context = []
    ) {
        parent::__construct(
            message: "The email address '{$email}' is already registered.",
            errorCode: 'EMAIL_ALREADY_TAKEN',
            businessRule: 'Email addresses must be unique',
            context: array_merge(['email' => $email], $context),
            httpStatusCode: 422
        );
    }
}
