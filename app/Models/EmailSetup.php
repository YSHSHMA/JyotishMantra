<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;


class EmailSetup extends Model
{

    protected $fillable = [
        'type',
        'mailername',
        'driver',
        'username',
        'encryption',
        'host',
        'port',
        'emailid',
        'password',
        'status',
    ];

    protected $casts = [
        'type' => 'string',
        'mailername' => 'string',
        'driver' => 'string',
        'username' => 'string',
        'encryption' => 'string',
        'host' => 'string',
        'port' => 'string',
        'emailid' => 'string',
        'status' => 'integer',
        'password' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function serviceAll(): HasMany
    {
        return $this->hasMany(Vippooja::class);
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
    public function translations(): MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }

    public function getNameAttribute($name): string|null
    {
        // dd($this->translations);
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations[0]->value ?? $name;
    }

    public function getShortBenifitsAttribute($short_benifits): string|null
    {
        // dd($this->translations);
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $short_benifits;
        }
        return $this->translations[1]->value ?? $short_benifits;
    }

    public function getProcessAttribute($process): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $process;
        }
        return $this->translations[2]->value ?? $process;
    }
    
    public function getBenefitsAttribute($benefits): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $benefits;
        }
        return $this->translations[3]->value ?? $benefits;
    }
    
    public function getTempleDetailsAttribute($temple_details): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $temple_details;
        }
        return $this->translations[4]->value ?? $temple_details;
    }
    public function getDetailsAttribute($detail): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $detail;
        }
        if (str_contains(url()->current(), 'counselling')) {
            return $this->translations[1]->value ?? $detail;
        }
        return $this->translations[5]->value ?? $detail;
    }
    public function getPoojaHeadingAttribute($pooja_heading): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $pooja_heading;
        }
        return $this->translations[6]->value ?? $pooja_heading;
    }

    protected static function boot(): void
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
    }
}
