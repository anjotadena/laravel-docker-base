<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'expired_at',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generate(): string
    {
        $code = random_int(100000, 999999);

        return $code;
    }
}
