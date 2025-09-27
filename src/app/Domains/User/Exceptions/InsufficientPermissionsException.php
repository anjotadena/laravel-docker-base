<?php

namespace App\Domains\User\Exceptions;

use App\Shared\Exceptions\UnauthorizedException;

class InsufficientPermissionsException extends UnauthorizedException
{
    public function __construct(
        string $requiredPermission,
        array $context = []
    ) {
        parent::__construct(
            message: "You do not have the required '{$requiredPermission}' permission to perform this action.",
            action: $requiredPermission,
            context: array_merge(['required_permission' => $requiredPermission], $context)
        );
    }
}
