<?php

namespace App\Enums\ViewPaths\Admin;

enum CalendarNakshatra
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.calendarnakshatra.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.calendarnakshatra.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.calendarnakshatra.edit'
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
