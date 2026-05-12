<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorSupportTicketConv extends Model
{
    use HasFactory;
    protected $table = 'vendor_support_tickets_conv';
    protected $fillable = ['id', 'ticket_id', 'created_by', 'type', 'vendor_id', 'query_title', 'status', 'created_at', 'updated_at'];

    public function Tour()
    {
        return $this->hasOne(TourAndTravel::class, 'id', 'vendor_id');
    }
    public function Event()
    {
        return $this->hasOne(EventOrganizer::class, 'id', 'vendor_id');
    }
    public function Trust()
    {
        return $this->hasOne(DonateTrust::class, 'id', 'vendor_id');
    }
    public function seller()
    {
        return $this->hasOne(Seller::class, 'id', 'vendor_id');
    }

    public function TicketTitle()
    {
        return $this->hasOne(VendorSupportTicket::class, 'id', 'ticket_id');
    }


    public function conversations()
    {
        return $this->hasMany(VendorSupportTicketConvHis::class, 'ticket_issue_id', 'id');
    }
}