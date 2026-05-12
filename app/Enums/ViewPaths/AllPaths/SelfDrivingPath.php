<?php

namespace App\Enums\ViewPaths\AllPaths;

enum SelfDrivingPath
{
    const ADDCAB = [
        URI => 'add-self-driving-vehicle',
        URL => 'store-self-driving-vehicle',
        VIEW => 'all-views.tour.self-driving.add-vehicle',
    ];

    const VEHICLECATEGORYGET = [
        URI => 'vehicle-category',
    ];
    const GETCABS = [
        URI => "get-cab-list",
    ];
    const SELFDRIVINGADD = [
        URI => "save-self-driving-vehicle",
    ];

    const SELFDRIVINGLIST = [
        URI => 'self-vehicle-list',
        VIEW => 'all-views.tour.self-driving.vehicle-list',
        REDIRECT => "tour-vendor.self-driving.self-vehicle-list",
    ];
    const SELFDRIVINGEDIT = [
        URI => 'self-vehicle-update',
        URL => 'self-vehicle-edit',
        VIEW => 'all-views.tour.self-driving.vehicle-edit',
    ];
    const SELFDRIVINGDELETE = [
        URI => 'self-vehicle-delete',
    ];
    const SELFDRIVINGDREMOVEIMG = [
        URI => 'self-vehicle-image-remove',
    ];
    const SELFDRIVINGLISTFILTER = [
        URI => 'self-driving-list-filter',
    ];
    const SELFDRIVINGSTATUSUPDATE = [
        URI => 'self-driving-status-update',
    ];
}
