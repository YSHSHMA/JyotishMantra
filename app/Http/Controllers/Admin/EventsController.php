<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\EventApproTransactionRepositoryInterface;
use App\Contracts\Repositories\EventartistRepositoryInterface;
use App\Contracts\Repositories\EventCategoryRepositoryInterface;
use App\Contracts\Repositories\EventOrderRepositoryInterface;
use App\Contracts\Repositories\EventOrganizerRepositoryInterface;
use App\Contracts\Repositories\EventPackageRepositoryInterface;
use App\Contracts\Repositories\EventsRepositoryInterface;
use App\Contracts\Repositories\EventsReviewRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Contracts\Repositories\EventsLeadsRepositoryInterface;
use App\Enums\ViewPaths\Admin\EventsPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventartistRequest;
use App\Http\Requests\Admin\EventartistUpdateRequest;
use App\Http\Requests\Admin\EventsAddRequest;
use App\Http\Requests\Admin\EventsUpdateRequest;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Traits\Payment;
use App\Library\Receiver;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\User;
use App\Models\EventApproTransaction;
use App\Models\EventFollowup;
use App\Models\EventOrder;
use App\Models\EventOrganizer;
use App\Models\WalletTransaction;
use App\Models\Events;
use App\Models\ServiceTax;
use App\Services\EventsService;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Tree\Test\Util\Helper;

class EventsController extends Controller
{
    use FileManagerTrait;
    public function __construct(
        private readonly EventsRepositoryInterface       $EventsRepo,
        private readonly EventCategoryRepositoryInterface       $EventscategoryRepo,
        private readonly EventOrganizerRepositoryInterface       $EventOrganizeraRepo,
        private readonly EventPackageRepositoryInterface       $EventpackeRepo,
        private readonly TranslationRepositoryInterface      $translationRepo,

        private readonly EventartistRepositoryInterface      $EventartistRepo,
        private readonly EventApproTransactionRepositoryInterface     $EventapproRepo,
        private readonly EventOrderRepositoryInterface $EventOrder,
        private readonly EventsReviewRepositoryInterface $EventReviewRepo,
        private readonly EventsLeadsRepositoryInterface $eventleads,
    ) {}

