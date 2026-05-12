<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourCabManage extends Model
{
    use HasFactory;
    protected $table = 'tour_traveller_cabs';
    protected $fillable = ['id', 'traveller_id', 'cab_id', 'model_number', 'reg_number','fuel_type', 'status', 'image', 'created_at', 'updated_at'];

    public function Cabs()
    {
        return $this->hasOne(TourCab::class, 'id', 'cab_id');
    }
}
