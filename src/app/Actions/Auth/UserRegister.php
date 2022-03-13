<?php

namespace App\Actions\Auth;

use App\Jobs\SendEmailVerification;
use App\Models\User;

class UserRegister {
    public static function execute($payload): User
    {
        $user = User::create($payload);

        // Send email
        dispatch(new SendEmailVerification($user));

        return $user;
    }
}
