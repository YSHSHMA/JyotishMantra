<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class Restaurant extends Model
{
    use HasFactory;
    protected $table = 'restaurant';
    protected $fillable = ['id', 'restaurant_name','latitude', 'longitude', 'country_id', 'state_id', 'cities_id', 'zipcode', 'phone_no', 'email_id', 'website_link', 'open_time', 'close_time', 'description', 'menu_highlights', 'more_details', 'status', 'images', 'created_at', 'updated_at','youtube_video','image'];

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

    public function cities(){
        return $this->hasOne(Cities::class,'id','cities_id');
    }

    public function country(){
        return $this->hasOne(Country::class,'id','country_id');
    }

    public function states(){
        return $this->hasOne(States::class,'id','state_id');
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
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    } 

    public function getMoreDetailsAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[1]->value ?? $name;
    } 

    public function getMenuHighlightsAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[2]->value ?? $name;
    } 
    public function getRestaurantNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[3]->value ?? $name;
    } 

}
