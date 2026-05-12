<?php

namespace App\Models;

use App\Models\Astrologer\Astrologer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'astro_id',
        'service_id',
        'booking_date',    
        'type',
        'order_id',
        'txn_id',
        'amount',
        'commission',
        'indivudual_commission',
        'tax',

    ];

    public function serviceOrder()
    {
        return $this->hasOne(Service_order::class, 'order_id', 'order_id');
    }
    public function chadhavaOrder()
    {
        return $this->hasOne(Chadhava_orders::class, 'order_id', 'order_id');
    }
    public function offlinepoojaOrder()
    {
        return $this->hasOne(OfflinePoojaOrder::class, 'order_id', 'order_id');
    }
    public function services()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function astrologer()
    {
        return $this->belongsTo(Astrologer::class, 'astro_id', 'id');
    }
}
