<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class Temple extends Model
{
    use HasFactory;

    protected $table = 'temples';

    protected $fillable = ['id', 'user_id', 'added_by', 'name', 'slug', 'short_description', 'details', 'more_details', 'country_id', 'state_id', 'district_id','city_id', 'entry_fee', 'opening_time', 'closeing_time', 'facilities', 'tips_restrictions', 'require_time', 'video_provider', 'video_url','logo', 'thumbnail', 'meta_title', 'meta_description', 'meta_image', 'status', 'created_at', 'updated_at', 'images', 'longitude', 'latitude', 'category_id', 'expect_details', 'tips_details', 'temple_known', 'temple_services', 'temple_aarti', 'tourist_place','package_service', 'temple_local_food','trust_id','aadhaar_verify_status'];

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

    // public function scopeWithCabOrderCheck($query, $travellerId)
    // {
    //     return $query->addSelect([
    //         'cab_data' => Temple::selectRaw('JSON_OBJECT("id", cab_id, "name", model_number)')
    //             ->where(function ($query) {
    //                 $query->whereRaw("                    
    //                     JSON_CONTAINS(tour_order.booking_package, JSON_OBJECT('type', 'cab', 'id', CAST(tour_traveller_cabs.cab_id AS CHAR)))
    //                     OR JSON_CONTAINS(tour_order.booking_package, JSON_OBJECT('type', 'cab', 'id', CAST(tour_traveller_cabs.cab_id AS UNSIGNED)))
    //                 ");
    //             })
    //             ->where('tour_traveller_cabs.traveller_id', $travellerId)
    //             ->limit(1)
    //     ])->havingRaw('cab_data IS NOT NULL OR per_data IS NOT NULL');
    // }

    public function purohits()
    {
        return $this->hasMany(Purohit::class, 'temple_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function cities()
    {
        return $this->hasOne(Cities::class, 'id', 'city_id');
    }

    public function states()
    {
        return $this->hasOne(States::class, 'id', 'state_id');
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'temple_id', 'id');
    }

    public function galleries2()
    {
        return $this->hasOne(Gallery::class, 'temple_id', 'id');
    }

    public function matchingTrust()
    {
        return DonateTrust::all()
            ->first(function ($item) {
                $ids = json_decode($item->trust_temple_id, true);
                return in_array($this->id, (array) $ids);
            });
    }

    public function Trust(){
        return $this->hasOne(DonateTrust::class, 'id', 'trust_id');
    }
    
    public function category()
    {
        return $this->hasOne(TempleCategory::class, 'id', 'category_id');
    }
    // Access package services as Collection
    public function servicePrices()
    {
        return $this->hasMany(TempleServicePrice::class, 'temple_id', 'id');
    }

    // Optional: decode package_service JSON as a collection
    public function packageServices()
    {
        $services = collect(json_decode($this->package_service ?? '[]', true));
        return $services;
    }

    public function scopeWithinRadius($query, $latitude, $longitude, $radius = 10)
    {
        $haversine = "(6371 * acos(cos(radians($latitude)) 
                         * cos(radians(latitude)) 
                         * cos(radians(longitude) 
                         - radians($longitude)) 
                         + sin(radians($latitude)) 
                         * sin(radians(latitude))))";

        return $query->select('*')
            ->selectRaw("{$haversine} AS distance")
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    public function getNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }
    public function getShortDescriptionAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[1]->value ?? $name;
    }
    public function getMoreDetailsAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[2]->value ?? $name;
    }
    public function getDetailsAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[3]->value ?? $name;
    }

    public function getFacilitiesAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[4]->value ?? $name;
    }
    public function getTipsRestrictionsAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[5]->value ?? $name;
    }

    public function getExpectDetailsAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[6]->value ?? $name;
    }
    public function getTipsDetailsAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[7]->value ?? $name;
    }

    public function getTempleKnownAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[8]->value ?? $name;
    }

    public function getTempleServicesAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[9]->value ?? $name;
    }

    public function getTempleAartiAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[10]->value ?? $name;
    }
    public function getTouristPlaceAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[11]->value ?? $name;
    }
    public function getTempleLocalFoodAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')) {
            return $name;
        }
        return $this->translations[12]->value ?? $name;
    }
}
