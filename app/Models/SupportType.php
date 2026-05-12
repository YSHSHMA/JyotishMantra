<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportType extends Model
{
    use HasFactory;
    protected $table = 'support_type';

    protected $fillable =['id', 'name', 'status', 'created_at', 'updated_at' ];

}
