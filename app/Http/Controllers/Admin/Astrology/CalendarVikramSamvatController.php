<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CalendarDay;
use App\Utils\Helpers as UtilsHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class CalendarVikramSamvatController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['searchValue']);
        $calendarvikramsamvats=CalendarDay::where(['status'=>1, 'type'=>'vikram-samvat'])->when(isset($key) , function ($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
        ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($kathas);
        return view('admin-views.calendarvikramsamvat.list',compact('calendarvikramsamvats'));
    }

    public function getAddView()
    {
        $calendarvikramsamvat = CalendarDay::all()->where('type','vikram-samvat');
        return view('admin-views.calendarvikramsamvat.add-new',compact('calendarvikramsamvat'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'vikram_samvat_title' => 'required',
            'date_month_year' => 'required',
        ], [
            'vikram_samvat_title' => translate('Calendar Vikram Samvat Title (Hindi) is Required'),
            'date_month_year'=>translate('Calendar Day Date (English) is Required'),
        ]);

        $arryTostring = implode('-', $request->input('date_month_year'));
        // echo $arryTostring;
        // die();

        $calendarvikramsamvat = new CalendarDay;
        $calendarvikramsamvat->title = $request->vikram_samvat_title;
        $calendarvikramsamvat->type = 'vikram-samvat';
        $calendarvikramsamvat->date = $arryTostring.'-1';
        if($calendarvikramsamvat->save()){
            Toastr::success(translate('Calendar Vikram Samvat Added Successfully'));
            UtilsHelpers::editDeleteLogs('Calendar Vikram Samvat','Calendar Vikram Samvat','Insert');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();

    }


    public function edit(string $id)
    {
        $calendarvikramsamvat = CalendarDay::findOrFail($id);
        return view('admin-views.services.calendarvikramsamvat.edit',compact('calendarvikramsamvat'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'vikram_samvat_title' => 'required',
            'date_month_year' => 'required',
        ], [
            'vikram_samvat_title' => translate('Calendar Vikram Samvat (Hindi) is Required'),
            'date_month_year'=>translate('Calendar Vikram Samvat (English) is Required'),
        ]);

        $calendarvikramsamvat = CalendarDay::find($id);
        $arryTostring = implode('-', $request->input('date_month_year'));
        // echo $arryTostring;
        // die();

        $calendarvikramsamvat->title = $request->vikram_samvat_title;
        $calendarvikramsamvat->date = $arryTostring.'-1';
        if($calendarvikramsamvat->save()){
            UtilsHelpers::editDeleteLogs('Calendar Vikram Samvat','Calendar Vikram Samvat','Update');
            Toastr::success(translate('Calendar Vikram Samvat Updated Successfully'));
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }

    public function destroy($id)
    {
        $calendarvikramsamvat = CalendarDay::findOrFail($id);
        if ($calendarvikramsamvat){
            $calendarvikramsamvat->delete();
            UtilsHelpers::editDeleteLogs('Calendar Vikram Samvat','Calendar Vikram Samvat','Delete');
            Toastr::success('Calendar Vikram Samvat removed!');
        }else{
            Toastr::warning(translate('Something went wrong'));
        }
        return back();
    }
}
