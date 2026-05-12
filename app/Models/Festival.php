<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class Festival extends Model
{

    protected $fillable = [
        'festival_id',
        'festival_date',
        'festival_image',
        'title',
        'tithi',
        'detail',
        'status'
    ];

    protected $casts = [
        'festival_id' => 'integer',
        'festival_date' => 'string',
        'festival_image' => 'string',
        'title' => 'string',
        'tithi' => 'string',
        'detail' => 'string',
        'status' => 'integer',
        'fastivaladd_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive(): mixed
    {
        return $this->where('status',1);
    }

    // public function Festival(): HasMany
    // {
    //     //return $this->hasMany(Festival::class)->active();
    // }

    // public function FestivalAll(): HasMany
    // {
    //     //return $this->hasMany(Festival::class);
    // }
    public function month()
    {
        return $this->belongsTo(FestivalHindiMonth::class, 'festival_id');
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
                if (strpos(url()->current(), '/api')){
                    return $query->where('locale', App::getLocale());
                }else{
                    return $query->where('locale', getDefaultLanguage());
                }
            }]);
        });
    }
}
