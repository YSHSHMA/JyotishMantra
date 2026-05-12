<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Leads extends Model
{
    protected $fillable = [
        'service_id',
        'order_id',
        'type',
        'package_id',
        'product_id',
        'add_product_id',
        'product_price',
        'platform',
        'pandit_id',
        'customer_id',
        'package_price',
        'person_name',
        'package_name',
        'person_phone',
        'noperson',
        'booking_date',
        'payment_status',
        'final_amount',
        'payment_id',
        'whatsapp_hit'
    ];
    
    protected $casts = [
        'service_id' => 'integer',
        'package_id' => 'integer',
        'product_id' => 'string',
        'order_id' => 'string',
        'package_price' => 'string',
        'customer_id' => 'integer',
        'add_product_id' => 'json',
        'product_price' => 'integer',
        'pandit_id' => 'integer',
        'platform'=> 'string',
        'person_name' => 'string',
        'package_name' => 'string',
        'person_phone' => 'string',
        'noperson' => 'string',
        'type' => 'string',
        'booking_date' => 'date',
        'payment_mode' => 'string',
        'payment_id' => 'string',
        'whatsapp_hit' => 'integer'
    ];
    // Relations With Services
    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }
    public function customers()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }
    public function vippooja()
    {
        return $this->hasOne(Vippooja::class, 'id', 'service_id');
    }
    public function followby()
    {
        return $this->hasOne(Followsup::class, 'lead_id', 'id');
    }
    //Relations  with Products Leads
    public function productLeads()
    {
        return $this->hasMany(ProductLeads::class, 'leads_id', 'id')->with('productsData');
    }
    public function scopeActive(): mixed
    {
        return $this->where('status', 1);
    }

    public function chadhava()
    {
        return $this->hasOne(Chadhava::class, 'id', 'service_id');
    }


    public function translations(): MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }

    public function getNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }

        return $this->translations[0]->value ?? $name;
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
    public function services()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
    public function addProducts()
    {
        $items = $this->add_product_id ?? [];

        if (empty($items)) {
            return Product::whereIn('id', []);
        }
        $ids = array_column($items, 'product_id');

        return Product::whereIn('id', $ids);
    }

}
