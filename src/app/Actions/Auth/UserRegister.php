<?php

namespace App\Actions\Auth;

use App\Jobs\SendEmailVerification;
use App\Mail\VerificationMail;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserRegister {
    public static function execute($payload): User
    {
        return DB::transaction(function () use ($payload) {
            $user = User::create($payload);

            self::sendEmailOnQueue($user, self::createVerificationCode($user));
            
            return $user;
        });
    }

    private static function sendEmailOnQueue($user, $code)
    {
        // Send email
        Mail::to($user->email)->queue(
            new VerificationMail([
                'name' => $user->name,
                'code' => $code
            ])
        );
    }

    private static function createVerificationCode(User $user): string
    {
        $code = VerificationCode::generate();

        $user->verificationCodes()->create([
            'user_id' => $user->id,
            'code' => $code,
            'expired_at' => now()->addHour()
        ]);

        return $code;
    }
}
