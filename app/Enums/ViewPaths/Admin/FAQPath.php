<?php

namespace App\Enums\ViewPaths\Admin;

enum FAQPath
{
    const CATEGORY =[
        URI=>"category-list",
        VIEW => 'admin-views.faq.category-list'
    ];
    const CATEGORYSTATUS =[
        URI=>"category-status-update",
    ];
    const CATEGORYUPDATE =[
        URI=>"category-update",
         VIEW => 'admin-views.faq.category-edit'
    ];
    const CATEGORYDELETE =[
        URI=>"category-delete",
    ];
    const LIST = [
        URI => 'index',
        VIEW => 'admin-views.faq.index'
    ];
    const ADD = [
        URI => 'add-new',
        VIEW => 'admin-views.faq.add-new'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.faq.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const STATUS = [
        URI => 'status-update',
        VIEW => ''
    ];

    const EXPORT = [
        URI => 'export',
        VIEW => ''
    ];
}