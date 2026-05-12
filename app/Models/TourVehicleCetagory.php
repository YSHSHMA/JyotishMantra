<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class TourVehicleCetagory extends Model
{
    use HasFactory;
    protected $table = 'tour_vehicle_category';
    protected $fillable = ['id', 'type', 'brand_name', 'status', 'created_at', 'updated_at'];
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

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function getType($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[1]->value ?? $name;
    }
    public function getBrandName($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller') || strpos(url()->current(), '/tour-vendor')) {
            return $name;
        }
        return $this->translations[2]->value ?? $name;
    }
}
