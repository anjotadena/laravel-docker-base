<?php

namespace App\Actions\Auth;

use App\Jobs\SendEmailVerification;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\DB;

class UserRegister {
    public static function execute($payload): User
    {
        return DB::transaction(function () use ($payload) {
            $user = User::create($payload);

            // generate verification code
            $code = VerificationCode::generate();

            $verification = $user->verificationCodes()->create(['user_id' => $user->id, 'code' => $code, 'expired_at' => now()->addHour()]);

            // Send email
            dispatch(new SendEmailVerification($user, $verification->code));

            return $user;
        });
    }
}
