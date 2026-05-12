<?php

namespace App\Enums\ViewPaths\Admin;

enum FestivalAdd
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.festivaladd.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.festivaladd.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.festivaladd.edit'
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
