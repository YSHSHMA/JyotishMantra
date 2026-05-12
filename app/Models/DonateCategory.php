<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;


class DonateCategory extends Model
{
    use HasFactory;
    protected $table = 'donate_categories';

    protected $fillable = ['id', 'name', 'slug', 'image', 'type', 'status', 'created_at', 'updated_at'];

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }
    public function donateTrusts()
    {
        return $this->hasMany(DonateTrust::class, 'category_id');
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

    public function getNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }
}
