<?php

namespace App\Enums\ViewPaths\Admin;

enum SangeetLanguage
{
    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.sangeet.sangeetlanguage.view'
    ];

    const STORE = [
        URI => 'store',
        VIEW => 'admin-views.sangeet.sangeetlanguage.view'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.sangeet.sangeetlanguage.edit'
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
