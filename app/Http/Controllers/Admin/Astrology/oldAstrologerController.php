<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\Contracts\Repositories\AstrologersCategoryRepositoryInterface;
use Illuminate\Http\Request;
use App\Contracts\Repositories\AstrologersRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Astrologer;
// use App\Http\Controllers\BaseController;
// use App\Http\Requests\Admin\CalculatorAddRequest;
use App\Services\AstrologersService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AstrologerCategoryAddRequest;
use App\Http\Requests\Admin\AstrologerCategoryUpdateRequest;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\Admin\AstrologerSkillsAddRequest;
use App\Http\Requests\Admin\AstrologerSkillUpdateRequest;
use App\Models\Astrologer\Astrologer as AstrologerAstrologer;
use App\Models\Astrologer\Availability;
use App\Models\Astrologer\Skills;
use App\Models\AstrologerCategory;
use App\Models\Category;
use App\Models\Chadhava;
use App\Models\Service;
use App\Models\Service_order;
use App\Models\ServiceReview;
use App\Models\ServiceTransaction;
use App\Models\Vippooja;
use App\Utils\Helpers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use DB;

class AstrologerController extends Controller
{

    public function __construct(
        private readonly AstrologersRepositoryInterface        $astroRepo,
        private readonly AstrologersCategoryRepositoryInterface        $astroCatRepo,
        private readonly AstrologersService       $astroService,
        private readonly TranslationRepositoryInterface     $translationRepo,
    ) {
    }


    // block functions---------------------------------------------
    public function block_list()
    {
        $blocked = AstrologerAstrologer::where('status', 2)->with('primarySkill')->with('orders')->paginate(10);
        return view(Astrologer::BLOCK_LIST[VIEW], compact('blocked'));
    }


    // manage functions---------------------------------------------
    public function manage_list(Request $request)
    {
        // dd($request->input());
        if ($request->has('search_name')) {
            $verified = AstrologerAstrologer::where('name', 'like', '%' . $request->search_name . '%')->where('status', 1)->with('primarySkill')->with('orders')->get();
        } else if ($request->has('search_type') && !empty($request->search_type)) {
            $verified = AstrologerAstrologer::where('type', $request->search_type)->where('status', 1)->with('primarySkill')->with('orders')->get();
        } else if ($request->has('search_service_type') && !empty($request->search_service_type)) {
            $verified = AstrologerAstrologer::where('primary_skills', $request->search_service_type)->where('status', 1)->with('primarySkill')->with('orders')->get();
        } else {
            $verified = AstrologerAstrologer::where('status', 1)->with('primarySkill')->with('orders')->paginate(10);
        }
        // dd($verified);
        return view(Astrologer::MANAGE_LIST[VIEW], compact('verified'));
    }

    public function detailManageOverview($id)
    {
        $overview = AstrologerAstrologer::where('id', $id)->with('primarySkill')->with('orders')->first();
        $transaction = ServiceTransaction::where('astro_id', $id)->get();
        return view(Astrologer::MANAGE_DETAIL_OVERVIEW[VIEW], compact('overview', 'transaction'));
    }

    public function detailManageOrder($id)
    {
        $poojaOrders = "";
        $consultationOrders = "";
        $userData = AstrologerAstrologer::where('id', $id)->first();
        if (!empty($userData['is_pandit_pooja'])) {
            $poojaOrders = Service_order::where('pandit_assign', $id)->where('type', 'pooja')->with('services.category')->paginate(10);
        }
        if (!empty($userData['consultation_charge'])) {
            $consultationOrders = Service_order::where('pandit_assign', $id)->where('type', 'counselling')->with('services.category')->with('pandit')->with('counselling_user')->paginate(10);
        }
        $pendingOrder = Service_order::where('pandit_assign', $id)->where('status', 0)->count();
        $completeOrder = Service_order::where('pandit_assign', $id)->where('status', 1)->count();
        // dd($consultationOrders);
        return view(Astrologer::MANAGE_DETAIL_ORDER[VIEW], compact('poojaOrders', 'consultationOrders', 'pendingOrder', 'completeOrder', 'id'));
    }

    public function detailManageService($id)
    {
        $service = AstrologerAstrologer::where('id', $id)->first();
        return view(Astrologer::MANAGE_DETAIL_SERVICE[VIEW], compact('service', 'id'));
    }

    public function detailManageSetting($id)
    {
        $setting = AstrologerAstrologer::where('id', $id)->first();
        return view(Astrologer::MANAGE_DETAIL_SETTING[VIEW], compact('setting', 'id'));
    }

    public function detailManageTransaction($id)
    {
        $transaction = ServiceTransaction::where('astro_id', $id)->get();
        return view(Astrologer::MANAGE_DETAIL_TRANSACTION[VIEW], compact('transaction', 'id'));
    }

    public function detailManageReview($id)
    {
        $reviews = ServiceReview::select('*', DB::raw('SUM(rating) as total_rate'), DB::raw('COUNT(*) as total_count'))
            ->groupBy('service_id')
            ->where('astro_id',$id)
            ->with('services')
            ->get();
        return view(Astrologer::MANAGE_DETAIL_REVIEW[VIEW], compact('reviews', 'id'));
    }

    public function user_review_list($serviceId, $astroId){
        $userReview = ServiceReview::where(['service_id'=>$serviceId,'astro_id'=>$astroId])->with('users')->get();
        if($userReview){
            return response()->json(['status'=>200, 'data'=>$userReview]);
        }
        return response()->json(['status'=>400]);
    }

