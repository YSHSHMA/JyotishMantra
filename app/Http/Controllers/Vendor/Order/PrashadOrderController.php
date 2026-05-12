<?php

namespace App\Http\Controllers\Vendor\Order;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\DeliveryCountryCodeRepositoryInterface;
use App\Contracts\Repositories\DeliveryManRepositoryInterface;
use App\Contracts\Repositories\DeliveryManTransactionRepositoryInterface;
use App\Contracts\Repositories\DeliveryManWalletRepositoryInterface;
use App\Contracts\Repositories\DeliveryZipCodeRepositoryInterface;
use App\Contracts\Repositories\LoyaltyPointTransactionRepositoryInterface;
use App\Contracts\Repositories\OrderDetailRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\OrderStatusHistoryRepositoryInterface;
use App\Contracts\Repositories\OrderTransactionRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Enums\GlobalConstant;
use App\Enums\ViewPaths\Vendor\Order;
use App\Enums\WebConfigKey;
use App\Events\OrderStatusEvent;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDigitalFileAfterSellRequest;
use App\Repositories\WalletTransactionRepository;
use App\Services\DeliveryCountryCodeService;
use App\Services\DeliveryManTransactionService;
use App\Services\DeliveryManWalletService;
use App\Services\OrderStatusHistoryService;
use App\Traits\CustomerTrait;
use App\Traits\FileManagerTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Order as ModelsOrder;
use App\Models\Order_Pickup;
use App\Traits\PdfGenerator;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Utils\Helpers;
use App\Models\Prashad_deliverys;
use App\Models\Service_order;
use App\Models\ServiceTax;
use App\Models\ServiceTransaction;
use App\Models\User;
use Illuminate\Support\Facades\View as PdfView;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\View;

class PrashadOrderController extends Controller
{
    use PdfGenerator;

    public function orders_list($status, Request $request)
    {

        $types = ['pooja', 'anushthan', 'vip'];
        if ($status == 'all') {
            $orders = Prashad_deliverys::where('pooja_status', 1)->where('status', 1)
                ->with(['services', 'vippoojas', 'customers'])->orderBy('created_at', 'DESC')->get();
        } else {
            $orders = Prashad_deliverys::where('pooja_status', 1)->where('status', 1)
                ->where('order_status', $status)->with(['services', 'vippoojas', 'customers'])->orderBy('created_at', 'DESC')->get();   // dd($orders);
        }

        $users = User::all();
        // dd($orders); // You can remove this in production
        return view('vendor-views.order.prashad.list', compact('orders', 'users'));
    }
    public function orders_by_prashad()
    {

        $seller = auth('seller')->user();
        $vendorId = $seller['id'];
        $orders = Prashad_deliverys::where('pooja_status', 1)->with(['services', 'vippoojas', 'customers'])->selectRaw('service_id, COUNT(*) as total_orders,booking_date, COUNT(booking_date) as booking_count,order_status,type,user_id,id')->groupBy('service_id', 'booking_date',)->orderBy('total_orders', 'DESC')->get();
        $users = User::all();
        // dd($orders);
        return view('vendor-views.order.prashad.orderbyprashad', compact('orders', 'users'));
    }

    public function prashad_details($service_id, $date)
    {
        $serviceDetails = Prashad_deliverys::where('service_id', $service_id)->where('booking_date', $date)->with(['services', 'vippoojas', 'customers', 'products'])->first();
        $totalOrder = Prashad_deliverys::where('service_id', $service_id)->where('booking_date', $date)->count();
        $prashadinfo = Prashad_deliverys::where('pooja_status', 1)->where('status', 1)->where('service_id', $service_id)->where('booking_date', $date)->with(['services', 'vippoojas', 'customers', 'products'])->paginate(10);
        // dd($prashadinfo);
        return view('vendor-views.order.prashad.prashadam_Details', compact('prashadinfo', 'serviceDetails', 'totalOrder'));
    }

