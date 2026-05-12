<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Purohit extends Authenticatable
{
    // use HasFactory;
    use HasApiTokens, Notifiable;
    protected $table = 'purohits';
    protected $fillable = [
        'id',
        'temple_id',
        'name',
        'mobile',
        'profile',
        'address',
        'description',
        'holdername',
        'bankname',
        'account_num',
        'ifsccode',
        'status',
        'withdrawal_amount',
        'requested_amount',
        'gst_amount',
        'trust_fee',
        'platform_fee',
        'collected_amount',
        'auth_token',
        'password',
        'relation_id',
        'created_at',
        'updated_at'
    ];
    protected $hidden = [
        'password',
        'auth_token',
    ];
    public function temple()
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }
}
