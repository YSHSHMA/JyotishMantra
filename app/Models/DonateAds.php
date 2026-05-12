<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class DonateAds extends Model
{
    use HasFactory;
    protected $table = "donate_ads";

    protected $fillable = ['id', 'ads_id', 'name', 'type', 'category_id', 'trust_id', 'purpose_id', 'set_type','set_json', 'set_title', 'set_amount', 'set_number', 'set_unit', 'description', 'image',"set_requirement_amount","set_requirement_date_range", 'status', 'is_approve', 'approve_amount', 'admin_commission', 'admin_commission_amount', 'total_amount_ads', 'auto_pay_set_status','created_at', 'updated_at', 'slug'];

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
            $model->ads_id = self::generateUniqueId();
        });
    }
    private static function generateUniqueId()
    {
        $lastRecord = self::where('ads_id', 'like', 'TA%')->orderBy('id', 'desc')->first();
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->ads_id, 2); // remove 'TA'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return 'TA' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // TA0001, TA0002, ...
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }
    public function category()
    {
        return $this->hasOne(DonateCategory::class, 'id', 'category_id');
    }
    public function Trusts()
    {
        return $this->hasOne(DonateTrust::class, 'id', 'trust_id')->with('translations');
    }
    public function Purpose()
    {
        return $this->hasOne(DonateCategory::class, 'id', 'purpose_id');
    }

    public function getNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }
    public function getDescriptionAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[1]->value ?? $name;
    }
}