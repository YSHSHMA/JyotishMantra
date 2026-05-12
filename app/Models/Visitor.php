<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'city',
        'country',
        'url',
        'referer',
    ];

    protected $casts = [
        'id' => 'integer',
        'ip_address' => 'string',
        'city' => 'string',
        'country' => 'string',
        'url' => 'string',
        'referer' => 'string',
    ];
    
}
