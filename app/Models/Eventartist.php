<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class Eventartist extends Model
{
    use HasFactory;
    protected $table = 'artist';

    protected $fillable = ['id', 'name', 'profession', 'description', 'image','created_by', 'created_at', 'updated_at'];

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    protected static function boot()
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

    public function getNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')  || strpos(url()->current(), '/event-vendor')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }
    public function getProfessionAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')  || strpos(url()->current(), '/event-vendor')) {
            return $name;
        }
        return $this->translations[2]->value ?? $name;
    }
    public function getDescriptionAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')  || strpos(url()->current(), '/event-vendor')) {
            return $name;
        }
        return $this->translations[1]->value ?? $name;
    }
}