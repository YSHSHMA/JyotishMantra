<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\AdminRepositoryInterface;
use App\Enums\ViewPaths\Admin\Profile;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AdminPasswordRequest;
use App\Http\Requests\Admin\AdminRequest;
use App\Models\Admin;
use App\Models\AdminPwdChangeLogs;
use App\Models\RemoteAccess;
use App\Models\LoginLogs;
use App\Models\Logs;
use App\Services\AdminService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use PragmaRX\Google2FAQRCode\Google2FA;

class ProfileController extends BaseController
{
    public function __construct(
        private readonly AdminRepositoryInterface $adminRepo,
        private readonly AdminService $adminService,
    )
    {
    }
    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
       return $this->getListView();
    }
    public function getListView():View
    {
        $admin = $this->adminRepo->getFirstWhere(['id'=>auth('admin')->id()]);
        return view(Profile::INDEX[VIEW],compact('admin'));
    }

    /**
     * @param string|int $id
     * @return View|RedirectResponse
     */
    public function getUpdateView(string|int $id):View|RedirectResponse
    {
        $admin = $this->adminRepo->getFirstWhere(['id' => $id]);
        $shopBanner = getWebConfig('shop_banner');
        return view(Profile::UPDATE[VIEW],compact('admin','shopBanner'));
    }

    /**
     * @param AdminRequest $request
     * @param string|int $id
     * @return RedirectResponse
     */
    public function update(AdminRequest $request, string|int $id):RedirectResponse
    {
        $admin = $this->adminRepo->getFirstWhere(['id' => $id]);
        $this->adminRepo->update(id: $id, data: $this->adminService->getAdminDataForUpdate(request: $request, admin: $admin));
        Toastr::success(translate('profile_updated_successfully'));
        return redirect()->back();
    }

    /**
     * @param AdminPasswordRequest $request
     * @param string|int $id
     * @return RedirectResponse
     */
    public function updatePassword(AdminPasswordRequest $request , string|int $id):RedirectResponse
    {
        $this->adminRepo->update(id:$id,data:$this->adminService->getAdminPasswordData(request:$request));

        $logs = new AdminPwdChangeLogs;
        $logs->admin_id = auth('admin')->user()->id;
        $logs->ip = Helpers::getPublicIp()??null;
        $logs->prev_pwd = auth('admin')->user()->password;
        $logs->save();

        Toastr::success(translate('admin_password_updated_successfully'));
        return redirect()->back();
    }

    public function show_qr(Request $request)
    {
        if ($request->enable == 1) {
            $google2fa = new Google2FA();
            $secret = $google2fa->generateSecretKey();
            $qr_code = $google2fa->getQRCodeInline(
                "Mahakal",
                $request->email,
                $secret
            );
            Admin::where('id', $request->id)->update(['google2fa_secret' => $secret]);
            return response()->json(['status' => 200, 'message' => 'success', 'qr_code' => $qr_code])->header("Access-Control-Allow-Origin",  "*");
            // echo "<pre>"; print_r($qr_code);die;
        } else {
            Admin::where('id', $request->id)->update(['enable_2fa' => 0]);
        }
        return response()->json(['status' => 200, 'message' => 'success', 'qr_code' => null]);
    }

    public function active(Request $request){
        $secret = Admin::select('google2fa_secret')->where('id',$request->id)->first();
        $google2fa = new Google2FA();
        if($google2fa->verify($request->input('otp'),$secret['google2fa_secret'])){
            Admin::where('id', $request->id)->update(['enable_2fa'=>1]);
            return response()->json(['status' => 200,'message' => 'successfully activated'])->header("Access-Control-Allow-Origin",  "*");
        }else{
            return response()->json(['status' => 400,'message' => 'Code is invalid']);
        }
        return response()->json(['status' => 400,'message' => 'Something went wrong']);
    }
    
    public function login_check(Request $request){
        $admin = Admin::where('email',$request->email)->first();
        if($admin){
            return response()->json(['status' => 200, 'admin'=>$admin]);
        }else{
            return response()->json(['status' => 400,'message' => 'unauthorized email']);
        }
        return response()->json(['status' => 400,'message' => 'something went wrong']);
    }

    public function login_submit(Request $request){
        $admin = Admin::where('email',$request->email)->first();
        $google2fa = new Google2FA();
        if($google2fa->verify($request->input('otp'),$admin['google2fa_secret'])){
            return response()->json(['status' => 200,'message' => 'successfully validated']);
        }else{
            return response()->json(['status' => 400,'message' => 'Code is invalid']);
        }
        return response()->json(['status' => 400,'message' => 'Something went wrong']);
    }
    
    public function auth_logs_list(){
        LoginLogs::where('notification_status',1)->update(['notification_status'=>0]);
        $adminLogs = LoginLogs::where('role', 'admin')->orderBy('created_at', 'desc')->paginate(10, ['*'], 'admin_page');
        $authLogs = LoginLogs::where('role', '!=', 'admin')->orderBy('created_at', 'desc')->paginate(10, ['*'], 'auth_page');
        return view('admin-views.logs.auth-list',compact('adminLogs','authLogs'));
    }

    public function logs_list(){
        $logs = Logs::orderBy('created_at', 'desc')->paginate(10);
        return view('admin-views.logs.list',compact('logs'));
    }

    public function pwd_change_logs_list(){
        $logs = AdminPwdChangeLogs::with('admins')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin-views.logs.pwd-change-list',compact('logs'));
    }

    public function remote_access_list(){
        $remoteAccess = RemoteAccess::paginate(10);
        return view('admin-views.remote-access.view',compact('remoteAccess'));
    }

    public function remote_access_add(Request $request){
        $remoteAccessAdd = new RemoteAccess;
        $remoteAccessAdd->host_address = $request->host_address;
        $remoteAccessAdd->comment = $request->comment;
        if($remoteAccessAdd->save()){
            Toastr::success(translate('host_saved_successfully'));
            Helpers::editDeleteLogs('Remote Access','Remote Access','Insert');
            return redirect()->back();
        }
        Toastr::success(translate('an_error_occured'));
        return redirect()->back();
    }

    public function remote_access_update(Request $request){
        $remoteAccessUpdate = RemoteAccess::where('id',$request->id)->first();
        $remoteAccessUpdate->host_address = $request->host_address;
        $remoteAccessUpdate->comment = $request->comment;
        if($remoteAccessUpdate->save()){
            Toastr::success(translate('host_updated_successfully'));
            Helpers::editDeleteLogs('Remote Access','Remote Access','Update');
            return redirect()->back();
        }
        Toastr::success(translate('an_error_occured'));
        return redirect()->back();
    }

    public function remote_access_delete(Request $request){
        $remoteAccessDelete = RemoteAccess::where('id',$request->id)->delete();
        if($remoteAccessDelete){
            Toastr::success(translate('host_deleted_successfully'));
            Helpers::editDeleteLogs('Remote Access','Remote Access','Delete');
            return redirect()->back();
        }
        Toastr::success(translate('an_error_occured'));
        return redirect()->back();
    }
    public function newOrderMessage(Request $request){

        if(!empty($request->name) && ($request->name == 'event'|| $request->name == 'tour' ||  $request->name == 'kundli')){

            if($request->name == 'event'){
                $events = \App\Models\EventOrder::where('transaction_status',1)->where('on_load', '0')->update(['on_load'=>1]);
            }elseif($request->name == 'tour'){
                $tour = \App\Models\TourOrder::where('amount_status',1)->where('on_load', '0')->update(['on_load'=>1]);
            }elseif($request->name == 'kundli'){
                $kundli = \App\Models\BirthJournalKundali::where('payment_status',1)->where('on_load', '0')->update(['on_load'=>1]);
            }
            
        }

        $events = \App\Models\EventOrder::where('transaction_status',1)->where('on_load', '0')->count();
        $tour = \App\Models\TourOrder::where('amount_status',1)->where('on_load', '0')->count();
        $kundli = \App\Models\BirthJournalKundali::where('payment_status',1)->where('on_load', '0')->count();
        return response()->json([
            'success' => 1,
            'data' => ['event' => $events,'tour'=>$tour,'kundli'=>$kundli]
        ]);
    }

}