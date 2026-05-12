<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrustPanditTransection extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type_order_id',
        'temple_id',
        'trust_id',
        'pandit_id',
        'package_id',
        'package_price',
        'payment_method',
        'payment_status',
    ];

    // Temple relationship
    public function temple()
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }

    // Trust relationship
    public function trust()
    {
        return $this->belongsTo(DonateTrust::class, 'trust_id');
    }

    // Pandit relationship
    public function purohit()
    {
        return $this->belongsTo(Purohit::class, 'pandit_id');
    }

    // Package relationship
    public function package()
    {
        return $this->belongsTo(TempleServicePrice::class, 'package_id');
    }

    // Order relationship
    public function darshanOrder()
    {
        return $this->belongsTo(DarshanOrder::class, 'order_id', 'order_id'); 
    }
    public function templeOrder()
    {
        return $this->belongsTo(TempleOrderDetails::class, 'order_id', 'order_id'); 
    }
}