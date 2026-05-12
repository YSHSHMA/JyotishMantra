<?php

namespace App\Enums\ViewPaths\Admin;

enum Rashi
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.rashi.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.rashi.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.rashi.edit'
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