    public function updatePrashadamStatus(Request $request)
    {
        // dd($request->all());
        $updatedRows = Service_order::where('service_id', $request->service_id)
            ->where('booking_date', $request->date)
            ->update(['prashad_status' => $request->prashad_status]);
        if ($updatedRows > 0) {
            Toastr::success(translate('prashad_status_successfully'));
        } else {
            Toastr::warning(translate('no_changes_made'));
        }
        return back();
    }
    public function shipwayorder($order_id, Request $request)
    {
        // dd($request->all());
        $response = Helpers::ShipwayGetCarrierrates($request->input('fromPincode'), $request->input('toPincode'), $request->input('paymentType'), $request->input('order_weight'), $request->input('box_length'), $request->input('box_breadth'), $request->input('box_height'));
        // dd($response);
        $rateCards = collect($response['rate_card']);
        $lowest_price = $rateCards->map(function ($item) {
            $item['total_cost'] = $item['delivery_charge'] + $item['rto_charge'];
            return $item;
        })->sortBy('total_cost')->first();
        // dd($lowest_price);
        $deliveryCharge = '';
        if ($request->input('paymentType') == 'cod') {
            $deliveryCharge = $lowest_price['delivery_charge'] + $lowest_price['cod_charges'];
        } else {
            $deliveryCharge = $lowest_price['delivery_charge'];
        }
        // if($lowest_price['zone']==1){
        //         $orderWeight = [
        //             'delivery_partner' => 'self_delivery',
        //             'order_status' => 'processing',
        //             'added_by' => 'vendor'
        //         ];
        //         Prashad_deliverys::where('order_id', $order_id)->update($orderWeight);
        //         Toastr::success(translate('Order Successfully Assign.'));
        //         return response()->json(['order_status' => $request['order_status']]);
        // }else{
        $prashaddata = [
            'delivery_partner' => 'shipway',
            'order_status' => 'processing',
            'carrier_id' => $lowest_price['carrier_id'],
            'carrier_name' => $lowest_price['courier_name'],
            'added_by' => 'vendor',
            'delivery_charge' => $deliveryCharge
        ];
        Prashad_deliverys::where('order_id', $order_id)->update($prashaddata);
        $responseLabel = Helpers::ShipWayorderPrashad($order_id);
        // dd($responseLabel);
        if (isset($responseLabel) && $responseLabel['success'] == true) {
            if (isset($responseLabel['awb_response'])) {
                $awbresponse = $responseLabel['awb_response'];
                if (isset($awbresponse['success']) && $awbresponse['success'] == true) {
                    $pickupData = [
                        'awb' => $awbresponse['AWB'],
                        'shippingurl' => $awbresponse['shipping_url'],
                        'carrier_id' => $awbresponse['carrier_id'],
                        'carrier_name' => $awbresponse['carrier_name'],
                        'message' => $awbresponse['message'],
                    ];
                    // dd($trakingnumber,$pickupData);
                    Prashad_deliverys::where('order_id', $order_id)->update($pickupData);
                    $response = Helpers::ShipwayCreatemanifest($order_id);
                    // dd($response);
                    if (isset($response['status']) && $response['status'] == true) {
                        Prashad_deliverys::where('order_id', $order_id)->update([
                            'manifest_id' => $response['manifest_ids'],
                            'order_status' => 'processing'
                        ]);
                        Toastr::success(translate($response['message']));
                        return back();
                    } else if (isset($response['status']) && $response['status'] == false) {
                        $errorMessage = $response['message'];
                        Prashad_deliverys::where('order_id', $order_id)->update(['order_status' => 'processing']);
                        Toastr::error(translate($errorMessage));
                        return back();
                    }
                } else {
                    $pickupData = [
                        'order_status' => 'processing',
                        'message' => $awbresponse['error'] ?? 'An error occurred',
                    ];
                    Prashad_deliverys::where('order_id', $order_id)->update($pickupData);
                    Toastr::error(translate('Order_processing_not_to_delivery_portal'));
                    return back();
                }
            } else {
                Toastr::error(translate('AWB response missing in ShipWay response.'));
                return back();
            }
        } else if (isset($response['success']) && $response['success'] == true) {
            // ModelsOrder::where('id', $request->id)->update(['order_status' => 'processing']);
            Prashad_deliverys::where('order_id', $order_id)->update([
                'order_status' => 'processing',
                'message' => $response['message']
            ]);
            Toastr::error(translate('Order_proccessng_not_to_delivery_portal'));
            return back();
        }
        // }


    }
    public function shipwaycancel($order_id, Request $request)
    {
        // Extract request data
        $trackingId = $request->input('awb');
        $warehouseId = $request->input('warehouse_id');
        $partner = $request->input('delivery_partner');
        $orderId = $order_id;
        if ($partner == 'shipway') {
            $shipmentCancel = Helpers::ShipwayCancelShipment($trackingId);
            if (isset($shipmentCancel['success']) && $shipmentCancel['success'] === true) {
                Helpers::ShipWayorderChancel($orderId);
                $errorMessage = $shipmentCancel['message'] ?? 'Shipment canceled from Shipway successfully.';
                Prashad_deliverys::where('id', $orderId)->update([
                    'manifest_id' => null,
                    'shippingurl' => null,
                    'awb' => null,
                    'carrier_id' => null,
                    'carrier_name' => null,
                    'added_by' => 'vendor',
                    'order_status' => 'canceled',
                    'delivery_partner' => null,
                    'warehouse_id' => $warehouseId,
                    'delivery_charge' => null,
                    'message' => $errorMessage
                ]);
                Toastr::success(translate($shipmentCancel['message'] ?? 'Shipment canceled from Shipway successfully.'));
                return back();
            } else if (isset($shipmentCancel['error']) && $shipmentCancel['error'] === true) {
                $errorMessage = $shipmentCancel['message'] ?? 'Unknown error';
                Prashad_deliverys::where('id', $orderId)->update([
                    'manifest_id' => null,
                    'shippingurl' => null,
                    'awb' => null,
                    'carrier_id' => null,
                    'added_by' => 'vendor',
                    'carrier_name' => null,
                    'order_status' => 'canceled',
                    'delivery_partner' => null,
                    'warehouse_id' => $warehouseId,
                    'delivery_charge' => null,
                    'message' => $errorMessage
                ]);
                $invalidTrackingNumbers = $shipmentCancel['invalid_tracking_numbers'] ?? '';
                Toastr::error(translate('Failed to cancel shipment: ' . $errorMessage . ' ' . $invalidTrackingNumbers));
                return back();
            }
        }
        Toastr::error(translate('Unable to connect to Shipway or invalid delivery partner.'));
        return back();
    }
}