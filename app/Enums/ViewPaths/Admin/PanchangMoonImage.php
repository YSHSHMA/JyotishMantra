<?php

namespace App\Enums\ViewPaths\Admin;

enum PanchangMoonImage
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.panchangmoonimage.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.panchangmoonimage.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.panchangmoonimage.edit'
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
