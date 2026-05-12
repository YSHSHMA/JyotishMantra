<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\ServiceTransaction;
use App\Models\Service;
use App\Models\Package;
use App\Models\Order;
use App\Models\Leads;
use App\Models\PaymentRequest;
use App\Models\ProductLeads;
use App\Models\Astrologer\Astrologer;


class Service_order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'customer_id',
        'service_id',
        'type',
        'leads_id',
        'package_id',
        'indivisual',
        'pandit_assign',
        'coupon_amount',
        'package_price',
        'booking_date',
        'wallet_amount',
        'wallet_translation_id',
        'transection_amount',
        'coupon_code',
        'payment_id',
        'pay_amount',
        'schedule_time',
        'schedule_created',
        'order_status',
        'live_stream',
        'live_created_stream',
        'pooja_video',
        'video_created_sharing',
        'pooja_certificate',
        'order_completed',
        'order_canceled_reason',
        'coupon_amount',
        'coupon_code',
        'delivery_partner',
        'delivery_order_id',
        'delivery_channel_id',
        'counselling_report_reject_reason',
        'counselling_report_verified',
        'counselling_report',
        'indivisual',
        'status',
        'prashad_status',
        'newPhone', 'gotra', 'pincode', 'city', 'state',
        'house_no', 'area', 'landmark', 'latitude', 'longitude',
        'members', 'is_prashad', 'is_edited',
        'payment_status'
    ];
    
    public function customers()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    public function customer()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    public function services()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function chadhava()
    {
        return $this->hasOne(Chadhava::class, 'id', 'service_id');
    }

    public function leads()
    {
        return $this->hasOne(Leads::class, 'id', 'leads_id');
    }

    public function payments()
    {
        return $this->hasOne(PaymentRequest::class, 'transaction_id', 'payment_id');
    }

    public function packages()
    {
        return $this->hasOne(Package::class, 'id', 'package_id');
    }
    public function panditpackage()
    {
        return $this->hasOne(PanditServicePackage::class, 'id', 'package_id');
    }

    public function pandit()
    {
        return $this->hasOne(Astrologer::class, 'id', 'pandit_assign');
    }

    public function product_leads()
    {
        return $this->hasMany(ProductLeads::class, 'leads_id', 'leads_id');
    }
    public function astrologer()
    {
        return $this->hasOne(Astrologer::class, 'id', 'pandit_assign');
    }

    public function counselling_user()
    {
        return $this->hasOne(CounsellingUser::class, 'order_id', 'order_id');
    }
    public function counselling()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'customer_id', 'customer_id');
    }

    public function vippoojas()
    {
        return $this->hasOne(Vippooja::class, 'id', 'service_id');
    }
    public function chadhavaOrders()
    {
        return $this->hasOne(Chadhava_orders::class, 'service_id', 'service_id');
    }

    public function prashadam()
    {
        return $this->hasOne(Product::class, 'id', 'prashadam_id');
    }
    public function prashadams()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
    public function offlinepoojaorders()
    {
        return $this->hasOne(OfflinePoojaOrder::class, 'service_id', 'service_id');
    }
    public function selectedPackage()
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
    public function serviceTransactions()
    {
        return $this->hasMany(ServiceTransaction::class, 'order_id', 'order_id');
    }
}
