<?php

namespace App\Enums\ViewPaths\Admin;

enum TourCabPath
{
    const VEHICLELIST = [
        URI => "vehicel-list",
        URL => "vehicel-list-filter",
        VIEW => "admin-views.tour_and_travels.vehicle-setting.list",
        REDIRECT => "admin.tour_vehicle_setting.view",
    ];

    const VEHICLEADD = [
        URI => "vehicel-add",
        URL => "vehicle-save",
        VIEW => "admin-views.tour_and_travels.vehicle-setting.add",
    ];
    const VEHICLEUPDATE = [
        URI => "vehicel-update",
        URL => "vehicle-edit",
        VIEW => "admin-views.tour_and_travels.vehicle-setting.update",
    ];
    const VEHICLEDELETE = [
        URI => "vehicel-delete",
    ];
    const VEHICLESTATUSUPDATE = [
        URI => "vehicel-status-update",
    ];


    const ADDCAB = [
        URI => "cab",
        VIEW => "admin-views.tour_and_travels.cab-service.list",
        REDIRECT => "admin.tour_cab_service.view",
    ];
    const VEHICLECATEGORYGET = [
        URI => "vehicle-category",
    ];

    const CABUPDATE = [
        URI => "cab-update",
        VIEW => "admin-views.tour_and_travels.cab-service.edit",
    ];
    const CABSTATUS = [
        URI => "status-update",
    ];

    const CABDELETE = [
        URI => "delete",
    ];
}
