<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class EventOrderItems extends Model
{
    use HasFactory;
    protected $table = 'event_orders_items';

    protected $fillable = ['id', 'order_id', 'package_id', 'no_of_seats', 'amount', 'user_information', 'created_at', 'updated_at'];

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function orderdata()
    {
        return $this->hasOne(EventOrder::class, 'id', 'order_id');
    }
    public function category()
    {
        return $this->hasOne(EventPackage::class, 'id', 'package_id');
    }
}