    public function user_review_delete($id){
        $userDelete = ServiceReview::where('id',$id)->delete();
        if($userDelete){
            Toastr::success(translate('Review Deleted'));
            Helpers::editDeleteLogs('Astrologer','Review','Delete');
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function commission_update(Request $request)
    {
        if ($request->type == 'pooja') {
            $poojaCommissionArr = json_encode(array_combine($request->pooja_commission_key, $request->pooja_commission_value));
            $poojaUpdate = AstrologerAstrologer::where('id', $request->user_id)->update(['is_pandit_pooja_commission' => $poojaCommissionArr]);
            if ($poojaUpdate) {
                Toastr::success(translate('Pooja Commission Updated'));
                Helpers::editDeleteLogs('Astrologer','Pooja Commission Service','Update');
                return back();
            }
        } else if ($request->type == 'consultation') {
            $consultationCommissionArr = json_encode(array_combine($request->consultation_commission_key, $request->consultation_commission_value));
            $consultationUpdate = AstrologerAstrologer::where('id', $request->user_id)->update(['consultation_commission' => $consultationCommissionArr]);
            if ($consultationUpdate) {
                Toastr::success(translate('Consultation Commission Updated'));
                Helpers::editDeleteLogs('Astrologer','Consultation Commission Service','Update');
                return back();
            }
        } else if ($request->type == 'commission') {
            $commissionUpdate = AstrologerAstrologer::where('id', $request->user_id)->first();
            if ($request->has('pandit_live_stream_commission')) {
                $commissionUpdate->is_pandit_live_stream_commission = $request->pandit_live_stream_commission;
            }
            if ($request->has('live_stream_commission')) {
                $commissionUpdate->is_astrologer_live_stream_commission = $request->live_stream_commission;
            }
            if ($request->has('call_commission')) {
                $commissionUpdate->is_astrologer_call_commission = $request->call_commission;
            }
            if ($request->has('chat_commission')) {
                $commissionUpdate->is_astrologer_chat_commission = $request->chat_commission;
            }
            if ($request->has('report_commission')) {
                $commissionUpdate->is_astrologer_report_commission = $request->report_commission;
            }
            if ($commissionUpdate->save()) {
                Toastr::success(translate('commission_Update'));
                Helpers::editDeleteLogs('Astrologer','Commission','Update');
                return back();
            }
        } else {
            Toastr::error(translate('An Error Occured'));
            return back();
        }
    }

    public function getManageAddView(): View
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $categories = AstrologerCategory::where('status', 1)->get();
        $skills = Skills::where('status', 1)->get();
        $panditCategories = Category::where('parent_id', 33)->get();
        $consultationList = Service::where('product_type', 'counselling')->where('status', 1)->get();
        return view(Astrologer::MANAGE_ADD[VIEW], compact('language', 'defaultLanguage', 'categories', 'skills', 'panditCategories', 'consultationList'));
    }

    // public function pandit_pooja(Request $request)
    // {
    //     $ids = $request->input('id');
    //     $pooja = "";
    //     if (!empty($ids)) {
    //         $pooja = Service::whereIn('sub_category_id', $ids)->get();
    //     }
    //     return response()->json(['status' => 200, 'pooja' => $pooja]);
    // }

    public function pandit_pooja(Request $request)
    {
        $ids = $request->input('id');
        $categoryIds = [];
        $pooja = "";
        $vipPooja = "";
        $anushthan = "";
        $chadhava = "";

        if (!empty($ids)) {
            //for pooja
            if (in_array("34", $ids) || in_array("35", $ids) || in_array("36", $ids) || in_array("38", $ids)) {
                foreach($ids as $id){
                    if($id == 34 || $id == 35 || $id == 36 || $id == 38){
                        $categoryIds[] = $id;
                    }
                }
                $pooja = Service::whereIn('sub_category_id', $categoryIds)->where('status',1)->get();
            }
            
            // for vip pooja
            if (in_array("50", $ids)) {
                $vipPooja = Vippooja::where('is_anushthan', 0)->where('status',1)->get();
            }
            
            //for anushthan
            if (in_array("51", $ids)) {
                $anushthan = Vippooja::where('is_anushthan', 1)->where('status',1)->get();
            }
            
            //for chadhava
            if (in_array("52", $ids)) {
                $chadhava = Chadhava::where('status',1)->get();
            }
        }

        return response()->json(['status' => 200, 'pooja' => $pooja, 'vipPooja'=>$vipPooja, 'anushthan'=>$anushthan, 'chadhava'=>$chadhava]);
    }

    public function check_email($email){
        $checkEmail = AstrologerAstrologer::where('email',$email)->exists();
        if($checkEmail){
            return response(['status'=>200]);
        }
        return response(['status'=>400]);
    }

    public function check_mobileno($mobileno){
        $checkMobileno = AstrologerAstrologer::where('mobile_no',$mobileno)->exists();
        if($checkMobileno){
            return response(['status'=>200]);
        }
        return response(['status'=>400]);
    }

    public function addManage(Request $request): RedirectResponse
    {
        // pooja charge array
        $poojaChargeJson = null;
        $poojaCommissionJson = null;
        if ($request->pooja_charge_id) {
            $poojaChargeArr = array_combine($request->pooja_charge_id, $request->pooja_charge);
            $poojaChargeFilterArr = array_filter($poojaChargeArr, function ($value) {
                return !is_null($value);
            });
            if (count($poojaChargeFilterArr) > 0) {
                $poojaChargeJson = json_encode($poojaChargeFilterArr);
                $poojaCommission = array_map(function ($value) {
                    return '5';
                }, $poojaChargeFilterArr);
                $poojaCommissionJson = json_encode($poojaCommission);
            }
        }

        // vip pooja charge array
        $vipPoojaChargeJson = null;
        $vipPoojaCommissionJson = null;
        if ($request->vip_pooja_charge_id) {
            $vipPoojaChargeArr = array_combine($request->vip_pooja_charge_id, $request->vip_pooja_charge);
            $vipPoojaChargeFilterArr = array_filter($vipPoojaChargeArr, function ($value) {
                return !is_null($value);
            });
            if (count($vipPoojaChargeFilterArr) > 0) {
                $vipPoojaChargeJson = json_encode($vipPoojaChargeFilterArr);
                $vipPoojaCommission = array_map(function ($value) {
                    return '5';
                }, $vipPoojaChargeFilterArr);
                $vipPoojaCommissionJson = json_encode($vipPoojaCommission);
            }
        }

        // anushthan pooja charge array
        $anushthanChargeJson = null;
        $anushthanCommissionJson = null;
        if ($request->anushthan_charge_id) {
            $anushthanChargeArr = array_combine($request->anushthan_charge_id, $request->anushthan_charge);
            $anushthanChargeFilterArr = array_filter($anushthanChargeArr, function ($value) {
                return !is_null($value);
            });
            if (count($anushthanChargeFilterArr) > 0) {
                $anushthanChargeJson = json_encode($anushthanChargeFilterArr);
                $anushthanCommission = array_map(function ($value) {
                    return '5';
                }, $anushthanChargeFilterArr);
                $anushthanCommissionJson = json_encode($anushthanCommission);
            }
        }

        // chadhava pooja charge array
        $chadhavaChargeJson = null;
        $chadhavaCommissionJson = null;
        if ($request->chadhava_charge_id) {
            $chadhavaChargeArr = array_combine($request->chadhava_charge_id, $request->chadhava_charge);
            $chadhavaChargeFilterArr = array_filter($chadhavaChargeArr, function ($value) {
                return !is_null($value);
            });
            if (count($chadhavaChargeFilterArr) > 0) {
                $chadhavaChargeJson = json_encode($chadhavaChargeFilterArr);
                $chadhavaCommission = array_map(function ($value) {
                    return '5';
                }, $chadhavaChargeFilterArr);
                $chadhavaCommissionJson = json_encode($chadhavaCommission);
            }
        }


        // consultation charge array
        $consultationChargeJson = null;
        $consultationCommissionJson = null;
        if ($request->consultation_charge) {
            $consultationChargeArr = array_combine($request->consultation_charge_id, $request->consultation_charge);
            $consultationChargeFilterArr = array_filter($consultationChargeArr, function ($value) {
                return !is_null($value);
            });
            if (count($consultationChargeFilterArr) > 0) {
                $consultationChargeJson = json_encode($consultationChargeFilterArr);
                $consultationCommission = array_map(function ($value) {
                    return '5';
                }, $consultationChargeFilterArr);
                $consultationCommissionJson = json_encode($consultationCommission);
            }
        }

        // dd('poojaCharge '.$poojaChargeJson, 'poojaCommission '.$poojaCommissionJson, 'vipCharge '.$vipPoojaChargeJson, 'vipCommission '.$vipPoojaCommissionJson, 'anushthanCharge '.$anushthanChargeJson, 'anushthanCommission '.$anushthanCommissionJson, 'chadhavaCharge '.$chadhavaChargeJson, 'chadhavaCommission '.$chadhavaCommissionJson);

        // validation
        $request->validate([
            'password' => 'confirmed'
        ]);

        // image
        $file = $request->file('image');
        $imageName = time() . '-astrologer' . $file->getClientOriginalName();
        $file->storeAs('public/astrologers', $imageName);

        // availability
        $sunday = [];
        foreach ($request->sunday_from as $sf_key => $sf) {
            if ($sf != null && $request->sunday_to[$sf_key] != null) {
                $sunday[$sf_key] = $sf . '-' . $request->sunday_to[$sf_key];
            }
        }

        $monday = [];
        foreach ($request->monday_from as $mf_key => $mf) {
            if ($mf != null && $request->monday_to[$mf_key] != null) {
                $monday[$mf_key] = $mf . '-' . $request->monday_to[$mf_key];
            }
        }

        $tuesday = [];
        foreach ($request->tuesday_from as $tuf_key => $tuf) {
            if ($tuf != null && $request->tuesday_to[$tuf_key] != null) {
                $tuesday[$tuf_key] = $tuf . '-' . $request->tuesday_to[$tuf_key];
            }
        }

        $wednesday = [];
        foreach ($request->wednesday_from as $w_key => $w) {
            if ($w != null && $request->wednesday_to[$w_key] != null) {
                $wednesday[$w_key] = $w . '-' . $request->wednesday_to[$w_key];
            }
        }

        $thursday = [];
        foreach ($request->thursday_from as $tf_key => $tf) {
            if ($tf != null && $request->thursday_to[$tf_key] != null) {
                $thursday[$tf_key] = $tf . '-' . $request->thursday_to[$tf_key];
            }
        }

        $friday = [];
        foreach ($request->friday_from as $ff_key => $ff) {
            if ($ff != null && $request->friday_to[$ff_key] != null) {
                $friday[$ff_key] = $ff . '-' . $request->friday_to[$ff_key];
            }
        }

        $saturday = [];
        foreach ($request->saturday_from as $stf_key => $stf) {
            if ($stf != null && $request->saturday_to[$stf_key] != null) {
                $saturday[$stf_key] = $stf . '-' . $request->saturday_to[$stf_key];
            }
        }

        $astrologer = new AstrologerAstrologer;
        $astrologer->name = $request->name;
        $astrologer->email = $request->email;
        $astrologer->mobile_no = $request->mobile_no;
        $astrologer->image = $imageName;
        $astrologer->password = Hash::make($request->password);
        $astrologer->gender = $request->gender;
        $astrologer->dob = $request->dob;
        $astrologer->pancard = $request->pancard;
        $astrologer->adharcard = $request->adharcard;
        $astrologer->type = $request->type;
        $astrologer->city = $request->city;
        $astrologer->address = $request->address;
        $astrologer->primary_skills = $request->primary_skills;
        $astrologer->is_pandit_pooja_category = $request->is_pandit_pooja_category ? json_encode($request->is_pandit_pooja_category) : null;
        $astrologer->is_pandit_pooja = $poojaChargeJson;
        $astrologer->is_pandit_vippooja = $vipPoojaChargeJson;
        $astrologer->is_pandit_anushthan = $anushthanChargeJson;
        $astrologer->is_pandit_chadhava = $chadhavaChargeJson;
        $astrologer->is_pandit_panda = $request->is_pandit_panda;
        $astrologer->is_pandit_gotra = $request->is_pandit_gotra;
        $astrologer->is_pandit_primary_mandir = $request->is_pandit_primary_mandir;
        $astrologer->is_pandit_primary_mandir_location = $request->is_pandit_primary_mandir_location;
        $astrologer->is_pandit_min_charge = $request->min_charge;
        $astrologer->is_pandit_max_charge = $request->max_charge;
        $astrologer->is_pandit_pooja_per_day = $request->pooja_per_day;
        $astrologer->is_pandit_pooja_commission = $poojaCommissionJson;
        $astrologer->is_pandit_vippooja_commission = $vipPoojaCommissionJson;
        $astrologer->is_pandit_anushthan_commission = $anushthanCommissionJson;
        $astrologer->is_pandit_chadhava_commission = $chadhavaCommissionJson;
        $astrologer->is_pandit_live_stream_charge = $request->pandit_live_stream_charge;
        $astrologer->is_pandit_live_stream_commission = !empty($request->pandit_live_stream_charge) ? 5 : null;
        $astrologer->other_skills = $request->other_skills ? json_encode($request->other_skills) : null;
        $astrologer->category = json_encode($request->category);
        $astrologer->language = json_encode($request->language);
        $astrologer->is_astrologer_live_stream_charge = $request->live_stream_charge;
        $astrologer->is_astrologer_live_stream_commission = !empty($request->live_stream_charge) ? 5 : null;
        $astrologer->is_astrologer_call_charge = $request->call_charge;
        $astrologer->is_astrologer_call_commission = !empty($request->call_charge) ? 5 : null;
        $astrologer->is_astrologer_chat_charge = $request->chat_charge;
        $astrologer->is_astrologer_chat_commission = !empty($request->chat_charge) ? 5 : null;
        $astrologer->is_astrologer_report_charge = $request->report_charge;
        $astrologer->is_astrologer_report_commission = !empty($request->report_charge) ? 5 : null;
        $astrologer->consultation_charge = $consultationChargeJson;
        $astrologer->consultation_commission = $consultationCommissionJson;
        $astrologer->experience = $request->experience;
        $astrologer->daily_hours_contribution = $request->daily_hours_contribution;
        $astrologer->office_address = $request->office_address;
        $astrologer->primary_qualification = $request->primary_qualification;
        $astrologer->primary_degree = $request->primary_degree;
        $astrologer->secondary_qualification = $request->secondary_qualification;
        $astrologer->secondary_degree = $request->secondary_degree;
        $astrologer->college = $request->college;
        $astrologer->onboard_you = $request->onboard_you;
        $astrologer->interview_time = $request->interview_time;
        $astrologer->business_source = $request->business_source;
        $astrologer->learn_primary_skill = $request->learn_primary_skill;
        $astrologer->instagram = $request->instagram;
        $astrologer->facebook = $request->facebook;
        $astrologer->linkedin = $request->linkedin;
        $astrologer->youtube = $request->youtube;
        $astrologer->website = $request->website;
        $astrologer->min_earning = $request->min_earning;
        $astrologer->max_earning = $request->max_earning;
        $astrologer->bank_name = $request->bank_name;
        $astrologer->holder_name = $request->holder_name;
        $astrologer->branch_name = $request->branch_name;
        $astrologer->bank_ifsc = $request->bank_ifsc;
        $astrologer->account_no = $request->account_no;
        $astrologer->foreign_country = $request->foreign_country;
        $astrologer->working = $request->working;
        $astrologer->bio = $request->bio;
        $astrologer->qualities = $request->qualities;
        $astrologer->challenge = $request->challenge;
        $astrologer->repeat_question = $request->repeat_question;
        if ($astrologer->save()) {
            $astrologerId = AstrologerAstrologer::select('id')->latest()->first();
            if ($astrologerId) {
                $avalability = new Availability;
                $avalability->astrologer_id = $astrologerId['id'];
                $avalability->sunday = json_encode($sunday);
                $avalability->monday = json_encode($monday);
                $avalability->tuesday = json_encode($tuesday);
                $avalability->wednesday = json_encode($wednesday);
                $avalability->thursday = json_encode($thursday);
                $avalability->friday = json_encode($friday);
                $avalability->saturday = json_encode($saturday);
                $avalability->save();
            }
            Toastr::success(translate('registration_successfully'));
            return redirect()->route('admin.astrologers.pending.list');
        }
        Toastr::success(translate('unable_to_store_data'));
        return redirect()->back();
    }

    public function getManageUpdateView($id)
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $categories = AstrologerCategory::get();
        $skills = Skills::where('status', 1)->get();
        $panditCategories = Category::where('parent_id', 33)->get();
        $consultationList = Service::where('product_type', 'counselling')->where('status', 1)->get();
        $astrologer = AstrologerAstrologer::where('id', $id)->with('primarySkill')->first();
        // $pooja = 
        $availability = Availability::where('astrologer_id', $id)->first();
        // dd($astrologer);
        return view(Astrologer::MANAGE_UPDATE[VIEW], compact('language', 'defaultLanguage', 'categories', 'skills', 'panditCategories', 'astrologer', 'availability', 'consultationList'));
    }

