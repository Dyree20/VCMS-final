<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_user')
                    ->withPivot('assigned_by', 'assigned_at')
                    ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function enforcers()
    {
        return $this->members()->where('role_id', 2); // Assuming 2 is enforcer role_id
    }

    public function parkingZones(): BelongsToMany
    {
        return $this->belongsToMany(ParkingZone::class, 'parking_zone_team')
                    ->withPivot('assigned_by', 'assigned_at')
                    ->withTimestamps();
    }
}
