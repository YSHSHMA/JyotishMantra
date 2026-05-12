<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrustPujaOrder extends Model
{
    use HasFactory;
    protected $table = 'trust_puja_order';
    protected $fillable = ['id', 'order_id', 'puja_name', 'trust_id', 'user_name', 'user_phone', 'rprice', 'discount', 'pprice', 'tax', 'tax_amount', 'admin_commission', 'final_amount', 'transaction_id', 'paymant_method', 'payment_status', 'created_at', 'updated_at'];

     protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->order_id = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('order_id', 'like', 'TPJ%')->orderBy('id', 'desc')->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->order_id, 3); // remove 'TPJ'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return 'TPJ' . str_pad($newNumber, 10, '0', STR_PAD_LEFT); // TPJ0001, TPJ0002, ...
    }

    public function Trust()
    {
        return $this->hasOne(DonateTrust::class, 'id', 'trust_id');
    }
}
