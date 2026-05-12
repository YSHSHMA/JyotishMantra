<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFollowup extends Model
{
    use HasFactory;
    protected $table = 'event_followup';
    protected $fillable =['id', 'lead_id', 'message', 'last_date', 'next_date', 'type', 'follow_by', 'follow_by_id', 'created_at', 'updated_at'];
}
