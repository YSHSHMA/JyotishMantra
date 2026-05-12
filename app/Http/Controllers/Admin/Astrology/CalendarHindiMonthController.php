<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CalendarDay;
use App\Utils\Helpers as UtilsHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class CalendarHindiMonthController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['searchValue']);
        $calendarhindimonths=CalendarDay::where(['status'=>1, 'type'=>'hindi-month'])->when(isset($key) , function ($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
        ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($kathas);
        return view('admin-views.calendarhindimonth.list',compact('calendarhindimonths'));
    }

    public function getAddView()
    {
        $calendarhindimonth = CalendarDay::all()->where('type','hindi-month');
        return view('admin-views.calendarhindimonth.add-new',compact('calendarhindimonth'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'hindimonth_title' => 'required',
            'date_month_year' => 'required',
        ], [
            'hindimonth_title' => translate('Calendar Hindi Month Title (Hindi) is Required'),
            'date_month_year'=>translate('Calendar Day Date (English) is Required'),
        ]);

        $arryTostring = implode('-', $request->input('date_month_year'));
        // echo $arryTostring;
        // die();

        $calendarhindimonth = new CalendarDay;
        $calendarhindimonth->title = $request->hindimonth_title;
        $calendarhindimonth->type = 'hindi-month';
        $calendarhindimonth->date = $arryTostring.'-1';
        if($calendarhindimonth->save()){
            Toastr::success(translate('Calendar Hindi Month Added Successfully'));
            UtilsHelpers::editDeleteLogs('Calendar Hindi Month','Calendar Hindi Month','Insert');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();

    }


    public function edit(string $id)
    {
        $calendarhindimonth = CalendarDay::findOrFail($id);
        return view('admin-views.services.calendarhindimonth.edit',compact('calendarhindimonth'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'hindimonth_title' => 'required',
            'date_month_year' => 'required',
        ], [
            'hindimonth_title' => translate('Calendar Hindi Month (Hindi) is Required'),
            'date_month_year'=>translate('Calendar Hindi Month (English) is Required'),
        ]);

        $calendarhindimonth = CalendarDay::find($id);
        $arryTostring = implode('-', $request->input('date_month_year'));
        // echo $arryTostring;
        // die();

        $calendarhindimonth->title = $request->hindimonth_title;
        $calendarhindimonth->date = $arryTostring.'-1';
        if($calendarhindimonth->save()){
            UtilsHelpers::editDeleteLogs('Calendar Hindi Month','Calendar Hindi Month','Update');
            Toastr::success(translate('Calendar Hindi Month Updated Successfully'));
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }

    public function destroy($id)
    {
        $calendarhindimonth = CalendarDay::findOrFail($id);
        if ($calendarhindimonth){
            $calendarhindimonth->delete();
            UtilsHelpers::editDeleteLogs('Calendar Hindi Month','Calendar Hindi Month','Delete');
            Toastr::success('Calendar Hindi Month removed!');
        }else{
            Toastr::warning(translate('Something went wrong'));
        }
        return back();
    }
}
