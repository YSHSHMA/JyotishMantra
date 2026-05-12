<?php

namespace App\Enums\ViewPaths\Admin;

enum VideoListType
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.video.videolisttype.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.video.videolisttype.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.video.videolisttype.edit'
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
