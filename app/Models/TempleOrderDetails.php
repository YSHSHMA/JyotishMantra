<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempleOrderDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'package_id',
        'temple_id',
        'trust_id',
        'purohit_id',
        'emp_id',
        'people_count',
        'gst',
        'base_price',
        'platform_fee',
        'receipt_fee',
        'final_amount',
        'booking_date',
        'customers',
        'time_slot',
        'locker_items',
        'type_order_id',
        'type',
        'purohit_id',
        'booking_status',
        'payment_status',
        'status',
        'print_status',
    ];

    protected $casts = [
        'customers' => 'array',
        'booking_date' => 'date',
    ];
    public function temple()
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }

    // Each order detail may belong to one trust
    public function trust()
    {
        return $this->belongsTo(DonateTrust::class, 'trust_id');
    }
    // Each order detail belongs to one main order
    public function order()
    {
        return $this->belongsTo(TempleOrderMaster::class, 'order_id', 'order_id')->with(['temple','user']);
    }
    public function price()
    {
        return $this->belongsTo(TempleServicePrice::class, 'package_id', 'id');
    }

    public function package()
    {
        return $this->belongsTo(TempleServicePrice::class, 'package_id', 'id');
    }

    public function timeslot()
    {
        return $this->belongsTo(TempleServiceSlot::class, 'time_slot_id', 'id');
    }

    public function purohit()
    {
        return $this->belongsTo(Purohit::class, 'purohit_id', 'id');
    }

    public function getCustomerAadhaarNumbers()
    {
        $customers = json_decode($this->customers, true);

        if (!is_array($customers)) {
            $customers = [];
        }

        $aadhaarNumbers = collect($customers)
            ->pluck('aadhaar')
            ->filter()
            ->values()
            ->toArray();

        if (empty($aadhaarNumbers)) {
            return collect();
        }
        $kycRecords = \Illuminate\Support\Facades\DB::table('user_aadhaar_kyc')
            ->whereIn('aadhaar_number', $aadhaarNumbers)
            ->get();

        return $kycRecords;
    }
    public function upgradeHistory()
    {
        return $this->hasMany(TemplePackageUpgradeHistory::class, 'order_id', 'order_id');
    }

}
