<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_no',
        'clamping_id',
        'name',
        'contact_number',
        'payment_method',
        'amount_paid',
        'amount',
        'payment_date',
        'status',
    ];

    public function clamping()
    {
        return $this->belongsTo(Clamping::class);
    }
    
}
