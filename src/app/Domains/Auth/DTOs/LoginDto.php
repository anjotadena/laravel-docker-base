<?php

namespace App\Domains\Auth\DTOs;

class LoginDto
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly bool $remember = false
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            remember: $data['remember'] ?? false
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'remember' => $this->remember,
        ];
    }
}
