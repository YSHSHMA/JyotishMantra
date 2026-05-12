<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

class BhagavadGitaChapter extends Model
{
    protected $fillable = [
        'name',
        'image',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'image' => 'string',
        'status' => 'string',
    ];

   // public function scopeActive(): mixed
   //  {
   //      return $this->where('status',1);
   //  }


    protected $appends =['verse_count','hi_name'];

    public function getVerseCountAttribute()
    {
        // Ensure the token is defined; otherwise, replace it with your actual token
        $token = 'Mahakal@2024@sahitya'; 

        $url = 'https://sahitya-mahakal.rizrv.net/bhagvad-geeta?chapter=' . $this->attributes['id'];
        
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

        return count(json_decode($response));

    }

    public function details()
    {
        return $this->hasMany(BhagavadGitaDetails::class, 'chapter_id');
    }

    public function translations(): MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }

    public function getHiNameAttribute($name): string|null
    {

        return $this->translations[0]->value ?? $name;
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