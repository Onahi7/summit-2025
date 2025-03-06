<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Validation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'validator_id',
        'type',
        'status',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    public function scopeAccreditation($query)
    {
        return $query->where('type', 'accreditation');
    }

    public function scopeMorningMeal($query)
    {
        return $query->where('type', 'morning_meal');
    }

    public function scopeEveningMeal($query)
    {
        return $query->where('type', 'evening_meal');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now());
    }
}
