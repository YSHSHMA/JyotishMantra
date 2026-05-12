<?php

namespace App\Enums\ViewPaths\Admin;

enum CitiesPath
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.temple.cities.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.temple.cities.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.temple.cities.edit'
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
    // Visite Routes
    const VISIT_LIST = [
        URI => 'list',
        VIEW => 'admin-views.temple.cities.visit'
    ];
    const VISIT_ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.temple.cities.add-new'
    ];

    const VISIT_UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.temple.cities.visit-edit'
    ];

    const VISIT_DELETE = [
        URI => 'delete',
        VIEW => ''
    ];
}
