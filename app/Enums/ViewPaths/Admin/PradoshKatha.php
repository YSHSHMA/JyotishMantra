<?php

namespace App\Enums\ViewPaths\Admin;

enum PradoshKatha
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.pradoshkatha.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.pradoshkatha.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.pradoshkatha.edit'
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