    public function updateManage(Request $request, $id): RedirectResponse
    {
        // pooja charge array
        $poojaCommissionData = AstrologerAstrologer::select('is_pandit_pooja_commission')->where('id', $id)->first();
        $poojaCommissionDataArr = json_decode($poojaCommissionData['is_pandit_pooja_commission'], true);
        $poojaChargeJson = null;
        $poojaCommissionJson = null;
        if ($request->pooja_charge_id) {
            $poojaChargeArr = array_combine($request->pooja_charge_id, $request->pooja_charge);
            $poojaChargeFilterArr = array_filter($poojaChargeArr, function ($value) {
                return !is_null($value);
            });
            if (count($poojaChargeFilterArr) > 0) {
                $poojaChargeJson = json_encode($poojaChargeFilterArr);
                $poojaCommission = array_map(function ($value) {
                    return '5';
                }, $poojaChargeFilterArr);
                foreach ($poojaCommission as $key => $value) {
                    if (!array_key_exists($key, $poojaCommissionDataArr)) {
                        $poojaCommissionDataArr[$key] = $value;
                    }
                }
                foreach ($poojaCommissionDataArr as $key => $value) {
                    if (!array_key_exists($key, $poojaCommission)) {
                        unset($poojaCommissionDataArr[$key]);
                    }
                }
                ksort($poojaCommissionDataArr);
                $poojaCommissionJson = json_encode($poojaCommissionDataArr);
            }
        }

        // vip pooja charge array
        $vipPoojaCommissionData = AstrologerAstrologer::select('is_pandit_vippooja_commission')->where('id', $id)->first();
        $vipPoojaCommissionDataArr = json_decode($vipPoojaCommissionData['is_pandit_vippooja_commission'], true);
        $vipPoojaChargeJson = null;
        $vipPoojaCommissionJson = null;
        if ($request->vip_pooja_charge_id) {
            $vipPoojaChargeArr = array_combine($request->vip_pooja_charge_id, $request->vip_pooja_charge);
            $vipPoojaChargeFilterArr = array_filter($vipPoojaChargeArr, function ($value) {
                return !is_null($value);
            });
            if (count($vipPoojaChargeFilterArr) > 0) {
                $vipPoojaChargeJson = json_encode($vipPoojaChargeFilterArr);
                $vipPoojaCommission = array_map(function ($value) {
                    return '5';
                }, $vipPoojaChargeFilterArr);
                foreach ($vipPoojaCommission as $key => $value) {
                    if (!array_key_exists($key, $vipPoojaCommissionDataArr)) {
                        $vipPoojaCommissionDataArr[$key] = $value;
                    }
                }
                foreach ($vipPoojaCommissionDataArr as $key => $value) {
                    if (!array_key_exists($key, $vipPoojaCommission)) {
                        unset($vipPoojaCommissionDataArr[$key]);
                    }
                }
                ksort($vipPoojaCommissionDataArr);
                $vipPoojaCommissionJson = json_encode($vipPoojaCommissionDataArr);
            }
        }

        // anushthan charge array
        $anushthanCommissionData = AstrologerAstrologer::select('is_pandit_anushthan_commission')->where('id', $id)->first();
        $anushthanCommissionDataArr = json_decode($anushthanCommissionData['is_pandit_anushthan_commission'], true);
        $anushthanChargeJson = null;
        $anushthanCommissionJson = null;
        if ($request->anushthan_charge_id) {
            $anushthanChargeArr = array_combine($request->anushthan_charge_id, $request->anushthan_charge);
            $anushthanChargeFilterArr = array_filter($anushthanChargeArr, function ($value) {
                return !is_null($value);
            });
            if (count($anushthanChargeFilterArr) > 0) {
                $anushthanChargeJson = json_encode($anushthanChargeFilterArr);
                $anushthanCommission = array_map(function ($value) {
                    return '5';
                }, $anushthanChargeFilterArr);
                foreach ($anushthanCommission as $key => $value) {
                    if (!array_key_exists($key, $anushthanCommissionDataArr)) {
                        $anushthanCommissionDataArr[$key] = $value;
                    }
                }
                foreach ($anushthanCommissionDataArr as $key => $value) {
                    if (!array_key_exists($key, $anushthanCommission)) {
                        unset($anushthanCommissionDataArr[$key]);
                    }
                }
                ksort($anushthanCommissionDataArr);
                $anushthanCommissionJson = json_encode($anushthanCommissionDataArr);
            }
        }


        // chadhava charge array
        $chadhavaCommissionData = AstrologerAstrologer::select('is_pandit_chadhava_commission')->where('id', $id)->first();
        $chadhavaCommissionDataArr = json_decode($chadhavaCommissionData['is_pandit_chadhava_commission'], true);
        $chadhavaChargeJson = null;
        $chadhavaCommissionJson = null;
        if ($request->chadhava_charge_id) {
            $chadhavaChargeArr = array_combine($request->chadhava_charge_id, $request->chadhava_charge);
            $chadhavaChargeFilterArr = array_filter($chadhavaChargeArr, function ($value) {
                return !is_null($value);
            });
            if (count($chadhavaChargeFilterArr) > 0) {
                $chadhavaChargeJson = json_encode($chadhavaChargeFilterArr);
                $chadhavaCommission = array_map(function ($value) {
                    return '5';
                }, $chadhavaChargeFilterArr);
                foreach ($chadhavaCommission as $key => $value) {
                    if (!array_key_exists($key, $chadhavaCommissionDataArr)) {
                        $chadhavaCommissionDataArr[$key] = $value;
                    }
                }
                foreach ($chadhavaCommissionDataArr as $key => $value) {
                    if (!array_key_exists($key, $chadhavaCommission)) {
                        unset($chadhavaCommissionDataArr[$key]);
                    }
                }
                ksort($chadhavaCommissionDataArr);
                $chadhavaCommissionJson = json_encode($chadhavaCommissionDataArr);
            }
        }

        // consultation charge array
        $consultationCommissionData = AstrologerAstrologer::select('consultation_commission')->where('id', $id)->first();
        $consultationCommissionDataArr = json_decode($consultationCommissionData['consultation_commission'], true);
        $consultationChargeJson = null;
        $consultationCommissionJson = null;
        if ($request->consultation_charge) {
            $consultationChargeArr = array_combine($request->consultation_charge_id, $request->consultation_charge);
            $consultationChargeFilterArr = array_filter($consultationChargeArr, function ($value) {
                return !is_null($value);
            });
            if (count($consultationChargeFilterArr) > 0) {
                $consultationChargeJson = json_encode($consultationChargeFilterArr);
                $consultationCommission = array_map(function ($value) {
                    return '5';
                }, $consultationChargeFilterArr);
                foreach ($consultationCommission as $key => $value) {
                    if (!array_key_exists($key, $consultationCommissionDataArr)) {
                        $consultationCommissionDataArr[$key] = $value;
                    }
                }
                foreach ($consultationCommissionDataArr as $key => $value) {
                    if (!array_key_exists($key, $consultationCommission)) {
                        unset($consultationCommissionDataArr[$key]);
                    }
                }
                ksort($consultationCommissionDataArr);
                $consultationCommissionJson = json_encode($consultationCommissionDataArr);
            }
        }

        //availability
        $sunday = [];
        foreach ($request->sunday_from as $sf_key => $sf) {
            if ($sf != null && $request->sunday_to[$sf_key] != null) {
                $sunday[$sf_key] = $sf . '-' . $request->sunday_to[$sf_key];
            }
        }

        $monday = [];
        foreach ($request->monday_from as $mf_key => $mf) {
            if ($mf != null && $request->monday_to[$mf_key] != null) {
                $monday[$mf_key] = $mf . '-' . $request->monday_to[$mf_key];
            }
        }

        $tuesday = [];
        foreach ($request->tuesday_from as $tuf_key => $tuf) {
            if ($tuf != null && $request->tuesday_to[$tuf_key] != null) {
                $tuesday[$tuf_key] = $tuf . '-' . $request->tuesday_to[$tuf_key];
            }
        }

        $wednesday = [];
        foreach ($request->wednesday_from as $w_key => $w) {
            if ($w != null && $request->wednesday_to[$w_key] != null) {
                $wednesday[$w_key] = $w . '-' . $request->wednesday_to[$w_key];
            }
        }

        $thursday = [];
        foreach ($request->thursday_from as $tf_key => $tf) {
            if ($tf != null && $request->thursday_to[$tf_key] != null) {
                $thursday[$tf_key] = $tf . '-' . $request->thursday_to[$tf_key];
            }
        }

        $friday = [];
        foreach ($request->friday_from as $ff_key => $ff) {
            if ($ff != null && $request->friday_to[$ff_key] != null) {
                $friday[$ff_key] = $ff . '-' . $request->friday_to[$ff_key];
            }
        }

        $saturday = [];
        foreach ($request->saturday_from as $stf_key => $stf) {
            if ($stf != null && $request->saturday_to[$stf_key] != null) {
                $saturday[$stf_key] = $stf . '-' . $request->saturday_to[$stf_key];
            }
        }

        $astrologer = AstrologerAstrologer::where('id', $id)->first();
        $astrologer->name = $request->name;
        // $astrologer->email = $request->email;
        // $astrologer->mobile_no = $request->mobile_no;
        if ($request->hasFile('image')) {
            $oldImagePath = storage_path('app/public/astrologers/' . $astrologer->image);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $file = $request->file('image');
            $imageName = time() . '-astrologer' . $file->getClientOriginalName();
            $file->storeAs('public/astrologers', $imageName);
            $astrologer->image = $imageName;
        }
        $astrologer->gender = $request->gender;
        $astrologer->dob = $request->dob;
        $astrologer->pancard = $request->pancard;
        $astrologer->adharcard = $request->adharcard;
        // $astrologer->type = $request->type;
        $astrologer->city = $request->city;
        $astrologer->address = $request->address;
        // $astrologer->primary_skills = $request->primary_skills;
        $astrologer->is_pandit_pooja_category = $request->is_pandit_pooja_category ? json_encode($request->is_pandit_pooja_category) : null;
        $astrologer->is_pandit_pooja = $poojaChargeJson;
        $astrologer->is_pandit_vippooja = $vipPoojaChargeJson;
        $astrologer->is_pandit_anushthan = $anushthanChargeJson;
        $astrologer->is_pandit_chadhava = $chadhavaChargeJson;
        $astrologer->is_pandit_panda = $request->is_pandit_panda;
        $astrologer->is_pandit_gotra = $request->is_pandit_gotra;
        $astrologer->is_pandit_primary_mandir = $request->is_pandit_primary_mandir;
        $astrologer->is_pandit_primary_mandir_location = $request->is_pandit_primary_mandir_location;
        $astrologer->is_pandit_min_charge = $request->min_charge;
        $astrologer->is_pandit_max_charge = $request->max_charge;
        $astrologer->is_pandit_pooja_per_day = $request->pooja_per_day;
        $astrologer->is_pandit_live_stream_charge = $request->pandit_live_stream_charge;
        $astrologer->is_pandit_live_stream_commission = empty($request->pandit_live_stream_charge) ? null : (!empty($request->pandit_live_stream_charge) && !empty($request->pandit_live_stream_commission) ? $request->pandit_live_stream_commission : 5);
        if (!empty($poojaCommissionJson)) {
            $astrologer->is_pandit_pooja_commission = $poojaCommissionJson;
        }
        if (!empty($vipPoojaCommissionJson)) {
            $astrologer->is_pandit_vippooja_commission = $vipPoojaCommissionJson;
        }
        if (!empty($anushthanCommissionJson)) {
            $astrologer->is_pandit_anushthan_commission = $anushthanCommissionJson;
        }
        if (!empty($chadhavaCommissionJson)) {
            $astrologer->is_pandit_chadhava_commission = $chadhavaCommissionJson;
        }
        $astrologer->other_skills = json_encode($request->other_skills);
        $astrologer->category = json_encode($request->category);
        $astrologer->language = json_encode($request->language);
        $astrologer->is_astrologer_live_stream_charge = $request->live_stream_charge;
        $astrologer->is_astrologer_live_stream_commission = empty($request->live_stream_charge) ? null : (!empty($request->live_stream_charge) && !empty($request->live_stream_commission) ? $request->live_stream_commission : 5);
        $astrologer->is_astrologer_call_charge = $request->call_charge;
        $astrologer->is_astrologer_call_commission = empty($request->call_charge) ? null : (!empty($request->call_charge) && !empty($request->call_commission) ? $request->call_commission : 5);
        $astrologer->is_astrologer_chat_charge = $request->chat_charge;
        $astrologer->is_astrologer_chat_commission = empty($request->chat_charge) ? null : (!empty($request->chat_charge) && !empty($request->chat_commission) ? $request->chat_commission : 5);
        $astrologer->is_astrologer_report_charge = $request->report_charge;
        $astrologer->is_astrologer_report_commission = empty($request->report_charge) ? null : (!empty($request->report_charge) && !empty($request->report_commission) ? $request->report_commission : 5);
        $astrologer->consultation_charge = $consultationChargeJson;
        if (!empty($consultationCommissionJson)) {
            $astrologer->consultation_commission = $consultationCommissionJson;
        }
        $astrologer->experience = $request->experience;
        $astrologer->daily_hours_contribution = $request->daily_hours_contribution;
        $astrologer->office_address = $request->office_address;
        $astrologer->primary_qualification = $request->primary_qualification;
        $astrologer->primary_degree = $request->primary_degree;
        $astrologer->secondary_qualification = $request->secondary_qualification;
        $astrologer->secondary_degree = $request->secondary_degree;
        $astrologer->college = $request->college;
        $astrologer->onboard_you = $request->onboard_you;
        $astrologer->interview_time = $request->interview_time;
        $astrologer->business_source = $request->business_source;
        $astrologer->learn_primary_skill = $request->learn_primary_skill;
        $astrologer->instagram = $request->instagram;
        $astrologer->facebook = $request->facebook;
        $astrologer->linkedin = $request->linkedin;
        $astrologer->youtube = $request->youtube;
        $astrologer->website = $request->website;
        $astrologer->min_earning = $request->min_earning;
        $astrologer->max_earning = $request->max_earning;
        $astrologer->bank_name = $request->bank_name;
        $astrologer->holder_name = $request->holder_name;
        $astrologer->branch_name = $request->branch_name;
        $astrologer->bank_ifsc = $request->bank_ifsc;
        $astrologer->account_no = $request->account_no;
        $astrologer->foreign_country = $request->foreign_country;
        $astrologer->working = $request->working;
        $astrologer->bio = $request->bio;
        $astrologer->qualities = $request->qualities;
        $astrologer->challenge = $request->challenge;
        $astrologer->repeat_question = $request->repeat_question;

        if ($astrologer->save()) {
            $avalability = Availability::where('astrologer_id', $id)->first();
            $avalability->sunday = json_encode($sunday);
            $avalability->monday = json_encode($monday);
            $avalability->tuesday = json_encode($tuesday);
            $avalability->wednesday = json_encode($wednesday);
            $avalability->thursday = json_encode($thursday);
            $avalability->friday = json_encode($friday);
            $avalability->saturday = json_encode($saturday);
            $avalability->save();
            Toastr::success(translate('astrologer_updated_successfully'));
            Helpers::editDeleteLogs('Astrologer','Astrologer','Update');
            return redirect()->route('admin.astrologers.manage.list');
        }
        Toastr::success(translate('unable_to_udpate_data'));
        return redirect()->back();
    }

