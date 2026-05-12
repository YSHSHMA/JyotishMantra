<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\SoftDeletes;

class SangeetDetails extends Model
{
    use HasFactory;
    use SoftDeletes;

protected $fillable = [
    'sangeet_id',
    'title',
    'singer_name',
    'audio',
    'lyrics',
    'song_hit',
    'image',
    'background_image',
    'famous',
    'status',
];


    protected $casts = [
    	'id' => 'integer',
        'sangeet_id' => 'integer',
        'title' => 'string',
        'singer_name' => 'string',
        'audio' => 'string',
        'lyrics' => 'string',
        'song_hit' => 'integer',
        'image' => 'string',
        'background_image' => 'string',
        'famous' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];



      protected $dates = ['deleted_at'];

/*
    public function getImageAttribute($image)
    {
        return url('storage/' . $image);
    }
*/


    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/sangeet-img/' . $this->image)
            : 'default/image/path.jpg'; // Provide a default image path
    }


        public function getBackgroundImageUrlAttribute()
    {
        return $this->background_image
            ? asset('storage/sangeet-background-img/' . $this->background_image)
            : 'default/background/image/path.jpg'; // Provide a default background image path
    }

    public function scopeActive(): mixed
    {
        return $this->where('status',1);
    }
 
    public function sangeet(): BelongsTo
    {
        return $this->belongsTo(Sangeet::class, 'sangeet_id');
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