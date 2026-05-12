<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelReview extends Model
{
    use HasFactory;
    protected $table = 'hotel_review';
    protected $fillable = ['id', 'user_id', 'hotel_id', 'comment', 'star', 'image', 'created_at', 'updated_at', 'status'];

    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function hotelData()
    {
        return $this->HasMany(Hotels::class, 'id', 'hotel_id');
    }
    public function hotelinfo()
    {
        return $this->hasOne(Hotels::class, 'id', 'hotel_id');
    }
}