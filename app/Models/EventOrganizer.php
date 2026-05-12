<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class EventOrganizer extends Model
{
    use HasFactory;
    protected $table = 'event_organizer';

    protected $fillable = ['id', 'unique_id', 'organizer_name', 'organizer_pan_no', 'organizer_address', 'gst_no_type', 'gst_no', 'itr_return', 'full_name', 'email_address', 'contact_number', 'beneficiary_name', 'account_type', 'bank_name', 'ifsc_code', 'account_no', 'pan_card_image', 'cancelled_cheque_image', 'aadhar_image', 'image',"itr_return_image", 'is_approve', 'status', 'created_at', 'updated_at', 'profit_information'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->unique_id = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('unique_id', 'like', 'EOrg%')->orderBy('id', 'desc')->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->unique_id, 2); // remove 'EOrg'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return 'EOrg' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // EOrg0001, EOrg0002, ...
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function sellers(){
        return $this->belongsTo(Seller::class,"id","relation_id")->where('type', 'event');
    }
}