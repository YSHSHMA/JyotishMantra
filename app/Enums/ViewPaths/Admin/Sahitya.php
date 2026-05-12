<?php

namespace App\Enums\ViewPaths\Admin;

enum Sahitya
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.sahitya.sahitya.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.sahitya.sahitya.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.sahitya.sahitya.edit'
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
