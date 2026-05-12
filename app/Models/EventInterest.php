<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventInterest extends Model
{
    use HasFactory;
    protected $table = 'event_interest';
    protected $fillable =['id', 'event_id', 'user_id', 'created_at', 'updated_at'];
}
