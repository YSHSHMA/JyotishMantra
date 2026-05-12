<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfVehicleLeads extends Model
{
    use HasFactory;
    protected $table = 'self_vehicle_lead';
    protected $fillable = ['id', 'user_id', 'order_id', 'vehicle_id', 'type', 'category_id', 'cab_id', 'traveller_id', 'package_id', 'wallet_type', 'price', 'security_amount', 'coupan_amount', 'coupan_id', 'admin_amount', 'tax_amount', 'tax', 'final_amount', 'pickup_address', 'pickup_date', 'droup_date', 'platform', 'via_wallet', 'via_online', 'status', 'whatsapp_hit', 'f_name', 'l_name', 'age', 'phone_number', 'email', 'aadhaar_number', 'pancard', 'driving_licence', 'created_at', 'updated_at'];

    public function SelfCabData()
    {
        return $this->hasOne(SelfDrivingCabs::class, 'id', 'vehicle_id');
    }
    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function Coupan()
    {
        return $this->hasOne(Coupon::class, 'id', 'coupan_id');
    }
    public function OrderInfo()
    {
        return $this->hasOne(SelfVehicleOrder::class, 'id', 'order_id');
    }
    public function followby()
    {
        return $this->hasOne(SelfVehicleFollowup::class, 'lead_id', 'id');
    }
}
