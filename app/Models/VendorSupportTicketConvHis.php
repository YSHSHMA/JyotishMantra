<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorSupportTicketConvHis extends Model
{
    use HasFactory;
    protected $table = 'vendor_support_tickets_convhis';
    protected $fillable = ['id','ticket_issue_id', 'sender_type', 'message', 'attached','read_admin_status','read_user_status', 'created_at', 'updated_at'];

    public function conversations(){
        return $this->hasMany(VendorSupportTicketConv::class,'id','ticket_issue_id');
    }
}
