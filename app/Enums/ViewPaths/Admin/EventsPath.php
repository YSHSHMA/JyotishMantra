<?php

namespace App\Enums\ViewPaths\Admin;

enum EventsPath
{
    const ADD = [
        URI => 'add',
        VIEW => 'admin-views.events.event.index',
        REDIRECT => 'admin.event-managment.event.add',
    ];

    const LIST = [
        URI => "list",
        VIEW => 'admin-views.events.event.list',
        REDIRECT => 'admin.event-managment.event.list',
    ];
    const LIST1 = [
        URI => "pending",
        URL => "booking",
        VIEW => 'admin-views.events.event.list1',
    ];
    const LIST2 = [
        URI => "upcomming",
        URL => "canceled",
        VIEW => 'admin-views.events.event.list1',
    ];
    const LIST3 = [
        URI => "completed",
        VIEW => 'admin-views.events.event.list1',
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.events.event.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const STATUS = [
        URI => 'status-update',
        VIEW => '',
        URL => 'varification-event',
    ];

    const VIEW = [
        URI => 'event-information',
        VIEW => 'admin-views.events.event.information'
    ];

    const ARTIST = [
        URI => 'artist',
        VIEW => "admin-views.events.event.artist",
        REDIRECT => 'admin.event-managment.event.artist',
    ];
    const ARTIST_UPDATE = [
        URI => 'artist-update',
        VIEW => "admin-views.events.event.artist_edit",
        REDIRECT => 'admin.event-managment.event.artist_edit',
        URL => "artist-status-change",
    ];
    const ARTIST_DELETE = [
        URI => 'artist-delete',
    ];
    const INFORMATION = [
        URI => "admin.event-managment.event.information",
    ];

    //
    const OVERALL = [
        URI => 'event-detail-overview',
        URL => 'event-overview',
        VIEW => "admin-views.events.event.events_details",
        REDIRECT => 'admin.event-managment.event.event-detail-overview',
    ];
    // 
    const ORDER = [
        URL => "event-order-view",
    ];
    //
    const COMMISSION = [
        URI => "event-commission",
        URL => "event-amount-calculation",
    ];
    const COMM_STATUS = [
        URI => "event-comment-status-change",
    ];

    const REJECT = [
        URI => "event-reject",
    ];
    const PAYREQ = [
        URI => "request-approve-amount",
        REDIRECT => 'admin.event-managment.event.event-overview',
    ];
    const LEADS = [
        URI => "list",
        VIEW => "admin-views.events.event.leads",
        URL => "lead-delete",
    ];
    const LEADSFOLLOW =[
        URI =>"event-follow-list",
        URL =>"event-follow-up",
    ];

    const BookingLIST =[
        URI =>"list",
        VIEW =>"admin-views.events.booking.list",
    ];
}
