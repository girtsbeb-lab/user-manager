<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'external_id',
        'first_name',
        'last_name',
        'email',
        'username',
        'gender',
        'date_of_birth',
        'age',
        'nationality',
        'avatar',
        'thumbnail',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
