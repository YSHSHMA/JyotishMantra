<?php

namespace App\Enums\ViewPaths\Admin;

enum Sangeet
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.sangeet.sangeet.list'
    ];
    
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.sangeet.sangeet.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.sangeet.sangeet.edit'
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
