<?php

namespace App\Enums\ViewPaths\Admin;

enum DonateCategoryPath
{
    const ADDCATEGORY =[
        URI =>"add-category",
        VIEW =>"admin-views.donate_management.add-category",
        REDIRECT =>"admin.donate_management.category.add",
    ];
    const ADDCATESTATUS = [
        URI =>"add-category-status",
    ];
    const ADDCATEDELETE = [
        URI =>"category-delete",
    ];

    const ADDCATEUPDATE =[
        URI => 'edit-category',
        VIEW => "admin-views.donate_management.edit-category",
    ];

    const ADDPURPOSE =[
        URI =>"add-purpose",
        VIEW =>"admin-views.donate_management.purpose.add-purpose",
        REDIRECT =>"admin.donate_management.purpose.add",
    ];

    const ADDPURSTATUS = [
        URI =>"purpose-status",
    ];
    const ADDPURDELETE = [
        URI =>"purpose-delete",
    ];

    const ADDPURUPDATE = [
        URI => 'edit-purpose',
        VIEW => "admin-views.donate_management.purpose.edit-purpose",
    ];

}

?>