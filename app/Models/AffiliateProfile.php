<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'referral_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'affiliate_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'affiliate_id');
    }
}