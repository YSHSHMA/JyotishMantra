<?php
namespace App\Enums\ViewPaths\Admin;

enum TempleCategoryEnum{
    const ADD =[
        URI=>"add_category",
        VIEW=>"admin-views.temple.category.index",
        REDIRECT =>"admin.temple.category.add",
    ];
    const LIST =[
        URI => "list",
        VIEW => "admin-views.temple.category.list",
        REDIRECT =>"admin.temple.category.list",
    ];
    const UPDATE =[
        URI=>"category_update",
        VIEW =>'admin-views.temple.category.edit',
    ];
    const DELETE = [
        URI =>'',
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
}

?>