<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

class Prashad_deliverys extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'type',
        'order_id',
        'service_id',
        'user_id',
        'product_id',
        'booking_date',
        'order_completed',
        'payment_type',
        'manifest_id',
        'shippingurl',
        'awb',
        'carrier_id',
        'carrier_name',
        'message',
        'warehouse_id',
        'delivery_charge',
        'delivery_partner',
        'pooja_status',
        'order_status',
        'shipment_status_scan',
        'added_by ',
        'status'
    ];

    protected $casts = [
        'seller_id' => 'integer',
        'type' => 'string',
        'order_id' => 'string',
        'service_id' => 'integer',
        'user_id' => 'integer',
        'product_id' => 'integer',
        'booking_date' => 'date',
        'order_completed' => 'date',
        'payment_type' => 'string',
        'manifest_id' => 'integer',
        'shippingurl' => 'string',
        'awb' => 'string',
        'carrier_id' => 'integer',
        'carrier_name' => 'string',
        'message' => 'string',
        'warehouse_id' => 'integer',
        'delivery_charge' => 'double',
        'delivery_partner' => 'string',
        'pooja_status' => 'integer',
        'order_status' => 'string',
        'shipment_status_scan' => 'string',
        'added_by ' => 'string',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function vippoojas()
    {
        return $this->hasOne(Vippooja::class, 'id', 'service_id');
    }
    public function products()
    {
        return $this->hasOne(product::class, 'id', 'product_id');
    }
    public function customers()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function services()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }
    public function scopeActive(): mixed
    {
        return $this->where('status', 1);
    }

    public function translations(): MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }

    public function getTitleAttribute($title): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $title;
        }

        return $this->translations[1]->value ?? $title;
    }

    public function getDescriptionAttribute($description): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $description;
        }
        return $this->translations[0]->value ?? $description;
    }

    public function getDefaultNameAttribute(): string|null
    {
        return $this->translations[0]->value ?? $this->name;
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')) {
                    return $query->where('locale', App::getLocale());
                } else {
                    return $query->where('locale', getDefaultLanguage());
                }
            }]);
        });
    }
}