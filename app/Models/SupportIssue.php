<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportIssue extends Model
{
    use HasFactory;
    protected $table = 'support_issue';

    protected $fillable =['id','issue_name', 'type_id', 'status', 'created_at', 'updated_at'];

    public function TicketType(){
        return $this->hasOne(SupportType::class,'id','type_id');
    }
}
