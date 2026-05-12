<?php

namespace App\Enums\ViewPaths\Admin;

enum HotelsEnums
{
    const ADD = [
        URI => 'add-hotel',
        VIEW => 'admin-views.temple.hotel.add',
    ];

    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.temple.hotel.list',
        REDIRECT=>"admin.temple.hotel.list",
    ];
    const STATUS = [
        URI => 'status',
        VIEW => '',
    ];

    const GALLERY=[
        URI => 'gallery',
        VIEW =>"admin-views.temple.hotel.gallery",
        URL => "deleteImage",
        REDIRECT => 'admin.temple.hotel.gallery',
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.temple.hotel.edit',
        EDIT => "edit",
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => '',
    ];

    const REVIEW = [
        URI =>"review",
        VIEW => "admin-views.temple.hotel.review_list",
        URL => 'review-delete',
        SAVE => "review-status",
        REDIRECT => 'admin.temple.hotel.review',
    ];
}
