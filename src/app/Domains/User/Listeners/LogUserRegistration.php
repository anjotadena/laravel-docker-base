<?php

namespace App\Domains\User\Listeners;

use App\Domains\User\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogUserRegistration implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        Log::info('User registered', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'name' => $event->user->name,
        ]);
    }
}
