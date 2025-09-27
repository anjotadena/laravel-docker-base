<?php

namespace App\Domains\Auth\Exceptions;

use App\Shared\Exceptions\BusinessRuleException;

class InvalidCodeException extends BusinessRuleException
{
    public function __construct(
        string $codeType = 'verification code',
        array $context = []
    ) {
        parent::__construct(
            message: "The provided {$codeType} is invalid or has expired.",
            errorCode: 'INVALID_CODE',
            businessRule: 'Verification codes must be valid and not expired',
            context: array_merge(['code_type' => $codeType], $context),
            httpStatusCode: 422
        );
    }
}
