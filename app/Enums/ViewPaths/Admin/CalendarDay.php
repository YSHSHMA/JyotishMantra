<?php

namespace App\Enums\ViewPaths\Admin;

enum CalendarDay
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.calendarday.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.calendarday.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.calendarday.edit'
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
