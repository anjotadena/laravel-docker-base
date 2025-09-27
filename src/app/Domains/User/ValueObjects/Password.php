<?php

namespace App\Domains\User\ValueObjects;

use InvalidArgumentException;

class Password
{
    public function __construct(
        private readonly string $value
    ) {
        if (strlen($value) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters long');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Password $other): bool
    {
        return $this->value === $other->value;
    }
}
