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
        'user_id',
        'activated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expired_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generate(): string
    {
        $code = random_int(100000, 999999);

        return $code;
    }

    public function activate(): VerificationCode
    {
        $this->activated_at = now()->format('Y-m-d H:i:s');

        $this->save();

        return $this;
    }
}
