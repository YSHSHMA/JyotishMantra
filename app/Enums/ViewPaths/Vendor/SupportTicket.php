<?php

namespace App\Enums\ViewPaths\Vendor;

enum SupportTicket
{
    const INBOX = [
        URL => 'view',
        VIEW => 'vendor-views.support-ticket.vendor-index',
    ];
    const INBOXSTATUS = [
        URL => 'vendor-status',
    ];
    const INBOXVIEW =[
        URL => 'ticket-view',
        VIEW => 'vendor-views.support-ticket.vendor-view',
    ];

    const INBOXS =[
        URL => 'view',
        VIEW => 'vendor-views.support-ticket.admin-index',
    ];
}
