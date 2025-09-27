<?php

namespace App\Domains\Auth\DTOs;

use App\Domains\User\ValueObjects\Email;
use App\Domains\User\ValueObjects\Name;
use App\Domains\User\ValueObjects\Password;

class RegisterDto
{
    public function __construct(
        public readonly Name $name,
        public readonly Email $email,
        public readonly Password $password
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: new Name($data['name']),
            email: new Email($data['email']),
            password: new Password($data['password'])
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name->value(),
            'email' => $this->email->value(),
            'password' => $this->password->value(),
        ];
    }
}
