<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class KundaliLeads extends Model
{
    use HasFactory;
    protected $table = 'kundli_leads';
    protected $fillable = ['id', 'user_id', 'lead_number', 'kundali_id', 'amount', 'phone_no', 'user_name', 'booking_date', 'payment_status', 'status', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->lead_number = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('lead_number', 'like', 'KL%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->lead_number, 2); // remove 'KL'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return 'KL' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // KL0001, KL0002, ...
    }
}