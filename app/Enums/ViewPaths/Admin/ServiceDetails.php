<?php

namespace App\Enums\ViewPaths\Admin;

enum ServiceDetails
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.service.index'
    ];

    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.service.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.service.edit'
    ];

    const VIEW = [
        URI => 'views',
        VIEW => 'admin-views.service.view',
        ROUTE => 'admin.service.views'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const STATUS = [
        URI => 'status-update',
        VIEW => ''
    ];
    const EXPORT = [
        URI => 'export',
        VIEW => ''
    ];
    const DELETE_IMAGE = [
        URI => 'delete-image',
        VIEW => ''
    ];
    const GET_CATEGORIES = [
        URI => 'get-categories',
        VIEW => ''
    ];
     const DENY = [
        URI => 'deny',
        VIEW => ''
    ];
    const APPROVE_STATUS = [
        URI => 'approve-status',
        VIEW => ''
    ];
    // Pooja shadule
    const SCHEDULE = [
        URI => 'schedule',
        VIEW => 'admin-views.service.schedule'
    ];
    const EVENTUPDATE = [
        URI => 'eventUpdate',
        VIEW => 'admin-views.service.schedule'
    ];
    const EVENTUPDATETIME = [
        URI => 'get',
        VIEW => ''
    ];
    // Counslling
    const COUNSELLING_LIST = [
        URI => 'counselling-list',
        VIEW => 'admin-views.service.counselling.index'
    ];
    const COUNSELLING_ADD = [
        URI => 'counselling-add-new',
        VIEW => 'admin-views.service.counselling.add-new'
    ];
    const COUNSELLING_UPDATE = [
        URI => 'counselling-update',
        VIEW => 'admin-views.service.counselling.edit'
    ];
    const COUNSELLING_DELETE = [
        URI => 'counselling-delete',
        VIEW => ''
    ];
    const COUNSELLING_DELETE_IMAGE = [
        URI => 'delete-image',
        VIEW => ''
    ];
    const COUNSELLING_STATUS = [
        URI => 'counselling-status-update',
        VIEW => ''
    ];
    
    // VIP ROUTES
    const VIP_LIST = [
        URI => 'VIP-list',
        VIEW => 'admin-views.service.vip.index'
    ];
    const VIP_ADD = [
        URI => 'VIP-add-new',
        VIEW => 'admin-views.service.vip.add-new'
    ];
    const VIP_UPDATE = [
        URI => 'VIP-update',
        VIEW => 'admin-views.service.vip.edit'
    ];
    const VIP_DELETE = [
        URI => 'VIP-delete',
        VIEW => ''
    ];
    const VIP_DELETE_IMAGE = [
        URI => 'delete-image',
        VIEW => ''
    ];
    const VIP_STATUS = [
        URI => 'vip-status-update',
        VIEW => ''
    ];
    const VIP_VIEW = [
        URI => 'view',
        VIEW => 'admin-views.service.vip.view',
        ROUTE => 'admin.service.vip.view'
    ];
    const VIP_APPROVE_STATUS=[
        URI => 'approve-status',
        VIEW => ''
    ];
    // OFFLINE POOJA ROUTES
    const ADD_OFFLINE_POOJA = [
        URI => 'offline-pooja-add-new',
        VIEW => 'admin-views.service.offline-pooja.add-new'
    ];
    const OFFLINE_POOJA_LIST = [
        URI => 'offline-pooja-list',
        VIEW => 'admin-views.service.offline-pooja.list'
    ];
    const OFFLINE_POOJA_UPDATE = [
        URI => 'offline-pooja-update',
        VIEW => 'admin-views.service.offline-pooja.edit'
    ];
    const OFFLINE_POOJA_DELETE = [
        URI => 'offline-pooja-delete',
        VIEW => ''
    ];
    const OFFLINE_POOJA_DELETE_IMAGE = [
        URI => 'offline-pooja-delete-image',
        VIEW => ''
    ];
    const OFFLINE_POOJA_STATUS = [
        URI => 'offline-pooja-status-update',
        VIEW => ''
    ];
    // offline pooja refund policy
    const ADD_OFFLINE_POOJA_REFUND_POLICY = [
        URI => 'offline-pooja-refund-policy-add-new',
        VIEW => ''
    ];
    const OFFLINE_POOJA_REFUND_POLICY_LIST = [
        URI => 'offline-pooja-refund-policy-list',
        VIEW => 'admin-views.service.offline-pooja.refund-policy.list'
    ];
    const OFFLINE_POOJA_REFUND_POLICY_UPDATE = [
        URI => 'offline-pooja-refund-policy-update',
        VIEW => 'admin-views.service.offline-pooja.refund-policy.edit'
    ];
    const OFFLINE_POOJA_REFUND_POLICY_DELETE = [
        URI => 'offline-pooja-refund-policy-delete',
        VIEW => ''
    ];
    const OFFLINE_POOJA_REFUND_POLICY_STATUS = [
        URI => 'offline-pooja-refund-policy-status-update',
        VIEW => ''
    ];

    // offline pooja schedule
    const ADD_OFFLINE_POOJA_SCHEDULE = [
        URI => 'offline-pooja-schedule-add-new',
        VIEW => ''
    ];
    const OFFLINE_POOJA_SCHEDULE_LIST = [
        URI => 'offline-pooja-schedule-list',
        VIEW => 'admin-views.service.offline-pooja.schedule.list'
    ];
    const OFFLINE_POOJA_SCHEDULE_UPDATE = [
        URI => 'offline-pooja-schedule-update',
        VIEW => 'admin-views.service.offline-pooja.schedule.edit'
    ];
    const OFFLINE_POOJA_SCHEDULE_DELETE = [
        URI => 'offline-pooja-schedule-delete',
        VIEW => ''
    ];
    const OFFLINE_POOJA_SCHEDULE_STATUS = [
        URI => 'offline-pooja-schedule-status-update',
        VIEW => ''
    ];
    // offline pooja category
    const ADD_OFFLINE_POOJA_CATEGORY = [
        URI => 'offline-pooja-category-add-new',
        VIEW => ''
    ];
    const OFFLINE_POOJA_CATEGORY_LIST = [
        URI => 'offline-pooja-category-list',
        VIEW => 'admin-views.service.offline-pooja.category.list'
    ];
    const OFFLINE_POOJA_CATEGORY_UPDATE = [
        URI => 'offline-pooja-category-update',
        VIEW => 'admin-views.service.offline-pooja.category.edit'
    ];
    const OFFLINE_POOJA_CATEGORY_STATUS = [
        URI => 'offline-pooja-category-status-update',
        VIEW => ''
    ];

    // city
    const ADD_OFFLINE_POOJA_CITY = [
        URI => 'offline-pooja-city-add-new',
        VIEW => ''
    ];
    const OFFLINE_POOJA_CITY_LIST = [
        URI => 'offline-pooja-city-list',
        VIEW => 'admin-views.service.offline-pooja.city.list'
    ];
    const OFFLINE_POOJA_CITY_UPDATE = [
        URI => 'offline-pooja-city-update',
        VIEW => 'admin-views.service.offline-pooja.city.edit'
    ];
    const OFFLINE_POOJA_CITY_STATUS = [
        URI => 'offline-pooja-city-status-update',
        VIEW => ''
    ];
}