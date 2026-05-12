<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Katha;
use App\Utils\Helpers as UtilsHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class KathaController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['searchValue']);
        $kathas=Katha::where(['status'=>1])->when(isset($key) , function ($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
        ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($kathas);
        return view('admin-views.katha.list',compact('kathas'));
    }
    public function getAddView()
    {
        $katha = Katha::all();
        return view('admin-views.katha.add-new',compact('katha'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'title' => 'required',
            'description' => 'required',
        ], [
            'date' => translate('katha Date is Required'),
            'title'=>translate('katha Title is Required'),
            'description'=>translate('katha Description is Required'),
        ]);

        $katha = new Katha;
        $katha->date = $request->date;
        $katha->title = $request->title;
        $katha->description = $request->description;
        if($katha->save()){
            Toastr::success(translate('katha Added Successfully'));
            UtilsHelpers::editDeleteLogs('Katha','Katha','Insert');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();

    }


    public function edit(string $id)
    {
        $katha = Katha::findOrFail($id);
        return view('admin-views.services.katha.edit',compact('katha'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'date' => 'required',
            'title' => 'required',
            'description' => 'required',
        ], [
            'date' => translate('katha Date is Required'),
            'title'=>translate('katha Title is Required'),
            'description'=>translate('katha Description is Required'),
        ]);

        $katha = Katha::find($id);
        $katha->date = $request->date;
        $katha->title = $request->title;
        $katha->description = $request->description;
        if($katha->save()){
            UtilsHelpers::editDeleteLogs('Katha','Katha','Update');
            Toastr::success(translate('katha Updated Successfully'));
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }

    public function destroy($id)
    {
        $katha = Katha::findOrFail($id);
        if ($katha){
            $katha->delete();
            UtilsHelpers::editDeleteLogs('Katha','Katha','Delete');
            Toastr::success('Katha removed!');
        }else{
            Toastr::warning(translate('Something went wrong'));
        }
        return back();
    }
}
