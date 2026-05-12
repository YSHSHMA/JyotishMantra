<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLicenseNumberVerified extends Model
{
    use HasFactory;
    protected $table = 'user_license_number_verified';
    protected $fillable = [
        'id',
        "license_number",
        "state",
        "name",
        "permanent_address",
        "permanent_zip",
        "temporary_address",
        "temporary_zip",
        "citizenship",
        "ola_name",
        "ola_code",
        "gender",
        "father_or_husband_name",
        "dob",
        "doe",
        "transport_doe",
        "doi",
        "transport_doi",
        "profile_image",
        "has_image",
        "blood_group",
        "vehicle_classes",
        "less_info",
        "additional_check",
        "initial_doi"
    ];
}
