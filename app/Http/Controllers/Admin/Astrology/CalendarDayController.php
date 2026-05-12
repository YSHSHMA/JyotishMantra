<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CalendarDay;
use App\Utils\Helpers as UtilsHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class CalendarDayController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['searchValue']);
        $calendardays=CalendarDay::where(['status'=>1, 'type'=>'day'])->when(isset($key) , function ($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
        ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($calendardays);
        return view('admin-views.calendarday.list',compact('calendardays'));
    }

    public function getAddView()
    {
        $calendarday = CalendarDay::all()->where('type','nakshatra');
        return view('admin-views.calendarday.add-new',compact('calendarday'));
    }


    public function add(Request $request)
    {
        // $e = $request->all();
        // print_r($e);
        // die();
        $request->validate([
            'day_title' => 'required',
            'date' => 'required',
        ], [
            'day_title' => translate('Calendar Day Title (Hindi) is Required'),
            'date'=>translate('Calendar Day Date (English) is Required'),
        ]);

        $calendarday = new CalendarDay;
        $calendarday->title = $request->day_title;
        $calendarday->type = 'day';
        $calendarday->date = $request->date;
        if($calendarday->save()){
            Toastr::success(translate('calendarday Added Successfully'));
            UtilsHelpers::editDeleteLogs('Calendar Day','Calendar Day','Insert');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();

    }


    public function edit(string $id)
    {
        $calendarday = CalendarDay::findOrFail($id);
        return view('admin-views.services.calendarday.edit',compact('calendarday'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'day_title' => 'required',
            'date' => 'required',
        ], [
            'day_title' => translate('calendarday (Hindi) is Required'),
            'date'=>translate('calendarday (English) is Required'),
        ]);

        $calendarday = CalendarDay::find($id);
        $calendarday->title = $request->day_title;
        $calendarday->date = $request->date;
        if($calendarday->save()){
            UtilsHelpers::editDeleteLogs('Calendar Day','Calendar Day','Update');
            Toastr::success(translate('Calendar Day Updated Successfully'));
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }

    public function destroy($id)
    {
        $calendarday = CalendarDay::findOrFail($id);
        if ($calendarday){
            $calendarday->delete();
            UtilsHelpers::editDeleteLogs('Calendar Day','Calendar Day','Delete');
            Toastr::success('Calendar Day removed!');
        }else{
            Toastr::warning(translate('Something went wrong'));
        }
        return back();
    }
}
