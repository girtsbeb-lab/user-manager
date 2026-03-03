<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'street_number',
        'street_name',
        'city',
        'state',
        'country',
        'postcode',
        'latitude',
        'longitude',
        'timezone_offset',
        'timezone_description',
    ];

    protected $casts = [
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->street_number} {$this->street_name}, {$this->city}, {$this->state}, {$this->country} {$this->postcode}";
    }
}
