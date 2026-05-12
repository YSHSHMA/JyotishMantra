<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflinepoojaReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'astro_id',
        'user_id',
        'service_id',    
        'comment',
        'service_type',
        'rating',
        'status',

    ];

    public function service()
    {
        return $this->belongsTo(PoojaOffline::class, 'service_id');
    }

    public function user()
    {
        return $this->belongsTo(service::class, 'user_id');
    }

    public function services()
    {
        return $this->hasOne(PoojaOffline::class, 'id', 'service_id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function offlinePooja()
    {
        return $this->hasOne(PoojaOffline::class, 'id', 'service_id');
    }
    
    public function userData(){
        return $this->hasOne(User::class,'id','user_id');
    }
}
