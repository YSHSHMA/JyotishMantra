<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountNumberVerified extends Model
{
    use HasFactory;
    protected $table = 'account_number_verified';
    protected $fillable = ['id', 'account_number', 'ifsc', 'account_exists', 'upi_id', 'remarks', 'ifsc_details', 'created_at', 'updated_at'];

}
