<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class EventOrder extends Model
{
    use HasFactory;
    protected $table = 'event_orders';

    protected $fillable = ['id', 'order_no', 'event_id', 'user_id', 'venue_id', 'amount', 'coupon_amount', 'admin_commission', 'gst_amount', 'final_amount', 'transaction_id', 'refund_id', 'transaction_status', 'coupon_id', 'status', 'payment_requests_id',"platform", 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->order_no = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('order_no', 'like', 'EO%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->order_no, 2); // remove 'EO'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'EO' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // EO0001, EO0002, ...
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function eventid()
    {
        return $this->hasOne(Events::class, 'id', 'event_id')->with('organizers')->with('categorys')->with('eventArtist');
    }

    public function userdata()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function orderitem()
    {
        return $this->hasMany(EventOrderItems::class, 'order_id', 'id')->with('category');
    }

    public function coupon()
    {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id');
    }
}