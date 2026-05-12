<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempleReview extends Model
{
    use HasFactory;
    protected $table = 'temple_review';
    protected $fillable = ['id', 'user_id', 'temple_id', 'comment', 'star', 'image', 'created_at', 'updated_at', 'status'];

    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function templeData()
    {
        return $this->HasMany(Temple::class, 'id', 'temple_id');
    }
    public function templeinfo()
    {
        return $this->hasOne(Temple::class, 'id', 'temple_id');
    }
}