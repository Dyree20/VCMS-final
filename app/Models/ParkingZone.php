<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParkingZone extends Model
{
    protected $fillable = [
        'name',
        'description',
        'latitude',
        'longitude',
        'radius_meters',
        'fine_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'fine_amount' => 'float',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function clampings(): HasMany
    {
        return $this->hasMany(Clamping::class, 'parking_zone_id');
    }

    public function assignedEnforcers(): HasMany
    {
        return $this->hasMany(User::class, 'parking_zone_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'parking_zone_team')
                    ->withPivot('assigned_by', 'assigned_at')
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isWithinZone($latitude, $longitude): bool
    {
        $distance = $this->calculateDistance($latitude, $longitude);
        return $distance <= $this->radius_meters;
    }

    private function calculateDistance($lat, $lon): float
    {
        $earthRadius = 6371000; // in meters
        $lat1 = deg2rad($this->latitude);
        $lat2 = deg2rad($lat);
        $deltaLat = deg2rad($lat - $this->latitude);
        $deltaLon = deg2rad($lon - $this->longitude);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1) * cos($lat2) * sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * asin(sqrt($a));

        return $earthRadius * $c;
    }
}
