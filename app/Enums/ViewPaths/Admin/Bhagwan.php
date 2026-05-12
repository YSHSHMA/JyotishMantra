<?php

namespace App\Enums\ViewPaths\Admin;

enum Bhagwan
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.bhagwan.list'
    ];

    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.bhagwan.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.bhagwan.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const DELETE_IMAGE = [
        URI => 'delete-image',
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
    const BHAGWANLOGS = [
        URI => 'bhagwan-logs-list',
        VIEW => 'admin-views.bhagwan.bhagwan-logs'
    ];
}