<?php

namespace App\Enums\ViewPaths\Admin;

enum VideoSubCategory
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.video.videosubcategory.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.video.videosubcategory.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.video.videosubcategory.edit'
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
