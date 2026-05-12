<?php

namespace App\Models;

use App\Models\Astrologer\Astrologer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthJournalKundali extends Model
{
    use HasFactory;
    protected $table = 'birth_journal_kundali';
    protected $fillable = ['id', 'user_id', 'order_id', 'birth_journal_id', 'name', 'email', 'gender', 'phone_no', 'bod', 'time', 'country_id', 'state', 'lat', 'log', 'language', 'tzone', 'chart_style', 'payment_status', 'amount', 'transaction_id', 'kundali_pdf', 'female_name', 'female_email', 'female_gender', 'female_phone_no', 'female_dob', 'female_time', 'female_country_id', 'female_place', 'female_lat', 'female_long', 'female_tzone', 'milan_verify', 'assign_pandit', 'astrologer_type', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->order_id = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('order_id', 'like', 'BJ%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->order_id, 2); // remove 'BJ'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'BJ' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // BJ0001, BJ0002, ...
    }

    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function astrologer()
    {
        return $this->hasOne(Astrologer::class, 'id', 'assign_pandit');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function country_female()
    {
        return $this->hasOne(Country::class, 'id', 'female_country_id');
    }

    public function birthJournal()
    {
        return $this->hasOne(BirthJournal::class, 'id', 'birth_journal_id');
    }

    public function birthJournal_kundali()
    {
        return $this->hasOne(BirthJournal::class, 'id', 'birth_journal_id')
            ->where('name', 'kundali');
    }

    public function birthJournal_kundalimilan()
    {
        return $this->hasOne(BirthJournal::class, 'id', 'birth_journal_id')
            ->where('name', 'kundali_milan');
    }
}