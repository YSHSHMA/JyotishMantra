<?php

namespace App\Enums\ViewPaths\Admin;

enum VarshikRashi
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.varshikrashi.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.varshikrashi.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.varshikrashi.edit'
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
