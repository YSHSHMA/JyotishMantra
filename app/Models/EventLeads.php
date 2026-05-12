<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventLeads extends Model
{
    use HasFactory;
    protected $table = 'event_leads';
    protected $fillable =['id','user_phone', 'user_name', 'event_id', 'package_id','venue_id', 'qty', 'amount','coupon_amount', 'total_amount', 'test','coupon_id','user_information','status', 'created_at', 'updated_at'];

    public function event(){
        return $this->hasOne(Events::class,'id','event_id');
    }
    public function package(){
        return $this->hasOne(EventPackage::class,'id','package_id');
    }
    public function coupon(){
        return $this->hasOne(Coupon::class,'id','coupon_id'); 
    }
    public function followby()
    {
        return $this->hasOne(EventFollowup::class, 'lead_id','id');
    }
}