    public function deleteManage(Request $request)
    {
        $delete = AstrologerAstrologer::where('id', $request->id)->delete();
        if ($delete) {
            Toastr::success(translate('astrologer_deleted_successfully'));
            Helpers::editDeleteLogs('Astrologer','Astrologer','Update');
            return redirect()->route('admin.astrologers.manage.list');
        }
        Toastr::success(translate('unable_to_delete_data'));
        return redirect()->back();
    }

    public function statusManage(Request $request)
    {
        $status = AstrologerAstrologer::where('id', $request->id)->update(['status' => $request->status]);
        if ($status) {
            Toastr::success(translate('status_updated_successfully'));
            if ($request->status == 0) {
                return redirect()->route('admin.astrologers.pending.list');
            } else if ($request->status == 1) {
                return redirect()->route('admin.astrologers.manage.list');
            } else if ($request->status == 2) {
                return redirect()->route('admin.astrologers.block.list');
            }
        }
    }


    // pending functions---------------------------------------------
    public function pending_list()
    {
        $pending = AstrologerAstrologer::where('status', 0)->with('primarySkill')->paginate(10);
        return view(Astrologer::PENDING_LIST[VIEW], compact('pending'));
    }


    // review functions---------------------------------------------
    public function review_list()
    {
        return view(Astrologer::REVIEW_LIST[VIEW]);
    }


