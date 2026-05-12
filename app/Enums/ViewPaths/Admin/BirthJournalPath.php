<?php

namespace App\Enums\ViewPaths\Admin;

enum BirthJournalPath
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.birth_journal.list',
        REDIRECT => 'admin.birth_journal.kundali_list',
    ];
    const ADD = [
        URI => 'add',
        VIEW => 'admin-views.birth_journal.add'
    ];
    const STATUS = [
        URI => 'status-update',
        VIEW => '--',
    ];
    const DELETE = [
        URI => 'delete',
        REDIRECT => 'admin.birth_journal.kundali_list',
    ];
    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.birth_journal.edit'
    ];

    const ALLORDER = [
        URI => 'orders/order-list',
        VIEW => 'admin-views.birth_journal.allorder'
    ];
    const PENDING = [
        URI => 'orders/order-pending',
        VIEW => 'admin-views.birth_journal.pending'
    ];
    const COMPLETED = [
        URI => 'orders/order-completed',
        VIEW => 'admin-views.birth_journal.completed'
    ];
    const VIEWKUNDLI = [
        URI => 'orders/view-kundali',
        URL => 'orders/verify-kundali-milan',
        VIEW => 'admin-views.birth_journal.kundli_milan_show'
    ];
    const PAIDKUNDLI = [
        URI => 'paid-kundali',
        URL => 'paid-kundali-upload-pdf',
        VIEW => 'admin-views.birth_journal.paid_kundli'
    ];


    const LEADS = [
        URI => 'kundli_leads',
        VIEW => "admin-views.birth_journal.leads",
    ];
    const LEADSDELETE = [
        URI => 'kundli-leads-delete',
        REDIRECT => 'admin.birth_journal.kundli_leads',
    ];

    const LEADSFOLLOW = [
        URI => 'kundli-follow-list',
    ];
}
