<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'reference',
        'status',
        'payment_data'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function markAsCompleted($paymentData = null)
    {
        $this->update([
            'status' => 'completed',
            'payment_data' => $paymentData
        ]);

        $this->user->update(['payment_status' => 'paid']);
    }
}
