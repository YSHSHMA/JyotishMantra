<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflinepoojaFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'lead_id',
        'pooja_id',
        'type',
        'follow_by',
        'follow_by_id',
        'last_date',
        'message',
        'next_date',
    ];
}
