<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourReviews extends Model
{
    use HasFactory;
    protected $table = 'tour_review';
    protected $fillable = ['id', 'order_id', 'user_id', 'tour_id', 'star', 'comment', 'image', 'status', 'is_edited', 'created_at', 'updated_at'];

    public function OrderTour()
    {
        return $this->hasOne(TourOrder::class, 'id', 'tour_id')->with('Tour');
    }
    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}