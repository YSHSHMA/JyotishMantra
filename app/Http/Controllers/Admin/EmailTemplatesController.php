<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EmailTemplatesController extends BaseController
{

    public function index(Request|null $request, string $type = null): View
    {
        return view('admin-views.business-settings.email-template.admin-mail-template.seller-registration-mail-template');
    }
}
