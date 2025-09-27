<?php

namespace App\Shared\Exceptions;

use App\Shared\Exceptions\Contracts\DomainExceptionInterface;
use Exception;

abstract class DomainException extends Exception implements DomainExceptionInterface
{
    protected string $errorCode;
    protected array $context;
    protected int $httpStatusCode;
    protected bool $shouldReport;

    public function __construct(
        string $message = '',
        string $errorCode = 'DOMAIN_ERROR',
        array $context = [],
        int $httpStatusCode = 500,
        bool $shouldReport = true,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->context = $context;
        $this->httpStatusCode = $httpStatusCode;
        $this->shouldReport = $shouldReport;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function shouldBeReported(): bool
    {
        return $this->shouldReport;
    }

    /**
     * Convert exception to array for API responses
     */
    public function toArray(): array
    {
        return [
            'error' => [
                'code' => $this->getErrorCode(),
                'message' => $this->getMessage(),
                'context' => $this->getContext(),
            ]
        ];
    }
}
