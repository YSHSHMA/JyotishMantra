<?php

namespace App\Enums\ViewPaths\Admin;

enum EmergencyContact
{
    const LIST = [
        URI => '/',
        VIEW => 'admin-views.delivery-man.emergency-contact'
    ];

    const ADD = [
        URI => 'add',
        VIEW => ''
    ];

    const DELETE = [
        URI => 'destroy',
        VIEW => ''
    ];
    const STATUS = [
        URI => 'ajax-status-change',
        VIEW => ''
    ];

}
