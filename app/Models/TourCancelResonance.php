<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourCancelResonance extends Model
{
    use HasFactory;
    protected $table = 'tour_cancel_resonance';
    protected $fillable = ['id', 'ticket_id','type', 'msg', 'created_at', 'updated_at'];
}
