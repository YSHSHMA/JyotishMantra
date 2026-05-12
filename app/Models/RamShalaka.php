<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

class RamShalaka extends Model
{
    protected $fillable = [
        'letter',
        'chaupai',
        'description',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'letter' => 'string',
        'chaupai' => 'string',
        'description' => 'string',
        'status' => 'string',
    ];

    public function scopeActive(): mixed
    {
        return $this->where('status', 1);
    }

    public function translations(): MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }

    public function getDescriptionAttribute($description): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $description;
        }

        return $this->translations[0]->value ?? $description;
    }

    //   public function getDefaultNameAttribute(): string|null
    // {
    //     return $this->translations[0]->value ?? $this->name;
    // }

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