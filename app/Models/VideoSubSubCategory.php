<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

class VideoSubSubCategory extends Model
{
    protected $fillable = [
    	'category_id',
        'subcategory_id',
        'name',
        'status'
    ];

    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
        'name' => 'string',
        'status' => 'integer',
    ];

   public function scopeActive(): mixed
    {
        return $this->where('status',1);
    }

      public function category()
    {
        return $this->belongsTo(VideoCategory::class, 'category_id');
    }

     public function subcategory()
    {
        return $this->belongsTo(VideoSubCategory::class, 'subcategory_id');
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

        return $this->translations[0]->value??$name;
    }

      public function getDefaultNameAttribute(): string|null
    {
        return $this->translations[0]->value ?? $this->name;
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

