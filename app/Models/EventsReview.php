<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsReview extends Model
{
    use HasFactory;
    protected $table = 'events_review';
    protected $fillable = ['id', 'user_id', 'event_id', 'star', 'comment', 'order_id', 'is_edited', 'status', 'created_at', 'updated_at'];


    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function event()
    {
        return $this->hasOne(Events::class, 'id', 'event_id');
    }
}