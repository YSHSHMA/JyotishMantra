<?php

namespace App\Enums\ViewPaths\Admin;

enum Dashboard
{
    const VIEW = [
        URI => '',
        VIEW => 'admin-views.system.dashboard'
    ];

    const EARNING_STATISTICS = [
        URI => 'earning-statistics',
        VIEW => ''
    ];
    const ORDER_STATISTICS = [
        URI => 'order-statistics',
        VIEW => 'admin-views.system.partials.order-statistics'
    ];
    const ORDER_STATUS = [
        URI => 'order-status',
        VIEW => ''
    ];
}
