<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CalendarDay;
use App\Utils\Helpers as UtilsHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class CalendarNakshatraController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['searchValue']);
        $calendarnakshatras=CalendarDay::where(['status'=>1, 'type'=>'nakshatra'])->when(isset($key) , function ($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
        ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($kathas);
        return view('admin-views.calendarnakshatra.list',compact('calendarnakshatras'));
    }

    public function getAddView()
    {
        $calendarnakshatra = CalendarDay::all()->where('type','nakshatra');
        return view('admin-views.calendarnakshatra.add-new',compact('calendarnakshatra'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'nakshatra_title' => 'required',
            'date_month_year' => 'required',
        ], [
            'nakshatra_title' => translate('Calendar Nakshatra Title (Hindi) is Required'),
            'date_month_year'=>translate('Calendar Nakshatra Date (English) is Required'),
        ]);

        $arryTostring = implode('-', $request->input('date_month_year'));
        // echo $arryTostring;
        // die();

        $calendarnakshatra = new CalendarDay;
        $calendarnakshatra->title = $request->nakshatra_title;
        $calendarnakshatra->type = 'nakshatra';
        $calendarnakshatra->date = $arryTostring.'-1';
        if($calendarnakshatra->save()){
            Toastr::success(translate('Calendar Nakshatra Added Successfully'));
            UtilsHelpers::editDeleteLogs('Calendar Nakshatra','Calendar Nakshatra','Insert');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();

    }


    public function edit(string $id)
    {
        $calendarnakshatra = CalendarDay::findOrFail($id);
        return view('admin-views.services.calendarnakshatra.edit',compact('calendarnakshatra'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nakshatra_title' => 'required',
            'date_month_year' => 'required',
        ], [
            'nakshatra_title' => translate('Calendar Nakshatra (Hindi) is Required'),
            'date_month_year'=>translate('calendarnakshatra (English) is Required'),
        ]);

        $calendarnakshatra = CalendarDay::find($id);
        $arryTostring = implode('-', $request->input('date_month_year'));
        // echo $arryTostring;
        // die();

        $calendarnakshatra->title = $request->nakshatra_title;
        $calendarnakshatra->date = $arryTostring.'-1';
        if($calendarnakshatra->save()){
            UtilsHelpers::editDeleteLogs('Calendar Nakshatra','Calendar Nakshatra','Update');
            Toastr::success(translate('Calendar Nakshatra Updated Successfully'));
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }

    public function destroy($id)
    {
        $calendarnakshatra = CalendarDay::findOrFail($id);
        if ($calendarnakshatra){
            $calendarnakshatra->delete();
            UtilsHelpers::editDeleteLogs('Calendar Nakshatra','Calendar Nakshatra','Delete');
            Toastr::success('Calendar Nakshatra removed!');
        }else{
            Toastr::warning(translate('Something went wrong'));
        }
        return back();
    }
}
