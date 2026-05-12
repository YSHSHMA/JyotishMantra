<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ViewPaths\Admin\VendorPermissionPath;
use App\Http\Controllers\Controller;
use App\Models\VendorEmployees;
use App\Models\VendorPermissionRole;
use App\Models\VendorPermissions;
use App\Models\VendorPhonePermissions;
use App\Models\VendorRoles;
use App\Services\VendorPermissionsService;
use Illuminate\Http\Request;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;

class VendorPermissionModule extends Controller
{

    public function PhoneCheck(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string'
        ]);
        $exists = VendorPhonePermissions::where('phone', $request->mobile)->exists();
        if ($exists) {
            return response()->json(['exists' => true, 'data' => $exists]);
        } else {
            return response()->json(['exists' => false, 'data' => $exists]);
        }
    }

    public function AddPerMissionModule(Request $request)
    {
        $groupedPermissions = [];
        if ($request['type']) {
            $getData = VendorPermissions::where('type', $request['type'])->get();

            $grouped = [];

            foreach ($getData as $row) {
                $module = $row->module;
                $subModule = $row->sub_module;
                $permissions = json_decode($row->permission, true);
                if (!isset($grouped[$module])) {
                    $grouped[$module] = ['name' => $module, 'children' => []];
                }
                $grouped[$module]['children'][] = [
                    'name' => $subModule,
                    'subchildren' => array_map(function ($perm) {
                        return ['name' => $perm];
                    }, $permissions)
                ];
            }
            $groupedPermissions = array_values($grouped);
        }
        return view(VendorPermissionPath::MODULE[VIEW], compact('groupedPermissions'));
    }

    public function UpdatePerMissionModule(Request $request, VendorPermissionsService $service)
    {
        $getData = $service->getAddData($request);
        foreach ($getData as $permissionItem) {
            VendorPermissions::updateOrCreate(
                [
                    'type'        => $permissionItem['type'],
                    'module'      => $permissionItem['module'],
                    'sub_module'  => $permissionItem['sub_module'],
                ],
                [
                    'permission'  => $permissionItem['permission'],
                    'updated_at'  => now(),
                ]
            );
        }
        return back();
    }

    public function AddPermissionRoles(Request $request)
    {
        $groupedPermissions = [];
        if ($request['type']) {
            $groupedPermissions = VendorPermissions::where('type', $request['type'])->get()->groupBy('module');
        }
        return view(VendorPermissionPath::ROLE[VIEW], compact('groupedPermissions'));
    }
    public function StorePermissionRoles(Request $request)
    {
        $Roles = new VendorRoles();
        $Roles->type = $request['module_name'];
        $Roles->name = $request['role_name'];
        $Roles->status = 1;
        $Roles->created_at = now();
        $Roles->updated_at = now();
        $Roles->save();
        foreach ($request->permission as $perKey => $per) {
            foreach ($per as $key => $value) {
                $permissions = new VendorPermissionRole();
                $permissions->role_id = $Roles->id;
                $permissions->module = $perKey;
                $permissions->sub_module = $key;
                $permissions->permission = json_encode($value);
                $permissions->save();
            }
        }
        Toastr::success(translate('role_Added_successfully'));
        return back();
    }

    public function ListPermissions(Request $request)
    {
        $groupedPermissions = VendorRoles::when(!empty($request), function ($query) use ($request) {
            $searchValue = $request['search'];
            return  $query->where('name', 'like', "%{$searchValue}%")->orWhere('type', 'like', "%{$searchValue}%");
        })->paginate(getWebConfig(name: 'pagination_limit'));

        return view(VendorPermissionPath::LIST[VIEW], compact('groupedPermissions'));
    }

    public function PermissionRolesStatus(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        VendorRoles::where('id', $request['id'])->update($data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function PermissionRolesDelete(Request $request)
    {
        $getData = VendorRoles::where('id', $request['id'])->first();
        if ($getData) {
            $getData->delete();
            VendorPermissionRole::where('role_id', $request['id'])->delete();
            return response()->json([
                'success' => 1,
                'message' => translate('role_deleted_successfully')
            ], 200);
        } else {
            return response()->json([
                'success' => 0,
                'message' => translate('role_deleted_failed')
            ], 200);
        }
    }

    public function PermissionRolesUpdate(Request $request)
    {
        $getData = VendorPermissionRole::where('role_id', $request['id'])->with(['Role'])->get();
        if ($getData) {
            $groupedPermissions = VendorPermissions::where('type', $getData[0]['Role']['type'] ?? "")->get()->groupBy('module');
            return view(VendorPermissionPath::ROLEUPDATE[VIEW], compact('getData', 'groupedPermissions'));
        } else {
            Toastr::error(translate('Data_null'));
        }
    }

    public function PermissionRolesEdit(Request $request)
    {
        $Roles = VendorRoles::where('id', $request['id'])->first();
        $Roles->name = $request['role_name'];
        $Roles->updated_at = now();
        $Roles->save();
        VendorPermissionRole::where('role_id', $request['id'])->delete();
        foreach ($request->permission as $perKey => $per) {
            foreach ($per as $key => $value) {
                $permissions = new VendorPermissionRole();
                $permissions->role_id = $request['id'];
                $permissions->module = $perKey;
                $permissions->sub_module = $key;
                $permissions->permission = json_encode($value);
                $permissions->save();
            }
        }
        Toastr::success(translate('role_Updated_successfully'));
        return back();
    }
    public function UserList(Request $request)
    {
        $groupedPermissions = VendorEmployees::with(['Role', 'seller', 'Tour', 'Trust', 'Event'])->when(!empty($request), function ($query) use ($request) {
            $searchValue = $request['search'];
            return  $query->where('identify_number', 'like', "%{$searchValue}%")
                ->orWhere('type', 'like', "%{$searchValue}%")
                ->orWhere('name', 'like', "%{$searchValue}%")
                ->orWhere('email', 'like', "%{$searchValue}%")
                ->orWhere('phone', 'like', "%{$searchValue}%");
        })->paginate(getWebConfig(name: 'pagination_limit'));

        return view(VendorPermissionPath::USERLIST[VIEW], compact('groupedPermissions'));
    }

    public function UserStatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        VendorEmployees::where('id', $request['id'])->update($data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function UserDeleted(Request $request) {
        
    }
}
