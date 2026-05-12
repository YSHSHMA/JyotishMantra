<?php

namespace App\Enums\ViewPaths\Admin;

enum FastFestival
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.fastfestival.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.fastfestival.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.fastfestival.edit'
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
