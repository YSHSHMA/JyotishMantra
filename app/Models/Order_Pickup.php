<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

class Order_Pickup extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_name',
        'carrier_id',
        'delivery_charge',
        'payment_type',
        'order_ids',
        'warehouse_id',
        'return_warehouse_id',
        'awb',
        'shippingurl',
        'message',
        'pickup_date',
        'pickup_time',
        'office_close_time',
        'package_count',
        'manifest_ids',
        'status'
    ];

    protected $casts = [
        'pickup_date' => 'datetime',
        'pickup_time' => 'datetime',
        'office_close_time' => 'datetime',
        'package_count' => 'integer',
        'carrier_id' => 'integer',
        'warehouse_id' => 'integer',
        'return_warehouse_id' => 'integer',
        'payment_type' => 'string',
        'message' => 'string',
        'delivery_charge' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeActive(): mixed
    {
        return $this->where('status',1);
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

        return $this->translations[1]->value??$title;
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
                if (strpos(url()->current(), '/api')){
                    return $query->where('locale', App::getLocale());
                }else{
                    return $query->where('locale', getDefaultLanguage());
                }
            }]);
        });
    }
}
?>