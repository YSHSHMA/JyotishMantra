<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Contracts\Repositories\AdminRepositoryInterface;
use App\Contracts\Repositories\AdminRoleRepositoryInterface;
use App\Enums\ViewPaths\Admin\CustomRole;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\CustomRoleRequest;
use App\Models\AdminRole;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Traits\PaginatorTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomRoleController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly AdminRepositoryInterface $adminRepo,
        private readonly AdminRoleRepositoryInterface $adminRoleRepo,
    )
    {
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {
        return $this->getAddView($request);
    }

    public function list(Request $request){
        $roles = $this->adminRoleRepo->getEmployeeRoleList(
            orderBy: ['id'=>'desc'],
            searchValue: $request['searchValue'],
            filters: ['admin_role_id' => $request['role']],
            dataLimit: 'all');

        return view(CustomRole::LIST[VIEW], compact('roles'));
    }

    public function getAddView(Request $request): View
    {
        $roles = $this->adminRoleRepo->getEmployeeRoleList(
            orderBy: ['id'=>'desc'],
            searchValue: $request['searchValue'],
            filters: ['admin_role_id' => $request['role']],
            dataLimit: 'all');

        $permissions = Permission::all()->groupBy('module');
        return view(CustomRole::ADD[VIEW], compact('roles','permissions'));
    }

    public function add(CustomRoleRequest $request): RedirectResponse
    {
        $data = [
            'name' => $request['name'],
            'module_access' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $this->adminRoleRepo->add(data: $data);

        $roleId = AdminRole::select('id')->latest()->first();
        foreach($request->permission as $perKey=>$per){
            foreach($per as $key=>$value){
                $permissions = new PermissionRole;
                $permissions->role_id = $roleId['id'];
                $permissions->module = $perKey;
                $permissions->sub_module = $key;
                $permissions->permission = json_encode($value);
                $permissions->save();
            }    
        }

        Toastr::success(translate('role_added_successfully'));
        Helpers::editDeleteLogs('Employee','Employee Role','Insert');
        return back();
    }

    public function getUpdateView($id): View
    {
        $role = $this->adminRoleRepo->getFirstWhere(params: ['id'=>$id]);
        $permissions = Permission::all()->groupBy('module');
        $permissionRoles = PermissionRole::where('role_id',$id)->get();
        return view(CustomRole::UPDATE[VIEW], compact('role','permissions','permissionRoles'));
    }

    public function update(CustomRoleRequest $request): RedirectResponse
    {
        $data = [
            'name' => $request['name'],
            'module_access' => null,
        ];
        $this->adminRoleRepo->update(id:$request['id'], data: $data);

        PermissionRole::where('role_id',$request->id)->delete();
        foreach($request->permission as $perKey=>$per){
            foreach($per as $key=>$value){
                $permissions = new PermissionRole;
                $permissions->role_id = $request['id'];
                $permissions->module = $perKey;
                $permissions->sub_module = $key;
                $permissions->permission = json_encode($value);
                $permissions->save();
            }    
        }

        Toastr::success(translate('role_updated_successfully'));
        Helpers::editDeleteLogs('Employee','Employee Role','Update');
        return back();
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $this->adminRoleRepo->update(id:$request['id'], data: ['status'=>$request->get('status', 0)]);
        return response()->json([
            'success' => 1,
            'message' => translate('status_updated_successfully'),
        ], 200);

    }


    public function exportList(Request $request): string|StreamedResponse
    {
        $roles = $this->adminRoleRepo->getEmployeeRoleList(
            orderBy: ['id'=>'desc'],
            searchValue: $request['searchValue'],
            filters: ['admin_role_id' => $request['role']],
            dataLimit: 'all');
        return (new FastExcel($roles))->download('role_list.xlsx');
    }

    public function delete(Request $request): JsonResponse
    {
        $this->adminRoleRepo->delete(params:['id'=>$request['id']]);
        Helpers::editDeleteLogs('Employee','Employee Role','Delete');
        return response()->json([
            'success' => 1,
            'message' => translate('role_deleted_successfully')
        ], 200);
    }

}
