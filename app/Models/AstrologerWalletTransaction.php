<?php

namespace App\Models;

use App\Models\Astrologer\Astrologer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class AstrologerWalletTransaction extends Model
{
    use HasFactory;
    protected $table = 'astrologer_wallet_histories';

    protected $fillable = [
        'astrologer_id',
        'req_id',
        'user_id',
        'payment_type',
        'start_time',
        'end_time',
        'duration_minutes',
        'total_amount_paid',
        'astrologer_earning',
        'commission_rate',
        'commission_amount',
        'transaction_status',
    ];

    public function astrologer()
    {
        return $this->belongsTo(Astrologer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

