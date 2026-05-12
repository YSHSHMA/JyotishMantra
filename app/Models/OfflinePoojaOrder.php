<?php

namespace App\Models;

use App\Models\Astrologer\Astrologer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflinePoojaOrder extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id','service_id','type','leads_id','package_id','package_main_price','package_price','transection_amount','wallet_amount','order_id','remain_amount','pay_amount','booking_date','payment_status','pooja_method','pooja_venue_type','temple_id','state','city','pincode','venue_address','latitude','longitude','landmark','is_edited','schedule_amount','schedule_status'];

    public function leads()
    {
        return $this->hasOne(OfflineLead::class, 'id', 'leads_id');
    }

    public function offlinePooja()
    {
        return $this->hasOne(PoojaOffline::class, 'id', 'service_id');
    }

    public function customers()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    public function customer()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    public function package()
    {
        return $this->hasOne(Package::class, 'id', 'package_id');
    }

    public function payments()
    {
        return $this->hasOne(PaymentRequest::class, 'transaction_id', 'payment_id');
    }

    public function pandit()
    {
        return $this->hasOne(Astrologer::class, 'id', 'pandit_assign');
    }

    public function temple()
    {
        return $this->hasOne(Temple::class, 'id', 'temple_id');
    }
}
