<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempleOrderMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id', 'order_id', 'user_id', 'temple_id', 'trust_id',
        'total_people_count', 'total_amount', 'transaction_id','payment_confirmed_by','payment_confirmed_at', 'booking_status', 'platform', 'payment_mode', 'status', 'payment_status', 'upgrade_id',
        'is_upgraded',
    ];

    public function temple()
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(TempleOrderDetails::class, 'order_id', 'order_id');
    }
    public function upgradeHistory()
    {
        return $this->hasMany(TemplePackageUpgradeHistory::class, 'order_id', 'order_id');
    }

}

