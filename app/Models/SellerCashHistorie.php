<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerCashHistorie extends Model
{
    protected $fillable = [
        'seller_id',
        'amount',
        'status',
        'created_at',
        'updated_at',
        'transaction_note',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

   
}
