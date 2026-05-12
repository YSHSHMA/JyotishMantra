<?php

namespace App\Enums\ViewPaths\Admin;

enum SangeetCategory
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.sangeet.sangeetcategory.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.sangeet.sangeetcategory.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.sangeet.sangeetcategory.edit'
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
