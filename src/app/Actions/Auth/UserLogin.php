<?php

namespace App\Actions\Auth;

use App\Exceptions\EmailNotVerifiedException;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserLogin {
    public static function execute($payload): User
    {
        if (!Auth::attempt($payload)) {
            throw new InvalidCredentialsException;
        }

        $user = auth()->user();

        if (!$user->isEmailVerified()) {
            throw new EmailNotVerifiedException;
        }

        return $user;
    }
}
