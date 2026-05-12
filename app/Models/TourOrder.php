<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TourOrder extends Model
{
    use HasFactory;

    protected $table = 'tour_order';
    protected $fillable = ['id', 'order_id', "user_id", 'tour_id', 'traveller_id', 'package_id', 'booking_package', 'qty', 'available_seat_cab_id', 'totals_seat_cab_id', "part_payment", 'amount', 'coupon_amount', 'admin_commission', 'gst_amount', 'final_amount', 'advance_withdrawal_amount', 'coupon_id', 'status', 'amount_status', "transaction_id", 'refound_id', 'refund_status', 'refund_date', 'refund_amount', 'refund_query_id', 'pickup_address', 'pickup_date', "drop_date", 'pickup_time', 'pickup_lat', 'pickup_long', 'payment_method', 'payment_platform', 'leads_id', 'use_date', 'created_at', 'updated_at', 'pickup_otp', "pickup_status", 'drop_opt', "drop_status", 'cab_assign', 'traveller_cab_id', 'traveller_driver_id','cancel_vendor_list'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->order_id = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('order_id', 'like', 'TO%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->order_id, 2); // remove 'TO'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'TO' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // TO0001, TO0002, ...
    }
    public function scopeWithCabOrderCheck($query, $travellerId)
    {
        return $query->addSelect([
            'cab_data' => TourCabManage::selectRaw('JSON_OBJECT("id", cab_id, "name", model_number)')
                ->where(function ($query) {
                    $query->whereRaw("                    
                        JSON_CONTAINS(tour_order.booking_package, JSON_OBJECT('type', 'cab', 'id', CAST(tour_traveller_cabs.cab_id AS CHAR)))
                        OR JSON_CONTAINS(tour_order.booking_package, JSON_OBJECT('type', 'cab', 'id', CAST(tour_traveller_cabs.cab_id AS UNSIGNED)))
                    ");
                })
                ->where('tour_traveller_cabs.traveller_id', $travellerId)
                ->limit(1),
            'per_data' => DB::table('tour_order as to2')
                ->selectRaw("JSON_EXTRACT(to2.booking_package, '$[0]')")
                ->whereRaw('to2.id = tour_order.id')
                ->whereRaw("JSON_SEARCH(to2.booking_package, 'one', 'per_head') IS NOT NULL")
                ->limit(1)
        ])->havingRaw('cab_data IS NOT NULL OR per_data IS NOT NULL');
    }

    public function scopeWithDriverInfo($query, $order_id)
    {
        return $query->addSelect([
            'driver_data' => TourDriverManage::selectRaw('
                CONCAT("[", GROUP_CONCAT(
                    JSON_OBJECT(
                        "id", id, 
                        "name", name, 
                        "phone", phone, 
                        "email", email,
                        "image",CONCAT("' . url('storage/app/public/tour_and_travels/tour_traveller_driver/') . '/", image)
                    )
                ), "]")
            ')
                ->whereRaw("
                JSON_CONTAINS(tour_order.traveller_driver_id, JSON_QUOTE(CAST(tour_traveller_driver.id AS CHAR)))
            "),
            'Cabs_data' => TourCabManage::selectRaw('
                CONCAT("[", GROUP_CONCAT(
                    JSON_OBJECT(
                        "id", tour_traveller_cabs.id, 
                        "model_number", tour_traveller_cabs.model_number, 
                        "reg_number", tour_traveller_cabs.reg_number,
                        "cab_name", tour_cab.name,
                        "cab_images",CONCAT("' . url('storage/app/public/tour_and_travels/tour_traveller_cab/') . '/", tour_traveller_cabs.image) 
                    )
                ), "]")
            ')->join('tour_cab', 'tour_traveller_cabs.cab_id', '=', 'tour_cab.id')
                ->whereRaw("
                JSON_CONTAINS(tour_order.traveller_cab_id, JSON_QUOTE(CAST(tour_traveller_cabs.id AS CHAR)))
            ")
        ])->where('tour_order.id', $order_id);
    }

    public function accept()
    {
        return $this->hasOne(TourOrderAccept::class, 'tour_id', 'tour_id');
    }

    public function acceptss()
    {
        return $this->hasMany(TourOrderAccept::class, 'tour_id', 'tour_id');
    }

    public function Tour()
    {
        return $this->hasOne(TourVisits::class, 'id', 'tour_id')->with(['TourPlane']);
    }

    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function company()
    {
        return $this->hasOne(TourAndTravel::class, 'id', 'cab_assign');
    }

    public function Driver()
    {
        return $this->hasOne(TourDriverManage::class, 'id', 'traveller_driver_id');
    }
    public function Drivers()
    {
        return $this->hasMany(TourDriverManage::class, 'id', 'traveller_driver_id')
            ->whereIn('id', json_decode($this->traveller_driver_id, true) ?? []);
    }
    public function CabsManage()
    {
        return $this->hasOne(TourCabManage::class, 'id', 'traveller_cab_id')->with('Cabs');
    }
}