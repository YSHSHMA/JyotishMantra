<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TempleServicePrice extends Model
{
    use HasFactory;

    protected $table = 'temple_service_prices';
    protected $fillable = [
        'id','addedBy','package_id','temple_id','trust_id','base_price','varient_name',
        'description','daily_slots_limit','color','max_qty_per_day','max_duration_hour',
        'platform_fee_percentage','receipt_fee_percentage','gst_rate','is_available','image','status',
        'created_at','updated_at'
    ];
    public function translations(): MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }
    
    public function serviceget(){
        return $this->hasOne(TempleServicePackages::class,'id','package_id');
    }
    public function getNameAttribute($name): string|null
    {
        // dd($this->translations);
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }

    public function getDetailsAttribute($detail): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $detail;
        }
        return $this->translations[5]->value ?? $detail;
    }

    // Relation to slots
    public function slots()
    {
        return $this->hasMany(TempleServiceSlot::class, 'temple_service_prices_id', 'id');
    }

    // Optional: Relation to Temple
    public function temple()
    {
        return $this->belongsTo(Temple::class, 'temple_id', 'id');
    }

    // Optional: Relation to Package
    public function servicePackage()
    {
        return $this->belongsTo(TempleServicePackages::class, 'package_id', 'id');
    }
}

