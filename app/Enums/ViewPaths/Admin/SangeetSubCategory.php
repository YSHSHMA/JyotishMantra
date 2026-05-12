<?php

namespace App\Enums\ViewPaths\Admin;

enum SangeetSubCategory
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.sangeet.sangeetsubcategory.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.sangeet.sangeetsubcategory.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.sangeet.sangeetsubcategory.edit'
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
