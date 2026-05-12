<?php

namespace App\Models\Astrologer;

use App\Models\AstrologerCategory;
use App\Models\Category;
use App\Models\Service;
use App\Models\Service_order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Astrologer extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable, HasApiTokens;
    
    public function getOtherSkillsAttribute($value)
    {
        if($value!='null'&&!empty($value))
        return Skills::whereIn('id',json_decode($value))->get();
    }
    
    public function getIsPanditPoojaCategoryAttribute($value)
    {
        if($value!='null'&&!empty($value))
        return Category::whereIn('id',json_decode($value))->get();
    }
    
    public function getImageAttribute($value)
    {
        if($value!='null'&&!empty($value))
        return asset('public/storage/astrologers/' . $value);
    }    
    public function getCategoryAttribute($value)
    {
        if($value!='null'&&!empty($value))
        return AstrologerCategory::whereIn('id',json_decode($value))->get();
    }
    
    public function getLanguageAttribute($value)
    {
        if($value!='null'&&!empty($value))
        return json_decode($value);
    }

    public function primarySkill(){
        return $this->hasOne(Skills::class,'id','primary_skills');
    }

    public function orders(){
        return $this->hasMany(Service_order::class,'pandit_assign','id');
    }
}
