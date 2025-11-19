<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DeviceManager extends Model
{
    use HasFactory;

    protected $table = 'device_managers';

    protected $fillable = [
        'user_id',
        'device_name',
        'ip_address',
        'browser',
        'os',
        'device_type',
        'user_agent',
        'session_token',
        'last_activity_at',
        'is_active',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns this device
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if device is currently active
     */
    public function isCurrentSession(): bool
    {
        return $this->session_token === session()->get('device_token');
    }

    /**
     * Get last activity time in human readable format
     */
    public function getLastActivityFormatted(): string
    {
        if (!$this->last_activity_at) {
            return 'Never';
        }

        return Carbon::parse($this->last_activity_at)->diffForHumans();
    }

    /**
     * Deactivate this device
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate this device
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
    }
}
