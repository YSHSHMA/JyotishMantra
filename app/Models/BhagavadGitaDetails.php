<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\SoftDeletes;

class BhagavadGitaDetails extends Model
{    
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'chapter_id',
        'chapter',
        'verse',
        'description',
        'image',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'chapter' => 'string',
        'chapter_id' => 'integer',
        'verse' => 'integer',
        'description' => 'string',
        'image' => 'string',
        'status' => 'string',
    ];


   protected $dates = ['deleted_at'];

   protected $appends = ['verse_data', 'hi_description'];

    public function getVerseDataAttribute()
    {
        // Ensure the token is defined; otherwise, replace it with your actual token
        $token = 'Mahakal@2024@sahitya'; 

        $url = 'https://sahitya-mahakal.rizrv.net/bhagvad-geeta?chapter=' . $this->attributes['chapter_id'] . '&verse=' . $this->attributes['verse'];
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return null;
        }

        return json_decode($response, true);

    }

   public function scopeActive(): mixed
    {
        return $this->where('status',1);
    }

   // In BhagavadGitaDetails model
    public function bhagavadgita(): BelongsTo
    {
        return $this->belongsTo(BhagavadGitaChapter::class, 'chapter_id');
    }

    public function translations(): MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }

    public function getHiDescriptionAttribute($description): string|null
    {

        return $this->translations[0]->value ?? $description;
    }


      public function getDefaultNameAttribute(): string|null
    {
        return $this->translations[0]->value ?? $this->name;
    }

    // protected static function boot(): void
    // {
    //     parent::boot();
    //     static::addGlobalScope('translate', function (Builder $builder) {
    //         $builder->with(['translations' => function ($query) {
    //             if (strpos(url()->current(), '/api')) {
    //                 return $query->where('locale', App::getLocale());
    //             } else {
    //                 return $query->where('locale', getDefaultLanguage());
    //             }
    //         }]);
    //     });
    // }
}