<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAadhaarKyc extends Model
{
    use HasFactory;
    protected $table = 'user_aadhaar_kyc';
    protected $fillable = ['id', 'full_name', 'aadhaar_number', 'dob', 'gender', 'aadhaar_pdf', 'mobile_verified', 'zip', 'mobile_hash', 'address', 'phone_no', 'user_id'];
}
