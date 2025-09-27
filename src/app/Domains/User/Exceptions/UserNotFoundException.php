<?php

namespace App\Domains\User\Exceptions;

use App\Shared\Exceptions\ResourceNotFoundException;

class UserNotFoundException extends ResourceNotFoundException
{
    public function __construct(
        string $identifier = '',
        array $context = []
    ) {
        parent::__construct(
            resourceType: 'User',
            identifier: $identifier,
            context: $context
        );
    }
}
