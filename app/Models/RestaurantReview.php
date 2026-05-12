<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantReview extends Model
{
    use HasFactory;
    protected $table = 'restaurant_review';
    protected $fillable = ['id', 'user_id', 'restaurant_id', 'comment', 'star', 'image', 'created_at', 'updated_at', 'status'];

    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function restaurantData()
    {
        return $this->HasMany(Restaurant::class, 'id', 'restaurant_id');
    }
     public function restaurantinfo()
    {
        return $this->hasOne(Restaurant::class, 'id', 'restaurant_id');
    }
}