<?php

namespace App\Enums\ViewPaths\Admin;

enum AppSection
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.appsection.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.appsection.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.appsection.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

     const STATUS = [
        URI => 'status-update',
        VIEW => ''
    ];
}
