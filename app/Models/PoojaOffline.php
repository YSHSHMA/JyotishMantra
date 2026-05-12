<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

class PoojaOffline extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_benifits',
        'type',
        'details',
        'benefits',
        'process',
        'terms_conditions',
        'package_details',
        'thumbnail',
        'images',
        'video_url',
        'temples_id',
        'meta_title',
        'meta_description',
        'meta_image',
        'status'
    ];

    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'short_benifits' => 'string',
        'type' => 'string',
        'details' => 'string',
        'benefits' => 'string',
        'process' => 'string',
        'terms_conditions' => 'string',
        'package_details' => 'string',
        'thumbnail' => 'string',
        'images' => 'string',
        'video_url' => 'string',
        'temples_id' => 'string',
        'meta_title' => 'string',
        'meta_description' => 'string',
        'meta_image' => 'string',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

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

    public function getShortBenifitsAttribute($short_benifits): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $short_benifits;
        }
        return $this->translations[1]->value ?? $short_benifits;
    }

    public function getDetailsAttribute($details): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $details;
        }
        return $this->translations[4]->value ?? $details;
    }

    public function getBenefitsAttribute($benefits): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $benefits;
        }
        return $this->translations[3]->value ?? $benefits;
    }

    public function getProcessAttribute($process): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $process;
        }
        return $this->translations[2]->value ?? $process;
    }


    public function getTermsConditionsAttribute($terms_conditions): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $terms_conditions;
        }
        return $this->translations[5]->value ?? $terms_conditions;
    }
    public function category()
    {
        return $this->hasOne(OfflinepoojaCategory::class, 'id', 'type');
    }

    public function review()
    {
        // return $this->hasMany(OfflinepoojaReview::class, 'service_id', 'id');
        return $this->hasMany(ServiceReview::class, 'service_id', 'id')
        ->where('service_type', '=', 'offlinepooja');
    }

    public function offlinePoojaOrder()
    {
        return $this->hasMany(OfflinePoojaOrder::class, 'service_id', 'id');
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
