<?php

namespace App\Shared\Exceptions\Contracts;

interface BusinessRuleExceptionInterface extends DomainExceptionInterface
{
    /**
     * Get the business rule that was violated
     */
    public function getBusinessRule(): string;
}
