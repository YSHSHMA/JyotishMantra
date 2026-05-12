<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourFollowup extends Model
{
    use HasFactory;
    protected $table = 'tour_followup';
    protected $fillable =['id', 'lead_id', 'message', 'last_date', 'next_date', 'follow_by', 'follow_by_id', 'created_at', 'updated_at'];
}
