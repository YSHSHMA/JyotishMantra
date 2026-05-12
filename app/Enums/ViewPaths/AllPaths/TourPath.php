<?php

namespace App\Enums\ViewPaths\AllPaths;

enum TourPath
{
    const DASHBOARD = [
        URL => "/",
        VIEW => "all-views.tour.dashboard.index",
        REDIRECT => "tour-vendor.dashboard.index",
    ];

    const DASHWITHDRAW = [
        URL => "withdraw-request-add",
    ];

    const PROFILEUPDATE = [
        URL => 'update',
        URI => 'edit',
        VIEW => "all-views.tour.profile.update-profile",
    ];

    const ORDERPENDING = [
        URL => "pending",
        VIEW => "all-views.tour.order.order-pending",
    ];

    const TOURLEADADD = [
        URL => "add-leads",
        VIEW => "all-views.tour.lead.add-lead",
    ];
    const TOURLEADLIST = [
        URL => "lead-list",
        URI => "get-tour-info-div",
        VIEW => "all-views.tour.lead.lead-list",
    ];
    const TOURLEADLISTFILTER = [
        URL => "lead-list-filter",
    ];
    const LEADSCLOSEUPDATE = [
        URI => 'leads-close-update',
    ];
    const LEADSGET = [
        URI => "tour-follow-up",
    ];
    const LEADMESSAGE = [
        URL => "tour-whatsapp-message",
    ];
    const UPDATELEADADMIN = [
        URL => "tour-lead-edit",
        URI => "tour-lead-update",
        VIEW => "all-views.tour.lead.lead-update",
    ];

    const ORDERCONFIRM = [
        URL => "confirm",
        VIEW => "all-views.tour.order.order-confirm",
    ];

    const ORDERASSIGNED = [
        URL => "assigned",
        VIEW => "all-views.tour.order.order-assigned",
    ];

    const ORDERPICKUP = [
        URL => "pickup",
        VIEW => "all-views.tour.order.order-pickup",
    ];

    const ORDERCOMPLETE = [
        URL => "complete",
        VIEW => "all-views.tour.order.order-complete",
    ];

    const ORDERCANCEL = [
        URL => "user-cancel-order",
        VIEW => "all-views.tour.order.user-cancel-order",
    ];

    const ORDERDETAILS = [
        URL => "details",
        VIEW => "all-views.tour.order.order-details",
    ];

    const ADDTOUR = [
        URL => "add-tour",
        VIEW => "all-views.tour.tour.add",
    ];

    const TOURLIST = [
        URL => "tour-list",
        VIEW => "all-views.tour.tour.list",
        REDIRECT => "tour-vendor.tour_visits.tour-list",
    ];

    const TOURUPDATE = [
        URL => "update",
        VIEW => "all-views.tour.tour.edit",
    ];
    const TOURVIEW = [
        URL => "view",
        VIEW => "all-views.tour.tour.view",
    ];
    const TOURIMGDELETE = [
        URL => "tour-delete-image",
    ];
    const TOURDELETE = [
        URL => "tour-delete",
    ];
    const TOUROVERVIEW = [
        URL => "overview",
        VIEW => "all-views.tour.overview.index",
    ];

    const TOURVISITLIST = [
        URL => "tour-visit",
        VIEW => "all-views.tour.visit.index",
        REDIRECT => "tour-vendor.tour_visits.add-visit",
    ];
    const TOURVISITDELETE = [
        URL => "delete-place",
    ];
    const TOURVISITIMGDELETE = [
        URL => "visit-delete-image",
    ];
    const TOURVISITUPDATE = [
        URL => "tour-visit-update",
        VIEW => "all-views.tour.visit.edit",
    ];
    const TOURACCEPT = [
        URL => "accept-tour",
    ];

    const WITHDRAW = [
        URL => "/",
        VIEW => "all-views.tour.withdraw.index",
    ];


    const CABLIST = [
        URL => "cab-list",
        URI => "cab-store",
        VIEW => "all-views.tour.cab.index",
        REDIRECT => "tour-vendor.tour_cab_management.cab-list",
    ];
    const CABUPDATE = [
        URL => "cab-update",
        URI => "cab-remove-image",
        VIEW => "all-views.tour.cab.edit",
    ];
    const CABSTATUSUPDATE = [
        URL => "cab-status-update",
    ];
    const CABTRAVELLERDELETE = [
        URL => "cab-traveller-delete",
    ];

    const DRIVERLIST = [
        URL => "cab-driver-list",
        URI => "driver-store",
        VIEW => "all-views.tour.driver.index",
        REDIRECT => "tour-vendor.tour_cab_management.cab-driver-list",
    ];
    const DRIVERSTATUSUPDATE = [
        URL => "driver-status-update",
    ];
    const DRIVERDETELE = [
        URL => "driver-delete",
    ];
    const DRIVERUPDATE = [
        URL => "driver-edit",
        VIEW => "all-views.tour.driver.edit",
    ];
    const ORDERCDASSIGN = [
        URL => "cab-driver-assign",
    ];

    const ORDERREMINDERMESSAGE = [
        URL => "tour-order-reminder-message",
    ];

    const INBOX = [
        URL => "inbox",
        VIEW => "all-views.tour.message.inbox",
    ];
    const INBOXSTATUS = [
        URL => "inbox-status",
    ];
    const INBOXVIEW = [
        URL => "view",
        URI => "replay",
        VIEW => "all-views.tour.message.single-view",
    ];

    //amdin
    const ADMININBOX = [
        URL => "inbox",
        VIEW => "all-views.tour.message.admin-inbox",
    ];
}
