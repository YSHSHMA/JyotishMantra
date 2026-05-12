<?php

namespace App\Models;

use App\Models\Astrologer\Astrologer;
use App\Models\Product;
use App\Models\Cities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Carbon\Carbon;
use Illuminate\Support\Collection;
class Chadhava extends Model
{
    protected $table = 'chadhava';

    protected $fillable = [
        'user_id',
        'bhagwan_id',
        'added_by',
        'name',
        'slug',
        'short_details',
        'pooja_heading',
        'chadhava_venue',
        'details',
        'chadhava_type',
        'chadhava_week',
        'start_date',
        'end_date',
        'is_video',
        'product_id',
        'images',
        'status',
        'thumbnail',
        'meta_title',
        'meta_description',
        'meta_image'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'bhagwan_id' => 'integer',
        'added_by' => 'string',
        'name' => 'string',
        'slug' => 'string',
        'short_details' => 'string',
        'pooja_heading' => 'string',
        'chadhava_venue' => 'string',
        'details' => 'string',
        'chadhava_type' => 'integer',
        'chadhava_week' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_video' => 'integer',
        'product_id' => 'string',
        'images' => 'string',
        'status' => 'integer',
        'meta_title' => 'string',
        'meta_description' => 'string',
        'meta_image' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected $dates = ['start_date', 'end_date'];

    // public function getNextAvailableDate(Carbon $cutoff = null): ?Carbon
    // {
    //     $now = Carbon::now();
    //     $today = Carbon::today();
    //     $cutoff = $cutoff ?? $today->copy()->addDays(7); // Next 7 days

    //     // === Type 0: Weekdays repeat ===
    //     if ($this->chadhava_type == 0 && $this->chadhava_week) {
    //         $weekDays = json_decode($this->chadhava_week); // [ "monday", "friday", ... ]

    //         if (is_array($weekDays)) {
    //             for ($i = 0; $i <= 7; $i++) {
    //                 $checkDate = $today->copy()->addDays($i);
    //                 $dayName = strtolower($checkDate->format('l'));

    //                 if (in_array($dayName, $weekDays)) {
    //                     // Skip today after 12 PM
    //                     if ($checkDate->isToday() && $now->hour >= 12) {
    //                         continue;
    //                     }

    //                     if ($checkDate->lessThanOrEqualTo($cutoff)) {
    //                         return $checkDate;
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     // === Type 1: Date Range ===
    //     if ($this->chadhava_type == 1 && $this->start_date && $this->end_date) {
    //         $start = $this->start_date->copy();
    //         $end = $this->end_date->copy();

    //         while ($start <= $end && $start <= $cutoff) {
    //             if ($start->greaterThan($today) || ($start->isToday() && $now->hour < 12)) {
    //                 return $start;
    //             }
    //             $start->addDay();
    //         }
    //     }

    //     return null; // No valid next date
    // }
    public function getNextAvailableDate(Carbon $cutoff = null): ?Carbon
    {
        $today = Carbon::today();
        $cutoff = $cutoff ?? $today->copy()->addDays(7);

        if ($this->chadhava_type == 0 && $this->chadhava_week) {
            $weekDays = json_decode($this->chadhava_week); 

            if (is_array($weekDays)) {
                for ($i = 1; $i <= 7; $i++) { 
                    $checkDate = $today->copy()->addDays($i);
                    $dayName = strtolower($checkDate->format('l'));

                    if (
                        in_array($dayName, $weekDays) &&
                        $checkDate->lessThanOrEqualTo($cutoff)
                    ) {
                        return $checkDate;
                    }
                }
            }
        }

        if ($this->chadhava_type == 1 && $this->start_date && $this->end_date) {
            $start = $this->start_date->copy();
            $end = $this->end_date->copy();

            if ($start->lessThanOrEqualTo($today)) {
                $start = $today->copy()->addDay();
            }

            while ($start <= $end && $start <= $cutoff) {
                return $start;
            }
        }

        return null; 
    }



    public function serviceAll(): HasMany
    {
        return $this->hasMany(Vippooja::class);
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
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

    public function getDetailsAttribute($detail): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $detail;
        }
        return $this->translations[1]->value ?? $detail;
    }

    public function getChadhavaVenueAttribute($chadhava_venue): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $chadhava_venue;
        }
        return $this->translations[2]->value ?? $chadhava_venue;
    }
    public function getPoojaHeadingAttribute($pooja_heading): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $pooja_heading;
        }
        return $this->translations[4]->value ?? $pooja_heading;
    }
    public function getShortDetailsAttribute($short_details): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $short_details;
        }
        return $this->translations[3]->value ?? $short_details;
    }

    // Leads Add the Serive
    public function leads()
    {
        return $this->hasMany(Leads::class, 'service_id');
    }

    public function pandit()
    {
        return $this->hasOne(Astrologer::class, 'id', 'pandit_assign');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(Service_order::class, 'service_id');
    }

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }

    public function cities()
    {
        return $this->hasOne(cities::class, 'id', 'chadhava_city');
    }

    // Services model
    public function review()
    {
        return $this->hasMany(ServiceReview::class, 'service_id', 'id');
    }

    public function PoojaOrderReview()
    {
        return $this->hasMany(Chadhava_orders::class, 'service_id', 'id');
    }
    public function chadhava_order()
    {
        return $this->hasMany(Chadhava_orders::class, 'service_id', 'id');
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