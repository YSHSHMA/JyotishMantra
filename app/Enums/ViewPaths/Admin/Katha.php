<?php

namespace App\Enums\ViewPaths\Admin;

enum Katha
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.katha.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.katha.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.katha.edit'
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
