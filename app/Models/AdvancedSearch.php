<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvancedSearch extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'filters',
        'is_public',
    ];

    protected $casts = [
        'filters' => 'array',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
