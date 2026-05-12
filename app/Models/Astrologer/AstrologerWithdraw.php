<?php

namespace App\Models\Astrologer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologerWithdraw extends Model
{
    use HasFactory;

    public function astrologer(){
        return $this->belongsTo(Astrologer::class,'astro_id','id');
    }
}
