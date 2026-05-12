<?php

namespace App\Models;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PoojaForecast extends Model
{
    use HasFactory;

    protected $table = 'pooja_forecasts';

    protected $fillable = [
        'service_id',
        'booking_date',
        'type',
        'category',
        'total_orders',
        'total_users',
        'earnings',
        'week_days',
        'start_datetime'
    ];

    protected $dates = ['booking_date'];

    // Relations (if needed)
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function translations(): MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }
    //review
    public function review()
    {
        return $this->hasMany(ServiceReview::class, 'service_id', 'id');
    }

    public function PoojaOrderReview()
    {
        return $this->hasMany(Service_order::class, 'service_id', 'id');
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
