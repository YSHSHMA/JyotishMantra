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
use App\Models\Service;
use App\Models\Package;
use App\Models\Order;
use App\Models\Leads;
use App\Models\PaymentRequest;
use App\Models\ProductLeads;
use App\Models\Astrologer\Astrologer;


class Chadhava_orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'service_id',
        'type',
        'leads_id',
        'booking_date',
        'wallet_amount',
        'wallet_translation_id',
        'transection_amount',
        'coupon_code',
        'pay_amount',
    ];

    public function customers()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }
    public function customer()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }
    public function chadhava()
    {
        return $this->hasOne(Chadhava::class, 'id', 'service_id');
    }
    public function vippoojas()
    {
        return $this->hasOne(Vippooja::class, 'id', 'service_id');
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
    public function order()
    {
        return $this->belongsTo(Order::class, 'customer_id', 'customer_id');
    }
}
