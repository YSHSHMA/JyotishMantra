<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sankalp extends Model
{
    use HasFactory;

    protected $table = 'sankalps';

    protected $fillable = [
        'sankalp_name',
        'user_id',
        'user_mantras_id',
        'order_id',
        'hours',
        'day',
        'end_time',
        'start_time',
        'end_date',
        'start_date',
        'count'
    ];
}
