<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WKundaliTemplate extends Model
{
    use HasFactory;
    protected $table = 'w_kundali_templates';
    protected $fillable =['id', 'order_id', 'template_name', 'body', 'created_at', 'updated_at'];

}
