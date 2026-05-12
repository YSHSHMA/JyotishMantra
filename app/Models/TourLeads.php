<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourLeads extends Model
{
    use HasFactory;
    protected $table = 'tour_leads';
    protected $fillable =['id', 'tour_id','order_id', 'package_id', 'user_id', 'amount', 'coupon_id', 'coupan_amount', 'booking_package', 'part_payment', 'pickup_address', 'pickup_date', 'pickup_time', 'pickup_long', 'pickup_lat', 'qty', 'status', 'platform', 'via_wallet', 'via_online', 'amount_status','whatsapp_hit','create_by_vendor', 'created_at', 'updated_at'];

    public function Tour(){
        return $this->hasOne(TourVisits::class,'id','tour_id');
    }
    public function Coupan(){
        return $this->hasOne(Coupon::class,'id','coupon_id');
    }
    public function TourOrder(){
        return $this->hasOne(TourOrder::class,'id','order_id');
    }
    public function userData(){
        return $this->hasOne(User::class,'id','user_id');
    }
    public function followby()
    {
        return $this->hasOne(TourFollowup::class, 'lead_id','id');
    }
}