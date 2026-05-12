<?php

namespace App\Models;

use App\Models\Astrologer\Astrologer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vippooja extends Model
{

    protected $fillable = [
        'user_id',
        'added_by',
        'name',
        'slug',
        'short_benifits',
        'is_anushthan',
        'details',
        'benefits',
        'process',
        'temple_details',
        'pooja_heading',
        'packages_id',
        'product_id',
        'images',
        'video_provider',
        'video_url',
        'status',
        'thumbnail',
        'digital_file_ready',
        'meta_title',
        'meta_description',
        'meta_image',
        'prashadam_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'added_by' => 'string',
        'name' => 'string',
        'slug' => 'string',
        'short_benifits' => 'string',
        'is_anushthan' => 'integer',
        'details' => 'string',
        'benefits' => 'string',
        'process' => 'string',
        'temple_details' => 'string',
        'pooja_heading' => 'string',
        'packages_id' => 'string',
        'prashadam_id' => 'integer',
        'product_id' => 'string',
        'images' => 'string',
        'status' => 'integer',
        'digital_file_ready' => 'string',
        'meta_title' => 'string',
        'meta_description' => 'string',
        'meta_image' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

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
    public function getPoojaHeadingAttribute($pooja_heading): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $pooja_heading;
        }
        return $this->translations[6]->value ?? $pooja_heading;
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'id', 'packages_id');
    }

    public function prashadam()
    {
        return $this->hasOne(Product::class, 'id', 'prashadam_id');
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

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
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