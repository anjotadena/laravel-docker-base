<?php

namespace Tests\Unit\Mail;

use App\Mail\VerificationMail;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    public function testVerificationEmailContent()
    {
        $user = User::factory()->create(['password' => 'password']);
        $code = VerificationCode::generate();

        $mailable = new VerificationMail([
            'name' => $user->name,
            'code' => $code
        ]);

        $mailable->assertSeeInHtml($user->name);
        $mailable->assertSeeInHtml($code);
    }
}
