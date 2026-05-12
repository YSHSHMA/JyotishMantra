<?php

namespace App\Http\Controllers\Admin\Deliveryman;

use App\Contracts\Repositories\DeliveryManWalletRepositoryInterface;
use App\Contracts\Repositories\WithdrawRequestRepositoryInterface;
use App\Enums\ExportFileNames\Admin\DeliverymanWithdraw as DeliverymanWithdrawExport;
use App\Enums\ViewPaths\Admin\DeliverymanWithdraw;
use App\Events\WithdrawStatusUpdateEvent;
use App\Exports\DeliveryManWithdrawRequestExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\DeliveryManWithdrawRequest;
use App\Services\DeliveryManWithdrawService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DeliverymanWithdrawController extends Controller
{

    /**
     * @param WithdrawRequestRepositoryInterface $withdrawRequestRepo
     * @param DeliveryManWalletRepositoryInterface $deliveryManWalletRepo
     */
    public function __construct(
        private readonly WithdrawRequestRepositoryInterface   $withdrawRequestRepo,
        private readonly DeliveryManWalletRepositoryInterface $deliveryManWalletRepo,
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
        return $this->getListView($request);
    }

    public function getListView(Request $request): View
    {
        $withdrawRequests = $this->withdrawRequestRepo->getListWhere(
            orderBy: ['id'=>'desc'],
            filters: ['admin_id'=> 0, 'whereNotNull' => 'delivery_man_id', 'status' => $request['approved']],
            relations: ['deliveryMan'],
            dataLimit: getWebConfig('pagination_limit')
        );
        return view(DeliverymanWithdraw::LIST[VIEW], compact('withdrawRequests'));
    }
    public function getFiltered(Request $request): JsonResponse
    {
        $withdrawRequests = $this->withdrawRequestRepo->getListWhere(
            orderBy: ['id'=>'desc'],
            filters: ['admin_id'=> 0, 'whereNotNull' => 'delivery_man_id', 'status' => $request['status']],
            relations: ['deliveryMan'],
            dataLimit: getWebConfig('pagination_limit')
        );
        return response()->json([
            'view' => view(DeliveryManWithdraw::LIST[TABLE_VIEW], compact('withdrawRequests'))->render(),
            'count' => $withdrawRequests->count(),
        ], 200);
    }

    public function getView($withdraw_id): JsonResponse
    {
        $details = $this->withdrawRequestRepo->getFirstWhereNotNull(
            params: ['id' => $withdraw_id],
            filters: ['whereNotNull' => 'delivery_man_id'],
            relations: ['deliveryMan'],
        );
        return response()->json(['view'=>view(DeliverymanWithdraw::VIEW[VIEW],compact('details'))->render()]);
    }


    public function updateStatus(DeliveryManWithdrawRequest $request , string|int $withdrawId, DeliveryManWithdrawService $deliveryManWithdrawService):RedirectResponse
    {
        $withdraw = $this->withdrawRequestRepo->getFirstWhere(params: ['id'=>$withdrawId], relations: ['deliveryMan']);

        if(!$withdraw){
            Toastr::warning(translate('Invalid_withdraw'));
            return redirect()->route('admin.delivery-man.withdraw-list');
        }
        $wallet = $this->deliveryManWalletRepo->getFirstWhere(params:['delivery_man_id'=>$withdraw['delivery_man_id']]);
        $formatData = $deliveryManWithdrawService->getUpdateData(request: $request, wallet: $wallet, withdraw: $withdraw);
        $walletData = $formatData['wallet'];
        $withdrawData = $formatData['withdraw'];

        $this->deliveryManWalletRepo->update(id: $wallet->id, data: $walletData);
        $this->withdrawRequestRepo->update(id: $withdrawId, data: $withdrawData);
        if(!empty($withdraw->deliveryMan?->fcm_token)) {
            WithdrawStatusUpdateEvent::dispatch('withdraw_request_status_message', 'delivery_man', $withdraw->deliveryMan?->app_language ?? getDefaultLanguage(), $request['approved'], $withdraw->deliveryMan?->fcm_token);
        }
        return redirect()->route('admin.delivery-man.withdraw-list');
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $withdrawRequests = $this->withdrawRequestRepo->getListWhere(
            orderBy: ['id'=>'desc'],
            filters: ['admin_id'=> 0, 'whereNotNull' => 'delivery_man_id', 'status' => $request['status']],
            relations: ['deliveryMan'],
            dataLimit: 'all'
        );
        return Excel::download(new DeliveryManWithdrawRequestExport([
                    'withdraw_request'=>$withdrawRequests,
                    'filter' => session('delivery_withdraw_status_filter'),
                    'pending_request'=> $withdrawRequests->where('approved',0)->count(),
                    'approved_request'=> $withdrawRequests->where('approved',1)->count(),
                    'denied_request'=> $withdrawRequests->where('approved',2)->count(),
                ]), DeliverymanWithdrawExport::EXPORT_XLSX
        );
    }
}
