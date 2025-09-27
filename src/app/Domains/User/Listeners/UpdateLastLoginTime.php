<?php

namespace App\Domains\User\Listeners;

use App\Domains\User\Events\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateLastLoginTime implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserLoggedIn $event): void
    {
        $event->user->update(['last_login_at' => now()]);
        
        Log::info('User logged in', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
        ]);
    }
}
