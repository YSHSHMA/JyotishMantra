<?php

namespace App\Enums\ViewPaths\Admin;

enum SupportTicket
{
    const LIST = [
        URI => 'view',
        URL => 'view-ticket',
        VIEW => 'admin-views.support-ticket.view'
    ];

    const VIEW = [
        URI => 'single-ticket',
        VIEW => 'admin-views.support-ticket.singleView'
    ];

    const STATUS = [
        URI => 'status',
        VIEW => ''
    ];
    
    //new type
    const TYPELIST = [
        URI => 'add-type',
        VIEW => 'admin-views.support-ticket.add-type',
        REDIRECT =>"admin.support-ticket.type-add",
    ];
    const TYPEUPDATE = [
        URI => 'edit-type',
        VIEW => 'admin-views.support-ticket.edit-type'
    ];
    const TYPESTATUSUPDATE =[
        URI => 'status-update',
    ];
    const TYPEDELETE =[
        URI =>"delete-type",
    ];
    const ISSUELIST = [
        URI => 'add-issue',
        VIEW => 'admin-views.support-ticket.add-issue',
        REDIRECT =>"admin.support-ticket.issue-add",
    ];
    const ISSUEUPDATE = [
        URI => 'edit-issue',
        VIEW => 'admin-views.support-ticket.edit-issue'
    ];

    const ISSUESTATUSUPDATE =[
        URI => 'status-update-issue',
    ];
    const ISSUEDELETE =[
        URI =>"delete-issue",
    ];

}