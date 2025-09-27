<?php

namespace App\Shared\Exceptions;

class ResourceNotFoundException extends DomainException
{
    public function __construct(
        string $resourceType = 'Resource',
        string $identifier = '',
        array $context = []
    ) {
        $message = $identifier
            ? "The {$resourceType} with identifier '{$identifier}' was not found."
            : "The requested {$resourceType} was not found.";

        parent::__construct(
            message: $message,
            errorCode: 'RESOURCE_NOT_FOUND',
            context: array_merge(['resource_type' => $resourceType, 'identifier' => $identifier], $context),
            httpStatusCode: 404,
            shouldReport: false
        );
    }
}
