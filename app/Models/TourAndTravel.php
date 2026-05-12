<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class TourAndTravel extends Model
{
    use HasFactory;
    protected $table = 'tour_and_travels';
    protected $fillable = ['id', 'traveller_id', 'owner_name', 'company_name', 'phone_no', 'email', 'state', 'city', 'address', 'web_site_link', 'services', 'area_of_operation', 'person_name', 'person_phone', 'person_email', 'person_address', 'bank_holder_name', 'bank_name', 'bank_branch', 'ifsc_code', 'account_number', 'gst_image','gst_number', 'pan_card_image', 'aadhaar_card_image', 'address_proof_image','pan_card_number','aadhar_card_number', 'image', 'banner', 'status','admin_commission','self_driving_commission', 'is_approve','experience', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')) {
                    return $query->where('locale', App::getLocale());
                } else {
                    return $query->where('locale', getDefaultLanguage());
                }
            }]);
        });

        static::creating(function ($model) {
            $model->traveller_id = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('traveller_id', 'like', 'TT%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->traveller_id, 2); // remove 'TT'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return 'TT' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // TT0001, TT0002, ...
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function getOwnerNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }

    public function getCompanyNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }

        return $this->translations[1]->value ?? $name;
    }

    public function getAddressAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }

        return $this->translations[2]->value ?? $name;
    }

    public function getServicesAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }

        return $this->translations[3]->value ?? $name;
    }

    public function getAreaOfOperationAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }

        return $this->translations[4]->value ?? $name;
    }

    public function getPersonNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }

        return $this->translations[5]->value ?? $name;
    }

    public function getPersonAddressAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }

        return $this->translations[6]->value ?? $name;
    }
}