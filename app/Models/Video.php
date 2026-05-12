<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

class Video extends Model
{
    use HasFactory;

protected $fillable = [
    'category_id',
    'subcategory_id',
    'list_type',
    'playlist_name',
    'title',
    'url',
    'image',
    'status',
    'url_status',
];


    protected $casts = [
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
        'list_type' => 'string',
        'playlist_name' => 'string',
        'title' => 'string',
        'url' => 'string',
        'image' => 'string',
        'status' => 'integer',
         'url_status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
/*
    public function getImageAttribute($image)
    {
        return url('storage/' . $image);
    }
*/
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

    public function getDescriptionAttribute($description): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $description;
        }
        return $this->translations[1]->value ?? $description;
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
                if (strpos(url()->current(), '/api')){
                    return $query->where('locale', App::getLocale());
                }else{
                    return $query->where('locale', getDefaultLanguage());
                }
            }]);
        });
    }
}
