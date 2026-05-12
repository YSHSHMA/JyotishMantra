<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfVehicleOrder extends Model
{
    use HasFactory;
    protected $table = 'self_vehicle_orders';
    protected $fillable = ['id', 'user_id', 'order_id', 'vehicle_id', 'type', 'category_id', 'cab_id', 'traveller_id', 'package_id', 'price', 'security_amount', 'coupan_amount', 'coupan_id', 'admin_amount', 'tax_amount', 'tax', 'final_amount', 'pickup_address', 'pickup_date', 'droup_date', 'platform', 'f_name', 'l_name', 'age', 'phone_number', 'email', 'aadhaar_number', 'pancard', 'driving_licence', 'lead_id', 'ex_time', 'ex_change', 'pickup_otp', 'droup_otp', 'order_accept_status','status', 'transaction_id', 'payment_method', 'created_at', 'updated_at', 'on_load', 'refund_status', 'refund_amount'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->order_id = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('order_id', 'like', 'SVO%')->orderBy('id', 'desc')->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->order_id, 3); // remove 'SVO'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'SVO' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // SVO0001, SVO0002, ...
    }



    public function SelfCabData()
    {
        return $this->hasOne(SelfDrivingCabs::class, 'id', 'vehicle_id')->with(['getTraveller','getType','getCategory','getCabId']);
    }
    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function TravellerInfo()
    {
        return $this->hasOne(TourAndTravel::class, 'id', 'traveller_id');
    }
}
