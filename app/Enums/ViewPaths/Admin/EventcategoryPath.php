<?php

namespace App\Enums\ViewPaths\Admin;

enum EventcategoryPath
{
    const ADD = [
        URI => 'add',
        VIEW => 'admin-views.events.category.index',
        REDIRECT=>'admin.event-managment.category.add',
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.events.category.edit'
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
