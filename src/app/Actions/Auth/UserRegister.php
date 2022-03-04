<?php

namespace App\Actions\Auth;

use App\Mail\VerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserRegister {
    public static function execute($payload): User
    {
        $user = User::create($payload);


        Mail::to($user->email)->send(new VerificationMail(['message' => 'verification']));

        return $user;
    }
}
