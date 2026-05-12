<?php

namespace App\Http\Controllers\Admin\Astrology;

// use App\CentralLogics\Helpers;
use App\Utils\Helpers;
use App\Http\Controllers\Controller;
use App\Models\VarshikRashi;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class VarshikRashiController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['searchValue']);
        $varshikrashis = VarshikRashi::where(['status' => 1])->when(isset($key), function ($q) use ($key) {
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
            ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($varshikrashis);
        return view('admin-views.varshikrashi.list', compact('varshikrashis'));
    }
    public function getAddView()
    {
        $varshikrashi = VarshikRashi::all();
        return view('admin-views.varshikrashi.add-new', compact('varshikrashi'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'lang' => 'required',
            'akshar' => 'required',
            'detail' => 'required',
        ], [
            'name' => translate('varshikrashi (Hindi) is Required'),
            'month' => translate('varshikrashi (English) is Required'),
        ]);

        $varshikrashi = new VarshikRashi;
        $varshikrashi->name = $request->name;
        $varshikrashi->language = $request->lang;
        $varshikrashi->akshar = $request->akshar;
        $varshikrashi->detail = $request->detail;

        if ($varshikrashi->save()) {
            Toastr::success(translate('Varshik rashi Added Successfully'));
            Helpers::editDeleteLogs('Varshik Rashi', 'Varshik Rashi', 'Insert');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }


    public function edit(string $id)
    {
        $varshikrashi = VarshikRashi::findOrFail($id);
        return view('admin-views.varshikrashi.edit', compact('varshikrashi'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'lang' => 'required',
            'akshar' => 'required',
            'detail' => 'required',
        ], [
            'name' => translate('varshikrashi (Hindi) is Required'),
            'month' => translate('varshikrashi (English) is Required'),
        ]);

        $varshikrashi = VarshikRashi::find($id);
        $varshikrashi->name = $request->name;
        $varshikrashi->language = $request->lang;
        $varshikrashi->akshar = $request->akshar;
        $varshikrashi->detail = $request->detail;
        if ($varshikrashi->save()) {
            Helpers::editDeleteLogs('Varshik Rashi', 'Varshik Rashi', 'Update');
            Toastr::success(translate('varshik Rashi Updated Successfully'));
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }

    public function destroy($id)
    {
        $varshikrashi = VarshikRashi::findOrFail($id);
        if ($varshikrashi) {
            $varshikrashi->delete();
            Helpers::editDeleteLogs('Varshik Rashi', 'Varshik Rashi', 'Delete');
            Toastr::success('varshik Rashi removed!');
        } else {
            Toastr::warning(translate('Something went wrong'));
        }
        return back();
    }
}