<?php

namespace App\Enums\ViewPaths\Admin;

enum ChadhavaPath
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.service.chadhava.index'
    ];

    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.service.chadhava.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.service.chadhava.edit'
    ];

    const VIEW = [
        URI => 'view',
        VIEW => 'admin-views.service.chadhava.view',
        ROUTE => 'admin.service.chadhava.view'
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
    const DELETE_IMAGE = [
        URI => 'delete-image',
        VIEW => ''
    ];
    const GET_CATEGORIES = [
        URI => 'get-categories',
        VIEW => ''
    ];
     const DENY = [
        URI => 'deny',
        VIEW => ''
    ];
    const APPROVE_STATUS = [
        URI => 'approve-status',
        VIEW => ''
    ];
    
}
