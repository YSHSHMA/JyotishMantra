<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitiesReview extends Model
{
    use HasFactory;

    protected $table ='cities_review';
    protected $fillable = ['id', 'user_id', 'cities_id', 'comment','star', 'image', 'created_at', 'updated_at', 'status'];

    public function userData(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function cities(){
        return $this->hasOne(Cities::class,'id','cities_id');
    }
}
