<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempleLeadMaster extends Model
{
    use HasFactory;
    protected $fillable = [
        'temple_id',
        'user_id',
        'trust_id',
        'order_id',
        'customer_qty',
        'amount',
        'status',
        'payment_status',
        'payment_mode',
    ];
    public function details()
    {
        return $this->hasMany(TempleLeadDetail::class, 'order_id', 'order_id');
    }
    public function temple()
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
