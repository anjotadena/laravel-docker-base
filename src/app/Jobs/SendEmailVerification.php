<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SendEmailVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    private $code;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, string $code)
    {
        $this->user = $user;

        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->send(
            new VerificationMail([
                'name' => $this->user->name,
                'code' => $this->code
            ])
        );
    }
}
