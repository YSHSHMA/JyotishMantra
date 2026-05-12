<?php

namespace App\Enums\ViewPaths\AllPaths;

enum TrusteesPath
{
    const DASHBOARD = [
        URL => "/",
        VIEW => "all-views.trustees.dashboard.index",
        REDIRECT => "partials-trustees.dashboard.index",
    ];

    const PUJADASHBOARD = [
        URL => "view",
        VIEW => "all-views.trustees.puja_dashboard.index",
        REDIRECT => "partials-trustees.puja_dashboard.view",
    ];

    const DASHWITHDRAW = [
        URL => "withdraw-request-add",
    ];

    const ADSADD = [
        URI => 'add',
        URL => "status-update",
        VIEW => "all-views.trustees.ads.add",
    ];

    const ADSLIST = [
        URI => 'list',
        VIEW => "all-views.trustees.ads.list",
        REDIRECT => "trustees-vendor.ads-management.list",
    ];

    const ADSUPDATE = [
        URI => 'update',
        VIEW => "all-views.trustees.ads.update",
    ];
    const ADSDELETE = [
        URI => 'delete',
    ];
    const ADSDETAILS = [
        URI => 'ads-details',
        VIEW => "all-views.trustees.ads.details",
    ];

    const PROFILEUPDATE = [
        URL => 'update',
        URI => 'edit',
        VIEW => "all-views.trustees.profile.update-profile",
    ];

    const TRUSTWITHDRAW = [
        URL => "/",
        VIEW => "all-views.trustees.withdraw.index",
    ];

    const TRUSTINBOX = [
        URL => "inbox",
        VIEW => "all-views.trustees.message.inbox",
    ];
    const TRUSTINBOXSTATUS = [
        URL => "inbox-status",
    ];
    const TRUSTINBOXVIEW = [
        URL => "view",
        URI => "replay",
        VIEW => "all-views.trustees.message.single-view",
    ];

    //amdin
    const TRUSTADMININBOX = [
        URL => "inbox",
        VIEW => "all-views.trustees.message.admin-inbox",
    ];
    const ADDEMPLOYEE = [
        URL => 'add-employee',
        URI => 'edit',
        VIEW => "all-views.trustees.employee.add",
    ];
    const EMPLOYEELIST = [
        URI => 'employee-list',
        VIEW => "all-views.trustees.employee.list",
        REDIRECT => "trustees-vendor.employee.employee-list",
    ];

    const EMPLOYEEUPDATE = [
        URI => "update",
        VIEW => "all-views.trustees.employee.edit",
    ];
    const EMPLOYEESTATUSUPDATE = [
        URI => "status-update",
        URL => "employee-delete",
    ];

    const CHECHEMAILPHONE = [
        URI => "check-value",
    ];



    const DARSHANTEMPLELIST = [
        URI => 'temple-list',
        VIEW => "all-views.trustees.darshan.temple-list",
        REDIRECT => "trustees-vendor.vip-darshan.temple-list",
    ];
    const DARSHANTEMPLESTATUS = [
        URI => "status-update",
    ];
    const DARSHANTEMPLEUPDATE = [
        URI => 'temple-update',
        VIEW => "all-views.trustees.darshan.temple-update",
    ];
    const GET_CITIES = [
        URI => 'get-cities',
    ];

    const DARSHANTEMPLEBOOKING = [
        URI => 'temple-booking',
        VIEW => "all-views.trustees.darshan.temple-booking",
        REDIRECT => "trustees-vendor.vip-darshan.temple-booking",
    ];
    const DARSHANTEMPLETODAYBOOKING = [
        URI => 'temple-today-booking',
        VIEW => "all-views.trustees.darshan.temple-today-booking",
        REDIRECT => "trustees-vendor.vip-darshan.temple-today-booking",
    ];
    const DARSHANTEMPLEBOOKINGCOMPLETE = [
        URI => 'temple-booking-complete',
        VIEW => "all-views.trustees.darshan.temple-booking-complete",
        REDIRECT => "trustees-vendor.vip-darshan.temple-booking-complete",
    ];
    const DARSHANBOOKINGMEMBERCHECK = [
        URI => 'check-member-valid',
    ];
    const DARSHANTEMPLEBOOKINGINFO = [
        URI => 'darshan-booking-information',
        VIEW => "all-views.trustees.darshan.temple-booking-info",
    ];
    const DARSHANTEMPLEGALLERY = [
        URI => 'temple-gallery',
        VIEW => "all-views.trustees.darshan.temple-gallery",
    ];
    const DARSHANTEMPLEGALLERYREMOVE = [
        URI => 'image-remove',
    ];
    const DARSHANTEMPLEGALLEUPDATE = [
        URI => 'gallery-update',
    ];

    const DARSHANTEMPLEBOOKINGLISTING = [
        URI => 'vip-booking-list',
        URL => 'vip-booking-filters',
        VIEW => "all-views.trustees.darshan.vip-user-booking-list",
    ];


    const PUJACREATE = [
        URI => 'puja-list',
        URL => 'puja-save',
        VIEW => "all-views.trustees.puja.puja-list",
    ];

    const PUJAUPDATE = [
        URI => 'puja-edit',
        URL => 'puja-update',
        VIEW => "all-views.trustees.puja.puja-update",
    ];
    const PUJADELETE = [
        URI => 'puja-delete',
    ];
    const PUJABOOKINGCREATE = [
        URI => 'puja-booking',
        URL => 'puja-booking-save',
        VIEW => "all-views.trustees.puja.puja-booking",
    ];
    const PUJABOOKINGORDERID = [
        URI => 'puja-order-id-info',
        // URL => 'puja-booking-save',
    ];
    const PUJABOOKINGORDERLIST = [
        URI => 'puja-order-list',
        URL => 'puja-booking-filter',
        VIEW => "all-views.trustees.puja.puja-booking-order-list",
    ];

    const PANDITLIST = [
        URI => 'list',
        URL => 'pandit-order-filter',
        VIEW => 'all-views.trustees.purohit.order-list',
    ];

    const PANDITBALANCESHEET = [
        URI => 'purohit-balance-sheet',
        VIEW => 'all-views.trustees.purohit.purohit-balance-sheet',
    ];

    const DARSHANBOOKINGCREATE = [
        URI => 'darshan-booking',
        URL => 'darshan-booking-save',
        VIEW => "all-views.trustees.darshan_dt.darshan-booking-add",
    ];
    const DARSHANBOOKINGORDERID = [
        URI => 'darshan-ticket-get-info',
    ];
}
