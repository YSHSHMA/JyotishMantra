<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPermissionRole extends Model
{
    use HasFactory;
    protected $table = 'vendor_permissions_role';
    protected $fillable = ['id', 'role_id', 'module', 'sub_module', 'permission', 'created_at', 'updated_at'];

    public function Role()
    {
        return $this->hasone(VendorRoles::class, 'id', 'role_id');
    }
}
