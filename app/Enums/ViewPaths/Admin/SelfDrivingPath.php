<?php

namespace App\Enums\ViewPaths\Admin;

enum SelfDrivingPath
{
    //policy
    const SELFDRIVINGADDPOLICY = [
        URI => "driving-policy",
        VIEW => "admin-views.self-driving-cab.policy.list",
        REDIRECT => "admin.driving-policy.driving-policy",
    ];
    const SELFDRIVINGPOLICYFILTER = [
        URI => "driving-policy-filter",
    ];
    const SELFDRIVINGPOLICYSTATUSUPDATE = [
        URI => "status-update",
    ];
    const SELFDRIVINGPOLICYDELETE = [
        URI => 'policy-delete',
    ];
    const SELFDRIVINGPOLICYDUPDATE = [
        URI => "policy-edit",
        URL => "policy-update",
        VIEW => "admin-views.self-driving-cab.policy.edit",
    ];

    //cancellation-policy
    const CANCELLATIONPOLICY = [
        URI => "cancellation-policy",
        VIEW => "admin-views.self-driving-cab.cancellation-policy.list",
        REDIRECT => "admin.driving-cancellation-policy.driving-cancellation-policy",
    ];

    const CANCELLATIONPOLICYFILTER = [
        URI => "cancellation-policy-filter",
    ];
    const CANCELLATIONPOLICYSTATUSUPDATE = [
        URI => 'status-update',
    ];
    const CANCELLATIONPOLICYDELETE = [
        URI => "cancellation-delete",
    ];

    const CANCELLATIONPOLICYUPDATE = [
        URI => "cancellation-edit",
        URL => "cancellation-update",
        VIEW => "admin-views.self-driving-cab.cancellation-policy.edit",
    ];

    const SELFDRIVINGADD = [
        URI => "self-driving-add",
        URL => "self-driving-store",
        VIEW => "admin-views.self-driving-cab.cab-setting.add",
    ];

    const SELFDRIVINGLIST = [
        URI => "self-driving-list",
        VIEW => "admin-views.self-driving-cab.cab-setting.list",
        REDIRECT => "admin.self-driving-management.self-driving-list",
    ];
    const SELFDRIVINGLISTFILTER = [
        URI => "self-driving-filter"
    ];
    const SELFDRIVINGSTATUSUPDATE = [
        URI => "status-update",
    ];
    const SELFDRIVINGDELETE = [
        URI => "delete",
    ];

    const SELFDRIVINGUPDATE = [
        URI => "self-driving-edit",
        URL => "self-driving-update",
        VIEW => "admin-views.self-driving-cab.cab-setting.edit",
    ];

    const GETCABS = [
        URI => "get-cab-list",
    ];
    const SELFVEHICLELEAD = [
        URI => "self-driving-lead",
        URL => "self-driving-lead-filter",
        VIEW => "admin-views.self-driving-cab.lead.list",
    ];
    const SELFVEHICLELEADDELETE = [
        URI => "self-driving-lead-delete",
    ];
    const SELFVEHICLELEFOLLOWUP = [
        URI => "self-vehicle-follow-up",
        URL => "self-vehicle-get-follow-up",
    ];

    const SELFVEHICLELEORDERPENDING = [
        URI => "self-vehicle-pending-order",
        URL => "self-vehicle-pending-filter",
        VIEW => "admin-views.self-driving-cab.order.pending",
    ];
    const SELFVEHICLELEORDERVIEW = [
        URI => "self-vehicle-order-view",
        VIEW => "admin-views.self-driving-cab.order.details",
    ];
    const SELFVEHICLELEORDERCONFIRM = [
        URI => "self-vehicle-confirm-order",
        URL => "self-vehicle-confirm-filter",
        VIEW => "admin-views.self-driving-cab.order.confirm",
    ];
    const SELFVEHICLELEORDERPICKUP = [
        URI => "self-vehicle-pickup-order",
        URL => "self-vehicle-pickup-filter",
        VIEW => "admin-views.self-driving-cab.order.pickup",
    ];
    const SELFVEHICLELEORDERDROP = [
        URI => "self-vehicle-drop-order",
        URL => "self-vehicle-drop-filter",
        VIEW => "admin-views.self-driving-cab.order.drop",
    ];
}
