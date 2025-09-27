<?php

namespace App\Domains\User\Exceptions;

use App\Shared\Exceptions\BusinessRuleException;

class UserAlreadyDeletedException extends BusinessRuleException
{
    public function __construct(
        string $userId,
        array $context = []
    ) {
        parent::__construct(
            message: 'This user has already been deleted and cannot be modified.',
            errorCode: 'USER_ALREADY_DELETED',
            businessRule: 'Deleted users cannot be modified',
            context: array_merge(['user_id' => $userId], $context),
            httpStatusCode: 410
        );
    }
}
