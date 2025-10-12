<?php

namespace App\Domains\Auth\Exceptions;

use App\Shared\Exceptions\BusinessRuleException;

class AccountLockedException extends BusinessRuleException
{
    public function __construct(
        string $lockReason = 'Multiple failed login attempts',
        ?\DateTimeInterface $unlockAt = null,
        array $context = []
    ) {
        $message = 'Your account has been temporarily locked.';

        if ($unlockAt) {
            $message .= " Please try again after {$unlockAt->format('Y-m-d H:i:s')}.";
        }

        parent::__construct(
            message: $message,
            errorCode: 'ACCOUNT_LOCKED',
            businessRule: 'Account must not be locked for authentication',
            context: array_merge([
                'lock_reason' => $lockReason,
                'unlock_at' => $unlockAt?->format('c')
            ], $context),
            httpStatusCode: 423
        );
    }
}
