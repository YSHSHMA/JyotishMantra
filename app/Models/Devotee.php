<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devotee extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'gotra',
        'service_order_id',
        'type',
        'members',
        'address_city',
        'address_state',
        'address_pincode',
        'pincode',
        'house_no',
        'area',
        'latitude',
        'longitude',
        'landmark',
        'is_prashad',
    ];
   public function serviceOrder()
    {
        // Here, local_key = service_orders.order_id
        return $this->belongsTo(Service_order::class, 'service_order_id', 'order_id');
    }

    public function chadhavaOrder()
    {
        return $this->belongsTo(Chadhava_orders::class, 'service_order_id', 'order_id');
    }
}