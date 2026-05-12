<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPanCardVerified extends Model
{
    use HasFactory;
    protected $table = 'user_pancard_verified';
    protected $fillable = ['id', 'pan_number', 'full_name', 'category'];
}
