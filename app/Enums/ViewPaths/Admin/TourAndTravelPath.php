<?php

namespace App\Enums\ViewPaths\Admin;

enum TourAndTravelPath
{
    const ADDTRAVEL = [
        URI => "add-traveller",
        VIEW => "admin-views.tour_and_travels.add-traveller",
    ];

    const TRAVELLIST = [
        URI => "traveller-list",
        VIEW => "admin-views.tour_and_travels.traveller-list",
        REDIRECT => "admin.tour_and_travels.traveller-list",
    ];
    const TRAVELSTATUS = [
        URI => "traveller-status-change",
        URL => "traveller-company-status",
    ];
    const VENDORTOURCOMMISSION = [
        URI => "vendor-tour-commission",
    ];
    const TRAVELUPDATE = [
        URI => "edit-traveller",
        VIEW => "admin-views.tour_and_travels.update-traveller",
    ];
    const TRAVELDELETE = [
        URI => "traveller-delete",
    ];

    const TRAVELVIEW = [
        URI => "information",
        VIEW => "admin-views.tour_and_travels.travels.information",
    ];

    const TRAVCABUPDATE = [
        VIEW => "admin-views.tour_and_travels.travels.cab-update",
    ];
    const TRAVDRIVERUPDATE = [
        VIEW => "admin-views.tour_and_travels.travels.driver-update",
    ];
}
