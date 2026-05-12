<?php

namespace App\Http\Controllers\Admin\Email;

use App\Http\Controllers\Controller;
use App\Traits\PdfGenerator;
use App\Models\EmailSetup;
use App\Models\EmailTemplates;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\View as PdfView;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\View;

class EmailController extends Controller
{
    use PdfGenerator;

    public function email_template(Request $request)
    {
        $setemail = EmailSetup::where('status', 1)->get();
        return view('admin-views.email.email-setup', compact('setemail'));
    }
    public function update_email_template(Request $request)
    {
        // dd($request->all());
        $updateEmail = [
            "mailername" => $request->mailername,
            "driver" => $request->driver,
            "username" => $request->username,
            "encryption" => $request->encryption,
            "host" => $request->host,
            "port" => $request->port,
            "emailid" => $request->emailid,
            "password" => $request->password,
        ];
        // dd($updateEmail);
        EmailSetup::where('id', $request->id)->update($updateEmail);
        Toastr::success(translate('Email Template Successfully Updated'));
        return back();
    }

    public function CreateTemplate(Request $request)
    {
        return view('admin-views.email.comman-template.add');
    }

    public function SaveEmailTemplate(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'template' => 'required',
        ]);
        $getOlddata = EmailTemplates::where('slug', str_replace(' ', '_', strtolower($request['type'])))->first();
        if (!$getOlddata) {
            $save = new EmailTemplates();
            $save->type = $request['type'];
            $save->slug = str_replace(' ', '_', strtolower($request['type']));
            $save->html = $request['template'];
            $save->status = 1;
            $save->save();
        }
        Toastr::success('Email ' . $request['type'] . ' Template Successfully Updated');
        return back();
    }

    public function EmailTemplateList(Request $request)
    {
        $getData =  EmailTemplates::paginate(10);
        return view('admin-views.email.comman-template.list', compact('getData'));
    }

    public function EmailTemplateUpdate(Request $request)
    {
        $getData = EmailTemplates::where('id', $request->id)->first();
        return view('admin-views.email.comman-template.update', compact('getData'));
    }

    public function sendEmailTesting(Request $request)
    {
        $getOlddata = EmailTemplates::where('id', $request->id)->first();
        \App\Utils\Helpers::TemplateTextEmail(['subject' => $getOlddata['type'], 'htmlContent' => $getOlddata['html']]);
    }

    public function EmailTemplateEdit(Request $request)
    {
        $request->validate([
            "id" => "required",
            'type' => 'required',
            'template' => 'required',
        ]);
        $getOlddata = EmailTemplates::where('id', '!=', $request->id)->where('slug', str_replace(' ', '_', strtolower($request['type'])))->first();
        if (!$getOlddata) {
            $save = EmailTemplates::find($request->id);
            $save->type = $request['type'];
            $save->slug = str_replace(' ', '_', strtolower($request['type']));
            $save->html = $request['template'];
            $save->status = 1;
            $save->save();
        }
        Toastr::success('Email ' . $request['type'] . ' Template Successfully Updated');
        return back();
    }
}