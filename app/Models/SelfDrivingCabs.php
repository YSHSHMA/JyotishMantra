<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class SelfDrivingCabs extends Model
{
    use HasFactory;
    protected $table = 'self_driving_cabs';
    protected $fillable = ['id', 'slug','type', 'category_id', 'cab_id','traveller_id', 'air_conditioning_status', 'car_type', 'basic_price', 'drivers_age_details', 'tip_for_driving', 'not_local_resident', 'local_resident', 'cab_about', 'policy_info', 'pick_point','thumbnail','images', 'is_approve','status', 'created_at', 'updated_at'];

    protected static function boot()
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

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function getTraveller()
    {
        return $this->hasOne(TourAndTravel::class, 'id', 'traveller_id');
    }

    public function getType()
    {
        return $this->hasOne(TourVehicleCetagory::class, 'id', 'type');
    }
    public function getCategory()
    {
        return $this->hasOne(TourVehicleCetagory::class, 'id', 'category_id');
    }
    public function getCabId()
    {
        return $this->hasOne(TourCab::class, 'id', 'cab_id');
    }

    public function getDriversAgeDetails($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }

    public function getTipForDriving($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }
    public function getNotLocalResident($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }
    public function getLocalResident($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }
}
