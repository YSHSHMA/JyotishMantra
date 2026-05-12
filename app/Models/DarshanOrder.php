<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DarshanOrder extends Model
{
    use HasFactory;
    protected $table = "darshan_order";

    protected $fillable = ['id', 'order_id', 'user_id','purohit_id', 'temple_id', 'package_id', 'title', 'package_name', 'date', 'time', 'price', 'people_qty', 'payment_method', 'transaction_id','receipt_price','platform_fee', 'gst_amount', 'payment_mode','admin_commission', 'platform_base_price', 'platform_gst', 'final_amount', 'status', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->order_id = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('order_id', 'like', 'TDO%')->orderBy('id', 'desc')->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->order_id, 3); // remove 'TDO'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'TDO' . str_pad($newNumber, 5, '0', STR_PAD_LEFT); // TDO0001, TDO0002, ...
    }

    public function Temple()
    {
        return $this->hasOne(Temple::class, 'id', 'temple_id');
    }
    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function purohit()
    {
        return $this->belongsTo(Purohit::class, 'purohit_id', 'id');
    }

    public function Members(){
        return $this->hasMany(DarshanOrderMembers::class,'darshan_id','id');
    }
}