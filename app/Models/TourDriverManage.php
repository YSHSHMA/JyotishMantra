<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourDriverManage extends Model
{
    use HasFactory;

    protected $table = 'tour_traveller_driver';
    protected $fillable = ['id', 'traveller_id', 'name', 'phone', 'email', 'gender', 'dob', 'year_ex', 'license_number', 'pan_number', 'aadhar_number', 'image','license_image', 'pan_image', 'aadhar_image', 'status', 'order_complete', 'created_at', 'updated_at'];
    
}
