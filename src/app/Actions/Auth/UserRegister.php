<?php

namespace App\Actions\Auth;

use App\Models\User;

class UserRegister {
    public static function execute($payload): User
    {
        return User::create($payload);
    }
}
