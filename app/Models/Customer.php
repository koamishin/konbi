<?php

namespace App\Models;

use App\Models\Traits\BelongsToStoreOrGlobal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, BelongsToStoreOrGlobal;

    protected $fillable = [
        'store_id',
        'name',
        'code',
        'phone',
        'email',
        'date_of_birth',
        'points',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'points' => 'integer',
    ];
}
