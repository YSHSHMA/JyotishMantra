<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KundliFollowup extends Model
{
    use HasFactory;
    protected $table = 'kundli_followup';
    protected $fillable = ['id', 'lead_id', 'type', 'follow_by', 'follow_by_id', 'last_date', 'message', 'next_date', 'created_at', 'updated_at'];
}
