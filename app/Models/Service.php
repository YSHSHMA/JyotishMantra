<?php

namespace App\Models;

use App\Models\Astrologer\Astrologer;
use App\Models\Product;
use App\Models\Package;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Utils\Helpers;
use function App\Utils\getNextPoojaDay;

class Service extends Model
{

    protected $fillable = [
        'user_id',
        'added_by',
        'name',
        'slug',
        'short_benifits',
        'pooja_heading',
        'product_type',
        'pooja_type',
        'schedule',
        'counselling_main_price',
        'counselling_selling_price',
        'details',
        'benefits',
        'process',
        'temple_details',
        'is_visible_city',
        'visible_city',
        'category_ids',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'product_id',
        'packages_id',
        'pandit_assign',
        'pooja_venue',
        'pooja_time',
        'week_days',
        'images',
        'video_provider',
        'video_url',
        'status',
        'thumbnail',
        'digital_file_ready',
        'meta_title',
        'meta_description',
        'meta_image'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'added_by' => 'string',
        'name' => 'string',
        'slug' => 'string',
        'short_benifits' => 'string',
        'pooja_heading' => 'string',
        'product_type' => 'string',
        'pooja_type' => 'integer',
        'schedule' => 'string',
        'counselling_main_price' => 'integer',
        'counselling_selling_price' => 'integer',
        'details' => 'string',
        'benefits' => 'string',
        'process' => 'string',
        'temple_details' => 'string',
        'is_visible_city' => 'integer',
        'visible_city' => 'string',
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
        'sub_sub_category_id' => 'integer',
        'product_id' => 'string',
        'packages_id' => 'string',
        'pandit_assign' => 'string',
        'pooja_venue' => 'string',
        'pooja_time' => 'datetime',
        'week_days' => 'string',
        'images' => 'string',
        'status' => 'integer',
        'digital_file_ready' => 'string',
        'meta_title' => 'string',
        'meta_description' => 'string',
        'meta_image' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $append=['next_date'];

    // public function getNextDateAttribute() {
    //     return getNextPoojaDay(
    //         json_decode($this->week_days),
    //         date('H:i:s', strtotime($this->pooja_time))
    //     );
    // }
    public function getNextDateAttribute()
    {
        if ($this->pooja_type == 0) {
            // Weekly schedule based
            return getNextPoojaDay(
                json_decode($this->week_days),
                date('H:i:s', strtotime($this->pooja_time))
            );
        } elseif ($this->pooja_type == 1 && !empty($this->schedule)) {
            $schedules = json_decode($this->schedule, true);
            if (is_array($schedules)) {
                $futureSchedules = array_filter($schedules, function ($item) {
                    return isset($item['schedule']) && strtotime($item['schedule']) >= strtotime(date('Y-m-d'));
                });
                usort($futureSchedules, function ($a, $b) {
                    return strtotime($a['schedule']) - strtotime($b['schedule']);
                });
                if (!empty($futureSchedules)) {
                    return $futureSchedules[0]['schedule'] . ' ' . ($futureSchedules[0]['schedule_time'] ?? '');
                }
            }
        }
        return null;
    }

    public function serviceAll(): HasMany
    {
        return $this->hasMany(Service::class);
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
        // dd($this->translations);
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }

    public function getShortBenifitsAttribute($short_benifits): string|null
    {
        // dd($this->translations);
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $short_benifits;
        }
        return $this->translations[1]->value ?? $short_benifits;
    }


    public function getProcessAttribute($process): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $process;
        }
        return $this->translations[2]->value ?? $process;
    }

    public function getBenefitsAttribute($benefits): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $benefits;
        }
        return $this->translations[3]->value ?? $benefits;
    }

    public function getTempleDetailsAttribute($temple_details): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $temple_details;
        }
        return $this->translations[4]->value ?? $temple_details;
    }

    public function getDetailsAttribute($detail): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $detail;
        }
        if (str_contains(url()->current(), 'counselling')) {
            return $this->translations[1]->value ?? $detail;
        }
        return $this->translations[5]->value ?? $detail;
    }
    public function getPoojaVenueAttribute($pooja_venue): string|null
    {

        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $pooja_venue;
        }
        return $this->translations[7]->value ?? $pooja_venue;
    }
    public function getPoojaHeadingAttribute($pooja_heading): string|null
    {

        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $pooja_heading;
        }
        return $this->translations[6]->value ?? $pooja_heading;
    }

    // Leads Add the Serive
    public function leads()
    {
        return $this->hasMany(Leads::class, 'service_id');
    }
    public function categories()
    {
        return $this->hasOne(Category::class, 'id', 'sub_category_id');
    }
    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'sub_category_id');
    }
    // 28/08/2024
    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }
    public function pandit()
    {
        return $this->hasOne(Astrologer::class, 'id', 'pandit_assign');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(Service_order::class, 'service_id');
    }

    public function prashadam()
    {
        return $this->hasOne(Product::class, 'id', 'prashadam_id');
    }

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }
    public function package(): HasMany
    {
        return $this->hasMany(Package::class, 'id', 'packages_id');
    }

    // Services model
    public function review()
    {
        return $this->hasMany(ServiceReview::class, 'service_id', 'id');
    }

    public function PoojaOrderReview()
    {
        return $this->hasMany(Service_order::class, 'service_id', 'id');
    }
    public function counsellingPackage()
    {
        return $this->hasOne(PanditServicePackage::class, 'service_id')->where('type', 'counselling');
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