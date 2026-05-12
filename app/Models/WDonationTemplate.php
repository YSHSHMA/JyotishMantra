<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class WDonationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'template_name',
        'body',
        'status',
    ];


    protected $casts = [
        'order_id' => 'string',
        'template_name' => 'string', 
        'body' => 'string',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
