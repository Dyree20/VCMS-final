<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clamping extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_no',
        'plate_no',
        'vehicle_type',
        'reason',
        'location',
        'date_clamped',
        'status',
        'photo',
        'fine_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function zone()
    {
        return $this->belongsTo(ParkingZone::class, 'parking_zone_id');
    }

    public function payees()
    {
        return $this->hasMany(Payee::class);
    }
}
