<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

class INPanditWallet extends Model
{
    use HasFactory;

    protected $table = 'i_n_pandit_wallets';
    
     protected $fillable = [
        'pandit_id',
        'service_id',
        'type',
        'entry_type',
        'amount',
        'credit',
        'debit',
        'balance',
        'status',
        'booking_date',
        'total_orders',
        'single_price',
        'slab_price',
    ];

    protected $casts = [
        'id'          => 'integer',
        'pandit_id'   => 'integer',
        'service_id'  => 'integer',
        'amount'      => 'decimal:2',
        'credit'      => 'decimal:2',
        'debit'       => 'decimal:2',
        'balance'     => 'decimal:2',
        'status'      => 'integer',
        'total_orders'=> 'integer',
        'single_price'=> 'decimal:2',
        'slab_price'  => 'decimal:2',
    ];

   // public function scopeActive(): mixed
   //  {
   //      return $this->where('status',1);
   //  }

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

