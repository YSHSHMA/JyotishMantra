<?php

namespace App\Http\Controllers\RestAPI\v3\seller;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\DarshanOrder;
use App\Models\DarshanOrderMembers;
use App\Models\DonateAds;
use App\Models\DonateAllTransaction;
use App\Models\DonateCategory;
use App\Models\DonateTrust;
use App\Models\PaymentRequest;
use App\Models\Purohit;
use App\Models\Seller;
use App\Models\Temple;
use App\Models\TempleOrderDetails;
use App\Models\TrustPuja;
use App\Models\TrustPujaOrder;
use App\Models\User;
use App\Models\VendorEmployees;
use App\Services\DonateTrustService;
use App\Utils\Helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class TrustController extends Controller
{

    public function Dashboard(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found darshan Order Data', 'recode' => 0, 'data' => []], 400);
        }
        $tourInformation = DonateTrust::where('id', $request->seller['relation_id'])->first();
        $dashboardData = [
            'totalEarning' => $tourInformation['trust_total_amount'] ?? 0,
            'pendingWithdraw' => $tourInformation['trust_req_withdrawal_amount'] ?? 0,
            "adminCommission" => $tourInformation['admin_commission'] ?? 0,
            "withdrawn" => $tourInformation['trust_total_withdrawal'] ?? 0,
            "gst_total_amount" => $tourInformation['gst_total_amount'] ?? 0
        ];
        $trustDonate = [
            'ads_approve' => (\App\Models\DonateAds::where('is_approve', 1)->where('trust_id', $request->seller['relation_id'])->count() ?? 0),
            "number_of_donated" => (\App\Models\DonateAllTransaction::whereIn('type', ['donate_ads', 'donate_trust'])->where('amount_status', 1)->where('trust_id', $request->seller['relation_id'])->count() ?? 0),
            "trust_donated" => (\App\Models\DonateAllTransaction::whereIn('type', ['donate_trust'])->where('amount_status', 1)->where('trust_id', $request->seller['relation_id'])->sum('final_amount') ?? 0),
            "ad_donated" => (\App\Models\DonateAllTransaction::whereIn('type', ['donate_ads'])->where('amount_status', 1)->where('trust_id', $request->seller['relation_id'])->sum('final_amount') ?? 0)
        ];
        $vipdarshan = [
            'total_temple' => (\App\Models\Temple::where('trust_id', $request->seller['relation_id'])->count() ?? 0),
            "total_booking" => (\App\Models\DarshanOrder::with(['Temple'])->where('status', 1)->whereHas('Temple', function ($q) use ($request) {
                $q->where('trust_id', $request->seller['relation_id']);
            })
                ->count() ?? 0),
            "total_amount" => (\App\Models\DarshanOrder::with(['Temple'])->where('status', 1)->whereHas('Temple', function ($q) use ($request) {
                $q->where('trust_id', $request->seller['relation_id']);
            })->sum('final_amount') ?? 0),
        ];
        $darshanOrd = DarshanOrder::where('status', 1)->with(['Temple'])->whereHas('Temple', function ($q) use ($request) {
            $q->where('trust_id', $request->seller['relation_id']);
        });
        $Ordersdarshan = [
            'today_darshan' => (clone $darshanOrd)->whereRaw("STR_TO_DATE(date, '%d-%m-%Y') = ?", [date('Y-m-d')])->count() ?? 0,
            'complete_darshan' => (clone $darshanOrd)->whereRaw("STR_TO_DATE(date, '%d-%m-%Y') < ?", [date('Y-m-d')])->count() ?? 0,
            'new_darshan' => (clone $darshanOrd)->whereRaw("STR_TO_DATE(date, '%d-%m-%Y') > ?", [date('Y-m-d')])->count() ?? 0,
        ];
        if ($tourInformation) {
            return response()->json(['status' => 1, 'message' => 'get Dashboard', 'recode' => 1, 'data' => ['wallet' => $dashboardData, 'trust' => $trustDonate, 'darshan' => $vipdarshan, 'darshanorder' => $Ordersdarshan]], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function TrustPujaDashboard(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found darshan Order Data', 'recode' => 0, 'data' => []], 400);
        }
        $getOrderHeader = \App\Models\TempleOrderMaster::with('temple')->where('trust_id', $request->seller['relation_id'])->whereHas('details', function ($q1) use ($request) {
            $q1->when(($request['type'] == 'trust_employee'), function ($q) use ($request) {
                $q->where('purohit_id', $request->seller['purohit_id'])
                    ->where('emp_id', $request->seller['id']);
            })->when(($request['type'] == 'purohit'), function ($q) use ($request) {
                $q->where('purohit_id', $request->seller['id']);
            });
        })->when(($request['temple_id']), function ($q) use ($request) {
            $q->where('temple_id', $request['temple_id']);
        })->where('booking_status', 'confirmed');

        $getData = [];
        $getRecodes = (clone $getOrderHeader)->get()->pluck('temple.package_service')->filter()
            ->flatMap(function ($services) {
                return json_decode($services, true);
            })->unique('name')->pluck('name')->values()->toArray();

        if ($getRecodes) {
            foreach ($getRecodes as $nn) {
                $getData['card2'][$nn] = (string)(\App\Models\TempleOrderDetails::where('type', $nn)->where('payment_status', 1)->where('trust_id', $request->seller['relation_id'])
                    ->when(($request['type'] == 'trust_employee'), function ($q) use ($request) {
                        $q->where('emp_id', $request->seller['id'])->where('purohit_id', $request->seller['purohit_id']);
                        $q->where('type', 'puja');
                    })->when(($request['type'] == 'purohit'), function ($q) use ($request) {
                        $q->where('purohit_id', $request->seller['id']);
                        $q->where('type', 'puja');
                    })->count() ?? 0);
            }
        }
        $getData['card2']['cash_order'] = (string)(\App\Models\TempleOrderMaster::where('payment_mode', 'cash')->where('payment_status', 1)->where('trust_id', $request->seller['relation_id'])->whereHas('details', function ($q1) use ($request) {
            $q1->when(($request['type'] == 'trust_employee'), function ($q) use ($request) {
                $q->where('emp_id', $request->seller['id'])->where('purohit_id', $request->seller['purohit_id']);
                $q->where('type', 'puja');
            })->when(($request['type'] == 'purohit'), function ($q) use ($request) {
                $q->where('purohit_id', $request->seller['id']);
                $q->where('type', 'puja');
            });
        })->count() ?? 0);
        $getData['card2']['online_order'] = (string)(\App\Models\TempleOrderMaster::where('payment_mode', 'online')->where('payment_status', 1)->where('trust_id', $request->seller['relation_id'])->whereHas('details', function ($q1) use ($request) {
            $q1->when(($request['type'] == 'trust_employee'), function ($q) use ($request) {
                $q->where('emp_id', $request->seller['id'])->where('purohit_id', $request->seller['purohit_id']);
                $q->where('type', 'puja');
            })->when(($request['type'] == 'purohit'), function ($q) use ($request) {
                $q->where('purohit_id', $request->seller['id']);
                $q->where('type', 'puja');
            });
        })->count() ?? 0);
        if ($request['type'] == 'trust_employee' || $request['type'] == 'purohit') {
            $getData['card2']['cash_amount'] =  (string)(\App\Models\TempleOrderMaster::where('payment_mode', 'cash')->where('payment_status', 1)->where('trust_id', $request->seller['relation_id'])->whereHas('details', function ($q1) use ($request) {
                $q1->when(($request['type'] == 'trust_employee'), function ($q) use ($request) {
                    $q->where('emp_id', $request->seller['id'])->where('purohit_id', $request->seller['purohit_id']);
                    $q->where('type', 'puja');
                })->when(($request['type'] == 'purohit'), function ($q) use ($request) {
                    $q->where('purohit_id', $request->seller['id']);
                });
            })->withSum(['details as total_base_price' => function ($q) {
                $q->where('type', 'puja');
            }], 'base_price')->get()->sum('total_base_price'));
            $getData['card2']['online_amount'] = (string)(\App\Models\TempleOrderMaster::where('payment_mode', 'online')->where('payment_status', 1)->where('trust_id', $request->seller['relation_id'])->whereHas('details', function ($q1) use ($request) {
                $q1->when(($request['type'] == 'trust_employee'), function ($q) use ($request) {
                    $q->where('emp_id', $request->seller['id'])->where('purohit_id', $request->seller['purohit_id']);
                    $q->where('type', 'puja');
                })->when(($request['type'] == 'purohit'), function ($q) use ($request) {
                    $q->where('purohit_id', $request->seller['id']);
                });
            })->withSum(['details as total_base_price' => function ($q) {
                $q->where('type', 'puja');
            }], 'base_price')->get()->sum('total_base_price'));
        } else {
            $getData['card2']['cash_amount'] =  (string)(\App\Models\TempleOrderMaster::where('payment_mode', 'cash')->where('payment_status', 1)->where('trust_id', $request->seller['relation_id'])->sum('total_amount') ?? 0);
            $getData['card2']['online_amount'] = (string)(\App\Models\TempleOrderMaster::where('payment_mode', 'online')->where('payment_status', 1)->where('trust_id', $request->seller['relation_id'])->sum('total_amount') ?? 0);
        }


        if ($request['type'] == 'trust_employee' || $request['type'] == 'purohit') {
            $getData['card1'] = [
                'totaltoday' => (string)((clone $getOrderHeader)->whereDate('created_at', date('Y-m-d'))->withSum(['details as total_base_price' => function ($q) {
                    $q->where('type', 'puja');
                }], 'base_price')->get()->sum('total_base_price')),
                'totaltoday_cash' => (string)((clone $getOrderHeader)->where('payment_mode', 'cash')->whereDate('created_at', date('Y-m-d'))->withSum(['details as total_base_price' => function ($q) {
                    $q->where('type', 'puja');
                }], 'base_price')->get()->sum('total_base_price')),
                'totaltoday_online' => (string)((clone $getOrderHeader)->where('payment_mode', 'online')->whereDate('created_at', date('Y-m-d'))->withSum(['details as total_base_price' => function ($q) {
                    $q->where('type', 'puja');
                }], 'base_price')->get()->sum('total_base_price')),
                'totalmonth' => (string)((clone $getOrderHeader)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->withSum(['details as total_base_price' => function ($q) {
                    $q->where('type', 'puja');
                }], 'base_price')->get()->sum('total_base_price')),
                'totalmonth_cash' => (string)((clone $getOrderHeader)->where('payment_mode', 'cash')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->withSum(['details as total_base_price' => function ($q) {
                    $q->where('type', 'puja');
                }], 'base_price')->get()->sum('total_base_price')),
                'totalmonth_online' => (string)((clone $getOrderHeader)->where('payment_mode', 'online')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->withSum(['details as total_base_price' => function ($q) {
                    $q->where('type', 'puja');
                }], 'base_price')->get()->sum('total_base_price')),
                'totalyear' => (string)((clone $getOrderHeader)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->withSum(['details as total_base_price' => function ($q) {
                    $q->where('type', 'puja');
                }], 'base_price')->get()->sum('total_base_price')),
                'totalyear_cash' => (string)((clone $getOrderHeader)->where('payment_mode', 'cash')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->withSum(['details as total_base_price' => function ($q) {
                    $q->where('type', 'puja');
                }], 'base_price')->get()->sum('total_base_price')),
                'totalyear_online' => (string)((clone $getOrderHeader)->where('payment_mode', 'online')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->withSum(['details as total_base_price' => function ($q) {
                    $q->where('type', 'puja');
                }], 'base_price')->get()->sum('total_base_price')),
                'totalreceipt' => (string)((clone $getOrderHeader)->whereHas('details', function ($q) {
                    $q->where('type', 'puja');
                    $q->where('print_status', '1');
                })->count())
            ];
        } else {
            $getData['card1'] = [
                'totaltoday' => (string)(clone $getOrderHeader)->whereDate('created_at', date('Y-m-d'))->sum('total_amount'),
                'totaltoday_cash' => (string)(clone $getOrderHeader)->where('payment_mode', 'cash')->whereDate('created_at', date('Y-m-d'))->sum('total_amount'),
                'totaltoday_online' => (string)(clone $getOrderHeader)->where('payment_mode', 'online')->whereDate('created_at', date('Y-m-d'))->sum('total_amount'),
                'totalmonth' => (string)(clone $getOrderHeader)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->sum('total_amount'),
                'totalmonth_cash' => (string)(clone $getOrderHeader)->where('payment_mode', 'cash')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->sum('total_amount'),
                'totalmonth_online' => (string)(clone $getOrderHeader)->where('payment_mode', 'online')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->sum('total_amount'),
                'totalyear' => (string)(clone $getOrderHeader)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->sum('total_amount'),
                'totalyear_cash' => (string)(clone $getOrderHeader)->where('payment_mode', 'cash')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->sum('total_amount'),
                'totalyear_online' => (string)(clone $getOrderHeader)->where('payment_mode', 'online')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->sum('total_amount'),
                'totalreceipt' =>  (string)((clone $getOrderHeader)->whereHas('details', function ($q) {
                    $q->where('print_status', '1');
                })->count())
            ];
        }

        if ($request['type'] == 'purohit') {
            $getTrust = Purohit::where('id', $request->seller['id'])->first();
        } elseif ($request['type'] == 'trust_employee') {
            $getTrust = VendorEmployees::where('id', $request->seller['id'])->first();
        } elseif ($request['type'] == 'trust') {
            $getTrust = DonateTrust::select('trust_total_amount as withdrawal_amount', 'trust_total_withdrawal as collected_amount', 'trust_req_withdrawal_amount as requested_amount', 'admin_commission as platform_fee', 'gst_total_amount as gst_amount', 'purohit_collected_amount')->where('id', $request->seller['relation_id'])->first();
        }
        $getData['card3'] = [
            'totalEarning' => (string)($getTrust['withdrawal_amount'] ?? 0),
            'pendingWithdraw' => (string)($getTrust['requested_amount'] ?? 0),
            'gstamount' => (string)($getTrust['gst_amount'] ?? 0),
            'trustfree' => (string)($getTrust['trust_fee'] ?? 0),
            'platformfree' => (string)($getTrust['platform_fee'] ?? 0),
            'collectedamount' => (string)($getTrust['collected_amount'] ?? 0),
            'trust_panding_amount' => (string)(\App\Models\PanditTransectionHistory::where('trust_id', $request->seller['relation_id'])
                ->when($request['type'] == 'purohit', function ($q) use ($request) {
                    $q->where('purohit_id', $request->seller['id']);
                })
                ->when($request['type'] == 'trust_employee', function ($q) use ($request) {
                    $q->where('purohit_id', $request->seller['purohit_id'])
                        ->where('emp_id', $request->seller['id']);
                })->where('status', 0)->sum('debit')),
        ];
        return response()->json(['status' => 1, 'message' => 'Successfully', 'data' => $getData], 200);
    }

    public function PurohitDashboard(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Seller', 'recode' => 0, 'data' => []], 200);
        }
        $getNewData = TempleOrderDetails::with('order')->where('type', 'puja')->where('trust_id', $request->seller['relation_id'])
            ->when($request['type'] == 'purohit', function ($q) use ($request) {
                $q->where('purohit_id', $request->seller['id']);
            })
            ->when($request['type'] == 'trust_employee', function ($q) use ($request) {
                $q->where('purohit_id', $request->seller['purohit_id'])
                    ->where('emp_id', $request->seller['id']);
            })->whereHas('order', function ($q) {
                $q->where('booking_status', 'confirmed');
            });

        $data['card1'] = [
            'totalamount' => (clone $getNewData)->sum('base_price'),
            'yearamount' => (clone $getNewData)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->sum('base_price'),
            'monthamount' => (clone $getNewData)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->sum('base_price'),
            'todayamount' => (clone $getNewData)->whereDate('created_at', date('Y-m-d'))->sum('base_price'),
        ];
        $data['card2'] = [
            'online' => (clone $getNewData)->whereHas('order', function ($q) {
                $q->where('payment_mode', 'online');
            })->sum('base_price'),
            'cash' => (clone $getNewData)->whereHas('order', function ($q) {
                $q->where('payment_mode', 'cash');
            })->sum('base_price'),
        ];
        if ($request['type'] == 'purohit') {
            $getTrust = Purohit::where('id', $request->seller['id'])->first();
        } elseif ($request['type'] == 'trust_employee') {
            $getTrust = VendorEmployees::where('id', $request->seller['id'])->first();
        }
        $data['card3'] = [
            'totalEarning' => ($getTrust['withdrawal_amount'] ?? 0),
            'pendingWithdraw' => ($getTrust['requested_amount'] ?? 0),
            'adminCommission' => ($getTrust['gst_amount'] ?? 0),
            'withdrawn' => ($getTrust['trust_fee'] ?? 0),
            'withdrawn' => ($getTrust['platform_fee'] ?? 0),
            'withdrawn' => ($getTrust['collected_amount'] ?? 0),
            'trust_panding_amount' => \App\Models\PanditTransectionHistory::where('trust_id', $request->seller['relation_id'])
                ->when($request['type'] == 'purohit', function ($q) use ($request) {
                    $q->where('purohit_id', $request->seller['id']);
                })
                ->when($request['type'] == 'trust_employee', function ($q) use ($request) {
                    $q->where('purohit_id', $request->seller['purohit_id'])
                        ->where('emp_id', $request->seller['id']);
                })->where('status', 0)->sum('debit'),
        ];
        $data['card4'] = [
            'totalamount' => (clone $getNewData)->count(),
            'yearamount' => (clone $getNewData)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->count(),
            'monthamount' => (clone $getNewData)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->count(),
            'todayamount' => (clone $getNewData)->whereDate('created_at', date('Y-m-d'))->count(),
        ];
        return response()->json(['status' => 1, 'message' => 'Successfully', 'data' => $data], 200);
    }

    public function TempleAllList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Seller', 'recode' => 0, 'data' => []], 200);
        }
        $assignedTempleIds = DonateTrust::where('id', '!=', $request->seller['relation_id'])->pluck('trust_temple_id')->filter()
            ->flatMap(function ($json) {
                return json_decode($json, true);
            })->filter()->unique()->values()->toArray();
        $temple_list = Temple::where('status', 1)->whereNotIn('id', $assignedTempleIds)->get();
        if ($temple_list) {
            $FiltersData = [];
            foreach ($temple_list as $k => $val) {
                $trust_name = [];
                if (!empty($val)) {
                    $trust_name = $val->translations()->pluck('value', 'key')->toArray();
                }
                $FiltersData[$k]['hi_name'] = $trust_name['name'] ?? '';
                $FiltersData[$k]['en_name'] = $val['name'];
                $FiltersData[$k]['id'] = $val['id'];
            }
            return response()->json(['status' => 1, 'message' => 'Successfully', 'recode' => count($FiltersData), 'data' => $FiltersData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function CategoryList(Request $request)
    {
        $getCategory  = DonateCategory::where(['status' => 1, 'type' => 'category'])->get();
        if ($getCategory) {
            $FiltersData = [];
            foreach ($getCategory as $k => $val) {
                $trust_name = [];
                if (!empty($val)) {
                    $trust_name = $val->translations()->pluck('value', 'key')->toArray();
                }
                $FiltersData[$k]['hi_name'] = $trust_name['name'] ?? '';
                $FiltersData[$k]['en_name'] = $val['name'];
                $FiltersData[$k]['id'] = $val['id'];
            }
            return response()->json(['status' => 1, 'message' => 'Successfully', 'recode' => count($FiltersData), 'data' => $FiltersData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 200);
        }
    }


    public function BankList(Request $request)
    {
        $getbank  = Bank::where(['status' => 1])->get();
        if ($getbank) {
            $FiltersData = [];
            foreach ($getbank as $k => $val) {
                $FiltersData[$k]['bank_name'] = $val['bank_name'];
                $FiltersData[$k]['id'] = $val['id'];
            }
            return response()->json(['status' => 1, 'message' => 'Successfully', 'recode' => count($FiltersData), 'data' => $FiltersData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function profileGet(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Seller', 'recode' => 0, 'data' => []], 200);
        }
        if (empty($request['type']) || $request['type'] == 'trust') {
            $getData = DonateTrust::where('id', $request->seller['relation_id'])->with(['category'])->first();
            if ($getData) {
                $FiltersData = [];
                $trust_name = [];
                if (!empty($getData['category'])) {
                    $trust_name = $getData['category']->translations()->pluck('value', 'key')->toArray();
                }
                $FiltersData['category_hi_name'] = $trust_name['name'] ?? '';
                $FiltersData['category_en_name'] = $getData['category']['name'] ?? "";
                $FiltersData['category_id'] = $getData['category_id'] ?? "";
                $templeNameShow = [];
                if ($getData['trust_temple_id'] && json_decode($getData['trust_temple_id'] ?? "[]", true)) {
                    $ii = 0;
                    foreach (json_decode($getData['trust_temple_id'] ?? "[]", true) as $vval) {
                        $getTample = \App\Models\Temple::where('id', $vval)->first();
                        $templeNameShow[$ii]['en_name'] = $getTample['name'] ?? "";
                        $temple_name = [];
                        if (!empty($getTample)) {
                            $temple_name = $getTample->translations()->pluck('value', 'key')->toArray();
                        }
                        $templeNameShow[$ii]['hi_name'] = $temple_name['name'] ?? "";
                        $templeNameShow[$ii]['id'] = $vval ?? "";
                        $ii++;
                    }
                }
                $FiltersData['trust_temple_id'] = $templeNameShow;
                $FiltersData['name'] = $getData['name'];
                $FiltersData['trust_name'] = $getData['trust_name'];
                $FiltersData['trust_email'] = $getData['trust_email'];
                $FiltersData['description'] = $getData['description'];
                $FiltersData['memberlist'] = json_decode($getData['memberlist'] ?? "[]", true);
                $FiltersData['trust_pan_card'] = $getData['trust_pan_card'];
                $FiltersData['trust_pan_card_image'] = getValidImage(path: 'storage/app/public/donate/document/' . $getData['trust_pan_card_image'] ?? '', type: 'backend-product');
                $FiltersData['pan_card'] = $getData['pan_card'];
                $FiltersData['pan_card_image'] = getValidImage(path: 'storage/app/public/donate/document/' . $getData['pan_card_image'] ?? '', type: 'backend-product');
                $FiltersData['website'] = $getData['website'];
                $FiltersData['gst_number'] = $getData['gst_number'];
                $FiltersData['full_address'] = $getData['full_address'];
                $FiltersData['beneficiary_name'] = $getData['beneficiary_name'];
                $FiltersData['account_type'] = $getData['account_type'];
                $FiltersData['bank_name'] = $getData['bank_name'];
                $FiltersData['ifsc_code'] = $getData['ifsc_code'];
                $FiltersData['account_no'] = $getData['account_no'];
                $multiimage = [];
                if ($getData['gallery_image'] && json_decode($getData['gallery_image'], true)) {
                    foreach (json_decode($getData['gallery_image'], true) as $key => $value) {
                        $multiimage[] =  getValidImage(path: 'storage/app/public/donate/trust/' . $value ?? '', type: 'backend-product');
                    }
                }
                $FiltersData['gallery_image'] = $multiimage;
                $FiltersData['theme_image'] = getValidImage(path: 'storage/app/public/donate/trust/' . $getData['theme_image'] ?? '', type: 'backend-product');;
                $FiltersData['twelve_a_certificate'] = getValidImage(path: 'storage/app/public/donate/document/' . $getData['twelve_a_certificate'] ?? '', type: 'backend-product');;
                $FiltersData['eighty_g_certificate'] = getValidImage(path: 'storage/app/public/donate/document/' . $getData['eighty_g_certificate'] ?? '', type: 'backend-product');;
                $FiltersData['niti_aayog_certificate'] = getValidImage(path: 'storage/app/public/donate/document/' . $getData['niti_aayog_certificate'] ?? '', type: 'backend-product');;
                $FiltersData['astin_g_number'] = $getData['astin_g_number'];
                $FiltersData['twelve_a_number'] = $getData['twelve_a_number'];
                $FiltersData['eighty_g_number'] = $getData['eighty_g_number'];
                $FiltersData['niti_aayog_number'] = $getData['niti_aayog_number'];
                $FiltersData['csr_number'] = $getData['csr_number'];
                $FiltersData['e_anudhan_number'] = $getData['e_anudhan_number'];
                $FiltersData['frc_number'] = $getData['frc_number'];
                $FiltersData['csr_certificate'] = getValidImage(path: 'storage/app/public/donate/document/' . $getData['csr_certificate'] ?? '', type: 'backend-product');
                $FiltersData['e_anudhan_certificate'] = getValidImage(path: 'storage/app/public/donate/document/' . $getData['e_anudhan_certificate'] ?? '', type: 'backend-product');
                $FiltersData['frc_certificate'] = getValidImage(path: 'storage/app/public/donate/document/' . $getData['frc_certificate'] ?? '', type: 'backend-product');
                $FiltersData['verified_access_certificate'] = getValidImage(path: 'storage/app/public/donate/verified/' . $getData['verified_access_certificate'] ?? '', type: 'backend-product');
                $FiltersData['cancelled_cheque_image'] = getValidImage(path: 'storage/app/public/donate/document/' . $getData['cancelled_cheque_image'] ?? '', type: 'backend-product');
                $FiltersData['id'] = $getData['id'];
                return response()->json(['status' => 1, 'message' => 'Successfully', 'recode' => 1, 'data' => $FiltersData], 200);
            } else {
                return response()->json(['status' => 0, 'message' => ' Not Found Data', 'recode' => 0, 'data' => []], 200);
            }
        } elseif ($request['type'] == 'trust_employee') {
            $getData = \App\Models\VendorEmployees::where('id', $request->seller['id'])->with(['Trust', 'Role'])->first();
            if ($getData) {
                $FiltersData = [];
                $FiltersData['identify_number'] = $getData['identify_number'];
                $FiltersData['name'] = $getData['name'];
                $FiltersData['phone'] = $getData['phone'];
                $FiltersData['email'] = $getData['email'];
                $FiltersData['emp_role_id'] = $getData['emp_role_id'];
                $FiltersData['role_name'] = ($getData['Role']['name'] ?? "");
                // $FiltersData['temple_id'] = ($getData['temple_id']??"");
                // $FiltersData['temple_name'] = ($getData['Role']['name']??"");
                $FiltersData['selected_services'] = json_decode($trust_name['selected_services'] ?? '');
                $FiltersData['beneficiary_name'] = $getData['holdername'];
                $FiltersData['bank_name'] = $getData['bankname'];
                $FiltersData['ifsc_code'] = $getData['ifsccode'];
                $FiltersData['account_no'] = $getData['account_num'];
                $FiltersData['image'] = getValidImage(path: 'storage/app/public/event/employee/' . $getData['image'] ?? '', type: 'backend-product');
                $FiltersData['id'] = $getData['id'];

                return response()->json(['status' => 1, 'message' => 'Successfully', 'recode' => 1, 'data' => $FiltersData], 200);
            }
        } elseif ($request['type'] == 'purohit') {

            $getData = \App\Models\Purohit::where('id', $request->seller['id'])->with(['temple'])->first();
            if ($getData) {
                $FiltersData = [];
                $FiltersData['name'] = $getData['name'];
                $FiltersData['phone'] = $getData['mobile'];
                $FiltersData['address'] = $getData['address'];
                $FiltersData['description'] = ($getData['description'] ?? "");
                $FiltersData['temple_id'] = ($getData['temple_id'] ?? "");
                $FiltersData['temple_name'] = ($getData['temple']['name'] ?? "");
                $FiltersData['beneficiary_name'] = $getData['holdername'];
                $FiltersData['bank_name'] = $getData['bankname'];
                $FiltersData['ifsc_code'] = $getData['ifsccode'];
                $FiltersData['account_no'] = $getData['account_num'];
                $FiltersData['image'] = getValidImage(path: 'storage/app/public/' . $getData['profile'] ?? '', type: 'backend-product');
                $FiltersData['id'] = $getData['id'];
                return response()->json(['status' => 1, 'message' => 'Successfully', 'recode' => 1, 'data' => $FiltersData], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 200);
            }
        }
        return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 200);
    }
    public function profileUpdate(Request $request, DonateTrustService $service)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Seller', 'recode' => 0, 'data' => []], 200);
        }
        if (empty($request['type']) || $request['type'] == 'trust') {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|integer',
                'trust_temple_id' => 'required|array',
                'trust_temple_id.*' => 'integer',
                'name' => 'required|string|max:255',
                'trust_name' => 'required|string|max:255',
                'trust_email' => 'required|email',
                'description' => 'sometimes|nullable|string',
                'memberlist' => 'required|array|min:1',
                'memberlist.*.member_name' => 'required|string|max:255',
                'memberlist.*.member_phone_no' => 'required|string|max:20',
                'memberlist.*.member_position' => 'required|string|max:255',
                'trust_pan_card_image' => 'sometimes|nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
                'pan_card_image' => 'sometimes|nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
                'gallery_image' => 'sometimes|nullable|array',
                'gallery_image.*' => 'sometimes|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',

                // Bank details
                'beneficiary_name' => 'required|string|max:255',
                'account_type' => 'required|string|max:255',
                'bank_name' => 'required|string|max:255',
                'ifsc_code' => 'required|string|max:50',
                'account_no' => 'required|string|max:50',

                // Optional other certificates
                'csr_certificate' => 'sometimes|nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
                'e_anudhan_certificate' => 'sometimes|nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
                'frc_certificate' => 'sometimes|nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
                'verified_access_certificate' => 'sometimes|nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
                'cancelled_cheque_image' => 'sometimes|nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            ]);
        } elseif ($request['type'] == 'trust_employee') {
            $validator = Validator::make($request->all(), [
                'identify_number' => 'required',
                'name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'image' => 'sometimes|nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',

                // Bank details
                'beneficiary_name' => 'required|string|max:255',
                'bank_name' => 'required|string|max:255',
                'ifsc_code' => 'required|string|max:50',
                'account_no' => 'required|string|max:50',
            ]);
        } elseif ($request['type'] == 'purohit') {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'description' => 'required',
                'image' => 'sometimes|nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',

                // Bank details
                'beneficiary_name' => 'required|string|max:255',
                'bank_name' => 'required|string|max:255',
                'ifsc_code' => 'required|string|max:50',
                'account_no' => 'required|string|max:50',
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        if (empty($request['type']) || $request['type'] == 'trust') {
            $model = \App\Models\DonateTrust::find($request->seller['relation_id']);
            $data1 = $service->getUpdateDataAPI($request, $model);
            $result = \App\Models\DonateTrust::where('id', $request->seller['relation_id'])->update($data1);
        } elseif ($request['type'] == 'trust_employee') {
            $model = \App\Models\VendorEmployees::find($request->seller['id']);
            $data1 = $service->getUpdateDataAPIEmployee($request, $model);
            $result = \App\Models\VendorEmployees::where('id', $request->seller['id'])->update($data1);
        } elseif ($request['type'] == 'purohit') {
            $model = \App\Models\Purohit::find($request->seller['id']);
            $data1 = $service->getUpdateDataAPIPurohit($request, $model);
            $result = \App\Models\Purohit::where('id', $request->seller['id'])->update($data1);
        }
        if ($result) {
            return response()->json([
                'status' => 1,
                'message' => 'Profile updated successfully',
                'data' => [],
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Profile updated Failed',
                'data' => []
            ], 200);
        }
    }
    public function TempleListings(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found seller Id', 'recode' => 0, 'data' => []], 400);
        }
        $templeList = Temple::select('id', 'name')->where('trust_id', $request->seller['relation_id'])
            ->when((((($request['type'] ?? "") == 'purohit') || (($request['type'] ?? "") == 'trust_employee')) && (($request->seller['temple_id'] ?? 0) != 0)), function ($q) use ($request) {
                $q->where('id', $request->seller['temple_id']);
            })
            ->orderBy('id', 'desc')->get();
        if ($templeList && count($templeList) > 0) {
            return response()->json(['status' => 1, 'message' => 'get temple List', 'recode' => count($templeList), 'data' => $templeList], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function VerifiedUserFilter(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found seller Id', 'recode' => 0, 'data' => []], 400);
        }
        $searchValue = $request->get('searchValue') ?? '';
        $temple_ids = $request->get('temple_id') ?? '';
        $start_date = $request->get('start_date') ?? '';
        $end_date = $request->get('end_date') ?? '';
        // Base query
        $query = DarshanOrderMembers::where('verify', 1)
            ->with(['darshanOrder.Temple'])
            ->whereHas('darshanOrder.Temple', function ($q3) use ($request) {
                $q3->where('trust_id', $request->seller['relation_id']);
            })
            ->when($temple_ids, function ($query3) use ($temple_ids) {
                $query3->whereHas('darshanOrder.Temple', function ($q3) use ($temple_ids) {
                    $q3->where('id', $temple_ids);
                });
            })
            ->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('updated_at', [$start_date, $end_date]);
            });

        // Apply search
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('aadhar', 'like', "%{$searchValue}%")
                    ->orWhere('barcode', 'like', "%{$searchValue}%")
                    ->orWhereHas('darshanOrder', function ($q2) use ($searchValue) {
                        $q2->where('order_id', 'like', "%{$searchValue}%")
                            ->orWhere('title', 'like', "%{$searchValue}%")
                            ->orWhere('package_name', 'like', "%{$searchValue}%")
                            ->orWhereHas('Temple', function ($q3) use ($searchValue) {
                                $q3->where('name', 'like', "%{$searchValue}%");
                            });
                    });
            });
        }


        // Total before filter
        $recordsTotal = DarshanOrderMembers::where('verify', 1)->with(['darshanOrder.Temple'])
            ->whereHas('darshanOrder.Temple', function ($q3) use ($request) {
                $q3->where('trust_id', $request->seller['relation_id']);
            })
            ->when($temple_ids, function ($query3) use ($temple_ids) {
                $query3->whereHas('darshanOrder.Temple', function ($q2) use ($temple_ids) {
                    $q2->where('id', $temple_ids);
                });
            })
            ->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('updated_at', [$start_date, $end_date]);
            })->count();
        $recordsFiltered = $query->count();
        $GetAllData = $query->orderBy('id', 'desc')->get();
        if ($GetAllData) {
            $information_data = [];
            $q = 0;
            foreach ($GetAllData as $val) {
                $information_data[$q]['user_name'] = $val['name'];
                $information_data[$q]['user_phone'] = $val['phone'];
                $information_data[$q]['user_aadhar'] = $val['aadhar'];
                $information_data[$q]['barcode'] = $val['barcode'];
                $information_data[$q]['date'] = date('d-m-Y h:i A', strtotime($val['updated_at']));
                $information_data[$q]['order_id'] = optional($val['darshanOrder'])->order_id ?? '';
                $information_data[$q]['package_name'] =  optional($val['darshanOrder'])->package_name;
                $information_data[$q]['title'] = optional($val['darshanOrder'])->title ?? '';
                $information_data[$q]['temple_name'] = optional(optional($val['darshanOrder'])->Temple)->name ?? '';
                $q++;
            }
            return response()->json(['status' => 1, 'message' => 'sucessfully', 'recode' => $recordsFiltered, 'data' => $information_data], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Recode', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function DarshanOrderList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found darshan Order Data', 'recode' => 0, 'data' => []], 400);
        }
        $querys = DarshanOrder::where('status', 1)->with(['Temple', 'userData', 'Members']);
        if ($request['types'] == 'today') {
            $querys->withCount([
                'Members as total_counts' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->whereIn('verify', [0, 1]);
                },
                'Members as verified_count' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->where('verify', 1);
                },
                'Members as not_verified_count' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->where('verify', 0);
                },
            ])->whereRaw("STR_TO_DATE(date, '%d-%m-%Y') = ?", [date('Y-m-d')]);
        } elseif ($request['types'] == 'complete') {
            $querys->withCount([
                'Members as total_counts' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->whereIn('verify', [0, 1]);
                },
                'Members as verified_count' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->where('verify', 1);
                },
                'Members as not_verified_count' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->where('verify', 0);
                },
            ])->whereRaw("STR_TO_DATE(date, '%d-%m-%Y') < ?", [date('Y-m-d')]);
        } else {
            $querys->withCount([
                'Members as total_counts' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->whereIn('verify', [0, 1]);
                },
            ])->whereRaw("STR_TO_DATE(date, '%d-%m-%Y') > ?", [date('Y-m-d')]);
        }

        $darshanlist =  $querys->whereHas('Temple', function ($q) use ($request) {
            $q->where('trust_id', $request->seller['relation_id']);
        })->orderBy('id', 'desc')->get();

        if ($darshanlist && count($darshanlist) > 0) {
            $getData = [];
            $p = 0;
            foreach ($darshanlist as $value) {
                $getData[$p]['id'] = $value['id'];
                $getData[$p]['order_id'] = $value['order_id'];
                $getData[$p]['title'] = $value['title'];
                $getData[$p]['package_name'] = $value['package_name'];
                $getData[$p]['date'] = $value['date'];
                $getData[$p]['time'] = $value['time'];
                $getData[$p]['price'] = $value['price'];
                $getData[$p]['people_qty'] = $value['people_qty'];
                $getData[$p]['created_at'] = date('d-m-Y h:i A', strtotime($value['created_at']));
                $trust_name = [];
                if (!empty($value['Temple'])) {
                    $trust_name = $value['Temple']->translations()->pluck('value', 'key')->toArray();
                }
                $getData[$p]['en_temple_name'] = $value['Temple']['name'] ?? '';
                $getData[$p]['hi_temple_name'] = $trust_name['name'] ?? "";
                $getData[$p]['image'] = getValidImage(path: 'storage/app/public/temple/thumbnail/' . ($value['Temple']['thumbnail'] ?? ''), type: 'backend-product');
                $getData[$p]['user_name'] = $value['userData']['name'] ?? '';
                $getData[$p]['user_email'] = $value['userData']['email'] ?? "";
                $getData[$p]['user_phone'] = $value['userData']['phone'] ?? "";
                $getData[$p]['status'] = $value['status'] ?? "";
                $getData[$p]['total_member'] = $value['total_counts'] ?? 0;
                $getData[$p]['verified_member'] = $value['verified_count'] ?? 0;
                $getData[$p]['not_verified_member'] = $value['not_verified_count'] ?? 0;
                $getData[$p]['member_list'] = [];
                if ($value['Members'] && count($value['Members']) > 0) {
                    $qy = 0;
                    foreach ($value['Members'] as $k => $val) {
                        $getData[$p]['member_list'][$qy]['name'] = ucwords($val['name']);
                        $getData[$p]['member_list'][$qy]['phone'] = $val['phone'];
                        $getData[$p]['member_list'][$qy]['aadhar'] = $val['aadhar'];
                        $getImages = \App\Models\UserAadhaarKyc::where('aadhaar_number', $val['aadhar'])->first();
                        $getData[$p]['member_list'][$qy]['image'] = $getImages['image'] ?? '';
                        $getData[$p]['member_list'][$qy]['pass'] = url('api/v1/darshan/vip-pass', ['barcode' => base64_encode($val['barcode'])]);
                        $qy++;
                    }
                }
                $p++;
            }
            return response()->json(['status' => 1, 'message' => 'get Order List', 'recode' => count($getData), 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found darshan order Data', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function DarshanOrderverify(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found darshan Order Data', 'recode' => 0, 'data' => []], 400);
        }
        $getData = DarshanOrderMembers::where('barcode', $request['barcode'])->with(['darshanOrder'])->whereHas('darshanOrder', function ($q) use ($request) {
            $q->where('date', '=', date('d-m-Y'));
            $q->whereHas('Temple', function ($q2) use ($request) {
                $q2->where('trust_id', $request->seller['relation_id']);
            });
        })->first();
        if ($getData) {
            if ($request['type'] == 'verify' && $getData->verify == 0) {
                $getData->verify = 1;
                $getData->save();
            } elseif ($getData->verify == 1) {
                return response()->json(['success' => 1, 'message' => 'Already success', 'data' => []], 200);
            }
            $getNewData = [];
            $getNewData['barcode'] = $getData['barcode'];
            $getNewData['name'] = $getData['name'];
            $getNewData['phone'] = $getData['phone'];
            $getNewData['aadhar'] = $getData['aadhar'];
            $getImages = \App\Models\UserAadhaarKyc::where('aadhaar_number', $getData['aadhar'])->first();
            $getNewData['image'] = $getImages['image'] ?? '';
            $getNewData['verify'] = $getData['verify'];
            $getNewData['temple_name'] = data_get($getData, 'darshanOrder.Temple.name', '');
            return response()->json(['success' => 1, 'message' => 'success', 'data' => $getNewData], 200);
        } else {
            return response()->json(['success' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function TemplePackagesList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found darshan Order Data', 'recode' => 0, 'data' => []], 400);
        }
        $validator = Validator::make($request->all(), [
            'temple_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $getTemple = Temple::where('id', $request['temple_id'])->first();
        $groupByArray = [];
        if ($getTemple) {
            $groupByArray['service'] = [];
            if ($getTemple['package_service'] && json_decode($getTemple['package_service'] ?? "[]", true)) {
                foreach (json_decode($getTemple['package_service'] ?? "[]", true) as $ke => $val) {
                    if ($val['status'] == '1' && (((($request['type'] ?? "") == 'trust_employee') && ((\App\Models\VendorRoles::where('id', $request->seller['emp_role_id'])->first()['name'] ?? "") == 'Sub Pandit') && $val['name'] == 'puja') || ((($request['type'] ?? "") == 'purohit') && $val['name'] == 'puja'))) {
                        $groupByArray['service'][] = $val['name'];
                    } elseif ($val['status'] == '1' && (((($request['type'] ?? "") == 'trust_employee') && ((\App\Models\VendorRoles::where('id', $request->seller['emp_role_id'])->first()['name'] ?? "") != 'Sub Pandit')) || (($request['type'] ?? "") == 'trust'))) {
                        $groupByArray['service'][] = $val['name'];
                    }
                }
            }
            foreach ($groupByArray['service'] as $key => $value) {
                $getDataN = \App\Models\TempleServicePrice::select('varient_name', 'base_price', 'daily_slots_limit', 'id', 'platform_fee_percentage', 'receipt_fee_percentage', 'gst_rate')
                    ->where([
                        'temple_id' => $request['temple_id'],
                        'trust_id' => $request->seller['relation_id'],
                        'status' => 1,
                        'package_id' => \App\Models\TempleServicePackages::where('name', $value)->value('id'),
                    ])
                    ->get()
                    ->toArray();
                $groupByArray[$value] = $getDataN;
            }
            $groupByArray['purohit'] = \App\Models\Purohit::select('name', 'id')
                ->when(((($request['type'] ?? "") == 'trust_employee')  && ((\App\Models\VendorRoles::where('id', $request->seller['emp_role_id'])->first()['name'] ?? "") == 'Sub Pandit')), function ($q) use ($request) {
                    $q->where('id', $request->seller['purohit_id']);
                })
                ->when((($request['type'] ?? "") == 'purohit'), function ($q) use ($request) {
                    $q->where('id', $request->seller['id']);
                })
                ->where([
                    'temple_id' => $request['temple_id'],
                    'status' => 1
                ])
                ->get()
                ->toArray();
            return response()->json(['status' => 1, "message" => "get Data Successfully", 'data' => $groupByArray], 200);
        } else {
            return response()->json(['status' => 0, "message" => "Temple Id Invalid", 'data' => []], 200);
        }
    }

    public function TemplePackagedateTimeList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found darshan Order Data', 'recode' => 0, 'data' => []], 400);
        }
        $validator = Validator::make($request->all(), [
            'package_id' => 'required',
            'date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        $datestr = date('l', strtotime($request['date']));
        $getData = \App\Models\TempleServiceSlot::select('id', 'start_time', 'end_time', 'day_of_week', 'slots_limi_capacity')->where(['status' => 1, 'temple_service_prices_id' => $request['package_id'], 'day_of_week' => $datestr])->get()->toArray();
        if ($getData) {
            return response()->json(['status' => 1, "message" => "get Data Successfully", 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, "message" => "Not Found Data", 'data' => []], 200);
        }
    }

    public function TemplePackageList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found darshan Order Data', 'recode' => 0, 'data' => []], 400);
        }
        $validator = Validator::make($request->all(), [
            'temple_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $getTemple = Temple::where('id', $request['temple_id'])->first();
        $BookingInfo = [];
        if ($getTemple) {
            $vipPlans = json_decode($getTemple['vip_plans'] ?? '[]', true);
            if (!empty($vipPlans)) {
                $q = 0;
                foreach ($vipPlans as $plan) {
                    $vipDarshan = $plan['package'][0] ?? [];
                    $BookingInfo[$q]['id'] = $plan['id'];
                    $BookingInfo[$q]['title'] = $plan['name'];
                    $BookingInfo[$q]['package_name'] = $plan['package'][0]['name'] ?? '';
                    $BookingInfo[$q]['price'] = $plan['package'][0]['price'] ?? '';
                    $BookingInfo[$q]['limit'] = $plan['package'][0]['limit'] ?? '';
                    $q++;
                }
            }
            return response()->json(['status' => 1, "message" => "get Data Successfully", 'data' => $BookingInfo], 200);
        } else {
            return response()->json(['status' => 0, "message" => "Temple Id Invalid", 'data' => []], 200);
        }
    }

    // public function TempleVipTicketBooking(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'temple_id' => 'required',
    //         'package_id' => 'required',
    //         'person_phone' => 'required',
    //         'user_name' => 'required',
    //         'payment_mode' => 'required|in:cash,online',
    //         'userList' => 'required|array',
    //         'userList.*.name'   => 'required|string|max:255',
    //         'userList.0.phone'  => 'required|string|max:15',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
    //     }
    //     if (empty($request->seller['relation_id'] ?? '')) {
    //         return response()->json(['status' => 0, 'message' => 'Not Found darshan Order Data', 'recode' => 0, 'data' => []], 400);
    //     }
    //     $old_data = Temple::where('trust_id', $request->seller['relation_id'])->find($request['temple_id']);

    //     if ($old_data && $request['payment_mode'] && $old_data->vip_plans) {
    //         $vipPlans = collect(json_decode($old_data->vip_plans, true));
    //         $matched = collect($vipPlans)->flatMap(fn($plan) => $plan['package'] ?? [])->first(fn($package) => (int) $package['id'] === (int) $request['package_id']);
    //         if (!$matched) {
    //             return response()->json(['error' => 'Invalid Package Select', 'data' => []], 200);
    //         }
    //         $userfind = User::where('phone', ($request->input('person_phone') ?? ""))->first();
    //         if ($userfind) {
    //             $user_id = $userfind['id'];
    //         } else {
    //             $user = new User();
    //             $user->phone = $request->input('person_phone');
    //             $user->name = $request->input('user_name');
    //             $user->f_name = (explode(" ", $request->input('user_name'))[0] ?? "");
    //             $user->l_name = (explode(" ", $request->input('user_name'))[1] ?? "");
    //             $user->email = $request->input('person_phone');
    //             $user->password =  bcrypt('12345678');
    //             $user->verify_otp = $request->input('verify_otp') ?? 1;
    //             $user->save();
    //             $user_id = $user->id ?? "";
    //             $data = [
    //                 'customer_id' => ($user->id ?? "")
    //             ];
    //             Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
    //         }
    //         $userList = $request->input('userList');
    //         if (is_string($userList)) {
    //             $userList = json_decode($userList, true);
    //         }
    //         if (!is_array($userList)) {
    //             $userList = [];
    //         }
    //         $userCount = count($userList);

    //         $darshanOrder = new DarshanOrder();
    //         $darshanOrder->user_id = $user_id;
    //         $darshanOrder->temple_id = $request['temple_id'];
    //         $darshanOrder->package_id = $request['package_id'];
    //         $darshanOrder->title = $vipPlans->where('id', $request['package_id'])->pluck('name')->first();
    //         $darshanOrder->package_name = $matched['name'];
    //         $darshanOrder->date = date('d-m-Y');
    //         $darshanOrder->time = date('h:i A') . " - " . date('h:i A', strtotime('+2 hours'));
    //         $darshanOrder->price = $matched['price'] * $userCount;
    //         $darshanOrder->people_qty = $userCount;
    //         $darshanOrder->final_amount = $matched['price'] * $userCount;
    //         $darshanOrder->status = 0;
    //         $darshan_memberbook = [];
    //         if ($request['userList'] && $userList) {
    //             $peopleInfo = $userList;
    //             for ($iq = 0; $iq < count($peopleInfo); $iq++) {
    //                 $darshan_memberbook[$iq]['name'] = $peopleInfo[$iq]['name'] ?? '';
    //                 $darshan_memberbook[$iq]['phone'] = $peopleInfo[$iq]['phone'] ?? '';
    //                 $darshan_memberbook[$iq]['aadhar'] = $peopleInfo[$iq]['aadhar'] ?? '';
    //                 $darshan_memberbook[$iq]['aadhar_verify_status'] = 0;
    //             }
    //         }
    //         if ($request['payment_mode'] == 'cash') {
    //             $darshanOrder->transaction_id = 'Cash';
    //             $darshanOrder->payment_method = 'Offline';
    //             $darshanOrder->status = 1;
    //             $darshanOrder->save();
    //             if ($darshan_memberbook) {
    //                 foreach ($darshan_memberbook as $key1 => $value1) {
    //                     $member = new DarshanOrderMembers();
    //                     $member->darshan_id = $darshanOrder->id;
    //                     $member->name = $value1['name'];
    //                     $member->phone = $value1['phone'];
    //                     $member->aadhar = $value1['aadhar'];
    //                     $member->aadhar_verify_status = $value1['aadhar_verify_status'];
    //                     $member->save();
    //                 }
    //             }
    //             $dataemail['orderId'] = $darshanOrder->order_id;
    //             $dataemail['admin_name'] = $request['user_name'];
    //             $dataemail['admin_phone'] = $request['person_phone'];
    //             $dataemail['rprice'] =  $matched['price'] * $userCount;
    //             $dataemail['discount'] = 0;
    //             $dataemail['tax_amount'] = 0;
    //             $dataemail['paymant_model'] = 'Cash';
    //             $dataemail['final_amount'] = $matched['price'] * $userCount;

    //             Helpers::whatsappMessage('donate', 'vip_darshan_ticket_order_message', $dataemail);
    //             return response()->json(['success' => 1, 'status' => 1, 'message' => 'Ticket get Successfully', 'data' => $darshanOrder], 200);
    //         } else {
    //             $wallet_amount = 0;
    //             $total_amount = $matched['price'] * $userCount;
    //             $onlinepay = $matched['price'] * $userCount;
    //             $data = [
    //                 'additional_data' => [
    //                     'business_name' => \App\Models\BusinessSetting::where(['type' => 'company_name'])->first()->value,
    //                     'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
    //                     'payment_mode' => 'web',
    //                     'customer_id' => $user_id,
    //                     "order_id" => $darshanOrder->id,
    //                     "memberList" => ($darshan_memberbook),
    //                     "darshanInfo" => $darshanOrder,
    //                     "amount" => ($onlinepay ?? 0),
    //                     "user_name" => ($request['user_name'] ?? ''),
    //                     "user_email" => '',
    //                     "user_phone" => $request['person_phone'],
    //                     'total_amount' => $total_amount,
    //                     'wallet_amount' => $wallet_amount,
    //                     "online_pay" => $onlinepay,
    //                     'page_name' => 'trust_vip_darshan_ticket',
    //                     'success_url' => route('trust-vip-darshan-ticket', ['id' => $request['person_phone']]),
    //                 ],
    //                 'user_id' => $user_id,
    //                 'payment_amount' => $onlinepay,
    //                 "order_id" => $request['person_phone'],
    //                 "attribute" => "trust_vip_darshan_ticket",
    //                 "external_redirect_link" => route('trust-vip-darshan-ticket', ['id' => $request['person_phone']]),
    //             ];
    //             $url_open = \App\Http\Controllers\Customer\PaymentController::TrustVIPTicketBooking($data);

    //             $dataemail['admin_phone'] = $request['person_phone'];
    //             $dataemail['admin_name'] = ($request['user_name'] ?? '');
    //             $dataemail['payment_link'] = ($url_open ?? '');
    //             $dataemail['service_name'] = $vipPlans->where('id', $request['package_id'])->pluck('name')->first();
    //             $dataemail['final_amount'] = $onlinepay;
    //             Helpers::whatsappMessage('donate', 'trust_puja_order_paymant_link_message', $dataemail);

    //             $qrCode = new \Endroid\QrCode\QrCode($url_open);
    //             $writer = new \Endroid\QrCode\Writer\PngWriter();
    //             $result = $writer->write($qrCode);
    //             $folder = storage_path('app/public/qrcodes');
    //             if (!\Illuminate\Support\Facades\File::exists($folder)) {
    //                 \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
    //             }
    //             $filePath = $folder . "/vipdarshanticketbookingamount.png";
    //             $result->saveToFile($filePath);
    //             $imageData = getValidImage(path: 'storage/app/public/qrcodes/vipdarshanticketbookingamount.png', type: 'backend-product');
    //             $query12 = parse_url($url_open, PHP_URL_QUERY);
    //             parse_str($query12, $params12);
    //             $paymentId = $params12['payment_id'] ?? null;
    //             return response()->json(['success' => 1, 'status' => 2, 'message' => 'get url Successfully', 'data' => $imageData, 'url' => $url_open, 'transaction_id' => $paymentId], 200);
    //         }
    //         return response()->json(['success' => 1, 'status' => 1, 'message' => 'Successfully', 'data' => $darshanOrder], 200);
    //     } else {
    //         return response()->json(['success' => 0, 'message' => 'Temple Recode Not Found', 'data' => []], 200);
    //     }
    // }

    public function TempleVipTicketBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temple_id' => 'required',
            'package_id' => 'required',
            "type" => "required",
            "date" => "required",
            'person_phone' => 'nullable',
            'user_name' => 'required',
            'payment_mode' => 'required|in:cash,free,online',
            'userList' => 'required|array',
            'userList.*.name'   => 'nullable|string|max:255',
            'userList.0.mobile'  => 'nullable|string|max:15',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found darshan Order Data', 'recode' => 0, 'data' => []], 400);
        }
        $lastOrder = \App\Models\TempleLeadMaster::select('id')->latest()->first();
        $lastId = !empty($lastOrder['id']) ? (100001 + $lastOrder['id']) : 100001;
        $custo_mers = $request->userList ?? [];

        $customerIds = [];

        if (!empty($custo_mers)) {
            $customers = [];
            $pq = 0;
            foreach ($custo_mers as $index => $cust) {
                $customers[$pq] = $cust;
                $pq++;
            }
            foreach ($customers as $index => $cust) {
                $numbering = '(' . str_pad($index + 1, 2, '0', STR_PAD_LEFT) . ')';
                $personName  = trim($cust['name']) . ' ' . $numbering;
                $cleanPhone = trim($cust['mobile']);
                $aadhaarNo   = trim($cust['aadhaar']);
                $address     = trim($cust['address']);
                $personPhone = preg_replace('/\D/', '', $cleanPhone);
                if (!empty($personPhone) && $index === 0 && (strlen((string)$personPhone) > 9)) {
                    $user = User::where('phone', $personPhone)->first();
                    if (!$user) {
                        $nameParts = explode(' ', $personName);
                        $firstName = $nameParts[0] ?? '';
                        $lastName  = $nameParts[1] ?? '';
                        $verifyOTP = rand(100000, 999999);
                        $user = User::create([
                            'name'        => $personName,
                            'f_name'      => $firstName,
                            'l_name'      => $lastName,
                            'phone'       => $personPhone,
                            'email'       => 'user@mahakal.com',
                            'password'    => bcrypt('12345678'),
                            'verify_otp'  => $verifyOTP,
                        ]);

                        $data = ['customer_id' => $user->id];
                        \App\Utils\Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
                    }
                    $customerIds[] = $user->id;
                }
                $customers[$index]['name'] = $personName;
            }
        }
        $packageData = \App\Models\TempleServicePrice::find($request->package_id);
        if ($request->type == 'puja') {
            $request['date'] = date('Y-m-d');
        }

        if ($packageData) {
            $basePrice   = ($packageData->base_price * count($customers));
            $platformFee = (($packageData->platform_fee_percentage ?? 0) * count($customers)); //($packageData->platform_fee_percentage / 100) * $basePrice;
            $receiptFee  = (($packageData->receipt_fee_percentage ?? 0) * count($customers)); // ($packageData->receipt_fee_percentage / 100) * $basePrice;

            $gstAmount = ($request->type == 'bhojan' || $request->type == 'locker') ? 0 : ($basePrice * $packageData->gst_rate / 100);

            $totalPricePerCustomer = $basePrice + $gstAmount + $platformFee + $receiptFee;
            $totalPrice = $totalPricePerCustomer;
        } else {
            $totalPrice = 0;
            $totalPricePerCustomer = 0;
        }
        $orderId = 'MCOM' . $lastId;
        $trust = Temple::select('trust_id')->where('id', $request->temple_id)->first();

        $leads = \App\Models\TempleLeadMaster::create([
            'temple_id'    => $request->temple_id,
            'user_id'      => $customerIds[0] ?? null,
            'trust_id'     => $trust->trust_id ?? null,
            'order_id'     => $orderId,
            'customer_qty' => count($customers),
            'amount'       => $totalPrice,
        ]);
        $purohits = \App\Models\Purohit::where('temple_id', $request->temple_id)
            ->where('status', 1)
            ->get();
        if ($request->type == 'puja') {
            $panditId = (($request['purohit_id'] ?? "") ? $request['purohit_id'] : ($purohits->count() ? $purohits->random()->id : 0));
        } else {
            $panditId =  0;
        }
        $typeOrderId = match ($request->type) {
            'puja'    => 'PJ' . $lastId,
            'darshan' => 'DS' . $lastId,
            'bhojan'  => 'BJ' . $lastId,
            default   => 'LK' . $lastId,
        };

        $data = \App\Models\TempleLeadDetail::create([
            'package_id'     => $request->package_id,
            'amount'         => $totalPrice,
            'booking_date'   => $request['date'],
            'order_id'       => $orderId,
            'type'           => $request->type,
            'type_order_id'  => $typeOrderId,
            'customer_qty'   => count($customers),
            'customers'      => json_encode($customers),
            'pandit_id'      => $panditId,
            'time_slot_id'   => $request->slot_id ?? null,
            'locker_items'   => $request->locker_items ? json_encode($request->locker_items) : null,
        ]);
        // $url_open = \App\Http\Controllers\Customer\PaymentController::temple_payment_request($request);
        // if ($urls == 1) {
        //     return back()->with('success', 'Order created Successfully!');
        // } else {
        //     return back()->with('success', 'Order Faild Unsccessfully!');
        // }

        if ($request->payment_mode === 'cash' || $request->payment_mode === 'free') {
            $leads->update(['payment_mode' => $request->payment_mode, 'payment_status' => 1, 'status' => 1,]);
            $order = \App\Models\TempleOrderMaster::updateOrCreate(
                ['order_id' => $leads->order_id ?? ('ORD' . time())],
                [
                    'lead_id'            => $leads->id,
                    'user_id'            => $leads->user_id,
                    'temple_id'          => $leads->temple_id,
                    'trust_id'           => $leads->trust_id,
                    'total_people_count' => $leads->customer_qty,
                    'total_amount'       => $leads->amount,
                    'transaction_id'     => $leads->payment_mode,
                    'booking_status'     => 'confirmed',
                    'platform'           => ((($request['logintype'] ?? "") == 'purohit' || ((($request['logintype'] ?? "") == 'trust_employee')  && ((\App\Models\VendorRoles::where('id', ($request->seller['emp_role_id'] ?? ""))->first()['name'] ?? "") == 'Sub Pandit'))) ? 'purohit' : 'counter'),
                    'payment_mode'       => $request->payment_mode,
                    'status'             => 1,
                    'payment_status'     => 1,
                ]
            );
            $whatsapp_message_data = [];
            $whatsapp_message_data['type'] = 'text-with-media';
            $whatsapp_message_data['temple_name'] = $leads['temple']['name'];

            $leadDetails = \App\Models\TempleLeadDetail::where('order_id', $leads->order_id)->where('status', 1)->with('package')->get();
            foreach ($leadDetails as $detail) {
                $customers = json_decode($detail->customers, true) ?? [];
                $basePrice  = (($detail->package->base_price ?? 0) * count($customers));
                $gstRate    = $detail->type == 'locker' || $detail->type == 'bhojan' ? 0 : $detail->package->gst_rate;
                $platformFeePercent = (($detail->package->platform_fee_percentage ?? 0) * count($customers));
                $receiptFeePercent  = (($detail->package->receipt_fee_percentage ?? 0) * count($customers));

                $gstAmount = ($gstRate > 0) ? (($basePrice * $gstRate) / 100) : 0;
                $timeSlot = \App\Models\TempleServiceSlot::where('id', $detail->time_slot_id)->where('temple_service_prices_id', $detail->package_id)->first();

                $orderGet = \App\Models\TempleOrderDetails::updateOrCreate(
                    [
                        'order_id' => $detail->order_id,
                        'type'     => $detail->type,
                    ],
                    [
                        'package_id'     => $detail->package_id,
                        'temple_id'      => $leads->temple_id,
                        'trust_id'       => $leads->trust_id,
                        'people_count'   => $detail->customer_qty,
                        'gst'            => $gstAmount,
                        'base_price'     => $basePrice,
                        'platform_fee'   => $platformFeePercent,
                        'receipt_fee'    => $receiptFeePercent,
                        'final_amount'   => $detail->amount,
                        'booking_date'   => $detail->booking_date,
                        'customers'      => $detail->customers,
                        'type_order_id'   => $detail->type_order_id,
                        'time_slot'      => $timeSlot ? ($timeSlot->start_time . ' - ' . $timeSlot->end_time) : 'pending',
                        'locker_items'   => $request->locker_items ? json_encode($request->locker_items) : null,
                        'purohit_id'     => $panditId,
                        'booking_status' => 'confirmed',
                        'status'         => 1,
                        'payment_status' => 1,
                    ]
                );
                if ($detail->type == 'puja' && (($request['logintype'] ?? "") == 'trust_employee')  && ((\App\Models\VendorRoles::where('id', ($request->seller['emp_role_id'] ?? ""))->first()['name'] ?? "") == 'Sub Pandit')) {
                    \App\Models\TempleOrderDetails::updateOrCreate(
                        [
                            'order_id' => $detail->order_id,
                            'type'     => $detail->type,
                        ],
                        ['emp_id' => $request->seller['id']]
                    );
                }
                $getTypes = \App\Models\TempleOrderDetails::where('order_id', $detail->order_id)->first();

                if ($getTypes && strtolower($getTypes->type ?? '') === 'puja') {
                    \App\Models\TrustPanditTransection::create([
                        'order_id'       => $getTypes->order_id,
                        'type_order_id'  => $getTypes->type_order_id ?? null,
                        'temple_id'      => $getTypes->temple_id ?? null,
                        'trust_id'       => $getTypes->trust_id ?? null,
                        'pandit_id'      => $getTypes->pandit_id ?? null,
                        'package_id'     => $getTypes->package_id ?? null,
                        'package_price'  => (($getTypes->base_price ?? 0) * ($getTypes->people_count ?? 0)),
                        'payment_method' => $request->payment_mode,
                        'payment_status' => 'complete',
                    ]);
                }

                if ($getTypes && strtolower($getTypes->type ?? '') === 'puja') {
                    DonateTrust::where('id', $getTypes->trust_id)->update([
                        'trust_total_withdrawal' => \Illuminate\Support\Facades\DB::raw('trust_total_withdrawal + ' . ($getTypes->receipt_fee)),
                        'purohit_collected_amount' => \Illuminate\Support\Facades\DB::raw('purohit_collected_amount + ' . ($getTypes->base_price)),
                        'gst_total_amount' => \Illuminate\Support\Facades\DB::raw('gst_total_amount + ' . ($getTypes->gst)),
                        'admin_commission' => \Illuminate\Support\Facades\DB::raw('admin_commission + ' . ($getTypes->platform_fee)),
                    ]);
                } else {
                    DonateTrust::where('id', $getTypes->trust_id)->update([
                        'trust_total_withdrawal' => \Illuminate\Support\Facades\DB::raw('trust_total_withdrawal + ' . (($getTypes->receipt_fee ?? 0) + ($getTypes->base_price ?? 0))),
                        'gst_total_amount' => \Illuminate\Support\Facades\DB::raw('gst_total_amount + ' . ($getTypes->gst)),
                        'admin_commission' => \Illuminate\Support\Facades\DB::raw('admin_commission + ' . ($getTypes->platform_fee)),
                    ]);
                }

                $purohits = \App\Models\Purohit::where('temple_id', $getTypes->temple_id)->where('id', $getTypes->pandit_id)
                    ->where('status', 1)
                    ->get();
                $order->load('temple');
                $orderGet->load('package');

                // Toastr::success(translate($request->payment_mode . ' payment recorded successfully!'));
                $memberNames = collect($customers)->pluck('name')->filter()->implode(', ');

                $serviceType = $detail->type == 'puja' ? 'Pooja Booking' : ($detail->type == 'darshan' ? 'Darshan Booking' : ($detail->type == 'bhojan' ? 'Bhojan Booking' : 'Locker Booking'));

                $whatsapp_message_data[$detail->type] = [
                    'Service' => $serviceType,
                    'Package Name' => $detail['package']['varient_name'],
                    'Booking Date' => date('d-m-Y', strtotime($detail->booking_date)),
                    'Amount' => webCurrencyConverter($detail['amount']),
                ];

                // Add time slot
                if (!empty($detail['time_slot_id'])) {
                    $whatsapp_message_data[$detail->type]['Time Slot'] =
                        $detail['timeslot']['start_time'] . '-' . $detail['timeslot']['end_time'];
                }
                $customers = json_decode($detail['customers'], true);
                $lockerItems = json_decode($detail['locker_items'], true);

                if (!empty($customers)) {
                    $whatsapp_message_data[$detail->type]['Customers'] =
                        collect($customers)->pluck('name')->implode(', ');
                }

                if (!empty($lockerItems)) {
                    $whatsapp_message_data[$detail->type]['Locker Items'] =
                        collect($lockerItems)->map(fn($v, $k) => "$k($v)")->implode(', ');
                }
            }

            // email
            $userInfo = User::where('id', ($leads->user_id ?? ""))->first();
            // $service_name = TempleServicePrice::where('temple_id', $leads->temple_id)->where('package_id', $detail->package_id)->where('status', 1)->first();

            // invoice
            $lead = $leads;
            $url = route('temple.show-qr-detail', ['order_id' => $lead->order_id]);
            $qrCode = new \Endroid\QrCode\QrCode($url);
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            $folder = storage_path('app/public/temple/qrcodes');
            if (!\Illuminate\Support\Facades\File::exists($folder)) {
                \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
            }
            $filePath = $folder . "/" . $lead->order_id . ".png";
            $result->saveToFile($filePath);
            $imageData = "<img class='upload-img-view' src='" . getValidImage(path: 'storage/app/public/temple/qrcodes/' . $lead->order_id . '.png', type: 'backend-product') . "' alt='' style='width:130px'>";
            $mpdf_view = \View::make('web-views.temple.invoice', compact('userInfo', 'lead', 'leadDetails', 'imageData'));
            Helpers::gen_mpdf_temple_Pdf($mpdf_view, 'temple_order_', $leads['order_id']);
            $whatsapp_message_data['attachment'] = asset('storage/app/public/temple/invoice/temple_order_' . $leads['order_id'] . '.pdf');

            // whatsapp msg 
            if ($userInfo) {
                $whatsapp_message_data['orderId'] = $leads->order_id;
                $whatsapp_message_data['final_amount'] = $leads->amount;
                $whatsapp_message_data['customer_id'] = $userInfo->id;
                $messages =  Helpers::whatsappMessage('temple', 'Service Booking', $whatsapp_message_data);
            }

            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'pooja';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Confirmation of Your Order #' . $leads->order_id;
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make(
                    'admin-views.email.email-template.temple-template',
                    compact('userInfo', 'lead', 'leadDetails')
                )->render();
                Helpers::emailSendMessage($data);
            }
            return response()->json([
                'status'  => 1,
                'message' => 'Order created successfully!',
                "url" => '',
                "imageData" => "",
                "paymentId" => "",
                'data' => $order
            ]);
        }
        $url_open = \App\Http\Controllers\Customer\PaymentController::temple_customer_payment_request($request, $leads);

        $dataemail['customer_id'] = ($customerIds[0] ?? 0);
        $dataemail['payment_link'] = ($url_open ?? '');
        $dataemail['service_name'] = $packageData['varient_name'] ?? "";
        $dataemail['final_amount'] = $totalPrice;
        Helpers::whatsappMessage('donate', 'trust_puja_order_paymant_link_message', $dataemail);

        $qrCode = new \Endroid\QrCode\QrCode($url_open);
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        $folder = storage_path('app/public/qrcodes');
        if (!\Illuminate\Support\Facades\File::exists($folder)) {
            \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
        }
        $filePath = $folder . "/vip-darshan-ticket-booking-amount.png";
        $result->saveToFile($filePath);
        $imagePath = 'storage/app/public/qrcodes/vip-darshan-ticket-booking-amount.png';
        $imageData = getValidImage(path: $imagePath, type: 'backend-product') . "?v=" . time();

        $query12 = parse_url($url_open, PHP_URL_QUERY);
        parse_str($query12, $params12);
        $paymentId = $params12['payment_id'] ?? null;
        return response()->json([
            'status'  => 2,
            'message' => 'Order created successfully!',
            "url" => $url_open,
            'imageData' => $imageData,
            "paymentId" => $paymentId,
        ]);
    }


    public function VipTicketgetorderId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'phone' => 'required',
            "transaction_id" => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }
        $getRecodes = PaymentRequest::where('id', $request['transaction_id'])->first();
        if ($getRecodes['is_paid'] == 0) {
            return response()->json(['status' => 0, "message" => "Paymant Proccess", 'data' => []], 200);
        } elseif ($getRecodes['is_paid'] == 1) {
            $old_data = \App\Models\TempleOrderDetails::with(['Temple','order'])
                ->whereHas('Temple', function ($q) use ($request) {
                    $q->where('trust_id', $request->seller['relation_id']);
                })
                ->where('order_id', json_decode($getRecodes['additional_data'] ?? "[]", true)['order_id'] ?? "")
                ->orderByDesc('id')
                ->first();
            if ($getRecodes['is_paid'] == 1 && empty($checkpayment['expires'])) {
            //     // if ($getPujaOLd->purohit_id) {
            //     //     Purohit::where('id', $getPujaOLd->purohit_id)->update(['withdrawal_amount' => \Illuminate\Support\Facades\DB::raw('withdrawal_amount + ' . $getPujaOLd->base_price)]);
            //     // }
                // DonateTrust::where('id', $old_data['order']['trust_id'])->update(['trust_total_amount' => \Illuminate\Support\Facades\DB::raw('trust_total_amount + ' . ($getPujaOLd->receipt_fee ?? 0) + ($getPujaOLd->base_price ?? 0))]);
            PaymentRequest::where('id', $request->id)->update(['expires' => date('Y-m-d h:i:s')]);
            }
            if ($old_data) {
                return response()->json(['status' => 1, "message" => "Paymant Success", 'data' => $old_data['order_id']], 200);
            } else {
                return response()->json(['status' => 0, "message" => "Recode Not Found", 'data' => []], 200);
            }
        } else {
            return response()->json(['status' => 0, "message" => "Paymant Failed", 'data' => []], 200);
        }
    }


    public function VipTicketgeneratorOrderId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => Helpers::error_processor($validator)[0]['message'],
                'errors' => $validator->errors()
            ], 200);
        }

        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid Seller',
                'recode' => 0,
                'data' => []
            ], 400);
        }
        $order = \App\Models\TempleOrderMaster::with(['temple', 'user', 'details.package'])
            ->whereHas('temple', function ($q) use ($request) {
                $q->where('trust_id', $request->seller['relation_id']);
            })
            ->where('order_id', $request->order_id)
            ->first();
        if (!$order) {
            return response()->json(['status' => 0, 'message' => 'Not Found Order Id']);
        }
        if ($order) {
            $puja_Details = $order->details->filter(function ($detail) {
                return $detail->type === 'puja';
            });
            $html2 = '';
            $folder = storage_path('app/public/qrcodes');
            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0777, true);
            }
            if ($puja_Details->isNotEmpty()) {
                $pujaDetails = $puja_Details->first();
                $qrCode = new \Endroid\QrCode\QrCode(route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => 'puja-slip', 'id' => $pujaDetails['id']]));
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);
                $folder = storage_path('app/public/qrcodes');
                if (!\Illuminate\Support\Facades\File::exists($folder)) {
                    \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
                }
                $filePath = $folder . "/vendor-cash-booking-qrs1.png";
                $result->saveToFile($filePath);
                $webPath = asset("storage/app/public/qrcodes/vendor-cash-booking-qrs1.png") . '?v=' . time();
                $html2 .= view('all-views.trustees.temple.partials.order_details', [
                    'order' => $order,
                    'detail' => $pujaDetails,
                    'qrUrl' => $webPath,
                    "puja__status" => 1,
                ])->render();
            }
            $html3 = '';
            if ($order) {
                $pujaDetails = $order->details[0] ?? [];
                $qrCode = new \Endroid\QrCode\QrCode(route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => 'all-order', 'id' => $order->id]));
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);
                $folder = storage_path('app/public/qrcodes');
                if (!\Illuminate\Support\Facades\File::exists($folder)) {
                    \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
                }
                $filePath = $folder . "/vendor-cash-booking-qrnew2.png";
                $result->saveToFile($filePath);
                $webPath = asset("storage/app/public/qrcodes/vendor-cash-booking-qrnew2.png") . '?v=' . time();
                $html3 .= view('all-views.trustees.temple.partials.order_details', [
                    'order' => $order,
                    'detail' => $pujaDetails,
                    'qrUrl' => $webPath,
                    "puja__status" => 2,
                ])->render();
            }
            $html = '';
            foreach ($order->details as $in => $detail) {
                $qrCode = new \Endroid\QrCode\QrCode(route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => 'single-order', 'id' => $detail->id]));
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);
                $folder = storage_path('app/public/qrcodes');
                if (!\Illuminate\Support\Facades\File::exists($folder)) {
                    \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
                }
                $filePath = $folder . "/vendor-cash-booking-qr" . $in . ".png";
                $result->saveToFile($filePath);
                $webPath = asset("storage/app/public/qrcodes/vendor-cash-booking-qr" . $in . ".png") . '?v=' . time();
                $html .= view('all-views.trustees.temple.partials.order_details', [
                    'order' => $order,
                    'detail' => $detail,
                    'qrUrl' => $webPath,
                    'puja__status' => 0
                ])->render();
            }
            return response()->json(['status' => 1, 'message' => 'get Data', 'data' => $html, 'data1' => $html2, 'data2' => $html3], 200);
        }
        return response()->json([
            'status' => 0,
            'message' => 'Temple Id Invalid',
            'data' => []
        ], 200);
    }

    function maskPhone($number, $start = 0, $end = 3, $maskChar = 'X')
    {
        $number = trim($number);
        $length = strlen($number);
        if ($length <= ($start + $end)) {
            return $number;
        }
        $visibleStart = substr($number, 0, $start);
        $visibleEnd   = substr($number, -$end);
        $maskedLength = $length - ($start + $end);
        return $visibleStart . str_repeat($maskChar, $maskedLength) . $visibleEnd;
    }

    //puja
    public function CreatePuja(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'puja_name' => 'required',
            'rprice' => 'required|numeric',
            'pprice' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value > $request->rprice) {
                        $fail('Purchase Price must be less than or equal to Regular price (Retailer Price).');
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }
        try {
            TrustPuja::create([
                'trust_id' => $request->seller['relation_id'],
                'puja_name' => $request->puja_name,
                'rprice' => $request->rprice,
                'pprice' => $request->pprice,
                'discount' => ($request->rprice - $request->pprice),
            ]);
            return response()->json(['status' => 1, 'message' => 'successfully Save', 'data' => []], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Sum Error', 'data' => []], 200);
        }
    }
    public function PujaList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }
        $pujaList = TrustPuja::where('trust_id', $request->seller['relation_id'])->orderBy('id', 'desc')->get();
        if ($pujaList && count($pujaList) > 0) {
            $pujaData = [];
            $gst_tax =  \App\Models\ServiceTax::find(1);
            foreach ($pujaList as $key => $value) {
                $gst_amount = (($value['pprice'] * $gst_tax['trust_puja_tax']) / 100);
                $pujaData[$key]['id'] = $value['id'];
                $pujaData[$key]['puja_name'] = $value['puja_name'];
                $pujaData[$key]['rprice'] = $value['rprice'];
                $pujaData[$key]['pprice'] = $value['pprice'];
                $pujaData[$key]['discount'] = $value['discount'];
                $pujaData[$key]['p_price'] = $value['pprice'] + ($gst_amount ?? 0);
                $pujaData[$key]['tax'] = $gst_amount;
            }
            return response()->json(['status' => 1, 'message' => 'get Data successfully', 'recode' => count($pujaList), 'data' => $pujaData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'not Found', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function PujaEdit(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $pujaList = TrustPuja::where('trust_id', $request->seller['relation_id'])->where('id', $request['id'])->first();
        if ($pujaList) {
            $pujaList1['id'] = $pujaList['id'];
            $pujaList1['puja_name'] = $pujaList['puja_name'];
            $pujaList1['rprice'] = $pujaList['rprice'];
            $pujaList1['pprice'] = $pujaList['pprice'];
            $pujaList1['discount'] = $pujaList['discount'];
            return response()->json(['status' => 1, 'message' => 'Data get successfully', 'recode' => 1, 'data' => $pujaList1], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'not Found', 'recode' => 0, 'data' => []], 200);
        }
    }
    public function PujaUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => "required",
            'puja_name' => 'required',
            'rprice' => 'required|numeric',
            'pprice' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value > $request->rprice) {
                        $fail('Purchase Price must be less than or equal to Regular price (Retailer Price).');
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }
        $pujaList = TrustPuja::where('trust_id', $request->seller['relation_id'])->where('id', $request['id'])->first();
        try {
            if ($pujaList) {
                $pujaList->puja_name =  $request->puja_name;
                $pujaList->rprice =  $request->rprice;
                $pujaList->pprice =  $request->pprice;
                $pujaList->discount =  ($request->rprice - $request->pprice);
                $pujaList->save();
                return response()->json(['status' => 1, 'message' => 'data Update successfully', 'data' => []], 200);
            } else {
                return response()->json(['status' => 1, 'message' => 'not Found Data', 'data' => []], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Sum Error', 'data' => []], 200);
        }
    }
    public function PujaDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }
        $pujaList = TrustPuja::where('trust_id', $request->seller['relation_id'])->where('id', $request['id'])->first();
        try {
            if ($pujaList) {
                $pujaList->delete();
                return response()->json(['status' => 1, 'message' => 'data deleted successfully', 'data' => []], 200);
            } else {
                return response()->json(['status' => 1, 'message' => 'not Found Data', 'data' => []], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Sum Error', 'data' => []], 200);
        }
    }

    public function pujaBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'puja_id' => 'required',
            'user_name' => 'required',
            'person_phone' => 'required',
            'payment_mode' => 'required|in:cash,online',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }
        $userfind = User::where('phone', ($request->input('person_phone') ?? ""))->first();
        if ($userfind) {
            $user_id = $userfind['id'];
        } else {
            $user = new User();
            $user->phone = $request->input('person_phone');
            $user->name = $request->input('user_name');
            $user->f_name = (explode(" ", $request->input('user_name'))[0] ?? "");
            $user->l_name = (explode(" ", $request->input('user_name'))[1] ?? "");
            $user->email = $request->input('person_phone');
            $user->password =  bcrypt('12345678');
            $user->verify_otp = $request->input('verify_otp') ?? 1;
            $user->save();
            $user_id = $user->id ?? "";
            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        $userfind = User::where('phone', ($request->input('person_phone') ?? ""))->first();
        $old_data = TrustPuja::where('trust_id', $request->seller['relation_id'])->find($request['puja_id']);
        if ($old_data && $request['payment_mode']) {
            $pujaOrder = new TrustPujaOrder();
            $pujaOrder->puja_name = $old_data['puja_name'];
            $pujaOrder->trust_id = $request->seller['relation_id'];
            $pujaOrder->user_name = $request['user_name'];
            $pujaOrder->user_phone = $request['person_phone'];
            $pujaOrder->rprice = $old_data['rprice'];
            $pujaOrder->pprice = $old_data['pprice'];
            $pujaOrder->discount = $old_data['discount'];
            $gst_tax =  \App\Models\ServiceTax::find(1);
            $pujaOrder->tax = $gst_tax['trust_puja_tax'];
            $pujaOrder->tax_amount = (($old_data['pprice'] * $gst_tax['trust_puja_tax']) / 100);
            $admin_amount = ((($old_data['pprice'] - $pujaOrder->tax_amount) * $gst_tax['trust_puja_admin_tax']) / 100);
            $final_amount = ($old_data['pprice'] - $admin_amount);
            $pujaOrder->admin_commission = $admin_amount;
            $pujaOrder->final_amount = $final_amount;
            if ($request['payment_mode'] == 'cash') {
                $pujaOrder->transaction_id = 'Cash';
                $pujaOrder->paymant_method = 'Offline';
                $pujaOrder->payment_status = 1;
                $getTrustdata = DonateTrust::where('id', $request->seller['relation_id'])->first();
                if ($getTrustdata && $getTrustdata['trust_total_amount'] >= ($admin_amount + $pujaOrder->tax_amount)) {
                    if ($gst_tax['trust_puja_admin_tax'] > 0 || $gst_tax['trust_puja_tax'] > 0) {
                        DonateTrust::where('id', $request->seller['relation_id'])->update(['trust_total_amount' => DB::raw('trust_total_amount - ' . ($admin_amount + $pujaOrder->tax_amount))]);
                        $createWith = new \App\Models\WithdrawalAmountHistory();
                        $createWith->type = 'trust';
                        $createWith->vendor_id = $request->seller['relation_id'];
                        $createWith->req_amount = ($admin_amount + $pujaOrder->tax_amount);
                        $createWith->approval_amount = ($admin_amount + $pujaOrder->tax_amount);
                        $createWith->message = "You have booked the puja in cash, so the amount has been debited accordingly.";
                        $createWith->status = 1;
                        $createWith->transcation_id = 'wallet';
                        $createWith->payment_method = 'wallet';
                        $createWith->save();
                        $pujaOrder->save();
                    } else {
                        $pujaOrder->save();
                    }
                    $dataemail['orderId'] = $pujaOrder->order_id;
                    $dataemail['admin_name'] = $request['user_name'];
                    $dataemail['admin_phone'] = $request['person_phone'];
                    $dataemail['rprice'] =  $pujaOrder->rprice;
                    $dataemail['discount'] = $old_data['discount'];
                    $dataemail['tax_amount'] = $pujaOrder->tax_amount;
                    $dataemail['paymant_model'] = 'Cash';
                    $dataemail['final_amount'] = ($old_data['pprice'] + $pujaOrder->tax_amount);
                    Helpers::whatsappMessage('donate', 'trust_puja_order_message', $dataemail);
                } else {
                    return response()->json(['success' => 0, 'message' => 'Please Wallet Rechage', 'data' => []], 200);
                }
                return response()->json(['success' => 1, 'status' => 1, 'message' => 'Puja get Successfully', 'data' => $pujaOrder], 200);
            } else {
                // $pujaOrder->save();
                $wallet_amount = 0;
                $total_amount = ($old_data['pprice'] + $pujaOrder->tax_amount);
                $onlinepay = ($old_data['pprice'] + $pujaOrder->tax_amount);
                $data = [
                    'additional_data' => [
                        'business_name' => \App\Models\BusinessSetting::where(['type' => 'company_name'])->first()->value,
                        'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                        'payment_mode' => 'web',
                        'customer_id' =>  $userfind['id'],
                        "order_id" => ($request['person_phone'] ?? ''),
                        "order_ids" => $pujaOrder,
                        "amount" => ($old_data['pprice'] ?? 0),
                        "user_name" => ($request['user_name'] ?? ''),
                        "user_email" => '',
                        "user_phone" => $request['person_phone'],
                        'total_amount' => $total_amount,
                        'wallet_amount' => $wallet_amount,
                        "online_pay" => $onlinepay,
                        'page_name' => 'trust_puja_order',
                        'success_url' => route('trust-puja-orders', [($request['person_phone'] ?? '')]),
                    ],
                    'user_id' =>  $userfind['id'],
                    "order_id" => ($request['person_phone'] ?? ''),
                    'payment_amount' => $onlinepay
                ];
                $url_open = \App\Http\Controllers\Customer\PaymentController::TrustPujaBooking($data);

                $dataemail['admin_phone'] = $request['person_phone'];
                $dataemail['admin_name'] = ($request['user_name'] ?? '');
                $dataemail['payment_link'] = ($url_open ?? '');
                $dataemail['service_name'] = $pujaOrder->puja_name;
                $dataemail['final_amount'] = $onlinepay;
                Helpers::whatsappMessage('donate', 'trust_puja_order_paymant_link_message', $dataemail);

                $qrCode = new \Endroid\QrCode\QrCode($url_open);
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);
                $folder = storage_path('app/public/qrcodes');
                if (!\Illuminate\Support\Facades\File::exists($folder)) {
                    \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
                }
                $filePath = $folder . "/trustpujabookingamount.png";
                $result->saveToFile($filePath);
                $imageData = getValidImage(path: 'storage/app/public/qrcodes/trustpujabookingamount.png', type: 'backend-product');
                return response()->json(['success' => 1, 'status' => 2, 'message' => 'Puja get Successfully', 'data' => $imageData, 'url' => $url_open], 200);
            }
            return response()->json(['success' => 1, 'status' => 1, 'message' => 'Puja get Successfully', 'data' => $pujaOrder], 200);
        } else {
            return response()->json(['success' => 0, 'message' => 'Puja Deleted Failed', 'data' => []], 200);
        }
    }

    public function PujaBookinggetorderId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }
        $old_data = TrustPujaOrder::where('trust_id', $request->seller['relation_id'])->where('user_phone', $request['phone'])->orderByDesc('id')->first();
        if ($old_data) {
            return response()->json(['status' => 1, "message" => "get Data", 'data' => $old_data['order_id']], 200);
        } else {
            return response()->json(['status' => 0, "message" => "Temple Id Invalid", 'data' => []], 200);
        }
    }

    public function PujaSleepgeneratorOrderId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }

        $old_data = TrustPujaOrder::where('trust_id', $request->seller['relation_id'])->where('order_id', $request['order_id'])->first();

        if ($old_data) {
            $htmlView = '<div style="width: 300px; padding: 10px;  border: 1px dashed #000;font-family: monospace;font-size: 14px; background: #fff; margin: auto;">';
            $phoneHtml = '';
            if (!empty($old_data['user_phone']) && !preg_match('/^0+$/', $old_data['user_phone'])) {
                $phoneHtml = '<p><strong>Phone:</strong> ' . $this->maskPhone($old_data['user_phone'], 3, 3) . '</p>';
            }
            $htmlView .= '
                <div class="receipt-body" style="border:1px dashed #000; padding:10px; margin-bottom:15px; text-align:center;">
                     <h4 class="receipt-title">PUJA RECEIPT</h4>
        <p><strong>Puja Name:</strong><span>' . $old_data['puja_name'] . '</span></p>
        <p><strong>User Name:</strong><span>' . $old_data['user_name'] . '</span></p>
        ' . $phoneHtml . '
        <p><strong>Date:</strong>' . date("d/m/Y, h:i:s A") . '</p>
        <hr>
        <table width="100%">
            <tr>
                <td>Puja Price</td>
                <td style="text-align: right;"><span>' . $old_data['rprice'] . '</span></td>
            </tr>
            <tr>
                <td>Discount Price</td>
                <td style="text-align: right;"><span>' . $old_data['discount'] . '</span></td>
            </tr>
            <tr>
                <td>Tax Price</td>
                <td style="text-align: right;"><span>' . $old_data['tax_amount'] . '</span></td>
            </tr>
            <tr>
                <td>Total Price</td>
                <td style="text-align: right;"><span>' . $old_data['pprice'] . '</span></td>
            </tr>
        </table>
                    <hr>
                    <p style="font-size:10px;"><strong>Note:</strong> This is a system-generated invoice and does not require a physical signature.</p>
                    <p style="font-size:12px;">Powered by Mahakal.com</p>
                </div>
          </div>  ';
            return response()->json(['status' => 1, 'message' => 'get Data', 'data' => $htmlView], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Temple Id Invalid', 'data' => []], 200);
    }

    public function AdsList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }
        $getads = DonateAds::select('name', 'id')->where('trust_id', $request->seller['relation_id'])->where('is_approve', 1)->where('status', 1)->get();
        if ($getads && count($getads) > 0) {
            return response()->json(['status' => 1, 'message' => 'get Data', 'data' => $getads], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function TrustDonateOrder(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Invalid Seller', 'recode' => 0, 'data' => []], 400);
        }
        $querys = DonateAllTransaction::with(['users', 'adsTrust', 'getTrust', 'PancardValid'])->where(['trust_id' => $request->seller['relation_id']])->whereIn("amount_status", [1, 2, 3]);
        if ($request['type'] == 'ads') {
            $querys->where('type', 'donate_ads');
        } elseif ($request['type'] == 'trust') {
            $querys->where('type', 'donate_trust');
        } else {
            $querys->whereIn('type', ['donate_trust', 'donate_ads']);
        }
        $querys->when(!empty($request['start_date']) && !empty($request['end_date']), function ($querys) use ($request) {
            $querys->whereBetween('created_at', [$request['start_date'], $request['end_date']]);
        });
        $querys->when(empty($request['start_date']) && !empty($request['end_date']), function ($querys) use ($request) {
            $querys->where('created_at', "<=", Carbon::parse($request['end_date'])->endOfDay());
        });
        $querys->when(!empty($request['start_date']) && empty($request['end_date']), function ($querys) use ($request) {
            $querys->where('created_at', ">=",  Carbon::parse($request['start_date'])->startOfDay());
        });
        $querys->when(!empty($request['ads_id']), function ($querys) use ($request) {
            $querys->where('ads_id', ($request['ads_id']));
        });
        $ads_transaction = $querys->get();
        $amount_sum = $tax_sum = $commission_sum = $total_sum = 0;
        if ($ads_transaction && count($ads_transaction) > 0) {
            $Array = [];
            foreach ($ads_transaction as $key => $value) {
                $Array[$key]['id'] = $value['id'];
                $Array[$key]['order_id'] = $value['trans_id'];
                $Array[$key]['donor_name'] = $value['user_name'];
                $Array[$key]['donor_phone'] = $value['user_phone'];
                $Array[$key]['user_name'] = $value['users']['name'] ?? '';
                $Array[$key]['user_phone'] = $value['users']['phone'] ?? '';

                $Array[$key]['trust_name'] = $value['getTrust']['trust_name'] ?? '';
                $Array[$key]['trust_name1'] = $value['getTrust']['name'] ?? '';
                $Array[$key]['ads_name'] = $value['adsTrust']['name'] ?? '';
                $Array[$key]['ads_type'] = $value['adsTrust']['type'] ?? '';
                $Array[$key]['ads_purpose'] = $value['adsTrust']['Purpose']['name'] ?? '';

                $Array[$key]['amount'] = $value['amount'];
                $amount_sum +=  $value['amount'];
                $Array[$key]['tax_amount'] = $value['tax_amount'];
                $tax_sum +=  $value['tax_amount'];
                $Array[$key]['admin_commission'] = $value['admin_commission'];
                $commission_sum += $value['admin_commission'];
                $Array[$key]['final_amount'] = $value['final_amount'];
                $total_sum += $value['final_amount'];
                $Array[$key]['paymant_mode'] = (($value['transaction_id'] == 'wallet') ? "Wallet" : "Online");
                $Array[$key]['date'] = date('d-m-Y h:i:s A', strtotime($value['created_at']));
                $ertiga_certificate = '';
                if ($value['ertiga_certificate']) {
                    $ertiga_certificate = getValidImage(path: 'storage/app/public/donate/certificate/' . ($value['ertiga_certificate'] ?? ''), type: 'product');
                }
                $Array[$key]['ertiga_certificate'] = $ertiga_certificate;
                $Array[$key]['invoice'] = route('donate-create-pdf-invoice', [$value['id']]);
                $Array[$key]['status'] = $value['amount_status'];
            }
            $amount = ['amount_sum' => $amount_sum, 'tax_sum' => $tax_sum, 'commission_sum' => $commission_sum, "total_sum" => $total_sum];
            return response()->json(['status' => 1, 'recode' => count($ads_transaction), 'message' => 'get Data', 'data' => $Array, 'amount' => $amount], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Invalid', 'data' => []], 200);
    }

    public function RecepitScannerUrl(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Seller', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'url' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $parsed_url = parse_url($request['url']);
        $query_params = [];
        if (isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $query_params);
        }
        $order = collect();
        if (($query_params["type"] ?? "") == "all-order" && ($query_params['id'] ?? "")) {
            $order = \App\Models\TempleOrderMaster::with(['temple', 'user', 'details.package'])
                ->where('id', $query_params['id'])->where('trust_id', $request->seller['relation_id'])
                ->first();
        } elseif (($query_params["type"] ?? "") == 'single-order' && ($query_params['id'] ?? "")) {
            $order = \App\Models\TempleOrderMaster::with(['temple', 'user', 'details' => function ($query) use ($query_params) {
                $query->where('id', $query_params['id'])->with('package');
            }])
                ->whereHas('details', function ($query) use ($query_params) {
                    $query->where('id', $query_params['id']);
                })->where('trust_id', $request->seller['relation_id'])
                ->first();
        } elseif (($query_params["type"] ?? "") == "puja-slip" && ($query_params['id'] ?? "")) {
            $order = \App\Models\TempleOrderMaster::with(['temple', 'user', 'details' => function ($query) use ($query_params) {
                $query->where('id', $query_params['id'])->with('package');
            }])
                ->whereHas('details', function ($query) use ($query_params) {
                    $query->where('id', $query_params['id']);
                })->where('trust_id', $request->seller['relation_id'])
                ->first();
        } else {
            $parts = explode('/', trim(($request['url'] ?? ""), '/'));
            $idUrls = end($parts);
            $order = \App\Models\TempleOrderMaster::with(['temple', 'user', 'details.package'])
                ->where('order_id', $idUrls)->where('trust_id', $request->seller['relation_id'])
                ->first();
        }

        $OrderArray = [];
        if ($order && $order->details && count($order->details) > 0) {
            $totalAmount = 0;
            foreach ($order->details as $keys => $value) {
                if (is_numeric($value['final_amount']) && ($query_params["type"] ?? "") == 'puja-slip') {
                    $totalAmount += $value['receipt_fee'];
                } elseif (is_numeric($value['final_amount'])) {
                    $totalAmount += $value['final_amount'];
                }
            }

            $OrderArray['temple_name'] =  strtoupper($order->temple->name ?? 'TEMPLE NAME');
            $OrderArray['id'] =   $value->id ?? "";
            $OrderArray['orderid'] =   $order->order_id ?? "";
            $OrderArray['order_date'] =  $order->created_at->format('d M Y');
            $OrderArray['order_amount'] = number_format(($totalAmount ?? 0), 2);
            $OrderArray['payment_mode'] =  ucfirst($order->payment_mode);
            foreach ($order->details as $ind => $detail) {
                $OrderArray['service'][$ind]['id'] = $detail['id'];
                $OrderArray['service'][$ind]['payment_mode'] = ucfirst($detail->type ?? '-') . " (" . ($detail->package->varient_name ?? '-') . ")";
                $OrderArray['service'][$ind]['yajman'] = ($order->user->name ?? '-');
                $OrderArray['service'][$ind]['purohit'] = ((strtolower($detail->type ?? '') == 'puja') ? ($detail->purohit->name ?? '-') : "");
                if (($query_params["type"] ?? "") == 'puja-slip') {
                    $amount = number_format($detail->receipt_fee ?? 0, 2);
                } else {
                    $amount = number_format($detail->final_amount ?? 0, 2);
                }
                $mode = ucfirst($order->payment_mode);
                $status = strtolower($detail->booking_status);
                $OrderArray['service'][$ind]['payment'] = $amount;
                $OrderArray['service'][$ind]['payment_model'] = $mode;
                $OrderArray['service'][$ind]['payment_status'] = $status;
                $userData = [];
                if ($detail->customers && json_decode($detail->customers, true)) {
                    foreach (json_decode($detail->customers, true) as $index => $vval) {
                        $userData[$index]['id'] = ($index);
                        $userData[$index]['name'] = ($vval['name'] ?? "");
                        $userData[$index]['mobile'] = ($vval['mobile'] ?? "");
                        $userData[$index]['aadhaar'] = ($vval['aadhaar'] ?? "");
                        $getAddhar = \App\Models\UserAadhaarKyc::where('aadhaar_number', ($vval['aadhaar'] ?? ""))->first();
                        $userData[$index]['aadhaar_image'] = ($getAddhar['image'] ?? "");
                        $userData[$index]['verify_status'] = (string)($vval['verify_status'] ?? "0");
                        $userData[$index]['verify_date'] = ($vval['verify_date'] ?? "");
                        $userData[$index]['verify_userid'] = (string)($vval['verify_userid'] ?? "");
                        $userData[$index]['verify_usertype'] = ($vval['verify_usertype'] ?? "");
                    }
                }
                $OrderArray['service'][$ind]['user'] = $userData;
            }
        }
        if ($OrderArray) {
            return response()->json(['status' => 1, 'message' => 'get Success', 'data' => $OrderArray], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Invalid', 'data' => []], 200);
        }
    }

    public function ScannerRecepitVerify(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Seller', 'recode' => 0, 'data' => []], 200);
        }
        $users = $request->input('user');
        $users = json_decode($users ?? "[]", true);
        if (empty($users)) {
            return response()->json([
                'success' => false,
                'message' => 'No users selected for verification'
            ]);
        }
        foreach ($users as $userData) {
            $detailId = $userData['order_id'] ?? null;
            $userId = $userData['user_id'] ?? null;
            if (!$detailId) {
                continue;
            }
            $orderDetail = \App\Models\TempleOrderDetails::find($detailId);
            if ($orderDetail) {
                $customers = json_decode($orderDetail->customers, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($customers)) {
                    $customerIndex = $userId;
                    if (isset($customers[$customerIndex])) {
                        $customers[$customerIndex]['verify_status'] = 1;
                        $customers[$customerIndex]['verify_date'] = date('d-m-Y h:i A');
                        // if (auth('trust')->check()) {
                        $seller = $request->seller['relation_id'] ?? '';
                        $sellertype = 'vendor';
                        // } elseif (auth('trust_employee')->check()) {
                        //     $seller = auth('trust_employee')->id();
                        //     $sellertype = 'employee';
                        // }
                        $customers[$customerIndex]['verify_userid'] = $seller;
                        $customers[$customerIndex]['verify_usertype'] = $sellertype;
                        $orderDetail->customers = json_encode($customers);
                        $orderDetail->save();
                    }
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Verification Successfully'
        ]);
    }

    public function OrderListUserVerified(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Seller', 'recode' => 0, 'data' => []], 200);
        }
        $searchValue = $request->input('search_by_name', '');
        $payment_mode = $request->input('payment_mode', '');
        $purohit_id = $request->input('purohit_id', '');
        $employee_id = $request->input('employee_id', '');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $query = \App\Models\TempleOrderMaster::with(['temple', 'user', 'details', 'details.package']);
        $query->when(!empty($payment_mode), function ($query) use ($payment_mode) {
            return $query->where(['payment_mode' => $payment_mode]);
        })->whereHas('details', function ($q) use ($start_date, $end_date, $purohit_id, $employee_id) {
            if ($start_date && empty($end_date)) {
                $q->where('booking_date', date('Y-m-d', strtotime($start_date)));
            } elseif ($start_date && $end_date) {
                $q->whereBetween('booking_date', [$start_date, $end_date]);
            }
            $q->when($purohit_id, function ($q1) use ($purohit_id) {
                $q1->where('purohit_id', $purohit_id);
            });
            $q->when($employee_id, function ($q1) use ($employee_id) {
                $q1->where('emp_id', $employee_id);
            });
        });
        if (($request['type'] ?? "") == 'trust_employee'  && (optional(\App\Models\VendorRoles::find($request->seller['emp_role_id']))->name === 'Sub Pandit')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('purohit_id', $request->seller['purohit_id'])->where('emp_id', $request->seller['id']);
            });
            $query->where('trust_id', $request->seller['relation_id']);
        } elseif (($request['type'] ?? "") == 'purohit') {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('purohit_id', $request->seller['id']);
                if ($request['emp_id']) {
                    $q->where('emp_id', $request['emp_id']);
                }
            });
            $query->where('trust_id', ($request->seller['relation_id'] ?? ""));
        } else {
            $query->where('trust_id', $request->seller['relation_id']);
        }
        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where(function ($querys) use ($searchValue) {
                    $querys->where('order_id', 'like', "%$searchValue%")
                        ->orWhereHas('temple', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        })
                        ->orWhereHas('user', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        });
                });
            });
        }
        $query->where('payment_status', 1);
        $getData = $query->orderBy('id', 'desc')->get();
        if ($getData) {
            $UserList = [];
            $serviceCounts = [];
            $serviceCounts2 = [];
            foreach ($getData as $key => $value) {
                $purohit_name = '-';
                if ($value['details'] && $value['details']->count() > 0) {
                    $purohitName = $value['details']->first()->purohit->name ?? '-';
                    $purohit_name = $purohitName;
                }
                $service_list = [];
                if ($value['details'] && count($value['details']) > 0) {
                    foreach ($value['details'] as $va) {
                        $getcustomers = json_decode($va['customers'] ?? "[]", true);
                        $serviceType = ucwords($va['type'] ?? 'Unknown');
                        if (!isset($serviceCounts[$serviceType])) {
                            $serviceCounts[$serviceType] = [
                                'name' => $serviceType,
                                'total' => 0,
                                "verify" => 0,
                                "notverify" => 0,
                            ];
                        }
                        foreach ($getcustomers as $keys => $valn) {
                            $service_list[] = [
                                "type" => ucwords($va['type']),
                                "name" => $valn['name'],
                                "verify_status" => (string)($valn['verify_status'] ?? 0),
                            ];
                            $serviceCounts[$serviceType]['total']++;

                            if (($valn['verify_status'] ?? 0) == '1') {
                                $serviceCounts[$serviceType]['verify']++;
                            } else {
                                $serviceCounts[$serviceType]['notverify']++;
                            }
                        }
                    }
                }
                $serviceCounts2 = [];
                foreach ($serviceCounts as $serviceType => $counts) {
                    $serviceCounts2[] = $counts;
                }
                $UserList[] = [
                    'id' => $value['id'],
                    'order_id' => $value['order_id'],
                    'temple_name' => ($value['temple']['name'] ?? ""),
                    "purohit_name" => $purohit_name,
                    'service' => (array)$service_list,
                    'yajman_name' => ($value['user']['name'] ?? '-') . " (" . ($value['total_people_count'] ?? 0) . ")",
                    'amount' => ($value['total_amount'] ?? 0),
                    'platform' => ucwords($value['platform'] ?? ""),
                    'payment_mode' => $value['payment_mode'],
                    'create_by' => date('d M,Y h:i A', strtotime($value['created_at'] ?? "")),
                    "booking_status" => $value['booking_status'],
                ];
            }
            return response()->json(['status' => 1, 'message' => 'Data Get Successfully', 'recode' => count($getData), 'data' => $UserList, 'servicecounts' => $serviceCounts2], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found User', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function OrderDetails(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Seller', 'recode' => 0, 'data' => []], 200);
        }
        $getData = TempleOrderDetails::with(['order', 'package', 'purohit'])->where('order_id', ($request['order_id'] ?? ""))->get();
        if ($getData && count($getData) > 0) {
            $newArray = [];
            $newArray['payment_mode'] = $getData[0]['order']['payment_mode'] ?? "";
            if ($getData[0]['order']['payment_status'] == 0) {
                $newArray['payment_status'] = "Pending";
            } elseif ($getData[0]['order']['payment_status'] == 1) {
                $newArray['payment_status'] = "Confirmed";
            } elseif ($getData[0]['order']['payment_status'] == 2) {
                $newArray['payment_status'] = "Cancelled";
            } else {
                $newArray['payment_status'] = "Unknown";
            }
            $newArray['total_amount'] = setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData[0]['order']['total_amount'] ?? 0), currencyCode: getCurrencyCode());
            $first_customerGet = '';
            foreach ($getData as $key => $detail) {
                if (empty($first_customerGet)) {
                    $first_customerGet = (json_decode($detail['customers'] ?? "[]", true)[0]['name'] ?? "");
                }
                $customerlist = [];
                $members = json_decode($detail->customers ?? '[]', true);
                $lockeritems = json_decode($detail['locker_items'], true);
                if (!empty($members)) {
                    foreach ($members as $member) {
                        $customerlist[] = [
                            "name" => $member['name'] ?? "",
                            "mobile" => ($member['mobile'] ?? 'N/A'),
                            'aadhar' => ($member['aadhar'] ?? ''),
                        ];
                    }
                }
                $newArray['list'][] = [
                    'type' => $detail['type'],
                    "varient_name" => ($detail->package->varient_name ?? '-'),
                    'booking_date' => (!empty($detail->booking_date) ? \Carbon\Carbon::parse($detail->booking_date)->format('d M Y') : '-'),
                    'final_amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $detail->final_amount ?? 0), currencyCode: getCurrencyCode()),
                    'type_order_id' => ($detail->type_order_id ?? ""),
                    "start_time" => (!empty($detail->timeslot) ? \Carbon\Carbon::parse($detail->timeslot->start_time)->format('h:i A') : '-'),
                    "end_time" => (!empty($detail->timeslot) ? \Carbon\Carbon::parse($detail->timeslot->end_time)->format('h:i A') : '-'),
                    "customerlist" => $customerlist,
                    "locker_mobile" => $lockeritems['mobile'] ?? '',
                    "locker_luggage" => $lockeritems['luggage'] ?? '',
                    "purohit_name" => ($detail->purohit->name ?? '-'),
                ];
            }
            $newArray['customer_name'] = ($getData[0]['order']['user']['name'] ?? ($first_customerGet));
            $newArray['customer_count'] = ($getData[0]['order']['total_people_count'] ?? 0);
            $newArray['temple_name'] = ($getData[0]['order']['temple']['name'] ?? '-');
            $newArray['temple_city'] = ($getData[0]['order']['temple']['cities']['city'] ?? '');
            $newArray['temple_state'] = (ucwords(strtolower($getData[0]['order']['temple']['states']['name'] ?? '')));
            $newArray['temple_country'] = ($getData[0]['order']['temple']['country']['name'] ?? '');
            return response()->json(['status' => 1, 'message' => 'Get Data Successfully', 'data' => $newArray], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 200);
        }
    }
    public function GetPermissionModule(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Seller', 'recode' => 0, 'data' => []], 200);
        }
        $getPermissions = [];
        $getData = \App\Models\VendorRoles::where('id', $request->seller['emp_role_id'])->where('status', 1)->first();
        if ($getData) {
            $getPermissions['type'] = $getData['type'];
            $getPermissions['name'] = $getData['name'];
            $getPermissions['permission'] = \App\Models\VendorPermissions::select("type", "module", "sub_module", "permission")->where('type', $getData['type'])->get();
            $getPermissions['applypermission'] = \App\Models\VendorPermissionRole::select("module", "sub_module", "permission")->where('role_id', $request->seller['emp_role_id'])->get();
            return response()->json([
                'success' => true,
                'message' => 'get Permission Successfully',
                "data" => $getPermissions
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Not Found',
                "data" => $getPermissions
            ]);
        }
    }

    public function WithdrawalReqAdd(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            "holder_name"    => "required_without:upi_code",
            "bank_name"      => "required_without:upi_code",
            "ifsc_code"      => "required_without:upi_code",
            "account_number" => "required_without:upi_code",
            "upi_code"       => "required_without_all:holder_name,bank_name,ifsc_code,account_number",
            "req_amount"     => "required|numeric|min:1",
            "type" => "nullable|in:trust,purohit,trust_employee",
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $InType = '';
        if (($request['type'] ?? "") == 'purohit') {
            // $getData = \App\Models\Purohit::with(['temple'])->where('id',$request->seller['id'])->first();
            // $vendorId =  $getData['temple']['trust_id']??0;
            // $exId = $request->seller['id'];
            // $InType = 'purohit';
            // $old_total_amount = ($getData['withdrawal_amount'] ?? 0);
            return response()->json(['status' => 0, 'message' => translate('Payment_Request_Not_sent'), 'recode' => 0, 'data' => []], 200);
        } elseif (($request['type'] ?? "") == 'trust_employee' && (optional(\App\Models\VendorRoles::find($request->seller['emp_role_id']))->name === 'Sub Pandit')) {
            $vendorId =  $request->seller['relation_id'];
            $exId = $request->seller['id'];
            $getData = \App\Models\VendorEmployees::find($request->seller['id']);
            $InType = 'purohit';
            $old_total_amount = ($getData['withdrawal_amount'] ?? 0);
        } else {
            $vendorId =  $request->seller['relation_id'];
            $exId = 0;
            $getData = \App\Models\DonateTrust::find($request->seller['relation_id']);
            $old_total_amount = ($getData['trust_total_amount'] ?? 0);
            $InType = 'trust';
        }

        if (!\App\Models\WithdrawalAmountHistory::where(['vendor_id' => $vendorId, 'ex_id' => $exId, 'type' => ($InType ?? ''), 'status' => 0])->exists()) {
            if ($request['req_amount'] <= $old_total_amount) {
                $withdrawal  =  new \App\Models\WithdrawalAmountHistory();
                $withdrawal->type = $InType;
                $withdrawal->vendor_id = $vendorId;
                $withdrawal->ex_id = $exId;
                $withdrawal->holder_name = $request['holder_name'] ?? "";
                $withdrawal->bank_name = $request['bank_name'] ?? "";
                $withdrawal->ifsc_code = $request['ifsc_code'] ?? "";
                $withdrawal->account_number = $request['account_number'] ?? "";
                $withdrawal->upi_code = $request['upi_code'] ?? '';
                $withdrawal->old_wallet_amount = $old_total_amount;
                $withdrawal->req_amount = $request['req_amount'];
                $withdrawal->save();
                if ($withdrawal) {
                    if (($request['type'] ?? "") == 'purohit') {
                        $getData->update(['requested_amount' => $request['req_amount']]);
                    } elseif (($request['type'] ?? "") == 'trust_employee' && (optional(\App\Models\VendorRoles::find($request->seller['emp_role_id']))->name === 'Sub Pandit')) {
                        $getData->update(['requested_amount' => $request['req_amount']]);
                    } else {
                        $getData->update(['trust_req_withdrawal_amount' => $request['req_amount']]);
                    }
                }
                return response()->json(['status' => 1, 'message' => translate('Payment_request_sent_successfully'), 'recode' => 0, 'data' => []], 200);
            } else {
                return response()->json(['status' => 0, 'message' => translate('Payment_Request_failed'), 'recode' => 0, 'data' => []], 200);
            }
        } else {
            return response()->json(['status' => 0, 'message' => translate('A_payment_request_has_already_been_sent'), 'recode' => 0, 'data' => []], 200);
        }
    }
    public function WithdrawalList(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Trust Id', 'recode' => 0, 'data' => []], 200);
        }
        $InType = '';
        if (($request['type'] ?? "") == 'purohit') {
            $getData = \App\Models\Purohit::with(['temple'])->where('id', $request->seller['id'])->first();
            $vendorId =  $getData['temple']['trust_id'] ?? 0;
            $exId = 0;
            $InType = 'purohit';
            return response()->json(['status' => 0, 'message' => translate('Payment_Request_Not_sent'), 'recode' => 0, 'data' => []], 200);
        } elseif (($request['type'] ?? "") == 'trust_employee' && (optional(\App\Models\VendorRoles::find($request->seller['emp_role_id']))->name === 'Sub Pandit')) {
            $vendorId =  $request->seller['relation_id'];
            $exId = $request->seller['id'];
            $InType = 'purohit';
        } else {
            $vendorId =  $request->seller['relation_id'];
            $exId = 0;
            $InType = 'trust';
        }
        if (($request['type'] ?? "") == 'trust_employee'  && (optional(\App\Models\VendorRoles::find($request->seller['emp_role_id']))->name === 'Sub Pandit')) {
            $withdrawRequests = \App\Models\WithdrawalAmountHistory::with(['Trust'])->where(['vendor_id' => $vendorId, 'ex_id' => $exId, 'type' => ($InType ?? '')])->orderBy('id', 'desc')->get();
            $getData = \App\Models\VendorEmployees::find($request->seller['id']);
            $dashboardData = [
                'totalEarning' => $getData['withdrawal_amount'] ?? 0,
                'pendingWithdraw' => $getData['requested_amount'] ?? 0,
                "adminCommission" => ($getData['platform_fee'] ?? 0) + ($getData['trust_fee'] ?? 0),
                "withdrawn" => $getData['collected_amount'] ?? 0,
                'collectedTotalTax' => $getData['gst_amount'] ?? 0,
            ];
        } else if (($request['type'] ?? "") == 'purohit') {
            $withdrawRequests = \App\Models\WithdrawalAmountHistory::with(['Trust'])->where(['vendor_id' => $vendorId, 'type' => ($InType ?? '')])->orderBy('id', 'desc')->get();
            $getData = \App\Models\Purohit::find($request->seller['id']);
            $dashboardData = [
                'totalEarning' => $getData['withdrawal_amount'] ?? 0,
                'pendingWithdraw' => $getData['requested_amount'] ?? 0,
                "adminCommission" => ($getData['platform_fee'] ?? 0) + ($getData['trust_fee'] ?? 0),
                "withdrawn" => $getData['collected_amount'] ?? 0,
                'collectedTotalTax' => $getData['gst_amount'] ?? 0,
            ];
        } else {
            $withdrawRequests = \App\Models\WithdrawalAmountHistory::with(['Trust'])->where(['vendor_id' => $vendorId, 'type' => "trust"])->orderBy('id', 'desc')->get();
            $getData = \App\Models\DonateTrust::find($request->seller['relation_id']);
            $dashboardData = [
                'totalEarning' => $getData['trust_total_amount'] ?? 0,
                'pendingWithdraw' => $getData['trust_req_withdrawal_amount'] ?? 0,
                "adminCommission" => $getData['admin_commission'] ?? 0,
                "withdrawn" => $getData['trust_total_withdrawal'] ?? 0,
                'collectedTotalTax' => $getData['gst_total_amount'] ?? 0,
            ];
        }
        if ($withdrawRequests && count($withdrawRequests) > 0) {
            $filtered = $withdrawRequests->map(function ($trust) {
                return [
                    'id' => $trust->id,
                    'old_wallet_amount' => $trust->old_wallet_amount,
                    'req_amount' => $trust->req_amount,
                    'approval_amount' => $trust->approval_amount,
                    'message' => $trust->message,
                    'transcation_id' => $trust->transcation_id,
                    'payment_method' => $trust->payment_method,
                    'upi_code' => $trust->upi_code,
                    'bank_name' => $trust->bank_name,
                    'branch_code' => $trust->branch_code,
                    'ifsc_code' => $trust->ifsc_code,
                    'account_number' => $trust->account_number,
                    'holder_name' => $trust->holder_name,
                    'status' => $trust->status,
                    'created_at' => date("d-m-Y h:i A", strtotime($trust->created_at)),
                ];
            });
            return response()->json(['status' => 1, 'message' => 'Get Request List Successfully', 'recode' => count($withdrawRequests), 'wallet' => $dashboardData, 'data' => $filtered], 200);
        } else {
            return response()->json(['status' => 1, 'message' => 'wallet Amount', 'recode' => 0, 'wallet' => $dashboardData, 'data' => []], 200);
        }
    }


    public function reset_password_submit(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Trust Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:8',
            'type' => 'required|in:trust_employee,trust,purohit'
        ], [
            'password.confirmed' => 'The new password and confirmation password do not match.'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'errors' => Helpers::error_processor($validator)], 403);
        }
        $user = null;
        if ($request->type === 'trust') {
            $user = auth('seller')->user();
            if (!$user || $user->type !== 'trust') {
                return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Unauthorized access or invalid account type.'], 200);
            }
        } elseif ($request->type === 'purohit') {
            $token = $request->bearerToken();

            if ($token) {
                $user = \App\Models\Purohit::where('auth_token', $token)
                    ->where('status', 1)
                    ->first();
            }
            if (!$user) {
                return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Unauthorized access.'], 200);
            }
        } elseif ($request->type === 'trust_employee') {
            $token = $request->bearerToken();

            if ($token) {
                $user = \App\Models\VendorEmployees::where('auth_token', $token)
                    ->where('type', 'trust')
                    ->where('status', 1)
                    ->first();
            }
            if (!$user) {
                return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Unauthorized access.'], 200);
            }
        }
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['status' => 0, 'code' => 'invalid', 'message' => 'Old password is incorrect.'], 200);
        }
        if (Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 0, 'code' => 'invalid', 'message' => 'New password cannot be the same as old password.'], 200);
        }

        try {
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json([
                'status' => 1,
                'message' => 'Password changed successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(
                ['status' => 0, 'code' => 'server_error', 'message' => 'Failed to update password. Please try again.'],
                200
            );
        }
    }


    public function PurohitEmployee(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Not Found Organizer Id', 'data' => []], 200);
        }
        $token = $request->bearerToken();
        $user = null;
        if ($token) {
            $user = \App\Models\Purohit::where('auth_token', $token)->where('status', 1)->first();
        }
        if (!$user) {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Unauthorized access.', 'data' => []], 200);
        }
        $getData = \App\Models\VendorEmployees::where('purohit_id', $user['id'])->get();
        if ($getData && count($getData) > 0) {
            $NewData = [];
            foreach ($getData as $value) {
                $NewData[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'image' => getValidImage(path: 'storage/app/public/event/employee/' . ($value['image'] ?? ''), type: 'backend-product'),
                    'total_order' => \App\Models\TempleOrderDetails::where(['purohit_id' => $value['purohit_id'], "emp_id" => $value['id'], 'status' => 1, 'payment_status' => 1, 'type' => "puja"])->count(),
                ];
            }
            return response()->json(['status' => 1, 'code' => '', 'message' => 'Successfully.', 'data' => $NewData], 200);
        } else {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Not Found Data.', 'data' => []], 200);
        }
    }

    public function EmployeeServiceList(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Not Found Organizer Id', 'data' => []], 200);
        }
        if (($request['type'] ?? "") == 'purohit') {
            $getData = \App\Models\Purohit::with(['temple'])->where('id', $request->seller['id'])->first();
            $trust_id = $getData['temple']['trust_id'] ?? 0;
        } elseif (($request['type'] ?? "") == 'trust_employee' && (optional(\App\Models\VendorRoles::find($request->seller['emp_role_id']))->name === 'Sub Pandit')) {
            $getData = \App\Models\VendorEmployees::find($request->seller['id']);
            $trust_id = $getData['relation_id'] ?? 0;
        } else {
            $trust_id =  $request->seller['relation_id'];
        }
        $roleList = \App\Models\VendorRoles::select('id', 'name')->where('type', 'trust')
            ->when(((($request['type'] ?? "") == 'purohit') || ((($request['type'] ?? "") == 'trust_employee') && ((\App\Models\VendorRoles::where('id', $request->seller['emp_role_id'])->first()['name'] ?? "") == 'Sub Pandit'))), function ($q) {
                $q->where('name', 'Sub Pandit');
            })->get();
        $templeList = Temple::select('id', 'name', 'package_service')->where('status', 1)->where('trust_id', $trust_id)
            ->when(((($request['type'] ?? "") == 'purohit') || ((($request['type'] ?? "") == 'trust_employee') && ((\App\Models\VendorRoles::where('id', $request->seller['emp_role_id'])->first()['name'] ?? "") == 'Sub Pandit'))), function ($q) use ($request) {
                $q->where('id', $request->seller['temple_id']);
            })->get()->makeHidden(['translations'])->map(function ($temple) {
                $packageServices = json_decode($temple->package_service, true) ?? [];
                $filteredServices = array_filter($packageServices, function ($service) {
                    return ($service['status'] ?? 0) == 1;
                });
                $temple->package_service = array_values($filteredServices);
                return $temple;
            });

        $templeIds = $templeList->pluck('id')->toArray();
        $purohitsList = \App\Models\Purohit::select('id', 'name')->whereIn('temple_id', $templeIds)
            ->when((($request['type'] ?? "") == 'purohit'), function ($q) use ($request) {
                $q->where('id', $request->seller['id']);
            })
            ->when(((($request['type'] ?? "") == 'trust_employee') && ((\App\Models\VendorRoles::where('id', $request->seller['emp_role_id'])->first()['name'] ?? "") == 'Sub Pandit')), function ($q) use ($request) {
                $q->where('id', $request->seller['purohit_id']);
            })->get();

        return response()->json(['status' => true, 'code' => '', 'message' => 'Successfully', 'data' => ['role' => $roleList, 'templelist' => $templeList, 'purohit' => $purohitsList]]);
    }

    public function EmployeeAdd(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Not Found Organizer Id', 'data' => []], 200);
        }
        $request->validate([
            'identify_number' => 'required|unique:vendor_employee,identify_number',
            'name' => 'required',
            'email' => 'required|unique:vendor_employee,email|unique:sellers,email',
            'em_phone' => 'required|unique:vendor_employee,phone|unique:sellers,phone|unique:purohits,mobile',
            'emp_role_id' => 'required|exists:vendor_roles,id',
            'temple_id' => 'required',
        ]);
        $employee = new VendorEmployees();
        $employee->identify_number = $request['identify_number'];
        $employee->type = 'trust';
        $employee->name = $request['name'];
        $employee->phone = $request['em_phone'];
        $employee->email = $request['email'];
        $employee->emp_role_id = $request['emp_role_id'];
        $employee->temple_id = $request['temple_id'];
        $employee->purohit_id = $request['purohit_id'] ?? 0;
        $employee->password = bcrypt('12345678');
        $employee->selected_services = json_encode(json_decode($request['selected_services'] ?? "[]", true) ?? "[]");
        if ($request['image']) {
            $fileName = $imageName = time() . '_' . uniqid() . '.' . $request['image']->getClientOriginalExtension();
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('event/employee')) {
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('event/employee');
            }
            \Illuminate\Support\Facades\Storage::disk('public')->put('event/employee/' . $fileName, file_get_contents($request['image']));
            $employee->image = $imageName;
        }
        if (($request['type'] ?? "") == 'purohit') {
            $getData = \App\Models\Purohit::with(['temple'])->where('id', $request->seller['id'])->first();
            $employee->relation_id = $getData['temple']['trust_id'] ?? 0;
        } elseif (($request['type'] ?? "") == 'trust_employee' && (optional(\App\Models\VendorRoles::find($request->seller['emp_role_id']))->name === 'Sub Pandit')) {
            $getData = \App\Models\VendorEmployees::find($request->seller['id']);
            $employee->relation_id = $getData['relation_id'] ?? 0;
        } else {
            $employee->relation_id =  $request->seller['relation_id'];
        }
        $employee->save();
        return response()->json(['status' => 1, 'code' => '', 'message' => 'Employee added successfully', 'data' => []], 200);
    }

    public function EmployeeList(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Not Found Trust Id', 'data' => []], 200);
        }
        $employees = \App\Models\VendorEmployees::select('id', 'identify_number', 'name', 'phone', 'email', 'emp_role_id', 'temple_id', 'purohit_id', 'selected_services', 'image', 'status')->where('type', 'trust')
            ->when((($request['type'] ?? "") == 'purohit'), function ($q) use ($request) {
                $q->where('purohit_id', $request->seller['id']);
            })
            ->when(((($request['type'] ?? "") == 'trust_employee') && ((\App\Models\VendorRoles::where('id', $request->seller['emp_role_id'])->first()['name'] ?? "") == 'Sub Pandit')), function ($q) use ($request) {
                $q->where('id', $request->seller['id']);
            })->get()->map(function ($temple) {
                $temple->image = getValidImage(path: 'storage/app/public/event/employee/' . ($temple['image'] ?? ''), type: 'backend-product');
                $temple->role_name = (\App\Models\VendorRoles::where('id', $temple['emp_role_id'])->first()['name'] ?? "");
                $temple->temple_name = (\App\Models\Temple::where('id', $temple['temple_id'])->first()['name'] ?? "");
                $temple->purohit_name = (\App\Models\Purohit::where('id', $temple['purohit_id'])->first()['name'] ?? "");
                $temple->selected_services = json_decode($temple['selected_services'] ?? "[]", true);
                $temple->total_order =  \App\Models\TempleOrderDetails::where(['purohit_id' => $temple['purohit_id'], "emp_id" => $temple['id'], 'status' => 1, 'payment_status' => 1, 'type' => "puja"])->count();
                return $temple;
            });
        if ($employees && count($employees) > 0) {
            return response()->json(['status' => 1, 'code' => '', 'message' => 'All Employee List', 'data' => $employees], 200);
        }
        return response()->json(['status' => 1, 'code' => '', 'message' => 'Not Found Employee List', 'data' => []], 200);
    }

    public function EmployeeGetById(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Not Found Organizer Id', 'data' => []], 200);
        }
        $employees = \App\Models\VendorEmployees::select('identify_number', 'name', 'phone', 'email', 'emp_role_id', 'temple_id', 'purohit_id', 'selected_services', 'image', 'status')->where('type', 'trust')
            ->when((($request['type'] ?? "") == 'purohit'), function ($q) use ($request) {
                $q->where('purohit_id', $request->seller['id']);
            })
            ->when(((($request['type'] ?? "") == 'trust_employee') && ((\App\Models\VendorRoles::where('id', $request->seller['emp_role_id'])->first()['name'] ?? "") == 'Sub Pandit')), function ($q) use ($request) {
                $q->where('id', $request->seller['id']);
            })->where('id', $request['id'])->first();
        if ($employees) {
            $employees->image = getValidImage(path: 'storage/app/public/event/employee/' . ($employees->image ?? ''), type: 'backend-product');
            $selectedServices = $employee->selected_services ?? "[]";
            try {
                $employees->selected_services = json_decode($selectedServices, true) ?? [];
            } catch (\Exception $e) {
                $employees->selected_services = [];
            }
            return response()->json(['status' => 1, 'code' => '', 'message' => 'Get Employee', 'data' => $employees], 200);
        }
        return response()->json(['status' => 1, 'code' => '', 'message' => 'Not Found Employee', 'data' => []], 200);
    }

    public function EmployeeUpdate(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Not Found Organizer Id', 'data' => []], 200);
        }
        $request->validate([
            'identify_number' => 'required|unique:vendor_employee,identify_number,' . $request->id,
            'name' => 'required',
            'email' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $vendorQuery = VendorEmployees::where('email', $value);
                    $SellerQuery = Seller::where('email', $value);
                    if ($request->id) {
                        $vendorQuery->where('id', '!=', $request->id);
                    }
                    if ($vendorQuery->exists() || $SellerQuery->exists()) {
                        $fail('The email already exists in our system.');
                    }
                },
            ],
            'em_phone' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $vendorQuery = VendorEmployees::where('phone', $value);
                    $SellerQuery = Seller::where('phone', $value);
                    $PurohitQuery = Purohit::where('mobile', $value);
                    if ($request->id) {
                        $vendorQuery->where('id', '!=', $request->id);
                    }
                    if ($vendorQuery->exists() || $SellerQuery->exists() || $PurohitQuery->exists()) {
                        $fail('The Phone already exists in our system.');
                    }
                },
            ],
            'emp_role_id' => 'required|exists:vendor_roles,id',
            'temple_id' => 'required',
        ]);
        $employee = VendorEmployees::where('id', $request['id'])->first();
        if (empty($employee)) {
            return response()->json(['status' => 0, 'code' => '', 'message' => 'Not Found Employee Please Valid Employee Id Pass', 'data' => []], 200);
        }
        $employee->identify_number = $request['identify_number'];
        $employee->type = 'trust';
        $employee->name = $request['name'];
        $employee->phone = $request['em_phone'];
        $employee->email = $request['email'];
        $employee->emp_role_id = $request['emp_role_id'];
        $employee->temple_id = $request['temple_id'];
        $employee->purohit_id = $request['purohit_id'] ?? 0;
        $employee->selected_services = json_encode(json_decode($request['selected_services'] ?? "[]", true) ?? "[]");
        if ($request['image']) {
            $filePath = "event/employee/" . $employee->image;
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($filePath);
            }
            $fileName = $imageName = time() . '_' . uniqid() . '.' . $request['image']->getClientOriginalExtension();
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('event/employee')) {
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('event/employee');
            }
            \Illuminate\Support\Facades\Storage::disk('public')->put('event/employee/' . $fileName, file_get_contents($request['image']));
            $employee->image = $imageName;
        }
        if (($request['type'] ?? "") == 'purohit') {
            $getData = \App\Models\Purohit::with(['temple'])->where('id', $request->seller['id'])->first();
            $employee->relation_id = $getData['temple']['trust_id'] ?? 0;
        } elseif (($request['type'] ?? "") == 'trust_employee' && (optional(\App\Models\VendorRoles::find($request->seller['emp_role_id']))->name === 'Sub Pandit')) {
            $getData = \App\Models\VendorEmployees::find($request->seller['id']);
            $employee->relation_id = $getData['relation_id'] ?? 0;
        } else {
            $employee->relation_id =  $request->seller['relation_id'];
        }
        $employee->save();
        return response()->json(['status' => 1, 'code' => '', 'message' => 'Employee Updated successfully', 'data' => []], 200);
    }

    public function EmployeeStatusUpdate(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Not Found Organizer Id', 'data' => []], 200);
        }
        $data = VendorEmployees::where('type', 'trust')->where('id', $request['id'])->first();
        $data->status = ((($data['status'] ?? 0) == 1) ? 0 : 1);
        $data->save();
        return response()->json(['status' => 1, 'code' => '', 'message' => 'Status Updated Successfully', 'data' => []], 200);
    }

    public function TrustPaymentSettlement(Request $request)
    {
        $request->validate([
            'purohit_id' => 'required',
            'emp_id' => 'required',
            'emp_code' => 'required',
        ]);
        $getEmployee = \App\Models\VendorEmployees::where(['id' => $request['emp_id'], 'purohit_id' => $request['purohit_id'], 'pay_code' => $request['emp_code']])->first();
        if ($getEmployee) {
            $querys = \App\Models\PanditTransectionHistory::where(['trust_id' => $getEmployee['relation_id'], 'purohit_id' => $request['purohit_id'], 'emp_id' => $request['emp_id'], 'status' => 0])->where('order_id', '!=', '');
            $getData = (clone $querys)->get();
            $total_amount = (clone $querys)->sum('debit');
            $idList = (clone $querys)->pluck('order_id');
            if ($getData) {
                \App\Models\PanditTransectionHistory::whereIn('order_id', $idList)->update(['status' => 1, 'note' => 'Puja Amount']);
                $withHistory  = new \App\Models\WithdrawalAmountHistory();
                $withHistory->type = 'purohit';
                $withHistory->vendor_id = $getEmployee['relation_id'];
                $withHistory->ex_id =  $request['emp_id'];
                $withHistory->old_wallet_amount = (\App\Models\VendorEmployees::where('id', $request['emp_id'])->first()['withdrawal_amount']);
                $withHistory->req_amount = ($total_amount);
                $withHistory->transaction_type = 'debit';
                $withHistory->message = "puja Amount Cash Collect";
                $withHistory->status = 1;
                $withHistory->transcation_id = "cash";
                $withHistory->payment_method = "manual";
                $withHistory->save();
                \App\Models\TrustPanditTransection::whereIn('order_id', $idList)->update(['payment_status' => 'complete']);
                \App\Models\Purohit::where('id', $request['purohit'])->update(['collected_amount' => \Illuminate\Support\Facades\DB::raw('collected_amount + ' . ($total_amount))]);
                \App\Models\VendorEmployees::where('id', $request['emp_id'])->update(['collected_amount' => \Illuminate\Support\Facades\DB::raw('collected_amount + ' . ($total_amount)), 'pay_code' => mt_rand('0000', '9999'), 'invalid_attendee_count' => 0]);
                \App\Models\DonateTrust::where('id', $getEmployee['relation_id'])->update([
                    'purohit_collected_amount' => \Illuminate\Support\Facades\DB::raw('purohit_collected_amount - ' . ($total_amount)),
                ]);
            }
            return response()->json(['status' => 1, 'message' => 'successfully', 'data' => $getData], 200);
        } else {
            \App\Models\VendorEmployees::where(['id' => $request['emp_id']])->update(['invalid_attendee_count' => \Illuminate\Support\Facades\DB::raw('invalid_attendee_count + 1')]);
            $getEmployee =  \App\Models\VendorEmployees::where(['id' => $request['emp_id']])->first();
            if ($getEmployee && ($getEmployee['invalid_attendee_count'] > 3) && (strlen((string)$getEmployee['phone'])) > 9) {
                $dataemail['admin_phone'] = $getEmployee['phone'];
                $dataemail['admin_name'] = ($getEmployee['name'] ?? '');
                Helpers::whatsappMessage('donate', 'trust_warning_entered_invalid_code', $dataemail);
            }
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => $getEmployee['invalid_attendee_count']], 200);
        }
    }


    public function PurohitList(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Not Found Organizer Id', 'data' => []], 200);
        }
        $data = Purohit::select('id', 'temple_id', 'name', 'mobile', 'profile', 'address', 'description', 'status')->where('relation_id', $request->seller['relation_id'])->get()->map(function ($temple) {
            $temple->profile = getValidImage(path: 'storage/app/public/' . ($temple['profile'] ?? ''), type: 'backend-product');
            $temple->temple_name = \App\Models\Temple::where('id', $temple->temple_id)->first()['name'] ?? "";
            return $temple;
        });
        if ($data) {
            return response()->json(['status' => 1, 'message' => 'Purohit Recodes', 'data' => $data], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
        }
    }

    public function PurohitAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temple_id'   => 'required|exists:temples,id',
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|digits:10|unique:purohits,mobile',
            'profile'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'address'     => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $imagePath = null;
        if ($request->hasFile('profile')) {
            $folderName = 'purohit_images/' . str_replace(' ', '_', strtolower($request->name));
            $fileName = time() . '.' . $request->file('profile')->getClientOriginalExtension();
            $imagePath = $request->file('profile')->storeAs($folderName, $fileName, 'public');
        }

        $purohit = Purohit::create([
            'temple_id'   => $request->temple_id,
            'name'        => $request->name,
            'mobile'      => $request->mobile,
            'profile'     => $imagePath,
            'address'     => $request->address,
            'description' => $request->description,
            'relation_id' => Temple::where('id', $request->temple_id)->value('trust_id') ?? 0,
            'password'    => bcrypt('12345678'),
        ]);
        if ($purohit) {
            return response()->json(['status'  => 1, 'message' => 'Purohit added successfully', 'data'    => []], 200);
        } else {
            return response()->json(['status'  => 0, 'message' => 'Purohit Not Added', 'data' => []], 200);
        }
    }

    public function PurohitUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purohit_id'  => 'required|exists:purohits,id',
            'temple_id'   => 'required|exists:temples,id',
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|digits:10|unique:purohits,mobile,' . $request->purohit_id,
            'profile'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'address'     => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $purohit = Purohit::findOrFail($request->purohit_id);
        $imagePath = $purohit->profile;

        if ($request->hasFile('profile')) {
            if ($purohit->profile && Storage::disk('public')->exists($purohit->profile)) {
                Storage::disk('public')->delete($purohit->profile);
            }
            $folderName = 'purohit_images/' . str_replace(' ', '_', strtolower($request->name));
            $fileName = time() . '.' . $request->file('profile')->getClientOriginalExtension();
            $imagePath = $request->file('profile')->storeAs($folderName, $fileName, 'public');
        }
        $purohit->update([
            'temple_id'   => $request->temple_id,
            'name'        => $request->name,
            'mobile'      => $request->mobile,
            'profile'     => $imagePath,
            'address'     => $request->address,
            'description' => $request->description,
        ]);
        if ($purohit) {
            return response()->json(['status'  => 1, 'message' => 'Purohit updated successfully', 'data'    => []], 200);
        } else {
            return response()->json(['status'  => 0, 'message' => 'Purohit Not Updated', 'data' => []], 200);
        }
    }

    public function PurohitStatusUpdate(Request $request)
    {
        if (empty($request->seller ?? '')) {
            return response()->json(['status' => 0, 'code' => 'unauthorized', 'message' => 'Not Found Organizer Id', 'data' => []], 200);
        }
        $data = Purohit::where('relation_id', $request->seller['relation_id'])->where('id', $request['id'])->first();
        $data->status = ((($data['status'] ?? 0) == 1) ? 0 : 1);
        $data->save();
        return response()->json(['status' => 1, 'code' => '', 'message' => 'Status Updated Successfully', 'data' => []], 200);
    }
}
