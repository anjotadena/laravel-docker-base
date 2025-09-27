<?php

namespace App\Shared\Exceptions;

use App\Shared\Exceptions\Contracts\BusinessRuleExceptionInterface;

abstract class BusinessRuleException extends DomainException implements BusinessRuleExceptionInterface
{
    protected string $businessRule;

    public function __construct(
        string $message = '',
        string $errorCode = 'BUSINESS_RULE_VIOLATION',
        string $businessRule = '',
        array $context = [],
        int $httpStatusCode = 422,
        bool $shouldReport = false
    ) {
        parent::__construct($message, $errorCode, $context, $httpStatusCode, $shouldReport);
        $this->businessRule = $businessRule;
    }

    public function getBusinessRule(): string
    {
        return $this->businessRule;
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['error']['business_rule'] = $this->getBusinessRule();
        return $array;
    }
}
