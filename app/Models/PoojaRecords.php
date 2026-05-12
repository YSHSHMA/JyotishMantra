<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoojaRecords extends Model
{
    use HasFactory;
    protected $table = 'pooja_records';
    
    protected $fillable = [
        'customer_id',
        'service_id',
        'product_id',
        'service_order_id',
        'package_id',
        'package_price',
        'amount',
        'coupon',
        'via_wallet',
        'via_online',
        'booking_date',
        'status',
    ];
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function vippooja()
    {
        return $this->belongsTo(Vippooja::class, 'service_id');
    }

}