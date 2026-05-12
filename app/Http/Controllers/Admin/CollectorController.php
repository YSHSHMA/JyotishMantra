<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ViewPaths\Admin\Employee;
use App\Http\Controllers\Controller;
use App\Models\Collector;
use App\Models\SDM;
use App\Models\SDMEmployee;
use App\Models\Temple;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class CollectorController extends Controller
{
    // public function add()
    // {
    //     return view('admin-views.collector.add');
    // }

    // collector    
    public function list()
    {
        $districts = DB::table('district')->where('status',1)->get();
        $collectors = Collector::where('type','collector')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin-views.collector.list', compact('collectors','districts'));
    }

    public function get_temple(Request $request)
    {
        $district = $request->district;

        $temples = Temple::select('id', 'name')
            ->where('status', 1)
            ->where('district_id', $district)
            ->get();

        if ($temples) {
            return response()->json(['status' => true, 'temples' => $temples], 200);
        }
        return response()->json(['status' => false, 'message' => translate('an_error_occured')], 200);
    }

    public function store(Request $request)
    {
        $mobile = $request->mobile;
        if (!str_starts_with($mobile, '+91')) {
            $mobile = '+91' . $mobile;
        }
        $request->merge([
            'mobile' => $mobile
        ]);

        $request->validate([
            'name' => 'required',
            'district' => 'required',
            'email' => 'required|email|unique:collectors,email',
            'mobile' => 'required|unique:collectors,mobile',
            'password' => 'required',
            'temples'  => 'required|array|min:1',
        ]);

        $collectorStore = new Collector;
        $collectorStore->name = $request->name;
        $collectorStore->type = 'collector';
        $collectorStore->district = $request->district;
        $collectorStore->email = $request->email;
        $collectorStore->mobile = $request->mobile;
        $collectorStore->password = bcrypt($request->password);
        $collectorStore->temples = json_encode($request->temples);
        if ($collectorStore->save()) {
            Toastr::success(translate('collector_submitted_successfully'));
            return redirect()->route('admin.collector.list');
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function edit($id)
    {
        $districts = DB::table('district')->where('status',1)->get();
        $edit = Collector::where('id', $id)->first();
        return view('admin-views.collector.edit', compact('districts','edit'));
    }

    public function get_selected_temple(Request $request)
    {
        $temples = Temple::select('id', 'name')
            ->where('status', 1)
            ->where('district_id', $request->district)
            ->get();

        $selectedTemples = Collector::select('temples')->where('id',$request->collectorId)->first();

        if ($temples) {
            return response()->json(['status' => true, 'temples' => $temples,'selectedTemple'=>json_decode($selectedTemples->temples,true)], 200);
        }
        return response()->json(['status' => false, 'message' => translate('an_error_occured')], 200);
    }

    public function update(Request $request)
    {
        $mobile = $request->mobile;
        if (!str_starts_with($mobile, '+91')) {
            $mobile = '+91' . $mobile;
        }
        $request->merge([
            'mobile' => $mobile
        ]);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:collectors,email,' . $request->id,
            'mobile' => 'required|unique:collectors,mobile,' . $request->id,
            'temples'  => 'required|array|min:1',
        ]);

        $collectorUpdate = Collector::findOrFail($request->id);
        $collectorUpdate->name    = $request->name;
        $collectorUpdate->email   = $request->email;
        $collectorUpdate->mobile  = $request->mobile;
        $collectorUpdate->temples = json_encode($request->temples);
        if ($collectorUpdate->save()) {
            Toastr::success(translate('collector_updated_successfully'));
            return redirect()->route('admin.collector.list');
        }

        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function view($id)
    {
        $details = Collector::where('id', $id)->with(['sdms','employees'])->first();
        $details->district_name = DB::table('district')
        ->where('id', $details->district)
        ->value('name');
        return view('admin-views.collector.detail', compact('details'));
    }

    public function status(Request $request)
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $status = Collector::where('id', $request->id)->update($data);
        if ($status) {
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
        }
        return response()->json(['success' => 0, 'message' => translate('an_error_occured')], 200);
    }

    // sdm    
    public function sdm_list()
    {
        $collectors = Collector::where('type','collector')->where('status', 1)->get();
        $sdms = Collector::where('type','sdm')->with('collector')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin-views.sdm.list', compact('collectors', 'sdms'));
    }

    public function sdm_get_temple(Request $request)
    {
        $collectorId = $request->collector;

        $collectorTempleIds = Collector::where('id', $collectorId)
            ->value('temples');

        $collectorTempleIds = json_decode($collectorTempleIds, true) ?? [];

        $sdmTempleIds = Collector::where('rel_collector_id', $collectorId)
            ->pluck('temples')
            ->toArray();

        $sdmTempleArray = [];

        foreach ($sdmTempleIds as $templesJson) {
            $sdmTempleArray = array_merge(
                $sdmTempleArray,
                json_decode($templesJson, true) ?? []
            );
        }

        $sdmTempleArray = array_unique($sdmTempleArray);

        $arrayFilter = array_values(
            array_diff($collectorTempleIds, $sdmTempleArray)
        );

        $temples = Temple::select('id', 'name')
            ->where('status', 1)
            ->whereIn('id', $arrayFilter)
            ->get();

        if ($temples) {
            return response()->json(['status' => true, 'temples' => $temples], 200);
        }
        return response()->json(['status' => false, 'message' => translate('an_error_occured')], 200);
    }

    public function sdm_store(Request $request)
    {
        $mobile = $request->mobile;
        if (!str_starts_with($mobile, '+91')) {
            $mobile = '+91' . $mobile;
        }
        $request->merge([
            'mobile' => $mobile
        ]);

        $request->validate([
            'name' => 'required',
            'collector_id' => 'required',
            'email' => 'required|email|unique:collectors,email',
            'mobile' => 'required|unique:collectors,mobile',
            'password' => 'required',
            'temples'  => 'required|array|min:1',
        ]);

        $sdmStore = new Collector;;
        $sdmStore->name = $request->name;
        $sdmStore->type = 'sdm';
        $sdmStore->rel_collector_id = $request->collector_id;
        $sdmStore->email = $request->email;
        $sdmStore->mobile = $request->mobile;
        $sdmStore->password = bcrypt($request->password);
        $sdmStore->temples = json_encode($request->temples);
        if ($sdmStore->save()) {
            Toastr::success(translate('sdm_submitted_successfully'));
            return redirect()->route('admin.sdm.list');
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function sdm_edit($id)
    {
        $edit = Collector::where('id', $id)->first();
        $collectors = Collector::where('type','collector')->where('status', 1)->get();
        return view('admin-views.sdm.edit', compact('edit','collectors'));
    }

    public function sdm_get_selected_temple(Request $request)
    {
        $collectorId = $request->collector;

        $collectorTempleIds = Collector::where('id', $collectorId)
            ->value('temples');

        $collectorTempleIds = json_decode($collectorTempleIds, true) ?? [];

        $sdmTempleIds = Collector::where('rel_collector_id', $collectorId)->where('id','!=',$request->sdmId)
            ->pluck('temples')
            ->toArray();

        $sdmTempleArray = [];

        foreach ($sdmTempleIds as $templesJson) {
            $sdmTempleArray = array_merge(
                $sdmTempleArray,
                json_decode($templesJson, true) ?? []
            );
        }

        $sdmTempleArray = array_unique($sdmTempleArray);

        $arrayFilter = array_values(
            array_diff($collectorTempleIds, $sdmTempleArray)
        );

        $temples = Temple::select('id', 'name')
            ->where('status', 1)
            ->whereIn('id', $arrayFilter)
            ->get();

        $selectedTemples = Collector::select('temples')->where('id',$request->sdmId)->first();

        if ($temples) {
            return response()->json(['status' => true, 'temples' => $temples,'selectedTemple'=>json_decode($selectedTemples->temples,true)], 200);
        }
        return response()->json(['status' => false, 'message' => translate('an_error_occured')], 200);
    }

    public function sdm_update(Request $request)
    {
        $mobile = $request->mobile;
        if (!str_starts_with($mobile, '+91')) {
            $mobile = '+91' . $mobile;
        }
        $request->merge([
            'mobile' => $mobile
        ]);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:collectors,email,' . $request->id,
            'mobile' => 'required|unique:collectors,mobile,' . $request->id,
            'temples'  => 'required|array|min:1',
        ]);

        $sdmUpdate = Collector::findOrFail($request->id);
        $sdmUpdate->name = $request->name;
        $sdmUpdate->email = $request->email;
        $sdmUpdate->mobile = $request->mobile;
        $sdmUpdate->temples = json_encode($request->temples);
        if ($sdmUpdate->save()) {
            Toastr::success(translate('sdm_updated_successfully'));
            return redirect()->route('admin.sdm.list');
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function sdm_view($id)
    {
        $details = Collector::where('id', $id)->with('employees')->first();
        return view('admin-views.sdm.detail', compact('details'));
    }

    public function sdm_status(Request $request)
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $status = Collector::where('id', $request->id)->update($data);
        if ($status) {
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
        }
        return response()->json(['success' => 0, 'message' => translate('an_error_occured')], 200);
    }
    
    // employee    
    public function employee_list()
    {
        $sdms = Collector::where('type','sdm')->where('status', 1)->get();
        $employees = Collector::where('type','sdm-employee')->with('sdm')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin-views.sdm.employee.list', compact('employees', 'sdms'));
    }

    public function employee_get_temple(Request $request)
    {
        $sdmId = $request->sdm;

        $sdmTempleIds = Collector::where('id', $sdmId)
            ->value('temples');

        $sdmTempleIds = json_decode($sdmTempleIds, true) ?? [];

        $employeeTempleIds = Collector::where('rel_sdm_id', $sdmId)
            ->pluck('temples')
            ->toArray();

        $employeeTempleArray = [];

        foreach ($employeeTempleIds as $templesJson) {
            $employeeTempleArray = array_merge(
                $employeeTempleArray,
                json_decode($templesJson, true) ?? []
            );
        }

        $employeeTempleArray = array_unique($employeeTempleArray);

        $arrayFilter = array_values(
            array_diff($sdmTempleIds, $employeeTempleArray)
        );

        $temples = Temple::select('id', 'name')
            ->where('status', 1)
            ->whereIn('id', $arrayFilter)
            ->get();

        if ($temples) {
            return response()->json(['status' => true, 'temples' => $temples], 200);
        }
        return response()->json(['status' => false, 'message' => translate('an_error_occured')], 200);
    }

    public function employee_store(Request $request)
    {
        $mobile = $request->mobile;
        if (!str_starts_with($mobile, '+91')) {
            $mobile = '+91' . $mobile;
        }
        $request->merge([
            'mobile' => $mobile
        ]);

        $request->validate([
            'name' => 'required',
            'sdm_id' => 'required',
            'email' => 'required|email|unique:collectors,email',
            'mobile' => 'required|unique:collectors,mobile',
            'password' => 'required',
            'temples'  => 'required|array|min:1',
        ]);

        $employeeStore = new Collector;
        $employeeStore->name = $request->name;
        $employeeStore->type = 'sdm-employee';
        $employeeStore->rel_sdm_id = $request->sdm_id;
        $employeeStore->email = $request->email;
        $employeeStore->mobile = $request->mobile;
        $employeeStore->password = bcrypt($request->password);
        $employeeStore->temples = json_encode($request->temples);
        if ($employeeStore->save()) {
            Toastr::success(translate('employee_submitted_successfully'));
            return redirect()->route('admin.sdm.employee.list');
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function employee_edit($id)
    {
        $edit = Collector::where('id', $id)->first();
        $sdms = Collector::where('type','sdm')->where('status', 1)->get();
        return view('admin-views.sdm.employee.edit', compact('edit','sdms'));
    }

    
    public function employee_get_selected_temple(Request $request)
    {
        $sdmId = $request->sdm;

        $sdmTempleIds = Collector::where('id', $sdmId)
            ->value('temples');

        $sdmTempleIds = json_decode($sdmTempleIds, true) ?? [];

        $employeeTempleIds = Collector::where('rel_sdm_id', $sdmId)->where('id','!=',$request->empId)
            ->pluck('temples')
            ->toArray();

        $employeeTempleArray = [];

        foreach ($employeeTempleIds as $templesJson) {
            $employeeTempleArray = array_merge(
                $employeeTempleArray,
                json_decode($templesJson, true) ?? []
            );
        }

        $employeeTempleArray = array_unique($employeeTempleArray);

        $arrayFilter = array_values(
            array_diff($sdmTempleIds, $employeeTempleArray)
        );

        $temples = Temple::select('id', 'name')
            ->where('status', 1)
            ->whereIn('id', $arrayFilter)
            ->get();

        $selectedTemples = Collector::select('temples')->where('id',$request->empId)->first();

        if ($temples) {
            return response()->json(['status' => true, 'temples' => $temples,'selectedTemple'=>json_decode($selectedTemples->temples,true)], 200);
        }
        return response()->json(['status' => false, 'message' => translate('an_error_occured')], 200);
    }

    public function employee_update(Request $request)
    {
        $mobile = $request->mobile;
        if (!str_starts_with($mobile, '+91')) {
            $mobile = '+91' . $mobile;
        }
        $request->merge([
            'mobile' => $mobile
        ]);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:collectors,email,' . $request->id,
            'mobile' => 'required|unique:collectors,mobile,' . $request->id,
            'temples'  => 'required|array|min:1',
        ]);

        $employeeUpdate = Collector::findOrFail($request->id);
        $employeeUpdate->name = $request->name;
        $employeeUpdate->email = $request->email;
        $employeeUpdate->mobile = $request->mobile;
        $employeeUpdate->temples = json_encode($request->temples);
        if ($employeeUpdate->save()) {
            Toastr::success(translate('employee_updated_successfully'));
            return redirect()->route('admin.sdm.employee.list');
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function employee_view($id)
    {
        $details = Collector::where('id', $id)->first();
        return view('admin-views.sdm.employee.detail', compact('details'));
    }

    public function employee_status(Request $request)
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $status = Collector::where('id', $request->id)->update($data);
        if ($status) {
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
        }
        return response()->json(['success' => 0, 'message' => translate('an_error_occured')], 200);
    }
}

