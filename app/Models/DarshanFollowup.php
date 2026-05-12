<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DarshanFollowup extends Model
{
    use HasFactory;
    protected $table = "darshan_followup";

    protected $fillable = ['id', 'lead_id', 'follow_by', 'follow_by_id', 'message', 'last_date', 'next_date', 'created_at', 'updated_at'];

    

}
