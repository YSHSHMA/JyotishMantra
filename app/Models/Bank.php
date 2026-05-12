<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    protected $table ='bank';
    protected $fillable = ['id', 'bank_name', 'status', 'created_at', 'updated_at'];
}
