<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TempleServicePackages extends Model
{
    use HasFactory;
    protected $table = 'temple_service_packages';
    protected $fillable = ['id','addedBy' ,'name', 'description', 'type', 'status', 'created_at', 'updated_at'];
    
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

    public function getShortBenifitsAttribute($short_description): string|null
    {
        // dd($this->translations);
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $short_description;
        }
        return $this->translations[1]->value ?? $short_description;
    }

}
