<?php

namespace App\Models;

use App\Models\Astrologer\Astrologer;
use App\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceReview extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'astro_id',
        'user_id',
        'service_id',    
        'comment',
        'service_type',
        'rating',
        'youtube_link ',
        'status',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'rating' => 'integer',
        'status' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function user()
    {
        return $this->belongsTo(service::class, 'user_id');
    }

    public function services()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function pandit()
    {
        return $this->hasOne(Astrologer::class, 'id', 'astro_id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function userData(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function vippoojas()
    {
        return $this->hasOne(Vippooja::class, 'id', 'service_id');
    }

    public function chadhava()
    {
        return $this->hasOne(Chadhava::class, 'id', 'service_id');
    }

    public function offlinePooja()
    {
        return $this->hasOne(PoojaOffline::class, 'id', 'service_id');
    }
}
