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

class Followsup  extends Model
{
    protected $fillable = [
        'customer_id',
        'pooja_id',
        'type',
        'follow_by',
        'follow_by_id',
        'last_date',
        'message',
        'lead_id',
        'next_date',
    ];

    protected $casts = [
        'customer_id' => 'string',
        'pooja_id' => 'integer',
        'lead_id' => 'integer',
        'type' => 'string',
        'follow_by' => 'string',
        'follow_by_id' => 'integer',
        'message' => 'string',
        'last_date' => 'date',
        'next_date' => 'date',
    ];
    // Relations With Services
    public function service()
    {
        return $this->hasOne(Service::class, 'id','service_id');
    }
    public function vippooja()
    {
        return $this->hasOne(Vippooja::class, 'id','service_id');
    }
    public function customers()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }
    //Relations  with Products Leads
    public function productLeads()
    {
        return $this->hasMany(ProductLeads::class, 'leads_id', 'id')->with('productsData');
    }
    public function scopeActive(): mixed
    {
        return $this->where('status',1);
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

        return $this->translations[0]->value??$name;
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
    public function services(){
        return $this->hasOne(Service::class,'id','service_id');
    }
    public function serviceOrder()
    {
        return $this->hasOne(Service_order::class, 'id', 'service_id');
    }

}

