<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class TourVisits extends Model
{
    use HasFactory;

    protected $table = 'tour_visits';
    protected $fillable = ['id', 'tour_id', 'created_type', 'slug', 'created_id', 'tour_name', 'plan_type', 'tour_type', 'youtube_link', 'number_of_day', 'number_of_night', 'is_person_use', 'is_included_package', 'customized_type', 'customized_dates', 'cities_tour', 'cities_name', 'country_name', 'state_name', 'part_located', 'days', 'lat', 'long', 'time_slot', 'description', 'highlights', 'package_list', "cab_list_price", 'package_list_price', 'ex_transport_price', 'inclusion', 'exclusion', 'terms_and_conditions', 'cancellation_policy', 'notes', 'tour_image', 'image', 'itineraryupload', 'percentage_off', 'use_date', 'startandend_date', 'ex_distance', 'one_way_type', 'pickup_time', 'pickup_location', 'pickup_lat', 'pickup_long', 'tour_commission', 'meta_title', 'meta_description', 'meta_image', 'status', 'created_at', 'updated_at'];

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
        static::creating(function ($model) {
            $model->tour_id = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('tour_id', 'like', 'TV%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->tour_id, 2); // remove 'TV'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return 'TV' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // TV0001, TV0002, ...
    }


    public function scopeWithTourCheck($query)
    {
        return $query->addSelect([
            // 'cab_data' => TourOrderAccept::selectRaw('JSON_OBJECT("id", tour_id)')
            //     ->whereColumn("tour_order_accept.tour_id", "tour_visits.id")
            //     ->where("tour_order_accept.status", 1)
            //     ->limit(1)
            'cab_data' =>    TourOrderAccept::selectRaw('JSON_OBJECT("id", tour_order_accept.tour_id)')
                ->leftJoin('tour_and_travels', 'tour_and_travels.id', '=', 'tour_order_accept.traveller_id')
                ->whereColumn('tour_order_accept.tour_id', 'tour_visits.id')
                ->where('tour_and_travels.status', 1)
                ->where('tour_order_accept.status', 1)
                ->limit(1)
        ])->havingRaw('cab_data IS NOT NULL');
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function TourPlane()
    {
        return $this->hasMany(TourVisitPlace::class, 'tour_visit_id', 'id')->where('status', 1);
    }

    public function getTourNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[1]->value ?? $name;
    }

    public function getDescriptionAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }

    public function getInclusionAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[2]->value ?? $name;
    }
    public function getExclusionAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[3]->value ?? $name;
    }
    public function getTermsAndConditionsAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[4]->value ?? $name;
    }
    public function getCancellationPolicyAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[5]->value ?? $name;
    }


    public function getHighlightsAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[6]->value ?? $name;
    }

    public function getCitiesNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[7]->value ?? $name;
    }
    public function getCountryNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[8]->value ?? $name;
    }
    public function getStateNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[9]->value ?? $name;
    }
    public function getNotesAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[10]->value ?? $name;
    }

    // public function getNumberOfDayAttribute($name): string|null
    // {
    //     if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
    //         return $name;
    //     }
    //     return $this->translations[11]->value ?? $name;
    // }

    public function review()
    {
        return $this->hasMany(TourReviews::class, 'tour_id', 'id');
    }

    public function TourOrderReview()
    {
        return $this->hasMany(TourReviews::class, 'tour_id', 'id');
    }
}
