<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;


    protected $table = 'district';


    protected $fillable = [
        'state_id',
        'name',
        'status',
    ];

    public $timestamps = false;

    /**
     * State relation
     */
    public function state()
    {
        return $this->belongsTo(States::class, 'state_id', 'id');
    }
}