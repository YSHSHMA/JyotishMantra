<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplates extends Model
{
    use HasFactory;
    protected $table = 'email_templates';

    protected $fillable =['id','type', 'slug', 'html', 'status', 'created_at', 'updated_at'];
}
