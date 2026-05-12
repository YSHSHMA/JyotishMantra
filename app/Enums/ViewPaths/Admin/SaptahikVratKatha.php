<?php

namespace App\Enums\ViewPaths\Admin;

enum SaptahikVratKatha
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.saptahikvratkatha.list'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.saptahikvratkatha.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.saptahikvratkatha.edit'
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
