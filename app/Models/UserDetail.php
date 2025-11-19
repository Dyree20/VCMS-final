<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id',
        'photo',
        'address',
        'gender',
        'birth_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
