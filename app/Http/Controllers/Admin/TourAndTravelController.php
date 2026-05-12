<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\TourAndTravelRepositoryInterface;
use App\Contracts\Repositories\TourCabManageRepositoryInterface;
use App\Contracts\Repositories\TourCabRepositoryInterface;
use App\Contracts\Repositories\TourDriverManageRepositoryInterface;
use App\Contracts\Repositories\TourOrderRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\TourAndTravelPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourAndTravelRequest;
use App\Models\TourAndTravel;
use App\Models\TourVisits;
use App\Models\WithdrawalAmountHistory;
use App\Services\TourAndTravelService;
use App\Traits\FileManagerTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TourAndTravelController extends Controller
{
    use FileManagerTrait;
    public function __construct(
        private readonly TranslationRepositoryInterface      $translationRepo,
        private readonly TourAndTravelRepositoryInterface  $tourtraveller,
        private readonly TourOrderRepositoryInterface  $tourorder,
        private readonly TourCabManageRepositoryInterface  $tourtravellercabRepo,
        private readonly TourCabRepositoryInterface $tourcabRepo,
        private readonly TourDriverManageRepositoryInterface  $tourtravellerdriverRepo,
    ) {}
    public function AddTravels()
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(TourAndTravelPath::ADDTRAVEL[VIEW], compact('languages', 'defaultLanguage', 'googleMapsApiKey'));
    }

    public function AddTraveller(TourAndTravelRequest $request, TourAndTravelService $service)
    {
        $request->validate([
            'person_email' => 'required|unique:sellers,email',
        ]);
        $dataArray = $service->getAddTourData($request);
        $insert = $this->tourtraveller->add(data: $dataArray);
        $sellers = new \App\Models\Seller();
        $sellers->f_name = explode(' ', $dataArray['person_name'])[0] ?? $dataArray['person_name'];
        $sellers->l_name =  explode(' ', $dataArray['person_name'])[1] ?? '';
        $sellers->phone = $dataArray['person_phone'];
        $sellers->image = $dataArray['image'];
        $sellers->email = $dataArray['person_email'];
        $sellers->password = bcrypt('12345678');
        $sellers->status = 'pending';
        $sellers->bank_name = $dataArray['bank_name'];
        $sellers->branch = $dataArray['bank_branch'];
        $sellers->ifsc = $dataArray['ifsc_code'];
        $sellers->account_no = $dataArray['account_number'];
        $sellers->holder_name = $dataArray['bank_holder_name'];
        $sellers->type = 'tour';
        $sellers->relation_id = $insert->id;
        $sellers->aadhar_front_image = $dataArray['aadhaar_card_image'];
        $sellers->pancard_image = $dataArray['pan_card_image'];
        $sellers->save();
        // $this->translationRepo->add(request: $request, model: 'App\Models\TourAndTravel', id: $insert->id);
        Toastr::success(translate('Tour_&_Traveller_added_successfully'));
        return redirect()->route(TourAndTravelPath::TRAVELLIST[REDIRECT]);
    }

    public function TravellerList(Request $request)
    {
        $getDatalist = $this->tourtraveller->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourAndTravelPath::TRAVELLIST[VIEW], compact('getDatalist'));
    }

    public function StatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->tourtraveller->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function TravellerUpdate(Request $request, $id)
    {
        $getData  = $this->tourtraveller->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        if (empty($getData)) {
            return back();
        }
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(TourAndTravelPath::TRAVELUPDATE[VIEW], compact('getData', 'languages', 'defaultLanguage','googleMapsApiKey'));
    }

    public function Travelleredit(TourAndTravelRequest $request, TourAndTravelService $service, $id)
    {
        $seller = \App\Models\Seller::where('relation_id', $id)->first();
        $request->validate([
            'person_email' => [
                'required',
                \Illuminate\Validation\Rule::unique('sellers', 'email')->ignore($seller->id),
            ],
        ]);

        $dataArray = $service->getUpdateTourData($request);
        $insert = $this->tourtraveller->update(id: $id, data: $dataArray);
        $sellers = \App\Models\Seller::where('relation_id', $id)->where('type', 'tour')->first();
        $sellers->f_name = explode(' ', $dataArray['person_name'])[0] ?? $dataArray['person_name'];
        $sellers->l_name =  explode(' ', $dataArray['person_name'])[1] ?? '';
        $sellers->phone = $dataArray['person_phone'];
        if (isset($dataArray['image']) && !empty($dataArray['image'])) {
            $sellers->image = $dataArray['image'];
        }
        $sellers->email = $dataArray['person_email'];

        $sellers->bank_name = $dataArray['bank_name'];
        $sellers->branch = $dataArray['bank_branch'];
        $sellers->ifsc = $dataArray['ifsc_code'];
        $sellers->account_no = $dataArray['account_number'];
        $sellers->holder_name = $dataArray['bank_holder_name'];
        if (isset($dataArray['aadhaar_card_image']) && !empty($dataArray['aadhaar_card_image'])) {
            $sellers->aadhar_front_image = $dataArray['aadhaar_card_image'];
        }
        if (isset($dataArray['pan_card_image']) && !empty($dataArray['pan_card_image'])) {
            $sellers->pancard_image = $dataArray['pan_card_image'];
        }
        $sellers->save();
        // $this->translationRepo->update(request: $request, model: 'App\Models\TourAndTravel', id: $id);
        Toastr::success(translate('Tour_&_Traveller_updated_successfully'));
        return redirect()->route(TourAndTravelPath::TRAVELLIST[REDIRECT]);
    }

    public function TravellerView(Request $request, $id)
    {
        $getData  = $this->tourtraveller->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        if (empty($getData)) {
            return back();
        }
        $name = $request ?? 'null';
        $view_type = 1;
        $withdrawRequests = []; //$this->tourtraveller->getListWhere(
        //     orderBy: ['id'=>'desc'],
        //     filters: ['admin_id'=> 0, 'whereNotNull' => 'delivery_man_id', 'status' => $request['approved']],
        //     relations: ['deliveryMan'],
        //     dataLimit: getWebConfig('pagination_limit')
        // );
        $complete_order = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('type') == 'ok') ? $request->get('searchValue') : ''), relations: ['userData', 'company', 'Tour'], filters: ['amount_status' => 1, 'status' => 1, 'drop_status' => 1, 'cab_assign_id' => $id, 'refund_status' => 0], dataLimit: getWebConfig(name: 'pagination_limit'));
        $carlists = $this->tourcabRepo->getListWhere(orderBy: ['id' => 'desc'], dataLimit: "all");
        $cabDetails = $this->tourtravellercabRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['traveller_id' => $id], relations: ['Cabs'], dataLimit: 2); //getWebConfig(name: 'pagination_limit'));
        $travellerDetails = $this->tourtravellerdriverRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['traveller_id' => $id], dataLimit: getWebConfig(name: 'pagination_limit'));
        $getcheckbox  = \App\Models\TourOrderAccept::where('traveller_id', $id)->where('status', 1)->with(['TourVisit'])->get();

        $OrderInfo = \App\Models\TourOrder::whereIn('status', [1, 0])->where('refund_status', 0)->where('amount_status', 1)->with(['userData', 'company', 'Tour']);

        $orderStatus = [
            'pending' => \App\Models\TourOrder::whereIn('status', [1, 0])
                ->where(['refund_status' => 0, 'pickup_status' => 0, 'amount_status' => 1, 'drop_status' => 0, 'cab_assign' => 0])
                ->where('pickup_date', '>=', \Carbon\Carbon::today()->toDateString())
                ->whereHas('accept', function ($query) {
                    $query->where('tour_order_accept.status', 1);
                })
                ->withCabOrderCheck($id)
                ->with(['accept', 'userData', 'company', 'Tour'])
                ->orderBy('id', 'DESC')->paginate(10, ['*'], 'pending-page', request('pending-page', 1)),

            'confirmed' => $OrderInfo->where('pickup_status', 0)->where('drop_status', 0)->where('cab_assign', $id)->orderBy('id', 'DESC')->paginate(10, ['*'], 'confirm-page', request('confirm-page', 1)),
            'pickup'    => $OrderInfo->where('pickup_status', 1)->where('drop_status', 0)->where('cab_assign', $id)->orderBy('id', 'DESC')->paginate(10, ['*'], 'pickup-page', request('pickup-page', 1)),
            'canceled'  => optional(\App\Models\TourAndTravel::find($id))->cancel_order,
        ];
        $getAllBankAll = WithdrawalAmountHistory::select('vendor_id', 'bank_name', 'ifsc_code', 'account_number', 'holder_name', \DB::raw('0 as primary_status'))->where('vendor_id', $id)->where('type', 'tour')->where('account_number', "!=", '')->whereNotNull('account_number')->groupBy('account_number')->get();
        $getPrimaryBank = TourAndTravel::select('id as vendor_id', 'bank_name', 'ifsc_code', 'account_number', 'bank_holder_name as holder_name', \DB::raw('1 as primary_status'))->where('id', $id)->first();
        $getAllBankInfor = $getAllBankAll->push($getPrimaryBank)->unique('account_number')->values();
        $getAllUpiInfor = WithdrawalAmountHistory::where('vendor_id', $id)->where('type', 'tour')->select('upi_code')->whereNotNull('upi_code')->groupBy('upi_code')->get();
        return view(TourAndTravelPath::TRAVELVIEW[VIEW], compact('getAllBankInfor', 'getPrimaryBank', 'getAllUpiInfor', 'getcheckbox', 'carlists', 'cabDetails', 'travellerDetails', 'getData', 'name', 'view_type', 'withdrawRequests', 'complete_order', 'orderStatus'));
    }

    public function TravellerDelete(Request $request, TourAndTravelService $service, $id)
    {
        $service->removedoc($request);
        $this->tourtraveller->delete(params: ['id' => $id]);
        $this->translationRepo->delete(model: 'App\Models\TourAndTravel', id: $id);
        Toastr::success(translate('Tour_&_Traveller_deleted_successfully'));
        return redirect()->route(TourAndTravelPath::TRAVELLIST[REDIRECT]);
    }

    public function TravelleApproval(Request $request, $id)
    {
        $check_seller =  \App\Models\Seller::where(['type' => 'tour', 'relation_id' => $id])->first();
        if ($check_seller && $request['seller_status'] == 'update_profile_update') {
            \App\Models\Seller::where(['type' => 'tour', 'relation_id' => $id])->update(['update_seller_status' => 2]);
            Toastr::success('Tour & Traveler profile Edited Approval successfully');
        } elseif ($check_seller) {
            \App\Models\Seller::where(['type' => 'tour', 'relation_id' => $id])->update(['status' => $request->seller_status]);
            if ($request->seller_status == 'hold' || $request->seller_status == 'suspended') {
                \App\Models\TourOrderAccept::where('traveller_id', $id)->update(['status' => 0]);
            }
            $data['is_approve'] = $request->get('status', 0);
            $this->tourtraveller->update(id: $id, data: $data);
            Toastr::success('Tour & Traveler profile updated successfully');
        }
        return back();
    }


    public function CabStatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->tourtravellercabRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function CabTravellerDelete(Request $request)
    {
        $old_data = $this->tourtravellercabRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_cab/' . $old_data['image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_cab/' . $old_data['image']);
            }
            $this->tourtravellercabRepo->delete(params: ['id' => $request['id']]);
            Toastr::success(translate('Traveller_cab_Deleted_successfully'));
            return response()->json(['success' => 1, 'message' => translate('Traveller_cab_Deleted_successfully')], 200);
        } else {
            Toastr::error(translate('Traveller_cab_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Traveller_cab_Deleted_Failed')], 400);
        }
    }
    public function CabStore(Request $request)
    {
        $request->validate([
            "traveller_id" => 'required|exists:tour_and_travels,id',
            'cab_id' => 'required|integer|exists:tour_cab,id',
            'reg_number' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Za-z0-9 ]+$/',
                \Illuminate\Validation\Rule::unique('tour_traveller_cabs', 'reg_number'),
            ],
            'model_number' => 'required|string|max:50',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);

        $dataArray = [
            "traveller_id" => $request['traveller_id'],
            "cab_id" => $request['cab_id'],
            "model_number" => $request['model_number'],
            "reg_number" => $request['reg_number'],
            "status" => 0,
            "fuel_type" => $request['fuel_type'],
            "image" => json_encode(['']),
        ];
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $images1 = [];
            if (is_array($imageFile)) {
                foreach ($imageFile as $img) {
                    $filename = time() . '-tourcab-' . $img->getClientOriginalName();
                    $img->storeAs('public/tour_and_travels/tour_traveller_cab', $filename);
                    $images1[] = $filename;
                }
            } else {
                $filename = time() . '-tourcab-' . $imageFile->getClientOriginalName();
                $imageFile->storeAs('public/tour_and_travels/tour_traveller_cab', $filename);
                $images1[] = $filename;
            }

            $dataArray['image'] = json_encode($images1);
        }


        $this->tourtravellercabRepo->add(data: $dataArray);
        Toastr::success('Cab Added Successfully');
        return back();
    }

    public function CabImageRemove(Request $request, $id, $name)
    {
        $gallery_list = $this->tourtravellercabRepo->getFirstWhere(params: ['id' => $id]);
        if ($gallery_list) {
            if (Storage::disk('public')->exists('/tour_and_travels/tour_traveller_cab/' . $name)) {
                Storage::disk('public')->delete('/tour_and_travels/tour_traveller_cab/' . $name);
            }
            $dataImage = json_decode($gallery_list['image'] ?? "['']", true);
            if ($dataImage) {
                foreach ($dataImage as $index => $image) {
                    if ($image == $name) {
                        unset($dataImage[$index]);
                        break;
                    }
                }
                $dataImage = array_values($dataImage);
            }
            $array = ['image' => json_encode($dataImage)];
            $this->tourtravellercabRepo->update(id: $id, data: $array);
            Toastr::success(translate('image_Deleted_successfully'));
        } else {
            Toastr::success(translate('image_Deleted_Failed'));
        }
        return redirect()->route('admin.tour_and_travels.cab.cab-update', ['id' => $id]);
    }

    public function CabUpdate(Request $request)
    {
        $traveller_data =  $this->tourtravellercabRepo->getFirstWhere(params: ['id' => $request['id']]);
        $carlists = $this->tourcabRepo->getListWhere(orderBy: ['id' => 'desc'], dataLimit: "all");
        return view(TourAndTravelPath::TRAVCABUPDATE[VIEW], compact('carlists', 'traveller_data'));
    }
    public function CabEdit(Request $request)
    {
        $request->validate([
            'cab_id' => 'required|integer|exists:tour_cab,id',
            'reg_number' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Za-z0-9 ]+$/',
                Rule::unique('tour_traveller_cabs', 'reg_number')->ignore($request->id),
            ],
            'model_number' => 'required|string|max:50',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,web,gif|max:2048',

        ]);

        $traveller_data =  $this->tourtravellercabRepo->getFirstWhere(params: ['id' => $request['id']]);
        if (empty($traveller_data)) {
            return back();
        }
        $dataArray = [
            "cab_id" => $request['cab_id'],
            "model_number" => $request['model_number'],
            "reg_number" => $request['reg_number'],
            "fuel_type" => $request['fuel_type'],
        ];

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $images1 = json_decode($traveller_data['image'] ?? "['']", true);
            if (is_array($imageFile)) {
                foreach ($imageFile as $img) {
                    $filename = time() . '-tourcab-' . $img->getClientOriginalName();
                    $img->storeAs('public/tour_and_travels/tour_traveller_cab', $filename);
                    $images1[] = $filename;
                }
            } else {
                $filename = time() . '-tourcab-' . $imageFile->getClientOriginalName();
                $imageFile->storeAs('public/tour_and_travels/tour_traveller_cab', $filename);
                $images1[] = $filename;
            }

            $dataArray['image'] = json_encode($images1);
        }
        $this->tourtravellercabRepo->update(id: $request['id'], data: $dataArray);
        Toastr::success(translate('Traveller_cab_Updated_successfully'));
        return redirect()->route('admin.tour_and_travels.information', $traveller_data['traveller_id']);
    }

    public function DriverStore(Request $request)
    {
        $request->validate([
            "traveller_id" => 'required|exists:tour_and_travels,id',
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'numeric',
                'between:1000000000,999999999999',
                Rule::unique('tour_traveller_driver', 'phone'),
            ],
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:-18 years',
            'year_ex' => 'required|integer|min:0',
            'license_number' => [
                'required',
                Rule::unique('tour_traveller_driver', 'license_number'),
            ],
            'pan_number' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9 ]{1,15}$/',
                Rule::unique('tour_traveller_driver', 'pan_number'),
            ],
            'aadhar_number' => [
                'required',
                'string',
                'regex:/^\d{12}$/',
                Rule::unique('tour_traveller_driver', 'aadhar_number'),
            ],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pan_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhar_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $dataArray = [
            "traveller_id" => $request['traveller_id'],
            "name" => $request['name'],
            "phone" => $request['phone'],
            "email" => ($request['email'] ?? ""),
            "gender" => $request['gender'],
            "dob" => $request['dob'],
            "year_ex" => $request['year_ex'],
            "license_number" => $request['license_number'],
            "pan_number" => $request['pan_number'],
            "aadhar_number" => $request['aadhar_number'],
            "status" => 0,
        ];
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $dataArray['image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['image']);
        }
        if ($request->hasFile('license_image')) {
            $imageFile = $request->file('license_image');
            $dataArray['license_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['license_image']);
        }
        if ($request->hasFile('pan_image')) {
            $imageFile = $request->file('pan_image');
            $dataArray['pan_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['pan_image']);
        }
        if ($request->hasFile('aadhar_image')) {
            $imageFile = $request->file('aadhar_image');
            $dataArray['aadhar_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['aadhar_image']);
        }
        $this->tourtravellerdriverRepo->add(data: $dataArray);
        Toastr::success(translate('Traveller_Driver_Added_successfully'));
        return back();
    }

    public function DriverStatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->tourtravellerdriverRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function DriverDetele(Request $request)
    {
        $old_data = $this->tourtravellerdriverRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['image']);
            }
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['license_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['license_image']);
            }
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['pan_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['pan_image']);
            }
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['aadhar_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['aadhar_image']);
            }
            $this->tourtravellerdriverRepo->delete(params: ['id' => $request['id']]);
            Toastr::success(translate('Traveller_driver_Deleted_successfully'));
            return response()->json(['success' => 1, 'message' => translate('Traveller_driver_Deleted_successfully')], 200);
        } else {
            Toastr::error(translate('Traveller_driver_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Traveller_driver_Deleted_Failed')], 400);
        }
    }

    public function DriverUpdate(Request $request)
    {
        $getData = $this->tourtravellerdriverRepo->getFirstWhere(params: ['id' => $request['id']]);
        return view(TourAndTravelPath::TRAVDRIVERUPDATE[VIEW], compact('getData'));
    }

    public function DriverEdit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'numeric',
                'between:1000000000,999999999999',
                Rule::unique('tour_traveller_driver', 'phone')->ignore($request->id),
            ],
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:-18 years',
            'year_ex' => 'required|integer|min:0',
            'license_number' => [
                'required',
                Rule::unique('tour_traveller_driver', 'license_number')->ignore($request->id),
            ],
            'pan_number' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9 ]{1,15}$/',
                Rule::unique('tour_traveller_driver', 'pan_number')->ignore($request->id),
            ],
            'aadhar_number' => [
                'required',
                'string',
                'regex:/^\d{12}$/',
                Rule::unique('tour_traveller_driver', 'aadhar_number')->ignore($request->id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhar_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $old_data = $this->tourtravellerdriverRepo->getFirstWhere(params: ['id' => $request['id']]);
        if (empty($old_data)) {
            return back();
        }
        $dataArray = [
            "name" => $request['name'],
            "phone" => $request['phone'],
            "email" => ($request['email'] ?? ""),
            "gender" => $request['gender'],
            "dob" => $request['dob'],
            "year_ex" => $request['year_ex'],
            "license_number" => $request['license_number'],
            "pan_number" => $request['pan_number'],
            "aadhar_number" => $request['aadhar_number'],
        ];
        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['image']);
            }
            $imageFile = $request->file('image');
            $dataArray['image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['image']);
        }
        if ($request->hasFile('license_image')) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['license_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['license_image']);
            }
            $imageFile = $request->file('license_image');
            $dataArray['license_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['license_image']);
        }
        if ($request->hasFile('pan_image')) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['pan_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['pan_image']);
            }
            $imageFile = $request->file('pan_image');
            $dataArray['pan_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['pan_image']);
        }
        if ($request->hasFile('aadhar_image')) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['aadhar_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['aadhar_image']);
            }
            $imageFile = $request->file('aadhar_image');
            $dataArray['aadhar_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['aadhar_image']);
        }
        $this->tourtravellerdriverRepo->update(id: $request['id'], data: $dataArray);
        Toastr::success(translate('Traveller_driver_Updated_successfully'));
        return redirect()->route('admin.tour_and_travels.information', $old_data['traveller_id']);
    }

    public function VendorTourCommission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'self_driving_commission' => 'required',
            "tour_admin_commission" => "required",
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $this->tourtraveller->update(id: $request['id'], data: ['self_driving_commission' => $request['self_driving_commission'], 'admin_commission' => $request['tour_admin_commission']]);
        TourVisits::where('created_id',  $request['id'])->update(['tour_commission' => $request['tour_admin_commission']]);
        Toastr::success(translate('Changes_updated_successfully'));
        return back();
    }
}
