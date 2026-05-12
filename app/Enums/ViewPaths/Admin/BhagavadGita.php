<?php

namespace App\Enums\ViewPaths\Admin;

enum BhagavadGita
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.sahitya.bhagavadgita.list'
    ];
    
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.sahitya.bhagavadgita.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.sahitya.bhagavadgita.edit'
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
