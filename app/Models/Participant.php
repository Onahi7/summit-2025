<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    protected $fillable = [
        'user_id',
        'phone_number',
        'state',
        'napps_chapter',
        'passport_photo',
        'ticket_type',
        'qr_code',
        'payment_status',
        'amount_paid',
        'payment_reference',
    ];

    protected $casts = [
        'payment_status' => 'boolean',
        'amount_paid' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mealValidations(): HasMany
    {
        return $this->hasMany(MealValidation::class);
    }
}
