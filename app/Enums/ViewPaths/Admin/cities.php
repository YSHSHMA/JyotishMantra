<?php 
namespace App\Enums\ViewPaths\Admin;

enum cities{
    const LIST =[
        URI => "view",
        VIEW => "admin-views.cities.index",
    ];

    const INDEX = [
        URI =>"list",
        VIEW =>"admin-views.cities.list",
    ];

    const EDIT =[
        URI =>"update",
        VIEW => "admin-views.cities.edit",
        SAVE => "store",
    ];

    const GALLERY  =[
        URI =>"gallery",
        VIEW => "admin-views.cities.gallery",
        SAVE => "gallery_store",
        URL =>"remove-gallery",
        REDIRECT=>"admin.cities.gallery",
    ];

    const REVIEW = [
        URI =>"review",
        VIEW => "admin-views.cities.review_list",
        URL => 'review-delete',
        REDIRECT=>'admin.cities.review',
        SAVE=>"review-status",
    ];
}
?>