<?php
namespace App\Enums\ViewPaths\Admin;

enum RestaurantsPath{
    const ADD =[
        URI=>"add_restaurant",
        VIEW=>"admin-views.temple.restaurant.index",
        REDIRECT=>'admin.temple.restaurants.add',
    ];
    const LIST =[
        URI => "list",
        VIEW => "admin-views.temple.restaurant.list",
        REDIRECT=>'admin.temple.restaurants.list',
    ];
    const UPDATE =[
        URI=>"restaurant_update",
        VIEW =>'admin-views.temple.restaurant.edit',
        REDIRECT=>'admin.temple.restaurants.update',
    ];
    const DELETE = [
        URI =>'remove',
    ];
    const GALLERY =[
        URI =>'gallery_list',
        VIEW =>'admin-views.temple.restaurant.gallery',
        URL=>"remove-image",
        REDIRECT=>'admin.temple.restaurants.gallery',
    ];

    const DELETE_IMAGE= [
        URI=>"",
    ];
    const STATUS = [
        URI=>'status-update',

    ];
   
    const GET_CITIES=[
        URI =>"get-cities",
    ];

    const REVIEW = [
        URI =>"review",
        VIEW => "admin-views.temple.restaurant.review_list",
        URL => 'review-delete',
        SAVE => "review-status",
        REDIRECT => 'admin.temple.restaurants.review',
    ];
}

?>