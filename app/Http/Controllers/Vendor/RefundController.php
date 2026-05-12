<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\OrderDetailRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\RefundRequestRepositoryInterface;
use App\Contracts\Repositories\RefundStatusRepositoryInterface;
use App\Enums\ViewPaths\Vendor\Refund;
use App\Events\RefundEvent;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Vendor\RefundStatusRequest;
use App\Services\RefundStatusService;
use App\Traits\CustomerTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Order as ModelsOrder;
use App\Models\Order_Pickup;
use App\Models\Product;
use App\Models\RefundRequest as ReturnUpdate;
use App\Models\Seller;
use App\Models\Shop;
use App\Utils\Helpers;

class RefundController extends BaseController
{
    use CustomerTrait;
    public function __construct(
        private readonly RefundRequestRepositoryInterface $refundRequestRepo,
        private readonly CustomerRepositoryInterface $customerRepo,
        private readonly OrderDetailRepositoryInterface $orderDetailRepo,
        private readonly RefundStatusRepositoryInterface $refundStatusRepo,
        private readonly RefundStatusService $refundStatusService,
        private readonly OrderRepositoryInterface $orderRepo,

    ) {}

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View|Collection|LengthAwarePaginator|callable|RedirectResponse|null
     */
    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        return $this->getList(request: $request, status: $type);
    }

    /**
     * @param object $request
     * @param string $status
     * @return View
     */
    public function getList(object $request, string $status): View
    {
        $vendorId = auth('seller')->id();
        $searchValue =  $request['search'] ?? null;
        $refundList = $this->refundRequestRepo->getListWhereHas(
            orderBy: ['id' => 'desc'],
            searchValue: $searchValue,
            filters: ['status' => $status],
            whereHas: 'order',
            whereHasFilters: ['seller_is' => 'seller', 'seller_id' => $vendorId],
            dataLimit: getWebConfig('pagination_limit'),

        );
        return view(Refund::INDEX[VIEW], compact('refundList', 'searchValue'));
    }

    /**
     * @param string|int $id
     * @return View
     */
    public function getDetailsView(string|int $id): View
    {
        $vendorId = auth('seller')->id();
        $refund = $this->refundRequestRepo->getFirstWhereHas(
            params: ['id' => $id],
            whereHas: 'order',
            whereHasFilters: ['seller_is' => 'seller', 'seller_id' => $vendorId],
            relations: ['order.details'],
        );
        $order = $refund->order;
        $totalProductPrice = 0;
        foreach ($order->details as $key => $orderDetails) {
            $totalProductPrice += ($orderDetails->qty * $orderDetails->price) + $orderDetails->tax - $orderDetails->discount;
        }
        $shippingcharge = Product::where('id', $refund->product_id)->get()->value('shipping_cost');

        $subtotal = $refund->orderDetails->price * $refund->orderDetails->qty - $refund->orderDetails->discount + $refund->orderDetails->tax;
        $couponDiscount = ($order->discount_amount * $subtotal) / $totalProductPrice;
        $refundAmount = $subtotal - $couponDiscount + $shippingcharge;
        return view(Refund::DETAILS[VIEW], compact('refund', 'order', 'refundAmount', 'subtotal', 'couponDiscount', 'refundAmount', 'shippingcharge'));
    }

    /**
     * @param RefundStatusRequest $request
     * @return JsonResponse
     */

    public function updateStatus(RefundStatusRequest $request): JsonResponse
    {
        $vendorId = auth('seller')->id();
        $refund = $this->refundRequestRepo->getFirstWhereHas(
            params: ['id' => $request['id']],
            whereHas: 'order',
            whereHasFilters: ['seller_is' => 'seller', 'seller_id' => $vendorId],
        );
        // Ensure refund is first approved before marking as received
        if ($request['refund_status'] == 'received' && $refund['status'] !== 'approved') {
            return response()->json(['error' => translate('Refund must be approved before it can be received') . '!!']);
        }
        if ($request['refund_status'] == 'received') {
            //  Update only `status` and `received_note`
            $this->refundRequestRepo->update(
                id: $refund['id'],
                data: [
                    'status' => 'received',
                    'received_note' => $request['received_note'], // Store received note
                    'change_by' => 'seller',
                ]
            );
            return response()->json(['message' => translate('refund_status_updated') . '!!']);
        }
        if (($request['refund_status'] == 'approved' && $refund['approved_count'] >= 2) || $request['refund_status'] == 'rejected' && $refund['denied_count'] >= 2) {
            return response()->json(['error' => translate('you_already_changed_') . ($request['refund_status'] == 'approved' ? 'approve' : 'reject') . translate('_status_two_times') . '!!']);
        }
        $customer = $this->customerRepo->getFirstWhere(params: ['id' => $refund['customer_id']]);
        if (!isset($customer)) {
            return response()->json(['error' => translate('this_account_has_been_deleted') . ',' . translate('you_can_not_modify_the_status') . '!!']);
        }
        $loyaltyPointStatus = getWebConfig('loyalty_point_status');
        $orderDetails = $this->orderDetailRepo->getFirstWhere(['id' => $refund['order_details_id']]);
        if ($loyaltyPointStatus == 1) {
            $loyaltyPoint = $this->convertAmountToLoyaltyPoint(orderDetails: $orderDetails);
            if ($customer['loyalty_point'] < $loyaltyPoint && $request['refund_status'] == 'approved') {
                return response()->json(['error' => translate('customer_has_not_sufficient_loyalty_point_to_take_refund_for_this_order') . '!!']);
            }
        }

        if ($refund['change_by'] == 'admin') {
            return response()->json(['error' => translate('refunded_status_can_not_be_changed') . '!!' . ('admin_already_changed_the_status') . ': ' . $refund['status'] . '!!']);
        }
        if ($refund['status'] != 'refunded') {
            $statusMapping = [
                'pending' => 1,
                'approved' => 2,
                'rejected' => 3,
                'refunded' => 4,
                'received' => 5, // New received status added
            ];
            // dd($statusMapping);
            $this->orderDetailRepo->update(
                id: $orderDetails['id'],
                data: ['refund_request' => $statusMapping[$request['refund_status']]]
            );
            $this->refundStatusRepo->add($this->refundStatusService->getRefundStatusData(
                request: $request,
                refund: $refund,
                changeBy: 'seller'
            ));
            // Code New
            $order = $this->orderRepo->getFirstWhere(params: ['id' => $refund['order_id']]);
            $refundOrderId = $refund['order_id'];
            if ($request['refund_status'] == 'approved') {
                $payment_type = match ($order['payment_method']) {
                    "razor_pay", "pay_by_wallet" => "prepaid",
                    "cash_on_delivery" => "cod",
                    default => null
                };

                $fromPicode = $order['shipping_address_data'] ?? $order['billing_address_data'] ?? null;
                $toPicode = Shop::where('id', $order->seller_id)->value('pincode');

                if ($fromPicode && $toPicode) {
                    $response = Helpers::ShipwayGetCarrierrates($fromPicode->zip, $toPicode, $payment_type, $order->order_weight, $order->box_length, $order->box_breadth, $order->box_height);
                    if (!empty($response['rate_card'])) {
                        $rateCards = collect($response['rate_card']);
                        $lowest_price = $rateCards->map(function ($item) {
                            $item['total_cost'] = $item['delivery_charge'] + $item['rto_charge'];
                            return $item;
                        })->sortBy('total_cost')->first();
                        if ($lowest_price['zone'] == 1) {
                            // Self-managed return
                            $gst_rate = 0.18;
                            $deliveryCharge = ($payment_type == 'cod') ? ($lowest_price['delivery_charge'] * (1 + $gst_rate)) + ($lowest_price['cod_charges'] * (1 + $gst_rate)) : $lowest_price['delivery_charge'] * (1 + $gst_rate);

                            // dd($lowest_price,$deliveryCharge);
                            $this->refundRequestRepo->update(
                                id: $refund['id'],
                                data: [
                                    'status' => $request['refund_status'],
                                    'approved_count' => ($request['refund_status'] === 'approved') ? ($refund['approved_count'] + 1) : $refund['approved_count'],
                                    'approved_note' => ($request['refund_status'] === 'rejected') ? $request['approved_note'] : null,
                                    'change_by' => 'seller',
                                    'carrier_id' => $lowest_price['carrier_id'],
                                    'delivery_charge' => $deliveryCharge,
                                    'patner_type' => 'self'
                                ]
                            );
                            $order = $this->orderRepo->getFirstWhere(params: ['id' => $refund['order_id']]);
                            RefundEvent::dispatch($request['refund_status'], $order);
                            return response()->json(['message' => translate('refund_status_updated') . '!!']);
                        } else {
                            // Shipway return pickup
                            if ($order['delivery_partner'] == 'shipway') {
                                $reverseData = Helpers::ShipWayGenerateReversePickup($refundOrderId);

                                if (!empty($reverseData) && isset($reverseData['success']) && $reverseData['success'] == 1) {
                                    $rmaNo = $reverseData['create_return_response']['rma_no'] ?? null;
                                    $awbNo = $reverseData['awb_response']['AWB'] ?? null;
                                    $message = $reverseData['message'] ?? 'No message';

                                    ModelsOrder::where('id', $refund['order_id'])->update([
                                        'third_party_delivery_tracking_id' => $awbNo,
                                        'order_status' => 'returned',
                                        'message' => $message,
                                        'updated_at' => now(),
                                    ]);

                                    $this->refundRequestRepo->update(
                                        id: $refund['id'],
                                        data: [
                                            'status' => $request['refund_status'],
                                            'approved_count' => ($request['refund_status'] === 'approved') ? ($refund['approved_count'] + 1) : $refund['approved_count'],
                                            'approved_note' => ($request['refund_status'] === 'rejected') ? $request['approved_note'] : null,
                                            'change_by' => 'seller',
                                            'rma_no' => $rmaNo,
                                            'patner_type' => 'shipway',
                                        ]
                                    );

                                    RefundEvent::dispatch($request['refund_status'], $order);
                                    return response()->json(['message' => translate('refund_status_updated') . '!!']);
                                } else {
                                    return response()->json(['message' => $reverseData['create_return_response']['message'] ?? 'Reverse pickup failed.']);
                                }
                            }
                        }
                    }
                }
            }

            $this->refundRequestRepo->update(
                id: $refund['id'],
                data: [
                    'status' => $request['refund_status'],
                    'denied_count' => $request['refund_status'] == 'rejected' ? ($refund['denied_count'] + 1) : $refund['denied_count'],
                    'rejected_note' => $request['refund_status'] == 'rejected' ? $request['rejected_note'] : null,
                    'received_note' => $request['refund_status'] == 'received' ? $request['received_note'] : null, // New field for received_note
                    'change_by' => 'seller',
                ]
            );
            $order = $this->orderRepo->getFirstWhere(params: ['id' => $refund['order_id']]);
            RefundEvent::dispatch($request['refund_status'], $order);
            return response()->json(['message' => translate('refund_status_updated') . '!!']);
        } else {
            return response()->json(['message' => translate('refunded_status_can_not_be_changed') . '!!']);
        }
    }
    // //  -----------------------------------------------------------------------------------------------------------------------
    // public function updateStatusss(RefundStatusRequest $request):JsonResponse
    // {
    //     // dd($request->all());
    //     $vendorId = auth('seller')->id();
    //     $refund = $this->refundRequestRepo->getFirstWhereHas(
    //         params:['id'=>$request['id']],
    //         whereHas: 'order',
    //         whereHasFilters: [ 'seller_is'=>'seller', 'seller_id' => $vendorId],
    //     );
    //     if (($request['refund_status'] == 'approved' && $refund['approved_count'] >=2) || $request['refund_status'] == 'rejected' && $refund['denied_count'] >=2 || ($request['refund_status'] == 'received' && $refund['received_count'] >= 1)){
    //         return response()->json(['error'=>translate('you_already_changed_').($request['refund_status']=='approved'?'approve' : 'reject').translate('_status_two_times').'!!']);
    //     }
    //     $customer = $this->customerRepo->getFirstWhere(params:['id'=>$refund['customer_id']]);
    //     if(!isset($customer))
    //     {
    //         return response()->json(['error'=>translate('this_account_has_been_deleted').','.translate('you_can_not_modify_the_status').'!!']);
    //     }
    //     $loyaltyPointStatus = getWebConfig('loyalty_point_status');
    //     $orderDetails = $this->orderDetailRepo->getFirstWhere(['id' => $refund['order_details_id']]);
    //     if($loyaltyPointStatus == 1){
    //         $loyaltyPoint = $this->convertAmountToLoyaltyPoint(orderDetails:$orderDetails);
    //         if($customer['loyalty_point'] < $loyaltyPoint && $request['refund_status'] == 'approved')
    //         {
    //             return response()->json(['error'=>translate('customer_has_not_sufficient_loyalty_point_to_take_refund_for_this_order').'!!']);
    //         }
    //     }

    //     if($refund['change_by'] =='admin'){
    //         return response()->json(['error'=>translate('refunded_status_can_not_be_changed').'!!'.('admin_already_changed_the_status') .': '.$refund['status'].'!!']);
    //     }
    //     if($refund['status'] != 'refunded'){
    //         $statusMapping = [
    //             'pending' => 1,
    //             'approved' => 2,
    //             'rejected' => 3,
    //             'refunded' => 4,
    //             'received' => 5,
    //         ];
    //         // dd($statusMapping);
    //         $this->orderDetailRepo->update(
    //             id:$orderDetails['id'],
    //             data: ['refund_request'=>$statusMapping[$request['refund_status']]]
    //         );
    //         $this->refundStatusRepo->add($this->refundStatusService->getRefundStatusData(
    //             request:$request,
    //             refund: $refund,
    //             changeBy: 'seller'
    //         ));

    //         $order = $this->orderRepo->getFirstWhere(params: ['id' => $refund['order_id']]);
    //         $refundOrderId = $refund['order_id'];
    //         if ($request['refund_status'] == 'approved') { 
    //             $payment_type = match ($order['payment_method']) {
    //                 "razor_pay", "pay_by_wallet" => "prepaid",
    //                 "cash_on_delivery" => "cod",
    //                 default => null
    //             };

    //             $fromPicode = $order['shipping_address_data'] ?? $order['billing_address_data'] ?? null;
    //             $toPicode = Shop::where('id', $order->seller_id)->value('pincode');

    //             if ($fromPicode && $toPicode) {
    //                 $response = Helpers::ShipwayGetCarrierrates($fromPicode->zip, $toPicode, $payment_type, $order->order_weight, $order->box_length, $order->box_breadth, $order->box_height);

    //                 if (!empty($response['rate_card'])) {
    //                     $rateCards = collect($response['rate_card']);
    //                     $lowest_price = $rateCards->map(function ($item) {
    //                         $item['total_cost'] = $item['delivery_charge'] + $item['rto_charge'];
    //                         return $item;
    //                     })->sortBy('total_cost')->first();

    //                     if ($lowest_price['zone'] == 1) {
    //                         $deliveryCharge = ($payment_type == 'cod') 
    //                             ? $lowest_price['delivery_charge'] + $lowest_price['cod_charges']
    //                             : $lowest_price['delivery_charge'];

    //                         ReturnUpdate::where('id', $refundOrderId)->update([
    //                             'carrier_id' => $lowest_price['carrier_id'],
    //                             'delivery_charge' => $deliveryCharge
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }

    //         if ($order['delivery_partner'] == 'shipway' && $request['refund_status'] == 'approved') {
    //             $reverseData = Helpers::ShipWayGenerateReversePickup($refundOrderId);

    //             if (!empty($reverseData) && isset($reverseData['success']) && $reverseData['success'] == 1) {
    //                 $rmaNo = $reverseData['create_return_response']['rma_no'] ?? null;
    //                 $awbNo = $reverseData['awb_response']['AWB'] ?? null;
    //                 $message = $reverseData['message'] ?? 'No message';

    //                 ModelsOrder::where('id', $refund['order_id'])->update([
    //                     'third_party_delivery_tracking_id' => $awbNo,
    //                     'order_status' => 'returned',
    //                     'message' => $message,
    //                     'updated_at' => now(),
    //                 ]);

    //                 $this->refundRequestRepo->update(
    //                     id: $refund['id'],
    //                     data: [
    //                         'status' => $request['refund_status'],
    //                         'approved_count' => ($request['refund_status'] === 'approved') ? ($refund['approved_count'] + 1) : $refund['approved_count'],
    //                         'approved_note' => ($request['refund_status'] === 'rejected') ? $request['approved_note'] : null,
    //                         'change_by' => 'seller',
    //                         'rma_no' => $rmaNo,
    //                         'third_party_delivery_tracking_id' => $awbNo,
    //                     ]
    //                 );
    //                 RefundEvent::dispatch($request['refund_status'], $order);
    //                 return response()->json(['message' => translate('refund_status_updated') . '!!']);
    //             } else {
    //                 return response()->json(['message' => $reverseData['create_return_response']['message'] ?? 'Reverse pickup failed.']);
    //             }
    //         } elseif ($request['refund_status'] === 'received') {
    //             $this->refundRequestRepo->update(
    //                 id: $refund['id'],
    //                 data: [
    //                     'status' => $request['refund_status'],
    //                     'received_note' => $request['received_note'] ?? null,
    //                     'change_by' => 'seller',
    //                 ]
    //             );

    //             RefundEvent::dispatch($request['refund_status'], $order);
    //             return response()->json(['message' => translate('refund_status_updated') . '!!']);
    //         } else {
    //             $this->refundRequestRepo->update(
    //                 id: $refund['id'],
    //                 data: [
    //                     'status' => $request['refund_status'],
    //                     'approved_count' => ($request['refund_status'] === 'approved') ? ($refund['approved_count'] + 1) : $refund['approved_count'],
    //                     'approved_note' => ($request['refund_status'] === 'rejected') ? $request['approved_note'] : null,
    //                     'denied_count' => ($request['refund_status'] === 'rejected') ? ($refund['denied_count'] + 1) : $refund['denied_count'],
    //                     'rejected_note' => ($request['refund_status'] === 'rejected') ? $request['rejected_note'] ?? null : null,
    //                     'change_by' => 'seller',
    //                 ]
    //             );

    //             RefundEvent::dispatch($request['refund_status'], $order);
    //             return response()->json(['message' => translate('refund_status_updated') . '!!']);
    //         }        


    //     }else {
    //         return response()->json(['message'=>translate('refunded_status_can_not_be_changed') . '!!']);
    //     }
    // }
}