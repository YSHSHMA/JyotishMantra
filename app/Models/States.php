<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    use HasFactory;
    protected $table = 'states';

    protected $fillable = [
        'id', 'name','logo', 'country_id'
    ];

    public $timestamps  = false;
}
