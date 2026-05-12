<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSponsor extends Model
{
    use HasFactory;
    protected $table = 'event_sponsor';
    protected $fillable = ['id', 'type', 'name', 'company_name', 'phone', 'link', 'package_id', 'image', 'status', 'created_at', 'updated_at'];
}
