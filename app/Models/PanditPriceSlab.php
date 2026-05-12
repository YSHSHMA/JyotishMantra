<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanditPriceSlab extends Model
{
    use HasFactory;
    protected $table = 'pandit_price_slabs';

    protected $fillable = [
        'pandit_id',
        'service_id',
        'min_qty',
        'max_qty',
        'price',
        'single_price',
        'by_type',
        'added_by',
        'is_request',
        'type',
        'status',
    ];

    protected $casts = [
        'pandit_id'    => 'integer',
        'service_id'   => 'integer',
        'min_qty'      => 'integer',
        'max_qty'      => 'integer',
        'price'        => 'decimal:2',
        'single_price' => 'decimal:2',
        'added_by'     => 'string',
        'status'       => 'integer',
    ];
    
    public function astrologer()
    {
        return $this->belongsTo(Astrologer::class, 'pandit_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
