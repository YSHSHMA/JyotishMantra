<?php

namespace App\Enums\ViewPaths\Admin;

enum Jaap
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.jaap.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.jaap.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.jaap.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

     const STATUS = [
        URI => 'status-update',
        VIEW => ''
    ];
    const JAAPUSER = [
        URI => 'jaap-user-list',
        VIEW => 'admin-views.jaap.jaapuser'
    ];
}
