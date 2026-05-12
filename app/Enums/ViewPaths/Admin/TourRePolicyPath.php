<?php

namespace App\Enums\ViewPaths\Admin;

enum TourRePolicyPath
{
    const ADDPOLICY =[
        URI =>"policy",
        VIEW =>"admin-views.tour_and_travels.refund-policy.list",
        REDIRECT =>"admin.tour-refund-policy.list",
    ];

    const POLICYUPDATE =[
        URI =>"policy-update",
        VIEW =>"admin-views.tour_and_travels.refund-policy.edit",
    ];
    const POLICYSTATUS =[
        URI =>"status-update",
    ];

    const POLICYDELETE =[
        URI =>"delete",
    ];
}
?>