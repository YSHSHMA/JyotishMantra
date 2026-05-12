<?php

namespace App\Enums\ViewPaths\Admin;

enum DonateTrustPath
{
    const ADDTRUST = [
        URI => "add-trust",
        VIEW => "admin-views.donate_management.trust.add-trust",
    ];

    const ADDTRUSTLIST = [
        URI => "trust-list",
        VIEW => 'admin-views.donate_management.trust.list',
        REDIRECT => "admin.donate_management.trust.list",
    ];

    const ADDTRUSTSTATUS = [
        URI => "trust-status",
    ];

    const ADDTRUSTDELETE  = [
        URI => "trust-delete",
    ];
    const ADDTRUSTUPDATE = [
        URI => "trust-update",
        VIEW => 'admin-views.donate_management.trust.update',
    ];

    const REMOVETGALLERY = [
        URI => "gallery-remove",
    ];

    const TRUSTDETAIL = [
        URI => "trust-details",
        VIEW => "admin-views.donate_management.trust.trust-details",
    ];

    const TRUSTCOMMISSION = [
        URI => "trust-admin-commission",
    ];
    const TRUSTVERIFY = [
        URI => "trust-verify-status",
    ];
    const TRUSTAPPROVED = [
        URI => "trust-approved",
    ];
    const TRUSTCANCELED = [
        URI => "trust-canceled",
    ];

    const TRUSTPENDING = [
        URI => "trust-pending",
        VIEW => 'admin-views.donate_management.trust.trust-pending',
    ];

    const TRUSTREQAPPROV = [
        URI => "trust-amount-approval",
    ];

    //trust
    const LEADS = [
        URI => "trust-lead",
        VIEW => "admin-views.donate_management.donate-lead.list",
    ];
    const LEADFOLLOWUP = [
        URI => "lead-follow-up",
        URL => "donate-follow-list",
    ];
    const LEADDELETE = [
        URI => "lead-delete",
    ];

    const DONATED = [
        URI => 'donated-list',
        VIEW => "admin-views.donate_management.donated.list",
    ];

    const DONATEDVIEW = [
        URI => 'donated-view',
        VIEW => "admin-views.donate_management.donated.view",
    ];
    const TRUSTPUJABOOKING = [
        URI => 'trust-puja-booking',
        URL => 'trust-puja-booking-filter',
        VIEW => "admin-views.donate_management.trust-puja.view",
    ];
}
