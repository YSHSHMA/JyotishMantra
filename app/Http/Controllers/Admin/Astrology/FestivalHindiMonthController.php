<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\FestivalHindiMonth;
use App\Utils\Helpers as UtilsHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class FestivalHindiMonthController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['searchValue']);
        $festivalhindimonths=FestivalHindiMonth::where(['status'=>1])->when(isset($key) , function ($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('festival_hindimonth_title', 'like', "%{$value}%");
                    }
                });
            })
        ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($festivalhindimonths);
        return view('admin-views.festivalhindimonth.list',compact('festivalhindimonths'));
    }
    public function getAddView()
    {
        $festivalhindimonth = FestivalHindiMonth::all();
        return view('admin-views.festivalhindimonth.add-new',compact('festivalhindimonth'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'festival_hindimonth_title' => 'required',
            'month' => 'required',
            'year' => 'required',
        ], [
            'festival_hindimonth_title' => translate('Festival Hindi Month (Hindi) is Required'),
            'month'=>translate('Festival Hindi Month (English) is Required'),
            'year'=>translate('Festival year (English) is Required'),
        ]);

        $festivalhindimonth = new FestivalHindiMonth;
        $festivalhindimonth->festival_hindimonth_title = $request->festival_hindimonth_title;
        $festivalhindimonth->month = $request->month;
        $festivalhindimonth->year = $request->year;
        if($festivalhindimonth->save()){
            Toastr::success(translate('Festival Hindi Month Added Successfully'));
            UtilsHelpers::editDeleteLogs('Fast Festival','Fast Festival','Insert');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();

    }


    public function edit(string $id)
    {
        $festivalhindimonth = FestivalHindiMonth::findOrFail($id);
        return view('admin-views.services.festivalhindimonth.edit',compact('festivalhindimonth'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'festival_hindimonth_title' => 'required',
            'month' => 'required',
            'year' => 'required',
        ], [
            'festival_hindimonth_title' => translate('Festival Hindi Month (Hindi) is Required'),
            'month'=>translate('Festival Hindi Month (English) is Required'),
            'year'=>translate('Festival year (English) is Required'),
        ]);

        $festivalhindimonth = FestivalHindiMonth::find($id);
        
        $festivalhindimonth->festival_hindimonth_title = $request->festival_hindimonth_title;
        $festivalhindimonth->month = $request->month;
        $festivalhindimonth->year = $request->year;
        if($festivalhindimonth->save()){
            Toastr::success(translate('Festival Hindi Month Updated Successfully'));
            UtilsHelpers::editDeleteLogs('Fast Festival','Fast Festival','Update');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }

    public function destroy($id)
    {
        $festivalhindimonth = FestivalHindiMonth::findOrFail($id);
        if ($festivalhindimonth){
            $festivalhindimonth->delete();
            Toastr::success('festival Hindi Month removed!');
            UtilsHelpers::editDeleteLogs('Fast Festival','Fast Festival','Delete');
        }else{
            Toastr::warning(translate('Something went wrong'));
        }
        return back();
    }
}
