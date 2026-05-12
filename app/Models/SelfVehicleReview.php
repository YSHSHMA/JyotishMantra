<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfVehicleReview extends Model
{
    use HasFactory;
    protected $table = 'self_vehicle_review';
    protected $fillable = ['id', 'order_id', 'user_id', 'self_vehicle_id', 'star', 'comment', 'status', 'is_edited', 'created_at', 'updated_at'];
    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
     public function selfvehicleinfo()
    {
        return $this->hasOne(SelfDrivingCabs::class, 'id', 'restaurant_id')->with('getTraveller');
    }
}
