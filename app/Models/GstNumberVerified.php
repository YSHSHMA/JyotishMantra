<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstNumberVerified extends Model
{
    use HasFactory;
    protected $table = 'gst_number_verified';
    protected $fillable = ['id', 'gstin', 'pan_number', 'business_name', 'legal_name', 'center_jurisdiction', 'state_jurisdiction', 'date_of_registration', 'constitution_of_business', 'taxpayer_type', 'gstin_status', 'date_of_cancellation', 'field_visit_conducted', 'nature_bus_activities', 'nature_of_core_business_activity_code', 'nature_of_core_business_activity_description', 'filing_status', 'address', 'hsn_info', 'created_at', 'updated_at'];
}
