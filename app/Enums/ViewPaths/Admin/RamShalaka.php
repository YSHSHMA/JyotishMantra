<?php

namespace App\Enums\ViewPaths\Admin;

enum RamShalaka
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.sahitya.ramshalaka.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.sahitya.ramshalaka.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.sahitya.ramshalaka.edit'
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
