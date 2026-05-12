<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventApproTransaction extends Model
{
    use HasFactory;
    protected $table = 'event_appro_transaction';

    protected $fillable = ['id', 'types', 'transaction_id', 'amount', 'status', 'organizer_id', 'event_id', 'uuid', 'payment_requests_id', 'transction_link', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('uuid', 'like', 'ET%')->orderBy('id', 'desc')->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->uuid, 2); // remove 'ET'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return 'ET' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // ET0001, ET0002, ...
    }

    public function EventData()
    {
        return $this->hasOne(Events::class, 'id', 'event_id');
    }
}