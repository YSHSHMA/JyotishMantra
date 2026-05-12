<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DarshanOrderMembers extends Model
{
    use HasFactory;
    protected $table = "darshan_order_member";

    protected $fillable = ['id', 'darshan_id', 'name', 'address','image','phone', 'aadhar', 'barcode', 'verify','aadhar_verify_status','created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->barcode = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('barcode', 'like', 'BT%')->orderBy('id', 'desc')->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->barcode, 2); // remove 'BT'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'BT' . str_pad($newNumber, 9, '0', STR_PAD_LEFT); // BT0001, BT0002, ...
    }

    public function darshanOrder()
    {
        return $this->belongsTo(DarshanOrder::class, 'darshan_id', 'id')->with(['userData','Temple']);
    }
}