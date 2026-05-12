<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\MasikRashi;
use App\Utils\Helpers as UtilsHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class MasikRashiController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['searchValue']);
        $masikrashis = MasikRashi::where(['status' => 1])->when(isset($key), function ($q) use ($key) {
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
            ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($masikrashi);
        return view('admin-views.masikrashi.list', compact('masikrashis'));
    }
    public function getAddView()
    {
        $masikrashi = MasikRashi::all();
        return view('admin-views.masikrashi.add-new', compact('masikrashi'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'month' => 'required',
            'lang' => 'required',
            'akshar' => 'required',
            'detail' => 'required',
        ], [
            'name' => translate('masikrashi (Hindi) is Required'),
            'month' => translate('masikrashi (English) is Required'),
        ]);

        $masikrashi = new MasikRashi;
        $masikrashi->name = $request->name;
        $masikrashi->month = $request->month;
        $masikrashi->language = $request->lang;
        $masikrashi->akshar = $request->akshar;
        $masikrashi->detail = $request->detail;

        if ($masikrashi->save()) {
            Toastr::success(translate('Masi Krashi Added Successfully'));
            UtilsHelpers::editDeleteLogs('Masik Rashi', 'Masik Rashi', 'Insert');
            return back();
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }


    public function edit(string $id)
    {
        $masikrashi = MasikRashi::findOrFail($id);
        return view('admin-views.masikrashi.edit', compact('masikrashi'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'month' => 'required',
            'lang' => 'required',
            'akshar' => 'required',
            'detail' => 'required',
        ], [
            'name' => translate('masikrashi (Hindi) is Required'),
            'month' => translate('masikrashi (English) is Required'),
        ]);

        $masikrashi = MasikRashi::find($id);
        $masikrashi->name = $request->name;
        $masikrashi->month = $request->month;
        $masikrashi->language = $request->lang;
        $masikrashi->akshar = $request->akshar;
        $masikrashi->detail = $request->detail;
        if ($masikrashi->save()) {
            UtilsHelpers::editDeleteLogs('Masik Rashi', 'Masik Rashi', 'Update');
            Toastr::success(translate('Masik Rashi Updated Successfully'));
            return redirect()->route('admin.masikrashi.list');
        }
        Toastr::error(translate('Something went Wrong'));
        return back();
    }

    public function destroy($id)
    {
        $masikrashi = MasikRashi::findOrFail($id);
        if ($masikrashi) {
            $masikrashi->delete();
            UtilsHelpers::editDeleteLogs('Masik Rashi', 'Masik Rashi', 'Delete');
            Toastr::success('Masik Rashi removed!');
        } else {
            Toastr::warning(translate('Something went wrong'));
        }
        return back();
    }
}