    public function index()
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $category_list = $this->EventscategoryRepo->getListWhere(filters: ['status' => 1],dataLimit:"all");
        $organizer_list = $this->EventOrganizeraRepo->getListWhere(filters: ['status' => 1, 'is_approve' => 1],dataLimit:"all");
        $package_list = $this->EventpackeRepo->getListWhere(filters: ['status' => 1],dataLimit:"all");
        $artist_list = $this->EventartistRepo->getListWhere(filters: ['status' => 1],dataLimit:"all");
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(EventsPath::ADD[VIEW], compact('language', 'googleMapsApiKey', 'artist_list', 'defaultLanguage', 'category_list', 'organizer_list', 'package_list'));
    }

    public function store(EventsAddRequest $request, EventsService $service)
    {
        $array = $service->getAddData($request);
        $insert = $this->EventsRepo->add(data: $array);
        $this->translationRepo->add(request: $request, model: 'App\Models\Events', id: $insert->id);
        Toastr::success(translate('Events_added_successfully'));
        Helpers::editDeleteLogs('Event', 'Event', 'Insert');
        return redirect()->route(EventsPath::LIST[REDIRECT]);
    }

    public function list(Request $request)
    {
        $getData = $this->EventsRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'), filters: ['organizer' => $request->get('organizer'), 'is_approve' => $request->get('is_approve')], relations: ['categorys', 'eventArtist', 'EventOrder', 'organizers']);
        return view(EventsPath::LIST[VIEW], compact('getData'));
    }

    public function list_all(Request $request)
    {
        if (request()->segment(4) == 'pending') {
            $filters = ['organizer' => $request->get('organizer'), 'is_approve' => $request->get('is_approve'), 'status_and_isactive' => 1];
        } elseif (request()->segment(4) == 'booking') {
            $filters = ['organizer' => $request->get('organizer'), 'is_approve' => 1, 'status' => 1, 'global_event' => 1];
        } elseif (request()->segment(4) == 'upcomming') {
            $filters = ['organizer' => $request->get('organizer'), 'is_approve' => 1, 'status' => 1, 'upcomming' => 1];
        } elseif (request()->segment(4) == 'canceled') {
            $filters = ['organizer' => $request->get('organizer'), 'status' => 2];
        } elseif (request()->segment(4) == 'completed') {
            $filters = ['organizer' => $request->get('organizer'), 'status' => 1, 'is_approve' => 1, 'completed' => 1];
        } else {
            $filters = ['organizer' => $request->get('organizer')];
        }
        $getData = $this->EventsRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'), filters: $filters, relations: ['categorys', 'eventArtist', 'EventOrder', 'organizers']);
        return view(EventsPath::LIST1[VIEW], compact('getData'));
    }

    public function event_approvel(Request $request, $id, $status)
    {
        $data['is_approve'] = $status;
        if (!empty($request->get('amount'))) {
            $data['event_approve_amount'] = $request->get('amount');
            $data['approve_amount_status'] = 2;
            $old_data = $this->EventsRepo->getFirstWhere(params: ['id' => $id], relations: ['organizers']);
            $payer = new Payer(
                $old_data['organizers']['full_name'],
                $old_data['organizers']['email_address'],
                $old_data['organizers']['contact_number'],
                ''
            );

            $currency_model = Helpers::get_business_settings('currency_model');
            if ($currency_model == 'multi_currency') {
                $currency_code = 'USD';
            } else {
                $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                $currency_code = Currency::find($default)->code;
            }
            $additional_data['event_id'] = $old_data['organizers'];
            $additional_data['organizer_name'] = $old_data['organizers']['full_name'];
            $additional_data['organizer_phone'] = $old_data['organizers']['contact_number'];
            $additional_data['expiration'] = (date('Y-m-d H:i:s', strtotime('+9 hours')));
            $additional_data['business_name'] = 'Event Amount';
            $additional_data['business_logo'] = asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo');

            $payment_info = new PaymentInfo(
                success_hook: 'digital_payment_success_custom',
                failure_hook: 'digital_payment_fail',
                currency_code: $currency_code,
                payment_method: 'razor_paywithexpir',
                payment_platform: "web",
                payer_id: $old_data['id'],
                receiver_id: '100',
                additional_data: $additional_data,
                payment_amount: $request->get('amount'),
                external_redirect_link: route('payment.success-transaction'),
                attribute: 'event_approval_amount',
                attribute_id: idate("U"),
            );
            $receiver_info = new Receiver('receiver_name', 'example.png');
            $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);
            $getAlready = $this->EventapproRepo->getFirstWhere(params: ['organizer_id' => $old_data['event_organizer_id'], 'event_id' => $old_data['id']]);
            if (!$getAlready) {
                $this->EventapproRepo->add(data: ['amount' => $request->get('amount'), 'status' => 0, 'organizer_id' => $old_data['event_organizer_id'], 'event_id' => $old_data['id'], 'transction_link' => $redirect_link]);
            } else {
                $this->EventapproRepo->update(id: $getAlready['id'], data: ['amount' => $request->get('amount'), 'status' => 0, 'organizer_id' => $old_data['event_organizer_id'], 'event_id' => $old_data['id'], 'transction_link' => $redirect_link]);
            }
            // dd($redirect_link);
            if ($old_data && !empty($old_data['organizers']['email_address']) && filter_var($old_data['organizers']['email_address'], FILTER_VALIDATE_EMAIL)) {

                $data1 = [
                    'type' => 'event',
                    'email' => $old_data['organizers']['email_address'],
                    'subject' => 'Event Approval Amount Request',
                    'htmlContent' =>"<p>Event Name : " . $old_data['event_name'] . "</p>Event Approve Pay Link : <a href='" . $redirect_link . "' style='display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Pay Now</a>",
                ];

                Helpers::emailSendMessage($data1);
            }
        }
        $this->EventsRepo->update(id: $id, data: $data);
        Toastr::success(translate('Event_payment_link_sent_successfully'));
        return redirect()->route(EventsPath::INFORMATION[URI], [$id]);
    }

    public function UserRefund(Request $request, $id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $getData = $this->EventOrder->getListWhere(filters: ['event_id' => $id, 'transaction_status' => 1], relations: ['orderitem'], dataLimit: 'all');
            $eventInfo = $this->EventsRepo->getFirstWhere(params: ['id' => $id]);

            if (empty($eventInfo)) {
                Toastr::error(translate('invalid_event_data'));
                return back();
            }

            $getOrg = EventOrganizer::where('id', $eventInfo['event_organizer_id'])->first();
            if (empty($getOrg)) {
                Toastr::error(translate('this_event_organizer_not_found_please_check'));
                return back();
            }
            $amount = 0;
            $gets_amount = 0;
            $admin_amount = 0;

            if (!empty($getData) && count($getData) > 0) {
                foreach ($getData as $value) {
                    User::where('id', $value['user_id'])->update([
                        'wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance + ' . $value['amount'])
                    ]);
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $value['user_id'];
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Tour Refund';
                    $wallet_transaction->transaction_type = 'tour_refund';
                    $wallet_transaction->balance = User::where('id', $value['user_id'])->value('wallet_balance');
                    $wallet_transaction->credit = $value['amount'];
                    $wallet_transaction->save();
                    EventOrder::where('id', $value['id'])->update([
                        'refund_id' => "wallet",
                        'status' => 3
                    ]);
                    $amount += $value['amount'];
                    $gets_amount += $value['gst_amount'];
                    $admin_amount += $value['admin_commission'];

                    if (!empty($eventInfo['all_venue_data']) && json_decode($eventInfo['all_venue_data'], true)) {
                        $bookingSeats = json_decode($eventInfo['all_venue_data'], true);
                    } else {
                        $bookingSeats = '';
                    }
                    $booking_date_w_message = '';
                    $booking_time_w_message = '';
                    $venue_name_w_message = '';

                    if ($bookingSeats) {
                        $pn = 0;
                        foreach ($bookingSeats as $keys => $bo_se) {
                            $booking_date_w_message = $bo_se['date'];
                            $booking_time_w_message = $bo_se['start_time'];
                            $venue_name_w_message = $bo_se['en_event_cities'];
                            break;
                        }
                    }

                    $message_data['title_name'] = $eventInfo['event_name'];
                    $message_data['place_name'] = $venue_name_w_message;
                    $message_data['booking_date'] = date('Y-m-d', strtotime($booking_date_w_message));
                    $message_data['time'] = ($booking_time_w_message);
                    $message_data['orderId'] = $value['order_no'];
                    $message_data['final_amount'] = webCurrencyConverter(amount: (float)$value['amount'] ?? 0);
                    $message_data['customer_id'] =  $value['user_id'];
                    $message_data['number'] =  ($value['orderitem'][0]['no_of_seats'] ?? 0);
                    // Helpers::whatsappMessage('event', 'Event Canceled', $message_data);
                    \App\Jobs\SendWhatsappMessage::dispatch('event', 'Event Canceled', $message_data);
                    // Helpers::whatsappMessage('event', 'Event Canceled', $message_data);
                }
            }
            EventOrganizer::where('id', $eventInfo['event_organizer_id'])->update([
                'org_withdrawable_ready' => \Illuminate\Support\Facades\DB::raw('org_withdrawable_ready - ' . $amount),
                'org_total_commission' => \Illuminate\Support\Facades\DB::raw('org_total_commission - ' . $admin_amount),
                'org_total_tax' => \Illuminate\Support\Facades\DB::raw('org_total_tax - ' . $gets_amount),
            ]);
            $this->EventsRepo->update(id: $id, data: ['status' => 2]);
            \Illuminate\Support\Facades\DB::commit();

            Toastr::success(translate('refund_successfully'));
            return back();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Toastr::error(translate('something_went_wrong'));
            return back();
        }
    }

    public function changeStatus(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->EventsRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(Request $request, EventsService $service)
    {
        $old_data = $this->EventsRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            $service->deleteImage($old_data);
            $this->EventsRepo->delete(params: ['id' => $request['id']]);
            $this->translationRepo->delete('App\Models\Events', $request['id']);
            Toastr::success(translate('Event_Deleted_successfully'));
            Helpers::editDeleteLogs('Event', 'Event', 'Delete');
            return response()->json(['success' => 1, 'message' => translate('events_deleted_successfully')], 200);
        } else {
            Toastr::error(translate('Event_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Not_found_data')], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        if ($getData) {
            $language = getWebConfig(name: 'pnc_language') ?? null;
            $defaultLanguage = $language[0];
            $category_list = $this->EventscategoryRepo->getListWhere(filters: ['status' => 1],dataLimit:"all");
            $organizer_list = $this->EventOrganizeraRepo->getListWhere(filters: ['status' => 1, 'is_approve' => 1],dataLimit:"all");
            $package_list = $this->EventpackeRepo->getListWhere(filters: ['status' => 1],dataLimit:"all");
            $artist_list = $this->EventartistRepo->getListWhere(filters: ['status' => 1],dataLimit:"all");
            $googleMapsApiKey = config('services.google_maps.api_key');
            return view(EventsPath::UPDATE[VIEW], compact('language', 'googleMapsApiKey', 'artist_list', 'defaultLanguage', 'category_list', 'organizer_list', 'package_list', 'getData'));
        } else {
            Toastr::error(translate('Events_Data_Not_found'));
            return redirect()->route(EventsPath::LIST[REDIRECT]);
        }
    }

    public function edit(EventsUpdateRequest $request, EventsService $service, $id)
    {
        $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $id]);
        $array = $service->getUpdateData($request, $getData);
        if ($array) {
            $this->EventsRepo->update(id: $id, data: $array);
            $this->translationRepo->update(request: $request, model: 'App\Models\Events', id: $id);
            Helpers::editDeleteLogs('Event', 'Event', 'Update');
            Toastr::success(translate('Events_update_successfully'));
        } else {
            Toastr::error(translate('Event_update_failed_because_tickets_have_already_booked.'));
        }
        return redirect()->route(EventsPath::LIST[REDIRECT]);
    }

    public function information(Request $request, $id)
    {
        $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $id], relations: ['categorys', 'organizers']);

        return view(EventsPath::VIEW[VIEW], compact('getData'));
    }

    public function add_artist(Request $request)
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $getData = $this->EventartistRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(EventsPath::ARTIST[VIEW], compact('getData', 'language', 'defaultLanguage'));
    }

    public function artist_store(EventartistRequest $request, EventsService $service)
    {
        $array = $service->getAddartistData($request);
        $insert = $this->EventartistRepo->add(data: $array);
        $this->translationRepo->add(request: $request, model: 'App\Models\Eventartist', id: $insert->id);
        Toastr::success(translate('Events_Artist_added_successfully'));
        return redirect()->route(EventsPath::ARTIST[REDIRECT]);
    }

    public function artist_statuschange(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->EventartistRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function artist_delete(Request $request, EventsService $service)
    {
        $old_data = $this->EventartistRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            $service->deleteImageartist($old_data);
            $this->EventartistRepo->delete(params: ['id' => $request['id']]);
            $this->translationRepo->delete('App\Models\Eventartist', $request['id']);
            Toastr::success(translate('Event_artist_Deleted_successfully'));
            return response()->json(['success' => 1, 'message' => translate('events_deleted_successfully')], 200);
        } else {
            Toastr::error(translate('Event_artist_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Not_found_data')], 400);
        }
    }

    public function artist_update(Request $request, $id)
    {
        $getData = $this->EventartistRepo->getFirstWhere(params: ['id' => $request['id']], relations: ['translations']);
        if ($getData) {
            $language = getWebConfig(name: 'pnc_language') ?? null;
            $defaultLanguage = $language[0];
            return view(EventsPath::ARTIST_UPDATE[VIEW], compact('getData', 'language', 'defaultLanguage'));
        } else {
            Toastr::error(translate('Event_artist_Not_Found'));
            return redirect()->route(EventsPath::ARTIST[REDIRECT]);
        }
    }

    public function artist_edit(EventartistUpdateRequest $request, EventsService $service, $id)
    {
        $getData = $this->EventartistRepo->getFirstWhere(params: ['id' => $request['id']]);
        $array = $service->getUpdateartistData($request, $getData);
        $this->EventartistRepo->update(id: $id, data: $array);
        $this->translationRepo->update(request: $request, model: 'App\Models\Eventartist', id: $id);
        Toastr::success(translate('Event_artist_Update_Successfully'));
        return redirect()->route(EventsPath::ARTIST[REDIRECT]);
    }

    public function event_details(Request $request, $id = null)
    {
        $name = $request['name'] ?? "null";
        $view_type = (($id == null) ? 1 : 2);
        if (!empty($id)) {
            $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $request['id']], relations: ['organizers']);
            $order_list = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('name') == 'order') ? $request->get('searchValue') : ""), relations: ['eventid', 'userdata'], filters: [
                'event_id' => $id,
                'order_status' => $request->get('order-status') ?? 1,
                'status' => 1,
                'venue_id' => (($request->get('name') == 'order') ? ($request->get('venue_id') ?? '') : "")
            ], dataLimit: getWebConfig(name: 'pagination_limit'));
            $withdrowalTransaction = \App\Models\WithdrawalAmountHistory::where('type', 'event')->where('vendor_id', $id)->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));
        } else {
            $getData = $this->EventOrganizeraRepo->getFirstWhere(params: ['id' => $request->get('organizer')]);
            $order_list = $this->EventapproRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), relations: ['EventData'], filters: ['organizer_id' => $request->get('organizer')], dataLimit: getWebConfig(name: 'pagination_limit'));
            $withdrowalTransaction = \App\Models\WithdrawalAmountHistory::where('type', 'event')->where('vendor_id', $request->get('organizer'))->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));
        }

        $getevent = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('name') == 'apevent') ? $request->get('searchValue') : ""), relations: ['eventid'], filters: [
            'transaction_status' => 1,
            'status' => 1,
            'groupby_event' => 1,
            'organizer_id' => $request->get('organizer'),
            'start_to_end_date' => (($request->get('name') == 'apevent') ? $request->get('start_to_end_date') : "")
        ], dataLimit: getWebConfig(name: 'pagination_limit'));

        if ($request->get('name') == 'review') {
            $event_reviews = $this->EventReviewRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), relations: ['userdata'], filters: ['event_id' => $request['id']], dataLimit: getWebConfig(name: 'pagination_limit'));
        } else {
            $event_reviews = $this->EventReviewRepo->getListWhere(orderBy: ['id' => 'desc'], relations: ['userdata'], filters: ['event_id' => $request['id']], dataLimit: getWebConfig(name: 'pagination_limit'));
        }
        if ($request->get('name') == 'refund') {
            $order_refund_list = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), relations: ['eventid', 'userdata'], filters: ['event_id' => ($id ?? ''), 'order_status' => $request->get('order-status') ?? 1,  'status' => 3, 'venue_id' => ($request->get('venue_id') ?? '')], dataLimit: getWebConfig(name: 'pagination_limit'));
        } else {
            $order_refund_list = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], relations: ['eventid', 'userdata'], filters: ['event_id' => ($id ?? ''), 'order_status' => $request->get('order-status') ?? 1,  'status' => 3, 'venue_id' => ($request->get('venue_id') ?? '')], dataLimit: getWebConfig(name: 'pagination_limit'));
        }

        return view(EventsPath::OVERALL[VIEW], compact('name', 'event_reviews', 'getevent', 'order_refund_list', 'view_type', 'getData', 'order_list', 'withdrowalTransaction'));
    }

    public function commission_update(Request $request, EventsService $service, $id)
    {
        $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $id]);
        if ($getData) {
            $array = $service->getUpdateCommissionData($request, $getData);
            $insert = $this->EventsRepo->update(id: $id, data: $array);
        }
        Toastr::success(translate('Events_update_successfully'));
        return redirect()->route(EventsPath::OVERALL[REDIRECT], [$id]);
    }



    public function CommentStatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->EventReviewRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function event_order_view(Request $request)
    {
        $getData = EventOrder::with(['orderitem', 'eventid'])->find($request['order_id']);
        if ($getData) {
            $html = '<table class="table order-item-table">';
            $html .= '<thead>
                    <tr>
                        <td>Sno.</td>
                        <td>package Name</td>
                        <td>No of seats</td>
                        <td>Amount</td>
                        <td>Final Amount</td>
                        <td>Print</td>
                    </tr>
                </thead>
                <tbody>';
            if ($getData['orderitem']) {
                $p = 1;
                foreach ($getData['orderitem'] as $key => $value) {
                    $html .= "
                                    <tr>
                                        <td>" . $p . "</td>
                                        <td>" . ($value['category']['package_name'] ?? "") . "</td>
                                        <td>" . $value['no_of_seats'] . "</td>
                                        <td>" . ($value['amount'] / $value['no_of_seats'] ?? 0) . "</td>
                                        <td>" . $value['amount'] . "</td>
                                        <td><i class='tio-print'></i></td>
                                    </tr>";
                    $p++;
                }
            }
            $html .= '</tbody>
        </table>';
            return response()->json(['success' => 1, 'data' => $html]);
        } else {
            return response()->json(['success' => 0, 'data' => '']);
        }
    }


    public function SendRequestReject()
    {
        $getData = EventApproTransaction::where('status', 0)->where('updated_at', '>', (date('Y-m-d H:i:s', strtotime('-9 hours'))))->get();
        if ($getData) {
            foreach ($getData as $value) {
                Events::where('id', $value['event_id'])->update(['is_approve' => 4, 'approve_amount_status' => 3]);
            }
        }
        return response()->json(['success' => 1, 'data' => [], 'message' => 'update Successfully']);
    }

    public function RequestApproveAmount(Request $request, $id, $status)
    {
        $getData =  EventOrganizer::where(['id' => $id, 'is_approve' => 1, 'status' => 1])->first();
        if (!empty($getData)) {
            if (($getData['org_withdrawable_pending'] <= $getData['org_withdrawable_ready']) && $status == 1) {
                EventOrganizer::where(['id' => $id, 'is_approve' => 1, 'status' => 1])->update(['org_withdrawable_pending' => 0, 'org_withdrawable_ready' => ($getData['org_withdrawable_ready'] - $getData['org_withdrawable_pending']), 'org_collected_cash' => ($getData['org_collected_cash'] + $getData['org_withdrawable_pending'])]);
                EventApproTransaction::where('types', 'withdrawal')->where(['organizer_id' => $id, 'status' => 0])->update(['status' => $status, 'transaction_id' => "manual transaction"]);
                Toastr::success(translate('Amount_transaction_successfully'));
            } else {
                if ($status == 2) {
                    Toastr::success(translate('Request_Reject_successfully'));
                } else {
                    Toastr::error(translate('please_send_currct_Amount'));
                }
                EventOrganizer::where(['id' => $id, 'is_approve' => 1, 'status' => 1])->update(['org_withdrawable_pending' => 0]);
                EventApproTransaction::where('types', 'withdrawal')->where(['organizer_id' => $id, 'status' => 0])->update(['status' => 3]);
            }
        }
        return redirect()->route(EventsPath::PAYREQ[REDIRECT], ['organizer' => $id]);
    }

    public function EventLeads(Request $request)
    {
        $EventLeads = $this->eventleads->getListWhere(filters: ['status' => [0, 2], 'test' => $request->get('test') ?? 1], searchValue: $request->get('searchValue'), relations: ['event', 'package', 'followby'], orderBy: ['id' => 'desc'], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(EventsPath::LEADS[VIEW], compact('EventLeads'));
    }

    public function EventLeadsDelete(Request $request)
    {
        $lead = $this->eventleads->getFirstWhere(params: ['id' => $request->id]);
        if ($lead) {
            $lead->delete();
            Toastr::success(translate('lead_Delete_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function EventLeadsFollow($id)
    {
        $followlist = EventFollowup::where('lead_id', $id)->get();
        if ($followlist) {
            return response()->json($followlist);
        } else {
            return response()->json([], 200);
        }
    }

    public function EventLeadsFollowUp(Request $request)
    {
        $follows = [
            'lead_id' => $request->input('lead_id'),
            'message' => $request->input('message'),
            'last_date' => $request->input('last_date'),
            'next_date' => $request->input('next_date'),
            'follow_by' => $request->input('follow_by'),
            'follow_by_id' => $request->input('follow_by_id'),
            'type' => $request->input('type') ?? "",
        ];
        EventFollowup::create($follows);
        Toastr::success(translate('lead_follow_up_successfully'));
        return back();
    }

    public function BookingList(Request $request)
    {
        $order_list = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'all') ? $request->get('searchValue') : ""), relations: ['eventid', 'userdata'], filters: [
            'transaction_status' => 1,
            'status' => 1,
            'start_to_end_date' => (($request->get('show') == 'all') ? $request->get('start_to_end_date') : "")
        ], dataLimit: getWebConfig(name: 'pagination_limit'));

        $order_list_array = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'all') ? $request->get('searchValue') : ""), relations: ['eventid', 'userdata'], filters: [
            'transaction_status' => 1,
            'status' => 1,
            'start_to_end_date' => (($request->get('show') == 'all') ? $request->get('start_to_end_date') : "")
        ], dataLimit: 'all');
        $order_array = [
            'amount' => 0,
            'coupon_amount' => 0,
            'admin_commission' => 0,
            'gst_amount' => 0,
            'final_amount' => 0
        ];
        if ($order_list_array) {
            foreach ($order_list_array as $k => $val) {
                $order_array['amount'] += $val['amount'];
                $order_array['coupon_amount'] += $val['coupon_amount'];
                $order_array['admin_commission'] += $val['admin_commission'];
                $order_array['gst_amount'] += $val['gst_amount'];
                $order_array['final_amount'] += $val['final_amount'];
            }
        }
        $getevent = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'event') ? $request->get('searchValue') : ""), relations: ['eventid'], filters: [
            'transaction_status' => 1,
            'status' => 1,
            'groupby_event' => 1,
            'start_to_end_date' => (($request->get('show') == 'event') ? $request->get('start_to_end_date') : "")
        ], dataLimit: getWebConfig(name: 'pagination_limit'));



        $event_list_array = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'event') ? $request->get('searchValue') : ""), relations: ['eventid'], filters: [
            'transaction_status' => 1,
            'status' => 1,
            'groupby_event' => 1,
            'start_to_end_date' => (($request->get('show') == 'event') ? $request->get('start_to_end_date') : "")
        ], dataLimit: "all");

        $event_array = [
            'amount' => 0,
            'coupon_amount' => 0,
            'admin_commission' => 0,
            'gst_amount' => 0,
            'final_amount' => 0
        ];
        if ($event_list_array) {
            foreach ($event_list_array as $k => $val) {
                $event_array['amount'] += $val['amount'];
                $event_array['coupon_amount'] += $val['coupon_amount'];
                $event_array['admin_commission'] += $val['admin_commission'];
                $event_array['gst_amount'] += $val['gst_amount'];
                $event_array['final_amount'] += $val['final_amount'];
            }
        }
        return view(EventsPath::BookingLIST[VIEW], compact('order_list', 'order_array', 'getevent', 'event_array'));
    }

    public function WithdrawalList(Request $request)
    {
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['type' => "event"])->with(['EventOrg'])
            ->when((isset($request['approved']) && $request['approved'] != 'all'), function ($query) use ($request) {
                return $query->where(['status' => (($request['approved'] == "approved") ? 1 : (($request['approved'] == "denied") ? 2 : 0))]);
            })
            ->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'), ['*'], 'page');
        return view("admin-views.events.withdrawal.index", compact('withdrawRequests'));
    }

    public function WithdrawalReqView(Request $request)
    {
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['type' => "event"])->with(['EventOrg'])->where('id', $request['id'])->first();
        return view('admin-views.events.withdrawal.view', compact('withdrawRequests'));
    }

    public function WithdrawalReqReject(Request $request)
    {
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['type' => "event"])->with(['EventOrg'])->where('id', $request['id'])->first();
        if ($withdrawRequests) {
            if ($withdrawRequests['ex_id'] == 0) {
                \App\Models\EventOrganizer::where('id', $withdrawRequests['vendor_id'])->update(['org_withdrawable_pending' => 0]);
            } else {
            }
            \App\Models\WithdrawalAmountHistory::where('id', $request['id'])->update(['status' => 2]);
            Toastr::success('pay_Request_Reject Successfully');
            return back();
        }
        Toastr::success('pay_Request_Reject Failed');
        return back();
    }

    public function RazorpaycreateContact(Request $request, $id, $type)
    {
        try {
            $get_Razorpay = \App\Models\Setting::where('key_name', 'razor_pay')->first(); //$this->settingRepo->getFirstWhere(params: ['key_name' => 'razor_pay']);
            $RAZORPAY_KEY_ID = '';
            $RAZORPAY_KEY_SECRET = '';
            $RAZORPAY_ACCOUNT_NO = '';
            if ($get_Razorpay['mode'] == 'live') {
                $RAZORPAY_KEY_ID = $get_Razorpay['live_values']['api_key'];
                $RAZORPAY_KEY_SECRET = $get_Razorpay['live_values']['api_secret'];
                $RAZORPAY_ACCOUNT_NO = $get_Razorpay['live_values']['account_number'] ?? '';
            } else {
                $RAZORPAY_KEY_ID = $get_Razorpay['live_values']['api_key'];
                $RAZORPAY_KEY_SECRET = $get_Razorpay['live_values']['api_secret'];
                $RAZORPAY_ACCOUNT_NO = $get_Razorpay['live_values']['account_number'] ?? '';
            }
            $api = new \Razorpay\Api\Api($RAZORPAY_KEY_ID, $RAZORPAY_KEY_SECRET);

            $getWithdrawal_recode = \App\Models\WithdrawalAmountHistory::where(['type' => "event"])->with(['EventOrg'])->where('id', $id)->first();
            $email = $getWithdrawal_recode['EventOrg']['email_address'];
            $contact = $getWithdrawal_recode['EventOrg']['contact_number'];

            $url = "https://api.razorpay.com/v1/contacts";

            $data = [
                "name" => $getWithdrawal_recode['EventOrg']['full_name'],
                "email" => $email,
                "contact" => $contact,
                "type" => "vendor"
            ];
            $headers = [
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode("$RAZORPAY_KEY_ID:$RAZORPAY_KEY_SECRET")
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode == 200 || $httpCode == 201) {
                $contact_data = json_decode($response, true);
            } else {
                if ($type != 'manual') {
                    return ["error" => "Failed to create contact", "response" => json_decode($response, true)];
                }
            }

            if ($type == 'bank') {
                $fundAccount = $api->fundAccount->create([
                    "account_type" => "bank_account",
                    "contact_id" => $contact_data['id'],
                    "bank_account" => [
                        "name" => $getWithdrawal_recode['holder_name'],
                        "ifsc" => $getWithdrawal_recode['ifsc_code'],
                        "account_number" => $getWithdrawal_recode['account_number']
                    ]
                ]);
            } elseif ($type == 'manual') {
                if ($getWithdrawal_recode['ex_id'] == 0) {
                    \App\Models\EventOrganizer::where('id', $getWithdrawal_recode['vendor_id'])->update(['org_withdrawable_pending' => 0, 'org_collected_cash' => \Illuminate\Support\Facades\DB::raw('org_collected_cash + ' . $getWithdrawal_recode['req_amount']), 'org_withdrawable_ready' => \Illuminate\Support\Facades\DB::raw('org_withdrawable_ready - ' . $getWithdrawal_recode['req_amount'])]);
                } else {
                }
                \App\Models\WithdrawalAmountHistory::where('id', $id)->update([
                    'status' => 1,
                    'transcation_id' => $request['transcation_id'] ?? '',
                    'approval_amount' => $getWithdrawal_recode['req_amount'],
                    'payment_method' => 'manual'
                ]);
                Toastr::success('Payment transferred successfully');
                return back();
            } else {
                $fundAccount = $api->fundAccount->create([
                    "account_type" => "vpa",
                    "contact_id" => $contact_data['id'],
                    "vpa" => [
                        "address" => $getWithdrawal_recode['upi_code']
                    ]
                ]);
            }

            $fund_account_id = $fundAccount['id'];
            $data_fund_tans = [
                'account_number' => $RAZORPAY_ACCOUNT_NO,
                'fund_account_id' => $fund_account_id,
                'amount' => $getWithdrawal_recode['req_amount'],
                'currency' => 'INR',
                'mode' => (($type == 'upi') ? 'UPI' : 'IMPS'),
                'purpose' => 'payout',
                'queue_if_low_balance' => true,
                'reference_id' => 'Payout123',
                'narration' => 'Payment for service',
                "notes" => [
                    "notes_key_1" => "Tea, Earl Grey, Hot",
                    "notes_key_2" => "Tea, Earl Grey… decaf."
                ]
            ];

            $headers_fund_tans = [
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode("$RAZORPAY_KEY_ID:$RAZORPAY_KEY_SECRET")
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/payouts");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_fund_tans));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_fund_tans);
            curl_setopt($ch, CURLOPT_VERBOSE, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
                return back();
            }
            curl_close($ch);
            if ($httpCode == 200 || $httpCode == 201) {
                if ($getWithdrawal_recode['ex_id'] == 0) {
                    \App\Models\EventOrganizer::where('id', $getWithdrawal_recode['vendor_id'])->update(['org_withdrawable_pending' => 0, 'org_collected_cash' => \Illuminate\Support\Facades\DB::raw('org_collected_cash + ' . $getWithdrawal_recode['req_amount'])]);
                } else {
                }
                \App\Models\WithdrawalAmountHistory::where('id', $id)->update(
                    [
                        'status' => 1,
                        'transcation_id' => '',
                        'approval_amount' => $getWithdrawal_recode['req_amount'],
                        'payment_method' => $type
                    ]
                );
                Toastr::success('Payment transferred successfully');
                return back();
            } else {
                if ($getWithdrawal_recode['ex_id'] == 0) {
                    \App\Models\EventOrganizer::where('id', $getWithdrawal_recode['vendor_id'])->update(['org_withdrawable_pending' => 0]);
                } else {
                }
                \App\Models\WithdrawalAmountHistory::where('id', $id)->update(['status' => 2]);
                return ["error" => "Failed to payouts", "response" => json_decode($response, true)];
            }
        } catch (\Exception $e) {
            Toastr::error('Payment transferred failed');
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function EventOrderDetails(Request $request)
    {
        $getData =   $this->EventOrder->getFirstWhere(params: ['id' => $request['id']], relations: ['orderitem', 'userdata', 'eventid']);
        if ($getData) {
            return view('admin-views.events.booking.details', compact('getData'));
        } else {
            Toastr::error('Not found');
            return back();
        }
    }

    public function EventBookingInvoice(Request $request)
    {
        $orderData = EventOrder::where('id', $request['id'])->with(['orderitem', 'eventid', 'userdata', 'coupon'])->first();
        if ($orderData) {
            $mpdf_view = \Illuminate\Support\Facades\View::make('web-views.event.pdf.invoice', compact('orderData'));
            Helpers::gen_mpdf($mpdf_view, 'event_order_', $request['id']);
        } else {
            Toastr::error('Not found');
            return back();
        }
    }
}
