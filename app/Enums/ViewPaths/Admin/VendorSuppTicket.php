<?php

namespace App\Enums\ViewPaths\Admin;

enum VendorSuppTicket
{
    const ISSUELIST = [
        URI => 'issue-list',
        VIEW => 'admin-views.vendor-support-ticket.add-issue',
        REDIRECT => "admin.vendor-support-ticket.issue-add",
    ];
    const ISSUEUPDATE = [
        URI => 'edit-issue',
        VIEW => 'admin-views.vendor-support-ticket.edit-issue'
    ];

    const ISSUESTATUSUPDATE = [
        URI => 'status-update-issue',
    ];
    const ISSUEDELETE = [
        URI => "delete-issue",
    ];

    // vendor

    const VENDORLIST = [
        URI => 'view',
        VIEW => 'admin-views.vendor-support-ticket.vendor-ticket-list'
    ];
    const VENDORISSUESTATUS = [
        URI => 'issue-status',
    ];

    const VENDORSINGLE = [
        URI => 'issue-view',
        VIEW => 'admin-views.vendor-support-ticket.vendor-ticket-view'
    ];
    //admin

    const ADMINLIST = [
        URI => 'view',
        VIEW => 'admin-views.vendor-support-ticket.admin-ticket-list'
    ];
    const ADMINISSUESTATUS = [
        URI => 'issue-status',
    ];

    const ADMINSINGLE = [
        URI => 'issue-view',
        VIEW => 'admin-views.vendor-support-ticket.vendor-ticket-view'
    ];
}
