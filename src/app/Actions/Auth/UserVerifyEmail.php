<?php

namespace App\Actions\Auth;

use App\Exceptions\InvalidCodeException;
use App\Models\User;

class UserVerifyEmail {
    public static function execute($payload): bool
    {
        $user = User::findOrFail($payload['user_id']);

        $verificationCode = $user->verificationCodes()
                    ->where('code', $payload['code'])
                    ->where('expired_at', '>', now()->format('Y-m-d H:i:s'))
                    ->latest()
                    ->first();

        // either invalid or expired throw generic error
        if (!$verificationCode) {
            throw new InvalidCodeException;
        }

        $verificationCode->activate();

        $user->verifyEmail();

        return true;
    }
}
