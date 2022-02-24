<?php

namespace App\Actions\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserLogin {
    public static function execute($payload): User
    {
        if (Auth::attempt($payload)) {
            return auth()->user();
        }

        throw new InvalidCredentialsException;
    }
}
