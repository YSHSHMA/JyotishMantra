<?php

namespace App\Enums\ViewPaths\Admin;


enum GalleryPath
{

    const LIST = [
        URI => "gallery_list",
        VIEW => "admin-views.temple.gallery.index",
    ];
    const ADD = [
        URI => "store",
        VIEW => '',
    ];

    const DELETE = [
        URI=>"remove-image",
    ];

    const UPDATE = [
        URI =>"update",
        VIEW=>"admin-views.temple.gallery.edit",
    ];

    const NEWADD = [
        URI=>'new-gallery',
        VIEW=>"admin-views.temple.gallery.add_new",
    ];
}
