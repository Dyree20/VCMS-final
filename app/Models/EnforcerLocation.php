<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnforcerLocation extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'address',
        'accuracy_meters',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    public function scopeRecent($query, $minutes = 30)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }
}
