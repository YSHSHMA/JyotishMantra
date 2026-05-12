<?php

namespace App\Enums\ViewPaths\Admin;

enum TulsidasRamayan
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.sahitya.tulsidasramayan.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.sahitya.tulsidasramayan.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.sahitya.tulsidasramayan.edit'
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
