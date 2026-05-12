<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourCancelTicket extends Model
{
    use HasFactory;
    protected $table = 'tour_cancel_ticket';
    protected $fillable =['id', 'user_id', 'order_id', 'message', 'status', 'created_at', 'updated_at'];
}
