<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\PradoshKatha;
use App\Utils\Helpers as UtilsHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class PradoshKathaController extends Controller
{
    function getAddView(){
        $pradoshkathas = PradoshKatha::all();
        return view('admin-views.pradoshkatha.add-new',compact('pradoshkathas'));
    }
    function index(Request $request)
    {
        $key = explode(' ', $request['searchValue']);
        $pradoshkathas=PradoshKatha::where(['status'=>1])->when(isset($key) , function ($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
        ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($kathas);
        return view('admin-views.pradoshkatha.list',compact('pradoshkathas'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ], [
            'title'=>translate('Pradoshkatha Title is Required'),
            'description'=>translate('Pradoshkatha Description is Required'),
        ]);

        $pradoshkatha = new PradoshKatha;
        $pradoshkatha->title = $request->title;
        $pradoshkatha->description = $request->description;
        if($pradoshkatha->save()){
            Toastr::success(translate('Pradoshkatha Added Successfully'));
            UtilsHelpers::editDeleteLogs('Pradosh Katha','Pradosh Katha','Insert');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();

    }


    public function edit(string $id)
    {
        $pradoshkatha = PradoshKatha::findOrFail($id);
        return view('admin-views.services.pradoshkatha.edit',compact('pradoshkatha'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ], [
            'title'=>translate('Pradoshkatha Title is Required'),
            'description'=>translate('Pradoshkatha Description is Required'),
        ]);

        $pradoshkatha = PradoshKatha::find($id);
        $pradoshkatha->title = $request->title;
        $pradoshkatha->description = $request->description;
        if($pradoshkatha->save()){
            UtilsHelpers::editDeleteLogs('Pradosh Katha','Pradosh Katha','Update');
            Toastr::success(translate('Pradoshkatha Updated Successfully'));
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }

    public function destroy($id)
    {
        $pradoshkatha = PradoshKatha::findOrFail($id);
        if ($pradoshkatha){
            $pradoshkatha->delete();
            UtilsHelpers::editDeleteLogs('Pradosh Katha','Pradosh Katha','Delete');
            Toastr::success('Pradoshkatha removed!');
        }else{
            Toastr::warning(translate('Something went wrong'));
        }
        return back();
    }
}
