<?php

namespace App\Enums\ViewPaths\Admin;

enum MasikRashi
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.masikrashi.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.masikrashi.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.masikrashi.edit'
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
