<?php

namespace App\Enums\ViewPaths\Admin;

enum CalendarHindiMonth
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.calendarhindimonth.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.calendarhindimonth.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.calendarhindimonth.edit'
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
}
