<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRoles extends Model
{
    use HasFactory;
    protected $table = 'vendor_roles';
    protected $fillable = ['id','type', 'name', 'status', 'created_at', 'updated_at'];
}
