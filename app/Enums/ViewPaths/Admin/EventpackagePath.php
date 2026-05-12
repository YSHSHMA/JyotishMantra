<?php

namespace App\Enums\ViewPaths\Admin;

enum EventpackagePath
{
    const ADD = [
        URI => 'add',
        VIEW => 'admin-views.events.package.index',
        REDIRECT=>'admin.event-managment.event_package.add',
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.events.package.edit'
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
