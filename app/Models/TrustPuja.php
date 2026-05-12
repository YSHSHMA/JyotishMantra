<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrustPuja extends Model
{
    use HasFactory;

    protected $table = 'trust_puja';
    protected $fillable = ['id', 'trust_id', 'puja_name', 'rprice', 'pprice', 'discount', 'created_at', 'updated_at'];

    public function Trust()
    {
        return $this->hasOne(DonateTrust::class, 'id', 'trust_id');
    }
}
