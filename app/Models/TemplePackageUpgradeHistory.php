<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplePackageUpgradeHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'temple_id',
        'trust_id',
        'old_package_id',
        'new_package_id',
        'old_amount',
        'new_amount',
        'upgrade_percentage',
    ];
}
