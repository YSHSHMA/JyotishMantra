<?php

namespace App\Enums\ViewPaths\Admin;

enum TourTypePath
{
    const ADDTYPE =[
        URI =>"list",
        VIEW =>"admin-views.tour_and_travels.tour-type.list",
        REDIRECT =>"admin.tour_type.view",
    ];

    const TYPEUPDATE =[
        URI =>"type-update",
        VIEW =>"admin-views.tour_and_travels.tour-type.edit",
    ];
    const TYPESTATUS =[
        URI =>"status-update",
    ];

    const TYPEDELETE =[
        URI =>"delete",
    ];
}
?>