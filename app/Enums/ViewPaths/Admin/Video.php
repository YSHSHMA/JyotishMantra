<?php

namespace App\Enums\ViewPaths\Admin;

enum Video
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.video.video.list'
    ];
    
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.video.video.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.video.video.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const STATUS = [
        URI => 'status-update',
        VIEW => ''
    ];

    const EXPORT = [
        URI => 'export',
        VIEW => ''
    ];
}
