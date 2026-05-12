<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonateLeadFollowup extends Model
{
    use HasFactory;
    protected $table = 'donate_followup';
    protected $fillable =['id', 'lead_id', 'message', 'follow_by', 'follow_by_id','type', 'last_date', 'next_date', 'created_at', 'updated_at'];

}
