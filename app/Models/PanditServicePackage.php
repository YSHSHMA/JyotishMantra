<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanditServicePackage extends Model
{
    use HasFactory;

    
    protected $fillable = ['pandit_id','type','service_id','package_id','price','thumbnail','status'];
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    public function service()
    {
        return $this->belongsTo(\App\Models\Service::class, 'service_id');
    }


}
