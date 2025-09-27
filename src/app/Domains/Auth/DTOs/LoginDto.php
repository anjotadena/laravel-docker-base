<?php

namespace App\Domains\Auth\DTOs;

use App\Domains\User\ValueObjects\Email;
use App\Domains\User\ValueObjects\Password;

class LoginDto
{
    public function __construct(
        public readonly Email $email,
        public readonly Password $password,
        public readonly bool $remember = false
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: new Email($data['email']),
            password: new Password($data['password']),
            remember: $data['remember'] ?? false
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email->value(),
            'password' => $this->password->value(),
            'remember' => $this->remember,
        ];
    }
}
