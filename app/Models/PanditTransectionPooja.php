<?php

namespace App\Models;

use App\Models\Astrologer\Astrologer;
use App\Models\Vippooja;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;

class PanditTransectionPooja extends Model
{
    protected $table = 'pandit_transection_poojas'; // Check your actual table name

    protected $fillable = [
        'pandit_id',
        'service_id',
        'service_order_id',
        'type',
        'order_amount',
        'pandit_amount',
        'booking_date',
        'admin_commission',
        'govt_tax',
    ];
    public function serviceOrder() {
        return $this->belongsTo(Service_order::class,'service_order_id','order_id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function vipPooja() {
        return $this->belongsTo(Vippooja::class, 'service_id');
    }

    public function pandit() {
        return $this->belongsTo(Astrologer::class, 'pandit_id');
    }
    
    public function chadhavaOrder()
    {
        return $this->belongsTo(Chadhava_orders::class, 'service_order_id', 'order_id');
    }
    
    public function offlinepoojaOrder()
    {
        return $this->belongsTo(OfflinePoojaOrder::class, 'service_order_id', 'order_id');
    }
}