    // gift functions---------------------------------------------
    public function gift_list()
    {
        return view(Astrologer::GIFT_LIST[VIEW]);
    }


    // skill functions---------------------------------------------
    public function skill_list(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $skills = $this->astroRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(Astrologer::SKILL_LIST[VIEW], [
            'skills' => $skills,
            'languages' => $languages,
            'defaultLanguage' => $defaultLanguage,
        ]);
    }

    public function add_skills(AstrologerSkillsAddRequest $request, AstrologersService $astroService): RedirectResponse
    {
        // dd($request->input());
        $dataArray = $astroService->getAddData(request: $request);
        // dd($dataArray);
        $saveSkill = $this->astroRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\Astrologer\Skills', id: $saveSkill->id);
        Toastr::success(translate('Skills Successfully Added'));
        return back();
    }

    public function getSkillUpdateView(string|int $id): View|RedirectResponse
    {
        $skill = $this->astroRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Astrologer::SKILL_UPDATE[VIEW], compact('skill', 'language', 'defaultLanguage'));
    }

    public function skillUpdate(AstrologerSkillsAddRequest $request, $id, AstrologersService $astrologerService): RedirectResponse
    {
        $skill = $this->astroRepo->getFirstWhere(params: ['id' => $id]);
        $dataArray = $astrologerService->getSkillUpdateData(request: $request, data: $skill);
        $this->astroRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\Astrologer\Skills', id: $id);

        Toastr::success(translate('skill_updated_successfully'));
        Helpers::editDeleteLogs('Astrologer','Skill','Update');
        return redirect()->route('admin.astrologers.skill.list');
    }

    public function statusSkill(Request $request)
    {
        $status = Skills::where('id', $request->id)->update(['status' => $request->status]);
        if ($status) {
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 400]);
    }



    // category functions ----------------------------------
    public function category_list(Request $request)
    {
        $categories = $this->astroCatRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(Astrologer::CATEGORY_LIST[VIEW], compact('categories'));
    }

    public function getCategoryAddView(): View
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Astrologer::CATEGORY_ADD[VIEW], compact('language', 'defaultLanguage'));
    }

    public function addCategory(AstrologerCategoryAddRequest $request, AstrologersService $astrologerService): RedirectResponse
    {
        $dataArray = $astrologerService->getCategoryAddData(request: $request);
        $savedAttributes = $this->astroCatRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\AstrologerCategory', id: $savedAttributes->id);

        Toastr::success(translate('category_added_successfully'));
        return redirect()->route('admin.astrologers.category.list');
    }

    public function getCategoryUpdateView(string|int $id): View|RedirectResponse
    {
        $category = $this->astroCatRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Astrologer::CATEGORY_UPDATE[VIEW], compact('category', 'language', 'defaultLanguage'));
    }

    public function categoryUpdate(AstrologerCategoryUpdateRequest $request, $id, AstrologersService $astrologerService): RedirectResponse
    {
        $category = $this->astroCatRepo->getFirstWhere(params: ['id' => $id]);
        $dataArray = $astrologerService->getCategoryUpdateData(request: $request, data: $category);
        $this->astroCatRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\AstrologerCategory', id: $id);

        Toastr::success(translate('category_updated_successfully'));
        Helpers::editDeleteLogs('Astrologer','Category','Update');
        return redirect()->route('admin.astrologers.category.list');
    }

    public function statusCategory(Request $request)
    {
        $status = AstrologerCategory::where('id', $request->id)->update(['status' => $request->status]);
        if ($status) {
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 400]);
    }


    // comission functions ------------------------------------
    public function comission_list()
    {
        return view(Astrologer::COMISSION_LIST[VIEW]);
    }
}