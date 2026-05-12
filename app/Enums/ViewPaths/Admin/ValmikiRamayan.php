<?php

namespace App\Enums\ViewPaths\Admin;

enum ValmikiRamayan
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.sahitya.valmikiramayan.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.sahitya.valmikiramayan.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.sahitya.valmikiramayan.edit'
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
