<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class Cities extends Model
{
    use HasFactory;
    protected $table = 'cities';

    protected $fillable = ['id', 'city', 'country_id', 'state_id', 'images', 'image', 'slider_image', 'short_desc', 'description', 'famous_for', 'created_at', 'status', 'latitude', 'longitude', 'festivals_and_events'];

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


    public function states()
    {
        return $this->hasOne(States::class, 'id', 'state_id');
    }
    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
    public function visits()
    {
        return $this->hasMany(CitiesVisits::class, 'citie_id')->with('translations');
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
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

    public function getDescriptionAttribute($name): string|null
    {
        if (
            strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')
        ) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }
    public function getCityAttribute($name): string|null
    {
        if (
            strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')
        ) {
            return $name;
        }
        return $this->translations[1]->value ?? $name;
    }
    public function getFamousForAttribute($name): string|null
    {
        if (
            strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')
        ) {
            return $name;
        }
        return $this->translations[2]->value ?? $name;
    }
    public function getShortDescAttribute($name): string|null
    {
        if (
            strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')
        ) {
            return $name;
        }
        return $this->translations[3]->value ?? $name;
    }
    public function getFestivalsAndEventsAttribute($name): string|null
    {
        if (
            strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/trustees-vendor')
        ) {
            return $name;
        }
        return $this->translations[4]->value ?? $name;
    }
}
