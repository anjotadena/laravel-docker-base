<?php

namespace App\Shared\Exceptions\Contracts;

use Throwable;

interface DomainExceptionInterface extends Throwable
{
    /**
     * Get the error code for this exception
     */
    public function getErrorCode(): string;

    /**
     * Get additional context for this exception
     */
    public function getContext(): array;

    /**
     * Get the HTTP status code for this exception
     */
    public function getHttpStatusCode(): int;

    /**
     * Check if this exception should be reported
     */
    public function shouldBeReported(): bool;
}
