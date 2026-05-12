<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempleDarshanLead extends Model
{
    use HasFactory;

    protected $table = 'temple_darshan_lead';
    protected $fillable = ['id', 'lead_id', 'user_id', 'receipt_price', 'platform_fee', 'platform_base_price', 'platform_gst', 'purohit_id', 'name', 'phone', 'temple_id', 'package_id', 'title', 'package_name', 'price','date','time','people_qty','people_info','whatsapp_hit', 'status', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->lead_id = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('lead_id', 'like', 'VDL%')->orderBy('id', 'desc')->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->lead_id, 3); // remove 'VDL'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return 'VDL' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // VDL0001, VDL0002, ...
    }

    public function Temple()
    {
        return $this->hasOne(Temple::class, 'id', 'temple_id')->with(['Trust']);
    }
    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function followby()
    {
        return $this->hasOne(DarshanFollowup::class, 'lead_id', 'id')->latest();
    }
}
