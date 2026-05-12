<?php

namespace App\Enums\ViewPaths\Admin;

enum CitiesVisits
{
    const LIST = [
        URI => 'list',
        VIEW => "admin-views.cities_visit.list",
    ];

    CONST STORE = [
        URI => "store",
        VIEW =>"",
    ];
    const INDEX = [
        URI => "edit",
        VIEW => "admin-views.cities_visit.edit",
    ];
    const UPDATE = [
        URI =>"update",
        VIEW=>"admin-views.cities_visit.update",
        EDIT=>"edit_cities_visit",
    ];
    const DELETE = [
        URI=>"delete",
    ];
}
