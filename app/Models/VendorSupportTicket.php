<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorSupportTicket extends Model
{
    use HasFactory;
    
    protected $table = 'vendor_support_tickets';
    protected $fillable = ['id','created_by', 'type', 'message', 'created_at', 'updated_at', 'status'];
}
