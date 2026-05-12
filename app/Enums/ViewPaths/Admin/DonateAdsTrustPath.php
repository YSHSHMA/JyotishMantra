<?php

namespace App\Enums\ViewPaths\Admin;

enum DonateAdsTrustPath
{
    const ADDADS =[
        URI =>"create-ads",
        URL =>"api-donate-trust-list",
        VIEW =>"admin-views.donate_management.ads.create-ads",
    ];
    const LIST = [
        URI =>"ads-list",
        VIEW =>"admin-views.donate_management.ads.ads-list",
        REDIRECT =>"admin.donate_management.ad_trust.list",
    ];
    const DELETE =[
        URI =>"ads-remove",
    ];
    const ADSSTATUS = [
        URI =>"ads-status-update",
    ];
    const UPDATEADS = [
        URI =>"update-ads",
        VIEW =>"admin-views.donate_management.ads.update-ads",
    ];

    const ADSINFO = [
        URI =>"ads-details",
        VIEW =>"admin-views.donate_management.ads.ads-details",
    ];
    const ADSCOMMISSION =[
        URI =>"ads-commission-update",
    ];
    const ADSAPPROVAL = [
        URI =>"ads-approval-send-link",
    ];

}
?>