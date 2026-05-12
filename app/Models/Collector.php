<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Collector extends Authenticatable
{
    use HasFactory;
    use Notifiable, HasApiTokens;

    public function collector()
    {
        return $this->hasOne(Collector::class, 'id','rel_collector_id')
                    ->where('type', 'collector');
    }

    public function sdm()
    {
        return $this->hasOne(Collector::class, 'id','rel_sdm_id')
                    ->where('type', 'sdm');
    }

    public function sdms()
    {
        return $this->hasMany(Collector::class, 'rel_collector_id', 'id')
                    ->where('type', 'sdm');
    }

    public function employees()
    {
        return $this->hasMany(Collector::class, 'rel_sdm_id', 'id')
                    ->where('type', 'sdm-employee');
    }
}
