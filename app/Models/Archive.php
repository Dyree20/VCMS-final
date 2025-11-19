<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'clamping_id',
        'user_id',
        'ticket_no',
        'plate_no',
        'vehicle_type',
        'reason',
        'location',
        'fine_amount',
        'archived_status',
        'archived_date',
        'archived_by',
    ];

    protected $casts = [
        'archived_date' => 'datetime',
    ];

    // Relationships
    public function clamping()
    {
        return $this->belongsTo(Clamping::class, 'clamping_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}
