<?php

namespace App\Enums\ViewPaths\Admin;

enum Festival
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.festival.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.festival.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.festival.edit'
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
