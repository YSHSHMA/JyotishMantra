<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

/**
 * @property int $id
 * @property string $question
 * @property string $detail
 * @property int $status
 */
class FAQ extends Model
{

    protected $fillable = [
        'question',
        'category_id',
        'detail',
        'status'
    ];

    protected $casts = [
        'question' => 'string',
        'detail' => 'string',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive(): mixed
    {
        return $this->where('status',1);
    }
   

    public function translations(): MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }

    public function getQuestionAttribute($question): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $question;
        }

        return $this->translations[0]->value??$question;
    }

    public function getDetailAttribute($detail): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $detail;
        }

        return $this->translations[1]->value??$detail;
    }

    public function getDefaultNameAttribute(): string|null
    {
        return $this->translations[0]->value ?? $this->name;
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')){
                    return $query->where('locale', App::getLocale());
                }else{
                    return $query->where('locale', getDefaultLanguage());
                }
            }]);
        });
    }
    public function Category(){
        return $this->hasOne(FaqCategory::class, 'id','category_id');
    }
}