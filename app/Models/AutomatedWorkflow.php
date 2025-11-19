<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomatedWorkflow extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'conditions',
        'actions',
        'is_enabled',
        'execution_order',
        'created_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true)->orderBy('execution_order');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
