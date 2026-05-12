<?php

namespace App\Enums\ViewPaths\Admin;

enum FestivalHindiMonth
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.festivalhindimonth.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.festivalhindimonth.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.festivalhindimonth.edit'
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
