<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class SaptahikKatha extends Model
{

    protected $fillable = [
        'title',
        'description',
        'status'
    ];

    protected $casts = [
        'title' => 'string',
        'description' => 'string',
        'status' => 'integer',
        'saptahikkatha_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive(): mixed
    {
        return $this->where('status',1);
    }

    // public function Saptahikkatha(): HasMany
    // {
    //     //return $this->hasMany(Saptahikkatha::class)->active();
    // }

    // public function SaptahikkathaAll(): HasMany
    // {
    //     //return $this->hasMany(Saptahikkatha::class);
    // }

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
