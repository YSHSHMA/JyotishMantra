<?php

namespace App\Enums\ViewPaths\Admin;

enum EventOrganizerPath
{
    const ADD = [
        URI => 'add',
        VIEW => 'admin-views.events.organizers.index',
        REDIRECT=>'admin.event-managment.organizers.add',
    ];

    const LIST =[
        URI => 'list',
        VIEW => 'admin-views.events.organizers.list',
        REDIRECT=>'admin.event-managment.organizers.list',
    ];
    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.events.organizers.edit',        
    ];
    const VIEW = [
        URI =>'view-information',
        VIEW => 'admin-views.events.organizers.information',
    ];
    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const STATUS = [
        URI => 'status-update',
        VIEW => '',
        URL =>"verified",
    ];

    const EXPORT = [
        URI => 'export',
        VIEW => ''
    ];
}
