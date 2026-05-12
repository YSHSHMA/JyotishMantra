<?php

namespace App\Enums\ViewPaths\Admin;

enum Calculator
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.calculator.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.calculator.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.calculator.edit'
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
