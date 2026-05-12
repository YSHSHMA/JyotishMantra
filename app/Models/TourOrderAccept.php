<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourOrderAccept extends Model
{
    use HasFactory;
    protected $table = 'tour_order_accept';
    protected $fillable = ['id','tour_id', 'traveller_id', 'status', 'created_at', 'updated_at'];
 
    
    public function TourVisit(){
        return $this->hasOne(TourVisits::class, 'id', 'tour_id');
    }

    public function order()
    {
        return $this->belongsTo(TourOrder::class, 'tour_id', 'tour_id');
    }

    public function TourTraveller(){
        return $this->hasOne(TourAndTravel::class, 'id', 'traveller_id');
    }
}
