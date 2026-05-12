<?php

namespace App\Enums\ViewPaths\Admin;

enum CalendarVikramSamvat
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.calendarvikramsamvat.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.calendarvikramsamvat.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.calendarvikramsamvat.edit'
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
