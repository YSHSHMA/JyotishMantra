<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class SDM extends Authenticatable
{
    use HasFactory;
    use Notifiable, HasApiTokens;

    // public function collector(){
    //     return $this->belongsTo(Collector::class,'collector_id');
    // }

    // public function employee(){
    //     return $this->hasMany(SDMEmployee::class,'sdm_id');
    // }
}
