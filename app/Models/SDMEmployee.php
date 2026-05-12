<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class SDMEmployee extends Authenticatable
{
    use HasFactory;
    use Notifiable, HasApiTokens;

    // public function sdm(){
    //     return $this->belongsTo(SDM::class,'sdm_id');
    // }
}
