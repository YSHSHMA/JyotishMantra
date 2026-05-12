<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Muhurat extends Model
{
    use HasFactory;
    protected $fillable = [
        'year',
        'type',
        'titleLink',
        'formatted_date',
        'message',
        'image',
        'muhurat',
        'nakshatra',
        'tithi',
    ];
}