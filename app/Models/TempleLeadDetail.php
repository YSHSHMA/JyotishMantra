<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempleLeadDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'temple_id',
        'pandit_id',
        'package_id',
        'booking_date',
        'payment_status',
        'type',
        'type_order_id',
        'customer_qty',
        'customers',
        'amount',
        'order_id',
        'time_slot_id',
        'locker_items',
        'type_order_id',
    ];
    // TempleLeadDetail
    public function master()
    {
        return $this->belongsTo(TempleLeadMaster::class, 'order_id', 'order_id');
    }
    public function package(){
        return $this->hasOne(TempleServicePrice::class, 'id', 'package_id');
    }

    public function timeslot(){
        return $this->hasOne(TempleServiceSlot::class, 'id', 'time_slot_id');
    }
    
    public function price() {
        // package_id in lead detail points to temple_service_prices.id
        return $this->belongsTo(TempleServicePrice::class, 'package_id', 'id');
    }
    
    public function packageInfo() {
        // optionally link to package master if needed
        return $this->belongsTo(TempleServicePackages::class, 'package_id', 'id');
    }
 
    public function purohit()
    {
        return $this->belongsTo(Purohit::class, 'pandit_id', 'id');
    }


}
