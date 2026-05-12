<?php

namespace App\Enums\ViewPaths\Vendor;

class Refund
{
    const INDEX =[
        URI => 'index',
        VIEW => 'vendor-views.refund.index',
    ];
    const DETAILS =[
        URI => 'details',
        VIEW => 'vendor-views.refund.details'
    ];
    const UPDATE_STATUS =[
        URI => 'update-status',
        VIEW => ''
    ];
}
