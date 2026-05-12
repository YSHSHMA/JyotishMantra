<?php

namespace App\Enums\ViewPaths\Admin;

enum VendorPermissionPath
{

    const MODULE = [
        URI => 'module',
        VIEW => 'admin-views.vendor-permissions.add-module'
    ];

    const ROLE = [
        URI => 'role',
        VIEW => 'admin-views.vendor-permissions.role-permission'
    ];
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.vendor-permissions.role-list-permission'
    ];
    const ROLESTATUS = [
        URI => 'vendor-role-status',
    ];
    const ROLEDELETE = [
        URI => 'role-delete',
    ];
    const ROLEUPDATE = [
        URI => 'role-update',
        VIEW => 'admin-views.vendor-permissions.role-update'
    ];

    const USERLIST = [
        URI => 'user-list',
        VIEW => 'admin-views.vendor-permissions.user-list'
    ];
    const USERSTATUS = [
        URI => 'user-status-update',
        URL =>"user-delete",
    ];
}
