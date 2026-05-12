<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPhonePermissions extends Model
{
    use HasFactory;
    protected $table = 'vendor_phone_permissions';
    protected $fillable = ['id', 'phone', 'name', 'created_at', 'updated_at'];
}
