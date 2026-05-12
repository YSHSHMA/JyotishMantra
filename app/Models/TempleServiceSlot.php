<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TempleServiceSlot extends Model
{
    use HasFactory;
    protected $fillable = [
        'temple_service_prices_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slots_limi_capacity',
        'is_available',
        'status',
        'created_at',
        'updated_at',
    ];
    // Relation to TempleServicePrice
    public function Packagesname()
    {
        return $this->belongsTo(TempleServicePackages::class, 'temple_service_prices_id', 'id');
    }
}
