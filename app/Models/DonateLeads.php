<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonateLeads extends Model
{
    use HasFactory;
    protected $table = 'donate_leads';
    protected $fillable = ['id', 'trust_id', 'ads_id', 'user_id', 'amount', 'type', 'status', 'uuid','information','frequency','platform', 'created_at', 'updated_at'];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('uuid', 'like', 'DL%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->uuid, 2); // remove 'DL'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'DL' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // DL0001, DL0002, ...
    }

    public function Trusts()
    {
        return $this->hasOne(DonateTrust::class, 'id', 'trust_id');
    }

    public function AdsDonate()
    {
        return $this->hasOne(DonateAds::class, 'id', 'ads_id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function followby()
    {
        return $this->hasOne(DonateLeadFollowup::class, 'lead_id', 'id');
    }
}
