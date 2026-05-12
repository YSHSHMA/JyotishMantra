<?php

namespace App\Enums\ViewPaths\Admin;

enum VideoCategory
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.video.videocategory.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.video.videocategory.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.video.videocategory.edit'
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
