<?php

namespace App;

use App\Models\BirthJournalKundali;
use App\Models\Chadhava_orders;
use App\Models\DonateAllTransaction;
use App\Models\EventOrder;
use App\Models\OfflinePoojaOrder;
use App\Models\ShippingAddress;
use App\Models\Order;
use App\Models\ProductCompare;
use App\Models\Service_order;
use App\Models\TourOrder;
use App\Models\Wishlist;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    public mixed $email;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'f_name',
        'l_name',
        'name',
        'email',
        'password',
        'country_code',
        'phone',
        'image',
        'login_medium',
        'is_active',
        'social_id',
        'is_phone_verified',
        'temporary_token',
        'referral_code',
        'referred_by',
        'street_address',
        'country',
        'city',
        'zip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'integer',
        'is_phone_verified' => 'integer',
        'is_email_verified' => 'integer',
        'wallet_balance' => 'float',
        'loyalty_point' => 'float',
        'referred_by' => 'integer',
    ];

    public function wish_list()
    {
        return $this->hasMany(Wishlist::class, 'customer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function poojaOrders()
    {
        return $this->hasMany(Service_order::class, 'customer_id');
    }

    public function chadhavaOrders()
    {
        return $this->hasMany(Chadhava_orders::class, 'customer_id');
    }
    public function kundaliOrders()
    {
        return $this->hasMany(BirthJournalKundali::class, 'user_id');
    }
    public function offlinepoojaOrders()
    {
        return $this->hasMany(OfflinePoojaOrder::class, 'customer_id');
    }

    public function tourOrders()
    {
        return $this->hasMany(TourOrder::class, 'user_id');
    }

    public function eventOrders()
    {
        return $this->hasMany(EventOrder::class, 'user_id');
    }

    public function donationOrders()
    {
        return $this->hasMany(DonateAllTransaction::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address');
    }
    public function compare_list()
    {
        return $this->hasMany(ProductCompare::class, 'user_id');
    }
}
