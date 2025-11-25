<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id',
        'photo',
        'address',
        'nationality',
        'gender',
        'birth_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getBirthdateAttribute()
    {
        return $this->attributes['birth_date'] ?? null;
    }

    public function setBirthdateAttribute($value): void
    {
        $this->attributes['birth_date'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
