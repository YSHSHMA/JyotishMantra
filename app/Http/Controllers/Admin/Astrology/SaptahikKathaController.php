<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\SaptahikKatha;
use App\Utils\Helpers as UtilsHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class SaptahikKathaController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['searchValue']);
        $saptahikkathas=SaptahikKatha::where(['status'=>1])->when(isset($key) , function ($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
        ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($kathas);
        return view('admin-views.saptahikkatha.list',compact('saptahikkathas'));
    }

    public function getAddView()
    {
        $saptahikkatha = SaptahikKatha::all();
        return view('admin-views.saptahikkatha.add-new',compact('saptahikkatha'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ], [
            'title'=>translate('saptahikkatha Title is Required'),
            'description'=>translate('saptahikkatha Description is Required'),
        ]);

        $saptahikkatha = new SaptahikKatha;
        $saptahikkatha->title = $request->title;
        $saptahikkatha->description = $request->description;
        if($saptahikkatha->save()){
            Toastr::success(translate('Saptahikkatha Added Successfully'));
            UtilsHelpers::editDeleteLogs('Saptahik Katha','Saptahik Katha','Insert');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();

    }


    public function edit(string $id)
    {
        $saptahikkatha = SaptahikKatha::findOrFail($id);
        return view('admin-views.services.saptahikkatha.edit',compact('saptahikkatha'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ], [
            'title'=>translate('saptahikkatha Title is Required'),
            'description'=>translate('saptahikkatha Description is Required'),
        ]);

        $saptahikkatha = SaptahikKatha::find($id);
        $saptahikkatha->title = $request->title;
        $saptahikkatha->description = $request->description;
        if($saptahikkatha->save()){
            UtilsHelpers::editDeleteLogs('Saptahik Katha','Saptahik Katha','Update');
            Toastr::success(translate('saptahikkatha Updated Successfully'));
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }

    public function destroy($id)
    {
        $saptahikkatha = SaptahikKatha::findOrFail($id);
        if ($saptahikkatha){
            $saptahikkatha->delete();
            UtilsHelpers::editDeleteLogs('Saptahik Katha','Saptahik Katha','Delete');
            Toastr::success('saptahikkatha removed!');
        }else{
            Toastr::warning(translate('Something went wrong'));
        }
        return back();
    }
}
