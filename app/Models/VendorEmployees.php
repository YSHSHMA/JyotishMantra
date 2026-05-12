<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class VendorEmployees extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'vendor_employee';
    // protected $casts = [
    //     'selected_services' => 'array',
    // ];
    protected $fillable = ['id', 'identify_number', 'type', 'name', 'phone', 'email', 'emp_role_id', 'temple_id', 'purohit_id', 'selected_services', 'image', 'auth_token', 'holdername', 'bankname', 'account_num', 'ifsccode', 'withdrawal_amount', 'requested_amount', 'collected_amount', 'platform_fee', 'trust_fee', 'gst_amount', 'password', 'status','pay_code','invalid_attendee_count', 'relation_id', 'created_at', 'updated_at'];

     protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->pay_code = mt_rand('0000','9999');
        });
    }

    protected $hidden = [
        'password',
        'auth_token',
    ];

    public function Role()
    {
        return $this->hasone(VendorRoles::class, 'id', 'emp_role_id');
    }

    public function seller()
    {
        return $this->hasone(Seller::class, 'id', 'relation_id');
    }
     public function Temple()
    {
        return $this->hasone(Temple::class, 'id', 'temple_id');
    }
    public function Tour()
    {
        return $this->hasone(TourAndTravel::class, 'id', 'relation_id');
    }
    public function Trust()
    {
        return $this->hasone(DonateTrust::class, 'id', 'relation_id');
    }
    public function Event()
    {
        return $this->hasone(EventOrganizer::class, 'id', 'relation_id');
    }
    public function purohit(){
        return $this->hasone(Purohit::class, 'id', 'purohit_id');
    }

    public function getVendorsAttribute()
    {
        switch ($this->type) {
            case 'seller':
                return $this->seller;
            case 'tour':
                return $this->tour;
            case 'trust':
                return $this->trust;
            case 'event':
                return $this->event;
            default:
                return null;
        }
    }
}
