<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeedback extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'message', 'status'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
