<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPermissions extends Model
{
    use HasFactory;
    protected $table = 'vendor_permissions';
    protected $fillable = ['id', 'type', 'module', 'sub_module', 'permission', 'created_at', 'updated_at'];
}
