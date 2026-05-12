<?php

namespace App\Enums\ViewPaths\Admin;

enum Package
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.package.list'
    ];

    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.package.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.package.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];
}