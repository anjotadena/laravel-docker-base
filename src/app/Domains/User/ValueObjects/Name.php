<?php

namespace App\Domains\User\ValueObjects;

use InvalidArgumentException;

class Name
{
    public function __construct(
        private readonly string $value
    ) {
        $trimmed = trim($value);
        
        if (empty($trimmed)) {
            throw new InvalidArgumentException('Name cannot be empty');
        }
        
        if (strlen($trimmed) < 2) {
            throw new InvalidArgumentException('Name must be at least 2 characters long');
        }
        
        if (strlen($trimmed) > 255) {
            throw new InvalidArgumentException('Name cannot exceed 255 characters');
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

    public function equals(Name $other): bool
    {
        return $this->value === $other->value;
    }
}
