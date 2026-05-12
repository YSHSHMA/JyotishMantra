<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\TourOrderRepositoryInterface;
use App\Contracts\Repositories\TourTypeRepositoryInterface;
use App\Contracts\Repositories\TourVisitPlaceRepositoryInterface;
use App\Contracts\Repositories\TourVisitRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\TourVisitPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourVisitPlaceRequest;
use App\Http\Requests\Admin\TourVisitRequest;
use App\Models\TourAndTravel;
use App\Models\TourCab;
use App\Models\TourFollowup;
use App\Models\TourLeads;
use App\Models\TourOrder;
use App\Models\TourPackage;
use App\Models\TourReviews;
use App\Models\TourVisits;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\TourVisitService;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\TourVisitValidation;

class TourVisitController extends Controller
{

    use FileManagerTrait;
    use TourVisitValidation;
    public function __construct(
        private readonly TranslationRepositoryInterface     $translationRepo,
        private readonly TourVisitRepositoryInterface  $tourtraveller,
        private readonly TourVisitPlaceRepositoryInterface  $tourvisitplac,
        private readonly TourOrderRepositoryInterface  $tourorder,
        private readonly TourTypeRepositoryInterface  $tourtypeRepo,
    ) {}

    public function AddTour()
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $package_list = TourPackage::where('status', 1)->orderBy('id', 'desc')->get();
        $cab_list = TourCab::where('status', 1)->orderBy('id', 'desc')->get();
        $travelar_list = TourAndTravel::where('status', 1)->where('is_approve', 1)->orderBy('id', 'desc')->get();
        $typeList = $this->tourtypeRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['status' => 1], dataLimit: "all");
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(TourVisitPath::ADDTRAVEL[VIEW], compact('cab_list', 'typeList', 'googleMapsApiKey', 'travelar_list', 'package_list', 'languages', 'defaultLanguage'));
    }
    public function SaveTour(Request $request, TourVisitService $service) //TourVisitRequest
    {
        $step = $request->input('step', 1);
        $rules = $this->getTourVisitRules($step, $request);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => Helpers::error_processor($validator)[0]['message'],
                'errors' => $validator->errors(),
                'step' => $step
            ], 422);
        }
        $dataArray = $service->getTourVisitData($request);
        if ($request->has('step') == 1 && (($request['id'] ?? "") == '')) {
            $insert = $this->tourtraveller->add(data: $dataArray);
            $id_tour = $insert->id;
            $this->translationRepo->add(request: $request, model: 'App\Models\TourVisits', id: $insert->id);
        } else {
            $insert = $this->tourtraveller->update(id: $request['id'], data: $dataArray);
            $id_tour = $request['id'];
            if ($request['step'] == 1) {
                $this->translationRepo->update(request: $request, model: 'App\Models\TourVisits', id: $request['id']);
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Step ' . $request['step'] . ' data saved successfully',
            'tour_id' => $id_tour,
        ]);
        // Toastr::success(translate('Tour_Visit_added_successfully'));
        // return redirect()->route(TourVisitPath::TRAVELLIST[REDIRECT]);
    }

    public function TourList(Request $request)
    {
        $getDatalist = $this->tourtraveller->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourVisitPath::TRAVELLIST[VIEW], compact('getDatalist'));
    }

    public function TourListFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('search_by_name', '');
        $searchByType = $request->input('search_by_type', '');
        $searchByCabId = $request->input('search_by_cabid', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $columnName = $request->input("columns.$orderColumnIndex.data");
        $query = TourVisits::query();
        $query->when(isset($searchByType), function ($query) use ($searchByType) {
            return $query->where(['use_date' => $searchByType]);
        })->when(isset($searchByCabId), function ($query) use ($searchByCabId) {
            if ($searchByCabId == 'admin') {
                return $query->where('created_id', '==', 0);
            } else {
                return $query->where('created_id', '!=', 0);
            }
        })
        ;
        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where(function ($querys) use ($searchValue) {
                    $querys->where('tour_name', 'like', "%$searchValue%");
                });
            });
        }
        $recordsTotal = TourVisits::when(isset($searchByType), function ($query) use ($searchByType) {
            return $query->where(['use_date' => $searchByType]);
        })->when(isset($searchByCabId), function ($query) use ($searchByCabId) {
            if ($searchByCabId == 'admin') {
                return $query->where('created_id', '==', 0);
            } else {
                return $query->where('created_id', '!=', 0);
            }
        })->count();

        $recordsFiltered = $query->count();
        $data = $query->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $formattedData = $data->map(function ($item, $key) use ($start) {
            $formId = 'temple-status' . $item->id . '-form';
            $inputId = 'temple-status' . $item->id;
            $checked = $item->status == 1 ? 'checked' : '';
            $routeUrl = route('admin.tour_visits.status-update');
            $csrf = csrf_token();

            $statusForm = '
        <form action="' . $routeUrl . '" method="post" id="' . $formId . '">
            <input type="hidden" name="_token" value="' . $csrf . '">
            <input type="hidden" name="id" value="' . $item->id . '">
            <label class="switcher mx-auto">
                <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                    id="' . $inputId . '" value="1" ' . $checked . '
                    data-modal-id="toggle-status-modal"
                    data-toggle-id="' . $inputId . '"
                    data-on-title="Want to Turn ON cab status"
                    data-off-title="Want to Turn OFF cab status"
                    data-on-message="<p>If enabled, this Tour Visit will be available on the website and customer app</p>"
                    data-off-message="<p>If disabled, this Tour Visit will be hidden from the website and customer app</p>">
                <span class="switcher_control"></span>
            </label>
        </form>';

            $options = ' <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn ' . ((!\App\Models\TourVisitPlace::where('tour_visit_id', $item['id'])->where('status', 1)->exists()) ? 'btn-tour-visit-empty' : '') . '" title="' . translate('visit-list') . '"
                                                href="' . route('admin.tour_visits.add-visit', [$item['id']]) . '">
                                                <i class="tio-boot_open">boot_open</i>
                                            </a>
                                            <a class="btn btn-outline-info btn-sm square-btn" title="' . translate('edit') . '"
                                                href="' . route('admin.tour_visits.update', [$item['id']]) . '">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="tourtravellers-' . $item['id'] . '" title="' . translate('delete') . '"><i class="tio-delete"></i>
                                            </a>
                                           <form action="' . route('admin.tour_visits.tour-delete', [$item['id']]) . '" method="post" id="tourtravellers-' . $item['id'] . '">
                                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                                    <input type="hidden" name="_method" value="DELETE">
                                            </form>';
            if ($item['tour_type'] == 'special_tour') {
                $options .= '<a class="btn btn-sm btn-outline-primary" onclick="booking_cancel(`' . $item['id'] . '`)"><i class="tio-autorenew"></i></a>';
            }
            $options .= ' <a class="btn btn-outline-primary btn-sm open-whatsapp-modal"
                                                href="javascript:void(0);"
                                                data-id="' . $item['id'] . '"
                                                data-slug="' . $item['slug'] . '"
                                                title="' . translate('whatsapp') . '">
                                                <i class="tio-whatsapp"></i>
                                            </a>
                                        </div>';

            $Create_By = '<span class="font-weight-bolder">' . (($item['created_id'] == 0) ? "Admin" : "Vendor (" . \Illuminate\Support\Str::limit((\App\Models\TourAndTravel::where('id', $item['created_id'])->first()['company_name'] ?? ""), 25)) . ')</span><br>
                                        <span>Commission: ' . ($item['tour_commission'] ?? 0) . '%</span><br>';

            if ($item['use_date'] == 1) {
                $tour_types = "Special Tour(With Date)";
            } elseif ($item['use_date'] == 2) {
                $tour_types =   "Daily Tour(With Address)";
            } elseif ($item['use_date'] == 3) {
                $tour_types =  "Daily Tour(WithOut Address)";
            } elseif ($item['use_date'] == 4) {
                $tour_types =  "Special Tour(Without Date)";
            } else {
                $tour_types = "Cities Tour";
            }
            return [
                'id' => $start + $key + 1,
                'tour_id' => '<a class="font-weight-bold text-secondary" href="' . route('admin.tour_visits.overview', [$item['id']]) . '">#' . ($item['tour_id'] ?? '') . '</a>',
                'tour_type' => ucwords(str_replace('_', ' ', $item['tour_type'] ?? "")),
                'use_date' => ($tour_types ?? ''),
                'tour_name' => \Illuminate\Support\Str::limit(($item['tour_name'] ?? ""), 30),
                'status' => $statusForm,
                'create_by' => $Create_By,
                'option' => $options,
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData
        ]);
    }

    public function StatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->tourtraveller->update(id: $request['id'], data: $data);
        Toastr::success(translate('status_updated_successfully'));
        return redirect()->route(TourVisitPath::TRAVELLIST[REDIRECT]);
        // return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function TourUpdate(Request $request, $id)
    {
        $getData  = $this->tourtraveller->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        if (empty($getData)) {
            return back();
        }
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $package_list = TourPackage::where('status', 1)->orderBy('id', 'desc')->get();
        $cab_list = TourCab::where('status', 1)->orderBy('id', 'desc')->get();
        $travelar_list = TourAndTravel::where('status', 1)->where('is_approve', 1)->orderBy('id', 'desc')->get();
        $typeList = $this->tourtypeRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['status' => 1], dataLimit: "all");
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(TourVisitPath::TRAVELUPDATE[VIEW], compact('cab_list', 'typeList', 'package_list', 'travelar_list', 'googleMapsApiKey', 'getData', 'languages', 'defaultLanguage'));
    }

    public function Touredit(Request $request, TourVisitService $service) //TourVisitRequest
    {
        $step = $request->input('step', 1);
        $rules = $this->getTourVisitRules($step, $request);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => Helpers::error_processor($validator)[0]['message'],
                'errors' => $validator->errors(),
                'step' => $step
            ], 422);
        }
        $getData  = $this->tourtraveller->getFirstWhere(params: ['id' => $request->id]);
        $dataArray = $service->getUpdateTourData($request, $getData);
        $this->tourtraveller->update(id: $request->id, data: $dataArray);
        if ($request['step'] == 1) {
            $this->translationRepo->update(request: $request, model: 'App\Models\TourVisits', id: $request->id);
        }
        return response()->json([
            'success' => true,
            'message' => 'Step ' . $request['step'] . ' data saved successfully',
            'tour_id' => $request->id,
        ]);
    }

    public function TourDelete(Request $request, TourVisitService $service, $id)
    {
        $old_data = $this->tourtraveller->getFirstWhere(params: ['id' => $id]);
        $service->removedoc($old_data);
        $this->tourtraveller->delete(params: ['id' => $id]);
        $this->translationRepo->delete(model: 'App\Models\TourVisits', id: $id);
        Toastr::success(translate('Tour_visit_deleted_successfully'));
        return redirect()->route(TourVisitPath::TRAVELLIST[REDIRECT]);
    }

    public function TourView(Request $request, $id)
    {
        $getData  = $this->tourtraveller->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        if (empty($getData)) {
            return back();
        }
        $name = 'null';
        $view_type = 1;
        $order_list = TourOrder::where('tour_id', $getData['id'])->where('status', '!=', 2)->with(['userData', 'company'])->paginate(10, ['*'], 'page1');
        $refund_list = TourOrder::where('tour_id', $getData['id'])->where('status', 2)->with(['userData', 'company'])->paginate(10, ['*'], 'page2');
        $tour_reviews = TourReviews::where('tour_id', $getData['id'])->with(['userData'])->paginate(10, ['*'], 'page3');

        return view(TourVisitPath::TRAVELVIEW[VIEW], compact('getData', 'name', 'view_type', 'order_list', 'refund_list', 'tour_reviews'));
    }

    public function VisitList(Request $request, $id)
    {
        $getData = $this->tourvisitplac->getListWhere(orderBy: ['id' => 'desc'], filters: ['tour_visit_id' => $id], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $tour_visit_id = $id;
        return view(TourVisitPath::VISIT[VIEW], compact('getData', 'tour_visit_id', 'languages', 'defaultLanguage'));
    }

    public function VisitStore(TourVisitPlaceRequest $request, TourVisitService $service)
    {
        $dataArray = $service->getTourVisitPlace($request);
        $insert = $this->tourvisitplac->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\TourVisitPlace', id: $insert->id);
        Toastr::success(translate('Tour_Visit_place_added_successfully'));
        return redirect()->route(TourVisitPath::VISIT[REDIRECT], [$request->tour_visit_id]);
    }
    public function VisitPlaceStatus(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->tourvisitplac->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function TourImageRemove(TourVisitService $service, $id, $name)
    {
        $getData  = $this->tourtraveller->getFirstWhere(params: ['id' => $id]);
        if (empty($getData)) {
            return back();
        }
        $dataIMage = $service->ImageRemove($getData, $name);
        $this->tourtraveller->update(id: $id, data: ['image' => json_encode($dataIMage)]);
        return back();
    }

    public function VisitPlaceDelete(Request $request, TourVisitService $service)
    {
        $old_data = $this->tourvisitplac->getFirstWhere(params: ['id' => $request->id]);
        $service->removeimages($old_data);
        $this->tourvisitplac->delete(params: ['id' => $request->id]);
        $this->translationRepo->delete(model: 'App\Models\TourVisitPlace', id: $request->id);
        Toastr::success(translate('Tour_visit_deleted_successfully'));
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
        //return redirect()->route(TourVisitPath::VISIT[REDIRECT],[$old_data['tour_visit_id']]);
    }

    public function TourLeads(Request $request)
    {
        $Tourlist = TourLeads::where('amount_status', 0)->with(['Tour', 'userData', 'followby'])
            ->when($request->searchValue, function ($query) use ($request) {
                $query->where('amount', 'like', "%$request->searchValue%");
                $query->orWhereHas('Tour', function ($q) use ($request) {
                    $q->where('tour_name', 'like', "%$request->searchValue%");
                });
                $query->orWhereHas('userData', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                    $q->orWhere('phone', 'like', "%$request->searchValue%");
                });
            })->orderBy('id', 'desc')->paginate(10);
        return view(TourVisitPath::LEADS[VIEW], compact('Tourlist'));
    }

    public function TourLeadListFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('searchValue', '');
        $searchByType = $request->input('search_by_type', '');
        $searchByCabId = $request->input('search_by_status', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $start_date = $request->get('start_date') ?? '';
        $end_date = $request->get('end_date') ?? '';
        $columnName = $request->input("columns.$orderColumnIndex.data");
        $querys = TourLeads::query();
        $querys->with(['Tour', 'TourOrder', 'userData', 'followby'])->when($searchValue, function ($qu1) use ($searchValue) {
            $qu1->Where('order_id', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            $qu1->orwhereHas('userData', function ($q2) use ($searchValue) {
                $q2->where('name', 'like', "%$searchValue%");
                $q2->orWhere('email', 'like', "%$searchValue%");
                $q2->orWhere('phone', 'like', "%$searchValue%");
            });
            $qu1->orWhereHas('Tour', function ($q3) use ($searchValue) {
                $q3->where('tour_name', 'like', "%$searchValue%");
            });
        })
            ->when(isset($searchByType), function ($query) use ($searchByType) {
                return $query->whereHas('Tour', function ($q) use ($searchByType) {
                    $q->where('use_date', $searchByType); // or ->where('use_date', $searchByType)
                });
            })->when(isset($searchByCabId) && in_array($searchByCabId, [0, 1, 2]), function ($query) use ($searchByCabId) {
                return $query->where('amount_status', $searchByCabId)->where('status', '!=', 3);
            })->when(isset($searchByCabId) && in_array($searchByCabId, [3]), function ($query) use ($searchByCabId) {
                return $query->where('status', $searchByCabId);
            })
            ->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('created_at', [$start_date, $end_date]);
            });
        $recordsTotal = TourLeads::with(['Tour', 'TourOrder', 'userData', 'followby'])->when(isset($searchByType), function ($query) use ($searchByType) {
            return $query->whereHas('Tour', function ($q) use ($searchByType) {
                $q->where('use_date', $searchByType); // or ->where('use_date', $searchByType)
            });
        })->when(isset($searchByCabId) && in_array($searchByCabId, [0, 1, 2]), function ($query) use ($searchByCabId) {
            return $query->where('amount_status', $searchByCabId)->where('status', '!=', 3);
        })->when(isset($searchByCabId) && in_array($searchByCabId, [3]), function ($query) use ($searchByCabId) {
            return $query->where('status', $searchByCabId);
        })->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
            $query4->whereBetween('created_at', [$start_date, $end_date]);
        })->count();

        $recordsFiltered = $querys->count();
        $data = $querys->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $formattedData = $data->map(function ($item, $key) use ($start) {

            $options = '<div class="d-flex justify-content-center gap-2">                                            
                                            <a href="' . route('admin.tour_visits.leads-delete', [$item['id']]) . '"
                                                class="btn btn-icon bg-label-danger waves-effect waves-light myactionbtn"
                                                onclick="return confirm(\'Are your sure, you want to delete\');"
                                                data-toggle="tooltip" aria-label="Delete"
                                                data-bs-original-title="Delete"><i class="tio-delete-outlined"></i></a>';
            $options = '
            <div class="options-container">               
                                                
                <input type="checkbox" id="options-toggle-' . $item['id'] . '" class="options-toggle-checkbox" style="display: none;"> 
                <div class="d-flex justify-content-center">';
            if ($item['amount_status'] != 1 && $item['status'] != 3) {
                $options .= ' <a href="' . route('admin.tour_visits.leads-close-update', [$item['id']]) . '"
                                                     class="btn btn-icon bg-label-danger waves-effect btn-sm mr-1"
                                                     onclick="return confirm(\'Are your sure, you want to Close Ticket\');" data-toggle="tooltip" aria-label="Close"
                                                     title="Close Ticket"><i class="tio-call_cancelled">call_cancelled</i></a>';
            }

            $options .= '   <label for="options-toggle-' . $item['id'] . '" 
                        class="btn btn-icon bg-label-primary waves-effect waves-light options-toggle-label btn-sm"
                        data-toggle="tooltip" 
                        title="Show Options"
                        data-bs-original-title="Show Options">
                        <i class="tio-menu_vs">menu_vs</i>
                    </label>
                    &nbsp;';
            if ($item['amount_status'] != 1 && $item['status'] != 3) {
                $options .= '<a class="btn btn-info btn-sm btn-icon"  target="_blank" rel="noopener noreferrer" href="' . route('admin.tour_visits.tour-admin-lead-edit', ['id' => $item['id']]) . '" data-toggle="tooltip" title="Update Lead"><i class="tio-edit"></i></a>';
            }
            $options .= '</div>
                
            <div class="options-content">
            <div class="d-flex justify-content-center gap-2"> 
            <a href="javascript:void(0)" class="btn btn-icon bg-label-success waves-effect waves-light myactionbtn"
                                                data-custId="' . $item['user_id'] . '" data-leadsId="' . $item['id'] . '"
                                                onclick="followUp(this)" data-toggle="tooltip" aria-label="Follow Up"
                                                data-bs-original-title="Follow Up">
                                                <i class="tio-message"></i>
                                            </a>
                                      
                                            <a href="javascript:0"
                                                class="btn btn-icon bg-label-info waves-effect waves-light myactionbtn"
                                                data-leadsId="' . $item['id'] . '" onclick="followHistory(this)"
                                                data-toggle="tooltip" aria-label="Follow Up History"
                                                data-bs-original-title="Follow Up History"><i
                                                    class="tio-history"></i></a>
                                            <a href="tel:' . ($item['userData']['phone'] ?? '') . '"
                                                class="btn btn-icon bg-label-warning waves-effect waves-light myactionbtn"
                                                data-toggle="tooltip" aria-label="Call" data-bs-original-title="Call"><i
                                                    class="tio-call"></i></a>
                                                     <a data-href="' . route('admin.tour_visits.tour-whatsapp-message', [$item['id']]) . '" class="btn btn-icon bg-label-success waves-effect waves-light myactionbtn" data-toggle="tooltip" aria-label="whatsapp" data-bs-original-title="whatsapp" onclick="sendmessgaeUser(this)"><i class="tio-whatsapp" title="whatsapp"></i>
                                                <span class="btn-status btn-sm-status btn-status-danger">' . $item['whatsapp_hit'] . '</span>
                                            </a>
                                            <a href="' . route('admin.whatsapp.tours-template') . '" target="_blank" rel="noopener noreferrer"
                                                class="btn btn-icon bg-label-primary waves-effect waves-light myactionbtn"
                                                data-toggle="tooltip" aria-label="customise message"
                                                data-bs-original-title="customise message"><i class="tio-message" title="customise message"></i>
                                            </a>
                                        </div>
                                         
                    </div></div> ';


            if (($item['Tour']['use_date'] ?? "") == 1) {
                $tour_types = "Special Tour(With Date)";
            } elseif (($item['Tour']['use_date'] ?? "") == 2) {
                $tour_types =   "Daily Tour(With Address)";
            } elseif (($item['Tour']['use_date'] ?? "") == 3) {
                $tour_types =  "Daily Tour(WithOut Address)";
            } elseif (($item['Tour']['use_date'] ?? "") == 4) {
                $tour_types =  "Special Tour(Without Date)";
            } else {
                $tour_types = "Cities Tour";
            }
            $tour_info = '<span class="font-weight-bolder" data-toggle="tooltip" title="' . e($item['Tour']['tour_name'] ?? "") . '">'
                . e(Str::limit($item['Tour']['tour_name'] ?? "", 25)) .
                '</span><br>';
            $tour_info .= '<span class="font-weight-bolder" data-toggle="tooltip" title="' . e($tour_types ?? "") . '">'
                . e(Str::limit($tour_types ?? "", 20)) .
                '</span><br>';
            if (!empty($item['Tour']['package_list'] ?? "") && json_decode($item['Tour']['package_list'], true)) {
                foreach (json_decode($item['Tour']['package_list'], true) as $val) {
                    if ($val['id'] == ($item['package_id'] ?? "")) {
                        $cab_name = \App\Models\TourCab::where('id', ($val['cab_id'] ?? ""))->first();
                        $tour_info .=  ($cab_name['name'] ?? "") . '
                                        <a data-toggle="tooltip" data-html="true" title="';
                        if (!empty($val['package_id'] ?? '')) {
                            foreach ($val['package_id'] as $pn) {
                                $tour_info .= '<p>Package added : <strong>' . (\App\Models\TourPackage::where('id', ($pn ?? ''))->first()['name'] ?? '') . '</strong></p>';
                            }
                        }
                        $tour_info .= '">
                                            <i class="tio-info"></i>
                                        </a>';
                        break;
                    }
                }
            }
            $platformColor = '#6c757d';
            if ($item['platform'] === 'web') {
                $platformColor = '#007bff';
            } else if ($item['platform'] === 'app') {
                $platformColor = '#17a2b8';
            } else if ($item['platform'] === 'instagram') {
                $platformColor = '#e1306c';
            } else if ($item['platform'] === 'facebook') {
                $platformColor = '#1877f2';
            } else if ($item['platform'] === 'ads') {
                $platformColor = '#ff9800';
            } else if ($item['platform'] === 'admin') {
                $platformColor = '#28a745';
            }
            if ($item['order_id']) {
                $tour_info .= '<span class="font-weight-bolder">ID : ' . ($item['TourOrder']['order_id'] ?? "") . '</span><br>';
            }
            $tour_info .= '<span class="font-weight-bolder">' . webCurrencyConverter(amount: (float)($item['TourOrder']['order_amount'] ?? 0)) . "</span>  <span style='
                                        background-color:" . $platformColor . ";
                                        color: white;
                                        align-items: center;
                                        border-radius: 0 4px 4px 0;
                                        padding: 5px 12px;
                                    '>" . $item['platform'] . "</span>";
            /////////////
            $user_info = '<span class="font-weight-bolder">' . ($item['userData']['name'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['userData']['email'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['userData']['phone'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . date('d M,Y h:i A', strtotime($item['created_at'])) . '</span><br>';
            ///////////////////////
            $statusForm = '';
            $closeTicket = "";
            if ($item['status'] == 3) {
                $closeTicket = "Close Ticket";
            }
            if ($item['amount_status'] == 0) {
                $statusForm = 'Pending';
            } elseif ($item['amount_status'] == 1) {
                $statusForm = 'Success';
            } elseif ($item['amount_status'] == 2) {
                $statusForm = 'Failed';
            }
            return [
                'id' => $start + $key + 1,
                'platform' => $item['platform'],
                'use_info' => $user_info,
                'tour_name' => $tour_info,
                'create_by' => date('d M,Y h:i A', strtotime($item['created_at'])),
                'via_wallet' => $item['via_wallet'],
                'via_online' => $item['via_online'],
                'status' => $statusForm,
                'follow_by' => ((!empty($item['followby']['follow_by'] ?? "")) ? ($item['followby']['follow_by'] ?? "") : 'pending'),
                'next_date' => ((!empty($item['followby']['next_date'] ?? "")) ? date('d M,Y', strtotime($item['followby']['next_date'] ?? "")) : 'pending'),
                'last_date' => ((!empty($item['followby']['last_date'] ?? "")) ? date('d M,Y', strtotime($item['followby']['last_date'] ?? "")) : 'pending'),
                'option' => $options,
                'closeTicket' => $closeTicket,
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData
        ]);
    }

    public function TourLeadDelete(Request $request, $id)
    {
        $lead = TourLeads::find($id);
        if ($lead) {
            $lead->delete();
            Toastr::success(translate('lead_Delete_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function TourLeadsFollow($id)
    {
        $followlist = TourFollowup::where('lead_id', $id)->get();
        if ($followlist) {
            return response()->json($followlist);
        } else {
            return response()->json([], 200);
        }
    }

    public function TourLeadCloseupdate(Request $request)
    {
        $lead = TourLeads::find($request['id']);
        if ($lead) {
            $lead->status = 3;
            $lead->save();
            Toastr::success(translate('lead_ticket_close_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function TourLeadsFollowUp(Request $request)
    {
        $follows = [
            'lead_id' => $request->input('lead_id'),
            'message' => $request->input('message'),
            'last_date' => $request->input('last_date'),
            'next_date' => $request->input('next_date'),
            'follow_by' => $request->input('follow_by'),
            'follow_by_id' => $request->input('follow_by_id'),
        ];
        TourFollowup::create($follows);
        Toastr::success(translate('lead_follow_up_successfully'));
        return response()->json(['success' => true], 200);;
    }

    public function CompanyBookingGet(Request $request)
    {
        $complete_order = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: '', relations: ['userData', 'company', 'Tour'], filters: ['amount_status' => 1, 'tour_id' => $request->id, 'refund_status' => 0], dataLimit: 'all');
        if (!empty($complete_order) && count($complete_order) > 0) {
            $array['order_list'] = $complete_order;
            $array['company'] = TourOrder::selectRaw('sum(qty) as qty, SUM(amount) as amount, cab_assign,tour_id')
                ->where(['amount_status' => 1, 'status' => 1, 'tour_id' => $request->id, 'refund_status' => 0])->where('cab_assign', '!=', '0')
                ->with(['company', 'Tour'])
                ->groupBy('cab_assign')
                ->get();
            $array['company_all'] = TourAndTravel::where('is_approve', 1)->where('status', 1)->get();
            return response()->json(['data' => $array, 'status' => 1], 200);
        } else {
            return response()->json(['data' => [], 'status' => 0], 200);
        }
    }

    function CompanyBookingSettlement(Request $request)
    {
        $tour_id = $request->tour_id ?? '';
        $type = $request->type ?? '';
        $getData = TourOrder::where('tour_id', $tour_id)->where('refund_status', 0)->get();
        if ($type == 1 && !empty($tour_id)) {
            if (!empty($getData) && count($getData) > 0) {
                foreach ($getData as $key => $value) {
                    User::where('id', $value['user_id'])->update(['wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance + ' .  $value['amount'])]);
                    TourOrder::where('id', $value['id'])->update(['status' => 2, 'refound_id' => "wallet", 'refund_status' => 1, 'refund_amount' => $value['amount'], 'refund_date' => date('Y-m-d H:i:s'), 'cab_assign' => 0]);
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $value['user_id'];
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Tour Refund';
                    $wallet_transaction->transaction_type = 'tour_refund';
                    $wallet_transaction->balance = User::where('id', $value['user_id'])->first()['wallet_balance'];
                    $wallet_transaction->credit =  $value['amount'];
                    $wallet_transaction->save();
                }
            }
        } elseif ($type == 2 && !empty($tour_id)) {
            if (isset($request->order_id) && !empty($request->order_id)) {
                foreach ($request->order_id as $order_id) {
                    TourOrder::where('id', $order_id)->where('cab_assign', $request->cab_id)->update(['status' => 1, 'cab_assign' => $request->transfor_cab]);
                }
            } else {
                $getData1 = TourOrder::where('tour_id', $tour_id)->where('cab_assign', $request->cab_id)->where('refund_status', 0)->get();
                if (!empty($getData1) && count($getData1) > 0) {
                    foreach ($getData1 as $key => $value) {
                        TourOrder::where('id', $value['id'])->where('cab_assign', $request->cab_id)->update(['status' => 1, 'cab_assign' => $request->transfor_cab]);
                    }
                }
            }
        } elseif ($type == 3 && !empty($tour_id)) {
            if (isset($request->order_id) && !empty($request->order_id)) {
                foreach ($request->order_id as $order_id) {
                    $orderData = TourOrder::where('id', $order_id)->first();
                    User::where('id', $orderData['user_id'])->update(['wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance + ' .  ($orderData['amount'] ?? 0))]);
                    TourOrder::where('id', $order_id)->update(['status' => 2, 'refound_id' => "wallet", 'refund_status' => 1, 'refund_amount' => $orderData['amount'], 'refund_date' => date('Y-m-d H:i:s'), 'cab_assign' => 0]);
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $orderData['user_id'];
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Tour Refund';
                    $wallet_transaction->transaction_type = 'tour_refund';
                    $wallet_transaction->balance = User::where('id', $orderData['user_id'])->first()['wallet_balance'];
                    $wallet_transaction->credit =  $orderData['amount'];
                    $wallet_transaction->save();
                }
            } else {
                $getData1 = TourOrder::where('tour_id', $tour_id)->where('cab_assign', $request->cab_id)->where('refund_status', 0)->get();
                if (!empty($getData1) && count($getData1) > 0) {
                    foreach ($getData1 as $key => $value) {
                        User::where('id', $value['user_id'])->update(['wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance + ' .  $value['amount'])]);
                        TourOrder::where('id', $value['id'])->update(['status' => 2, 'refound_id' => "wallet", 'refund_status' => 1, 'refund_amount' => $value['amount'], 'refund_date' => date('Y-m-d H:i:s'), 'cab_assign' => 0]);
                        $wallet_transaction = new WalletTransaction();
                        $wallet_transaction->user_id = $value['user_id'];
                        $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                        $wallet_transaction->reference = 'Tour Refund';
                        $wallet_transaction->transaction_type = 'tour_refund';
                        $wallet_transaction->balance = User::where('id', $value['user_id'])->first()['wallet_balance'];
                        $wallet_transaction->credit =  $value['amount'];
                        $wallet_transaction->save();
                    }
                }
            }
        }

        Toastr::success(translate('Changes_updated_successfully'));
        return back();
    }

    public function CommentStatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        TourReviews::where('id', $request['id'])->update($data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function CommissionUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tour_commission' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        TourVisits::where('id', $id)->update(['tour_commission' => $request->tour_commission]);
        Toastr::success(translate('Changes_updated_successfully'));
        return back();
    }

    public function VisitPlaceUpdate(Request $request)
    {
        $old_data = $this->tourvisitplac->getFirstWhere(params: ['id' => $request->id], relations: ['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view('admin-views.tour_and_travels.tour_visit.visit-update', compact('old_data', 'languages', 'defaultLanguage'));
    }

    public function VisitPlaceEdit(TourVisitPlaceRequest $request, TourVisitService $service)
    {
        $old_data = $this->tourvisitplac->getFirstWhere(params: ['id' => $request->id]);
        $dataArray = $service->getTourVisitPlaceupdate($request, $old_data);
        $this->tourvisitplac->update(id: $request->id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\TourVisitPlace', id: $request->id);
        Toastr::success(translate('Tour_Visit_place_updated_successfully'));
        return redirect()->route(TourVisitPath::VISIT[REDIRECT], [$request->tour_visit_id]);
    }

    public function VisitPlaceImageRemove(TourVisitService $service, $id, $name)
    {
        $getData  = $this->tourvisitplac->getFirstWhere(params: ['id' => $id]);
        if (empty($getData)) {
            return back();
        }
        $dataIMage = $service->VisitImageRemove($getData, $name);
        $this->tourvisitplac->update(id: $id, data: ['images' => json_encode($dataIMage)]);
        return back();
    }

    public function TourLeadMessages(Request $request, $id)
    {
        $leads = TourLeads::where('id', $id)->first();
        $request = new Request(['id' => $leads['order_id']]);
        $this->CustomerTourRemainingPay($request, true);
        $lead = TourLeads::where('id', $id)->with(['Tour'])->first();

        if ($lead) {
            $message_data = [
                'title_name' => ($lead['Tour']['tour_name'] ?? ''),
                'customer_id' => ($lead['user_id'] ?? ""),
                'final_amount' => $lead['amount'],
                'type' => 'text-with-media',
                'attachment' =>  getValidImage(path: 'storage\app\public\tour_and_travels\tour_visit' . $lead['Tour']['tour_image'], type: 'backend-product'),
                'link' => route('tour.tour-visit-id', ['id' => $lead['Tour']['slug']]),
                "payment_link" => $lead['paymant_link'] ?? "",
            ];
            Helpers::whatsappMessage('tour', 'tour_leads_message', $message_data);
            TourLeads::where('id', $id)->increment('whatsapp_hit');
            Toastr::success(translate('message_sent_successfully'));
            return response()->json(['success' => true], 200);
        } else {
            Toastr::error(translate('lead_Not_found'));
            return response()->json(['success' => false], 200);
        }
    }

    public function TourLeadCreateForm(Request $request)
    {
        $tourData = $this->tourtraveller->getListWhere(orderBy: ['id' => 'desc'], filters: ['status' => 1, 'use_date_status' => 1], dataLimit: "all");
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(TourVisitPath::CREATELEADADMIN[VIEW], compact('tourData', 'googleMapsApiKey'));
    }

    public function TourLeadEditForm(Request $request)
    {
        $tourData = $this->tourtraveller->getListWhere(orderBy: ['id' => 'desc'], filters: ['status' => 1, 'use_date_status' => 1], dataLimit: "all");
        $googleMapsApiKey = config('services.google_maps.api_key');
        $getData = TourLeads::with(['userData'])->where('id', $request['id'])->first();
        if ($getData) {
            return view(TourVisitPath::UPDATELEADADMIN[VIEW], compact('getData', 'tourData', 'googleMapsApiKey'));
        } else {
            Toastr::error(translate('lead_Not_found'));
            return back();
        }
    }

    public function TourGetFormDiv(Request $request)
    {
        $tourData = $this->tourtraveller->getFirstWhere(params: ['id' => $request['tour_id']], relations: ['TourPlane']);
        $html_cab_list_price = '';
        $packagesData = collect(json_decode($tourData['package_list_price'] ?? "[]", true));
        $packageIds = $packagesData->pluck('package_id')->toArray();
        $getTourLeads = TourLeads::where('id', $request['lead_id'])->first();
        if ($getTourLeads) {
            $bookingDatas = collect(json_decode($getTourLeads['booking_package'] ?? "[]", true));
        }

        $itinerary = '';
        if (isset($tourData['TourPlane']) && count($tourData['TourPlane']) > 0) {
            foreach ($tourData['TourPlane'] as $key => $va) {
                $itinerary .= '<div class="col-md-12 mt-2">
                                            <div class="card">
                                                <div class="card-body row">
                                                    <div class="col-md-2 small font-weight-bold">' . translate('days') . ' ' . ($key + 1) . ' &nbsp;&nbsp;<i class="tio-calendar_note" style="font-size: 19px;">calendar_note</i>
                                                    </div>
                                                    <div class="col-md-10 p-0">
                                                        <div style="border: 1px solid #b8d0e5;border-radius: 4px;" class="small">
                                                            <div class="font-weight-bold" style="background: linear-gradient(90deg, #c7dffe 0%, #d8f2ff 100%); padding: 6px 10px;">
                                                                ' . htmlspecialchars($va['name']) . ' , ' . htmlspecialchars($va['time']) . '
                                                            </div>
                                                            <div class="px-2">' . $va['description'] . '
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
            }
        }
        $packages = TourPackage::select('id', 'name', 'type', "seats", 'title', 'hotel_type')->whereIn('id', $packageIds)->get()->keyBy('id');
        $merged = $packagesData->map(function ($item) use ($packages, $tourData) {
            $package = $packages[$item['package_id']] ?? null;
            return [
                'id'        => $item['package_id'],
                'package_id' => $item['package_id'],
                'package_name' => $package?->name,
                'package_seats' => $package?->seats,
                'package_title' => $package?->title,
                'package_hotel_type' => (($package?->type == 'hotel') ? $package?->hotel_type : $package?->name),
                'day'       => $item['day'] ?? 1,
                'per_price' => $item['per_price'] ?? 0,
                'pprice'    => $item['pprice'] ?? 0,
                'included'  => $item['included'] ?? 0,
                'type'      => $package?->type,
                "tour_use_type" => ((($tourData['is_person_use'] ?? "") == 0) && (in_array($tourData['use_date'], [1, 2, 3, 4])) ? 1 : 0),
            ];
        });
        $grouped = $merged->groupBy('type');
        $htmlOutput = '';

        if (!empty($grouped)) {
            foreach ($grouped as $type => $items) {
                $htmlOutput .= $this->renderPackageHtml($items, $type);
            }
        }

        $htmldateTime = '
        <div class="col-md-3 mb-3">
            <label for="days" class="form-label">Days</label><br>
            <span class="font-weight-bolder">' . $tourData['number_of_day'] . 'D/' . $tourData['number_of_night'] . 'N </span>
        </div>

        <div class="col-md-3 mb-3">
            <label for="cities_name" class="form-label">Cities</label>
            <input type="text" id="cities_name_min" name="cities_name" class="form-control" value="' . $tourData['cities_name'] . '" ' . (($tourData['cities_name']) ? "readonly" : "") . '>
            <input type="hidden" id="country_name_min" name="country_name" class="form-control" value="' . $tourData['country_name'] . '">
            <input type="hidden" id="cities_lat_min" name="state_name" class="form-control" value="' . $tourData['lat'] . '">
            <input type="hidden" id="cities_long_min" name="state_name" class="form-control" value="' . $tourData['long'] . '">
        </div>
            <div class="col-md-3 mb-3">
                <label for="state_name" class="form-label">State Name</label>
                <input type="text" id="state_name_min" name="state_name" class="form-control" value="' . $tourData['state_name'] . '" ' . (($tourData['state_name']) ? "readonly" : "") . '>
            </div>

            <div class="col-md-3 mb-3">
                <label for="use_date" class="form-label">Use Date</label>
                <select id="use_date_tour" class="form-control" name="use_date" disabled>
                                    <option value="0" ' . ((old('use_date', $tourData['use_date']) == 0) ? 'selected' : '') . '>Cities Tour</option>
                                    <option value="1" ' . ((old('use_date', $tourData['use_date']) == 1) ? 'selected' : '') . '>Special Tour(With Date)</option>
                                    <option value="4" ' . ((old('use_date', $tourData['use_date']) == 4) ? 'selected' : '') . '>Special Tour(Without Date)</option>
                                    <option value="2" ' . ((old('use_date', $tourData['use_date']) == 2) ? 'selected' : '') . '>Daily Tour(With Address)</option>
                                    <option value="3" ' . ((old('use_date', $tourData['use_date']) == 3) ? 'selected' : '') . '>Daily Tour(WithOut Address)</option>
                                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label for="pickup_location" class="form-label">Pickup Location</label>
                <input type="text" id="pickup_location" name="pickup_location" autocomplete="off" class="form-control getAddress_google" value="' . $tourData['pickup_location'] . '" ' . (($tourData['pickup_location']) ? "readonly" : "") . ' onkeyup="getlocations()">
                <input type="hidden" name="pickup_lat" class="form-control pickup_lat" value="' . $tourData['pickup_lat'] . '">
                <input type="hidden" name="pickup_long" class="form-control pickup_long" value="' . $tourData['pickup_long'] . '">
                <input type="hidden" value="' . $tourData['is_person_use'] . '" class="is_person_use_tour">
                <input type="hidden" value="' . $tourData['id'] . '" class="tour_ids">
                <span class="address_error_message"></span>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="startandend_date" class="form-label">Start & End Date</label>';
                if($tourData['use_date'] == 1 && $tourData['customized_type'] == 1){
                    $htmldateTime .= '<input type="text" name="startandend_date" class="form-control hasDatepicker" autocomplete="off">';
                }else if($tourData['use_date'] == 1 && $tourData['customized_type'] == 2){
                    $htmldateTime .= '<input type="text" name="startandend_date" class="form-control hasDatepicker" autocomplete="off">';
                }else if($tourData['use_date'] == 1 && $tourData['customized_type'] == 3){
                    $htmldateTime .= '<input type="text" name="startandend_date" class="form-control hasDatepicker" autocomplete="off">';
                }else{
                    $htmldateTime .= '<input type="text" name="startandend_date" class="form-control hasDatepicker" autocomplete="off" value="' . (explode(' - ', $tourData['startandend_date'])[0] ?? '')   . '" ' . ((explode(' - ', $tourData['startandend_date'])[0] ?? '') ? "readonly" : '') . '>';
                }
        $htmldateTime .= ' </div>
            <div class="col-md-3 mb-3">';
        if ($tourData['time_slot'] && json_decode($tourData['time_slot'], true)) {
            $htmldateTime .=  '<label for="startandend_date" class="form-label">' . translate('Time Slot') . '</label>
                                                                            <select name="time" class="form-control" onchange="$(`.pickup_time`).val($(this).val())">
                                                                                <option value="" selected disabled>Select Time Slot</option>';
            foreach (json_decode($tourData['time_slot'], true) as $vva) {
                $htmldateTime .=  '<option value="' . $vva . '">' . $vva . '</option>';
            }
            $htmldateTime .=  ' </select>';
        } else {
            $htmldateTime .=  ' <label for="startandend_date" class="form-label">' . translate('Arrival Time') . '</label>
                                        <input type="text" name="time" class="form-control pickupopen_time" value="' . ($tourData['pickup_time'] ?? '') . '" id="opentime" onkeyup="$(`.pickup_time`).val(this.value)" onchange="$(`.pickup_time`).val(this.value)" onclick="window.$timepicker.open()" autocomplete="off">';
        }
        $htmldateTime .= '
            </div>
        ';
        if (($tourData['is_person_use'] ?? "") == 1) {
            $htmldateTime .= ' <div class="col-md-3 mb-3">
                            <span class="font-weight-bold"><input type="checkbox" class="only-pickup extracharges-transport" data-id="only-pickup" data-type="Pickup" data-type1="pick" onclick="transportOption(this)">&nbsp;Only Pickup</span><br>
                                                                    <span class="font-weight-bold"><input type="checkbox" class="only-droup extracharges-transport" data-id="only-droup" data-type="Drop" data-type1="drop" onclick="transportOption(this)">&nbsp;Only Droup</span><br>
                                                                    <span class="font-weight-bold"><input type="checkbox" class="only-both extracharges-transport" data-id="only-both" data-type="Both" data-type1="both" onclick="transportOption(this)">&nbsp;Both</span><br>
                                                                    <span class="extransportPrice font-wight-bolder text-primary font-size-13"></span></div><br>';
        } else if (($tourData['is_person_use'] ?? "") == 0 && ($tourData['use_date'] == 2 || $tourData['use_date'] == 3)) {
            $htmldateTime .= '<div class="col-md-3 mb-3">
                            <span class="font-weight-bold"><input type="checkbox" class="only-pickup extracharges-transport out_side_div" data-type="one_way" value="one_way" onclick="calculateDistance()" data-ex_distance="' . ($tourData['ex_distance'] ?? 0) . '">&nbsp;One Way</span><br>
                                                                    <span class="font-weight-bold"><input type="checkbox" class="only-droup extracharges-transport out_side_div" value="two_way" data-type="two_way" checked onclick="calculateDistance()" data-ex_distance="' . ($tourData['ex_distance'] ?? 0) . '">&nbsp;Two Way</span><br>
                                                       
                                                       <input type="radio" name="oneusedistance" class=" d-none" value="two_way">';
        }


        if (($tourData['is_person_use'] ?? "") == 1) {
            if ($tourData['cab_list_price'] && json_decode($tourData['cab_list_price'] ?? "[]", true)) {
                foreach (json_decode($tourData['cab_list_price'] ?? "[]", true) as $kper => $persons) {
                    $html_cab_list_price .= '<div class="row my-2">
                                                            <div class="col-4">
                                                                <div class="font-weight-bold">
                                                                    <span>Group of ' . $persons['min'] . '  ' . (($persons['min'] == $persons['max']) ? '' : ' - ' . $persons['max']) . ' (Per Person) </span><br>
                                                                    <a class="personMessageShow personMessageShow' . $kper . ' text-primary small d-sm-block d-none"></a>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="font-weight-bold">
                                                                    <span>' . webCurrencyConverter(amount: $persons['price'] ?? 0) . '</span><br>
                                                                    <span class="total_cab_and_perhead_price total_cab_and_perhead_price' . $kper . '">0</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-center">
                                                               <div>
                                                                    <div class="small" style="display: inline-flex;">
                                                                    <input type="number" 
                                                                            class="form-control per_head_' . $persons['id'] . ' text-center cab_qty_input cab_qty_input' . $kper . '" 
                                                                            value="0" 
                                                                            min="' . $persons['min'] . '" 
                                                                            max="' . $persons['max'] . '" 
                                                                            data-price="' . $persons['price'] . '"
                                                                            data-id="' . $persons['id'] . '"
                                                                        oninput="this.value = this.value.replace(/[^0-9]/g,\'\');updateCabTotal(' . $kper . ', this);" onchange="this.value = this.value.replace(/[^0-9]/g,\'\');updateCabTotal(' . $kper . ', this);">
                                                                   </div>
                                                                    <span class="OnepersonMessageShow OnepersonMessageShow' . $kper . ' text-danger small"></span>
                                                                </div>
                                                            </div>
                                                        </div>';
                }
            }
            $InfoVendor = [
                "html_cab_list_price" => $html_cab_list_price,
                "package_html_show" => $htmlOutput,
                "htmldateTime" => $htmldateTime,
                "is_person_use" => $tourData['is_person_use'],
                "days" => $tourData['number_of_day'] . "D" . "/" . $tourData['number_of_night'] . "N",
                "cities_name" => $tourData['cities_name'],
                "country_name" => $tourData['country_name'],
                "state_name" => $tourData['state_name'],
                "use_date" => $tourData['use_date'],
                "pickup_time" => $tourData['pickup_time'],
                "pickup_location" => $tourData['pickup_location'],
                "pickup_lat" => $tourData['pickup_lat'],
                "pickup_long" => $tourData['pickup_long'],
                "percentage_off" => $tourData['percentage_off'],
                "startandend_date" => $tourData['startandend_date'],
                "ex_transport_price" => json_decode($tourData['ex_transport_price'], true),
                "exclusion" => $tourData['exclusion'] ?? "",
                "inclusion" => $tourData['inclusion'] ?? "",
                "itinerary" => $itinerary ?? "",
            ];
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully'), "info" => $InfoVendor, 'data' => $tourData], 200);
        } elseif (($tourData['is_person_use'] ?? "") == 0) {
            $packages_price = 0;
            if (!empty($tourData['package_list_price']) && is_array(json_decode($tourData['package_list_price'], true)) && (in_array($tourData['use_date'], [1, 2, 3, 4]))) {
                foreach (json_decode($tourData['package_list_price'], true) as $plis) {
                    $packages_price += $plis['pprice'];
                }
            }
            if ($tourData['cab_list_price'] && json_decode($tourData['cab_list_price'] ?? "[]", true)) {
                foreach (json_decode($tourData['cab_list_price'] ?? "[]", true) as $kper => $cab) {

                    $price = (($cab['price'] ?? 0));
                    $cabId = $cab['cab_id'] ?? '';
                    $id    = $cab['cab_id'] ?? ''; //$cab['id'] ?? '';
                    $getCabInfo = TourCab::where('id', $cab['cab_id'] ?? '')->first();

                    $html_cab_list_price .= '<div class="row my-2">
                                    <div class="col-4">
                                        <div class="font-weight-bold">
                                            <span>' . $getCabInfo['name'] . ' (seats:' . $getCabInfo['seats'] . ')</span><br>
                                            <a class="personMessageShow personMessageShow' . $kper . ' text-primary small d-sm-block d-none"></a>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="font-weight-bold">
                                            <span>' . webCurrencyConverter(amount: ($price + $packages_price)) . '</span><br>
                                            <span class="total_cab_and_perhead_price total_cab_and_perhead_price' . $kper . '">0</span>
                                        </div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div>
                                            <div class="small" style="display: inline-flex;">
                                                <input type="number" 
                                                    class="form-control text-center cab_id_' . $cabId . ' cab_qty_input cab_qty_input' . $kper . '" 
                                                    value="0" 
                                                    min="1" 
                                                    max="' . (($tourData['use_date'] == 1 || $tourData['use_date'] == 4 || $tourData['use_date'] == 2 || $tourData['use_date'] == 3) ? $getCabInfo['seats'] : 99) . '"
                                                    data-id="' . $id . '" 
                                                    data-id="' . $cabId . '"
                                                    data-price="' . $price . '"
                                                    data-packageincl="' . $packages_price . '"
                                                    data-seats="' . $getCabInfo['seats'] . '"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g,\'\');updateCab_Total(' . $kper . ', this);" 
                                                    onchange="this.value = this.value.replace(/[^0-9]/g,\'\');updateCab_Total(' . $kper . ', this);">
                                            </div>
                                            <span class="OnepersonMessageShow OnepersonMessageShow' . $kper . ' text-danger small"></span>
                                        </div>
                                    </div>
                                </div>';
                }
            }

            $InfoVendor = [
                "html_cab_list_price" => $html_cab_list_price,
                "package_html_show" => $htmlOutput,
                "htmldateTime" => $htmldateTime,
                "is_person_use" => $tourData['is_person_use'],
                "days" => $tourData['number_of_day'] . "D" . "/" . $tourData['number_of_night'] . "N",
                "cities_name" => $tourData['cities_name'],
                "country_name" => $tourData['country_name'],
                "state_name" => $tourData['state_name'],
                "use_date" => $tourData['use_date'],
                "pickup_time" => $tourData['pickup_time'],
                "pickup_location" => $tourData['pickup_location'],
                "pickup_lat" => $tourData['pickup_lat'],
                "pickup_long" => $tourData['pickup_long'],
                "percentage_off" => $tourData['percentage_off'],
                "startandend_date" => $tourData['startandend_date'],
                "ex_transport_price" => json_decode($tourData['ex_transport_price'] ?? [], true),
                "exclusion" => $tourData['exclusion'] ?? "",
                "inclusion" => $tourData['inclusion'] ?? "",
                "itinerary" => $itinerary ?? "",
            ];
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully'), "info" => $InfoVendor, 'data' => $tourData], 200);
        } else {
            $InfoVendor = [
                "html_cab_list_price" => $html_cab_list_price,
                "package_html_show" => $htmlOutput,
                "htmldateTime" => $htmldateTime,
                "is_person_use" => $tourData['is_person_use'],
                "days" => $tourData['number_of_day'] . "D" . "/" . $tourData['number_of_night'] . "N",
                "cities_name" => $tourData['cities_name'],
                "country_name" => $tourData['country_name'],
                "state_name" => $tourData['state_name'],
                "use_date" => $tourData['use_date'],
                "pickup_time" => $tourData['pickup_time'],
                "pickup_location" => $tourData['pickup_location'],
                "pickup_lat" => $tourData['pickup_lat'],
                "pickup_long" => $tourData['pickup_long'],
                "percentage_off" => $tourData['percentage_off'],
                "startandend_date" => $tourData['startandend_date'],
                "ex_transport_price" => json_decode($tourData['ex_transport_price'] ?? [], true),
                "exclusion" => $tourData['exclusion'] ?? "",
                "inclusion" => $tourData['inclusion'] ?? "",
                "itinerary" => $itinerary ?? "",
            ];
            return response()->json(['success' => 0, 'message' => translate('status_updated_successfully'), "info" => $InfoVendor, 'data' => $tourData], 200);
        }
    }

    function renderPackageHtml($items, $type)
    {
        $html = "<div class='col-md-12'><h5 class='mt-3'>" . ucfirst($type) . " Packages</h5>";

        foreach ($items as $kper => $value) {
            $html .= '<div class="row my-2">
                    <div class="col-4">
                        <div class="font-weight-bold">
                            <span>' . ($value['package_hotel_type'] ?? ucfirst($type)) . ' - ' . ($value['package_name']) . ' (' . ($value['package_title']) . ' , Person: ' . ($value['package_seats']) . ')</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="small font-weight-bold">
                            <span>' . webCurrencyConverter(amount: $value['pprice'] ?? 0) . '</span><br>
                            <span class="person_total_amounts_' . ($value['type'] ?? "") . '' . $kper . ' person_total_amounts_' . ($value['type'] ?? "") . '"></span>
                        </div>
                    </div>
                    <div class="col-4 text-center">                       
                        <div>
                            <div class="font-weight-bolder" style="display: inline-flex;">';
            if (($value['included'] ?? 0) == 1) {
                $html .= 'included';
            } elseif ($value['tour_use_type'] == 1) {
                $html .= 'included';
                $html .= '<input type="hidden" class="form-control text-center package_per_head_max person_per_input_' . ($value['type'] ?? "") . '' . $kper . ' person_per_input_' . ($value['type'] ?? "") . '"  value="0" min="0" data-type="' . ($value['type'] ?? "") . '" data-price="' . ($value['pprice'] ?? "") . '" data-hotel_type="' . ($value['package_hotel_type'] ?? "") . '" data-id="' . ($value['id'] ?? "") . '" data-seats="' . ($value['package_seats'] ?? "") . '" oninput="this.value = this.value.replace(/[^0-9]/g,\'\');updatepackageTotal(' . $kper . ', this)"  onchange="this.value = this.value.replace(/[^0-9]/g,\'\');updatepackageTotal(' . $kper . ', this)" ' . (($value['tour_use_type'] == 1) ? "readonly" : " ") . '>';
            } else {
                $html .= '<input type="number" class="form-control text-center other_packages_' . ($value['id'] ?? "") . ' package_per_head_max person_per_input_' . ($value['type'] ?? "") . '' . $kper . ' person_per_input_' . ($value['type'] ?? "") . '"  value="0" min="0" data-type="' . ($value['type'] ?? "") . '" data-price="' . ($value['pprice'] ?? "") . '" data-hotel_type="' . ($value['package_hotel_type'] ?? "") . '" data-id="' . ($value['id'] ?? "") . '" data-seats="' . ($value['package_seats'] ?? "") . '" oninput="this.value = this.value.replace(/[^0-9]/g,\'\');updatepackageTotal(' . $kper . ', this)"  onchange="this.value = this.value.replace(/[^0-9]/g,\'\');updatepackageTotal(' . $kper . ', this)">';
            }
            $html .= '</div>
                            <span class="OnepersonMessageShow OnepersonMessageShow' . $kper . ' text-danger small"></span>
                        </div>
                    </div>
                </div>';
        }
        $html .= "</div>";

        return $html;
    }

    public function TourLeadSave(Request $request)
    {
        $request->validate([
            'platform'        => 'required',
            'person_phone'    => 'required|regex:/^\+?[0-9]{10,15}$/',
            'user_name'       => 'required|string|max:100',
            'tour_id'         => 'required|integer|exists:tour_visits,id',
            'cities_name'     => 'required|string|max:100',
            'country_name'    => 'required|string|max:100',
            'state_name'      => 'required|string|max:100',
            'pickup_location' => 'required|string|max:255',
            'pickup_lat'  => 'required|numeric|gte:-90|lte:90',
            'pickup_long' => 'required|numeric|gte:-180|lte:180',
            'startandend_date' => 'required|date|after_or_equal:today',
            'time'            => 'required|date_format:h:i A',
            'booking_package' => 'required|json',
            'amount'          => 'required|numeric|min:0',
        ]);
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
            $user->verify_otp = 0;
            $user->save();
            $user_id = $user->id ?? "";
            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        DB::beginTransaction();
        try {
            $leads = new TourLeads();
            $leads->tour_id = $request->tour_id ?? 0;
            $leads->package_id = 0;
            $leads->user_id = $user_id;

            $leads->amount = $request->amount;
            $leads->platform = $request->platform ?? "";
            $leads->coupon_id = $request->coupon_id;
            $leads->coupan_amount = $request->coupon_amount;
            $leads->booking_package = $request['booking_package'] ?? "[]";
            $leads->part_payment = ((!empty($request['part_payment'])) ? $request['part_payment'] : 'full');
            $leads->pickup_address = $request->pickup_location;
            $leads->pickup_date = $request->startandend_date;
            $leads->pickup_time = $request->time;
            $leads->pickup_long = $request->pickup_long;
            $leads->pickup_lat = $request->pickup_lat;
            $packages = json_decode($request->booking_package ?? '[]', true);
            $cabPackage = collect($packages)->firstWhere('type', 'cab');
            if (!$cabPackage) {
                $cabPackage = collect($packages)->firstWhere('type', 'per_head');
            }
            $userQty = ($cabPackage ? (int) $cabPackage['qty'] : 0);
            $leads->qty = ($userQty ?? 0);
            $leads->via_online = $request->amount;
            $leads->amount_status = 0;
            $leads->status = 1;
            $leads->save();
            $tourData = TourVisits::where('id',  $request->tour_id)->first();
            $tourLeads = TourLeads::where('id',  $leads->id)->first();
            if ($tourData['use_date'] == 1 && $tourData['is_person_use'] == 0) {
                $getseats = \App\Models\TourOrder::where('tour_id', $request->tour_id)->where('amount_status', 1)->where('status', 1)->where('available_seat_cab_id', ($userQty ?? 0))->sum('qty');
                if (($userQty - $getseats) < $tourLeads['qty']) {
                    DB::rollBack();
                    Toastr::error('Currently ' . ($userQty - $getseats) . ' seats are available');
                    return redirect()->route('admin.tour_visits.leads');
                }
            }
            $coupon_amount = 0;
            // $final_amount = $tourLeads['amount'] ?? 0;
            $final_amount = (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0)));
            $final_amount_admin_commission =  ($tourLeads['amount'] ?? 0);
            $gst_amount = $admin_commission = 0;
            $tourstax = \App\Models\ServiceTax::find(1);
            if ($tourstax['tour_tax']) {
                $booking_package1 = json_decode($leads['booking_package'] ?? "[]", true);
                $gst_amount = collect($booking_package1)->sum('tax_price');
                $final_amount = $final_amount - $gst_amount;
            }
            if ($tourData['tour_commission']) {
                // $admin_commission = (($final_amount * $tourData['tour_commission']) / 100);
                $admin_commission = ((($final_amount_admin_commission - $gst_amount) * $tourData['tour_commission']) / 100);
                $final_amount = ($final_amount - $admin_commission);
            }
            if ($final_amount < 0) {
                DB::rollBack();
                Toastr::error('Please Enter Valid amount');
                return redirect()->route('admin.tour_visits.leads');
            }
            $user = User::where("id", $tourLeads['user_id'])->first();
            $event_booking = new TourOrder();
            $event_booking->user_id = $user['id'];
            $event_booking->tour_id = $request->tour_id;
            $event_booking->package_id = $tourLeads['package_id'];
            $event_booking->coupon_amount = $tourLeads['coupan_amount'] ?? 0;
            $event_booking->coupon_id = $tourLeads['coupon_id'] ?? '';
            $event_booking->order_amount = ($tourLeads['amount'] ?? 0);
            $event_booking->amount = (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0)));
            $event_booking->qty = $tourLeads['qty'];

            $packages = json_decode($request->booking_package ?? '[]', true);
            $cabPackage = collect($packages)->firstWhere('type', 'cab');

            $event_booking->available_seat_cab_id = $cabPackage['id'] ?? 0;
            $event_booking->total_seats_cab = $userQty ?? 0;
            $event_booking->pickup_address = $tourLeads['pickup_address'];
            $event_booking->pickup_date = $tourLeads['pickup_date'];
            $event_booking->pickup_time = $tourLeads['pickup_time'];
            $event_booking->pickup_lat = $tourLeads['pickup_lat'];
            $event_booking->pickup_long = $tourLeads['pickup_long'];
            $event_booking->gst_amount = $gst_amount;
            $event_booking->admin_commission = $admin_commission;
            $event_booking->final_amount = $final_amount;
            $event_booking->payment_method = 'razor_pay';
            $event_booking->payment_platform = 'web';
            $event_booking->leads_id = $tourLeads['id'];
            $event_booking->use_date = $tourData['use_date'];
            $event_booking->part_payment = (($tourLeads['part_payment'] == 'part' || $tourLeads['part_payment'] == 'custom') ? $tourLeads['part_payment'] : 'full');

            $event_booking->traveller_id = ($tourData['created_id'] ?? 0);
            $event_booking->cab_assign = 0;
            $event_booking->booking_package = $tourLeads['booking_package'];

            $event_booking->pickup_otp = mt_rand(1000, 9999);
            $event_booking->drop_opt = mt_rand(1000, 9999);
            $event_booking->amount_status = 0;
            $event_booking->status = 1;
            // dd($event_booking);
            $event_booking->save();
            \App\Models\TourLeads::where('id', $tourLeads['id'])->update(['order_id' => $event_booking->id]);
            $additional_data = [
                'business_name' => \App\Models\BusinessSetting::where(['type' => 'company_name'])->first()->value,
                'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                'payment_mode' => 'web',
                'leads_id' => $tourLeads['id'],
                'package_id' => $tourLeads['package_id'],
                'customer_id' => $tourLeads['user_id'],
                "order_id" => $event_booking->id,
                "tour_id" => $request->tour_id,
                "amount" => $tourLeads['amount'],
                "user_name" => $user['name'],
                "user_email" => $user['email'],
                "user_phone" => $user['phone'],
            ];
            $currency_model = Helpers::get_business_settings('currency_model');
            if ($currency_model == 'multi_currency') {
                $currency_code = 'USD';
            } else {
                $default = \App\Models\BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                $currency_code = \App\Models\Currency::find($default)->code;
            }
            $customer = User::where("id", $tourLeads['user_id'])->first();
            $payer = new \App\Library\Payer(
                $customer['f_name'] . ' ' . $customer['l_name'],
                $customer['email'],
                $customer['phone'],
                ''
            );
            if (empty($customer['phone'])) {
                DB::rollBack();
                Toastr::error(translate('please_update_your_phone_number'));
                return redirect()->route('admin.tour_visits.tour-admin-lead-create');
            }

            $payment_info = new \App\Library\Payment(
                success_hook: 'digital_payment_success_custom',
                failure_hook: 'digital_payment_fail',
                currency_code: $currency_code,
                payment_method: 'razor_pay',
                payment_platform: 'web',
                payer_id: $customer['id'],
                receiver_id: '100',
                additional_data: $additional_data,
                payment_amount: (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0))),
                external_redirect_link: route('tour.tour-pay-success', [$tourData['slug'], 'lead' => ($tourLeads['id'] ?? '')]),
                attribute: 'tour_order',
                attribute_id: idate("U")
            );
            DB::commit();
            $receiver_info = new \App\Library\Receiver('receiver_name', 'example.png');
            $redirect_link = \App\Traits\Payment::generate_link($payer, $payment_info, $receiver_info);
            $parsed_url = parse_url($redirect_link);
            $query_string = $parsed_url['query'];
            parse_str($query_string, $query_params);

            $tourOrder = TourOrder::with(['Tour'])->where('id', $event_booking->id)->first();
            $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
            $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
            $message_data['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
            $message_data['time'] = ($tourOrder['pickup_time'] ?? '');
            $message_data['place_name'] = ($tourOrder['pickup_address'] ?? '');
            $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
            $message_data['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
            $message_data['payment_link'] = $redirect_link;
            $leadUpdate = TourLeads::find($tourLeads['id']);
            $leadUpdate->paymant_link = $redirect_link;
            $leadUpdate->save();
            $message_data['customer_id'] = $user['id'];
            if ($tourOrder['Tour']['tour_image']) {
                $message_data['type'] = 'text-with-media';
                $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['tour_image'] ?? '');
            }
            $remain_amount = (($tourLeads['part_payment'] == 'custom') ? (($tourOrder['order_amount'] ?? 0) - ($request['custom_amount_payment'] ?? 0)) : (($tourOrder['part_payment'] == 'part') ?  ($tourOrder['amount'] ?? 0) : (0)));
            $message_data['remain_amount'] = webCurrencyConverter(amount: (float)$remain_amount ?? 0);
            if (($request['whatsapp_msg'] ?? 0) == 1) {
                Helpers::whatsappMessage('tour', 'Tour booking payment link', $message_data);
            }
            if (($request['itinerary_pdf_send'] ?? 0) == 1) {
                $message_data2['orderId'] = ($tourOrder['order_id'] ?? '');
                $message_data2['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
                $message_data2['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
                $message_data2['time'] = ($tourOrder['pickup_time'] ?? '');
                $message_data2['place_name'] = ($tourOrder['pickup_address'] ?? '');
                $message_data2['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
                $message_data2['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
                $message_data2['customer_id'] = $user['id'];
                $message_data2['link'] =  route('tour.tourvisit', ['id' => ($tourOrder['Tour']['slug'] ?? "")]);
                if ($tourOrder['Tour']['itineraryupload']) {
                    $message_data2['type'] = 'text-with-media';
                    $message_data2['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['itineraryupload'] ?? '');
                    Helpers::whatsappMessage('tour', 'share itinerary pdf', $message_data2);
                }
            }
            Toastr::success('Booking Success');
            return redirect()->route('admin.tour_visits.leads');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('An error occurred: ' . $e->getMessage());
            return redirect()->route('admin.tour_visits.tour-admin-lead-create');
        }
    }

    public function TourLeadUpdateForm(Request $request)
    {
        $request->validate([
            'platform'        => 'required',
            'person_phone'    => 'required|regex:/^\+?[0-9]{10,15}$/',
            'user_name'       => 'required|string|max:100',
            'tour_id'         => 'required|integer|exists:tour_visits,id',
            'cities_name'     => 'required|string|max:100',
            'country_name'    => 'required|string|max:100',
            'state_name'      => 'required|string|max:100',
            'pickup_location' => 'required|string|max:255',
            'pickup_lat'  => 'required|numeric|gte:-90|lte:90',
            'pickup_long' => 'required|numeric|gte:-180|lte:180',
            'startandend_date' => 'required|date|after_or_equal:today',
            'time'            => 'required|date_format:h:i A',
            'booking_package' => 'required|json',
            'amount'          => 'required|numeric|min:0',
        ]);
        if (!$request['id']) {
            return back();
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
            $user->verify_otp = 0;
            $user->save();
            $user_id = $user->id ?? "";
            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        DB::beginTransaction();
        try {
            $leads = TourLeads::find($request['id']);
            $leads->tour_id = $request->tour_id ?? 0;
            $leads->package_id = 0;
            $leads->user_id = $user_id;
            $leads->amount = $request->amount;
            $leads->platform = $request->platform ?? "";
            $leads->coupon_id = $request['coupon_id'] ?? 0;
            $leads->coupan_amount = $request['coupon_amount'] ?? 0;
            $leads->booking_package = $request['booking_package'] ?? "[]";
            $leads->part_payment = ((!empty($request['part_payment'])) ? $request['part_payment'] : 'full');
            $leads->pickup_address = $request->pickup_location;
            $leads->pickup_date = $request->startandend_date;
            $leads->pickup_time = $request->time;
            $leads->pickup_long = $request->pickup_long;
            $leads->pickup_lat = $request->pickup_lat;
            $packages = json_decode($request->booking_package ?? '[]', true);
            $cabPackage = collect($packages)->firstWhere('type', 'cab');
            if (!$cabPackage) {
                $cabPackage = collect($packages)->firstWhere('type', 'per_head');
            }
            $userQty = ($cabPackage ? (int) $cabPackage['qty'] : 0);
            $leads->qty = ($userQty ?? 0);
            $leads->via_online = $request->amount;
            $leads->amount_status = 0;
            $leads->status = 1;
            $leads->save();
            $tourData = TourVisits::where('id',  $request->tour_id)->first();
            $tourLeads = TourLeads::where('id',  $leads->id)->first();
            if ($tourData['use_date'] == 1 && $tourData['is_person_use'] == 0) {
                $getseats = \App\Models\TourOrder::where('tour_id', $request->tour_id)->where('amount_status', 1)->where('status', 1)->where('available_seat_cab_id', ($userQty ?? 0))->sum('qty');
                if (($userQty - $getseats) < $tourLeads['qty']) {
                    DB::rollBack();
                    Toastr::error('Currently ' . ($userQty - $getseats) . ' seats are available');
                    return redirect()->route('admin.tour_visits.leads');
                }
            }
            $coupon_amount = 0;
            $final_amount = (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0)));
            $final_amount_admin_commission = ($tourLeads['amount'] ?? 0);
            $gst_amount = $admin_commission = 0;
            $tourstax = \App\Models\ServiceTax::find(1);
            if ($tourstax['tour_tax']) {
                // $booking_package1 = json_decode($leads['booking_package'] ?? "[]", true);
                // $gst_amount = collect($booking_package1)->sum('tax_price');
                $gst_amount = (($final_amount * $tourstax['tour_tax']) / 100);
                $final_amount = $final_amount - $gst_amount;
            }
            if ($tourData['tour_commission']) {
                // $admin_commission = (($final_amount * $tourData['tour_commission']) / 100);
                $admin_commission = ((($final_amount_admin_commission - $gst_amount) * $tourData['tour_commission']) / 100);
                $final_amount = ($final_amount - $admin_commission);
            }

            if ($final_amount < 0) {
                DB::rollBack();
                Toastr::error('Please Enter Valid amount');
                return redirect()->route('admin.tour_visits.leads');
            }
            $user = User::where("id", $tourLeads['user_id'])->first();
            if (!empty($leads->order_id ?? "")) {
                $event_booking = TourOrder::find($leads->order_id);
            } else {
                $event_booking = new TourOrder();
            }
            $event_booking->user_id = $user['id'];
            $event_booking->tour_id = $request->tour_id;
            $event_booking->package_id = $tourLeads['package_id'];
            $event_booking->coupon_amount = $tourLeads['coupan_amount'] ?? 0;
            $event_booking->coupon_id = $tourLeads['coupon_id'] ?? '';
            $event_booking->order_amount = $tourLeads['amount'] ?? 0;
            $event_booking->amount = (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0)));
            $event_booking->qty = $tourLeads['qty'];

            $packages = json_decode($request->booking_package ?? '[]', true);
            $cabPackage = collect($packages)->firstWhere('type', 'cab');

            $event_booking->available_seat_cab_id = $cabPackage['id'] ?? 0;
            $event_booking->total_seats_cab = $userQty ?? 0;
            $event_booking->pickup_address = $tourLeads['pickup_address'];
            $event_booking->pickup_date = $tourLeads['pickup_date'];
            $event_booking->pickup_time = $tourLeads['pickup_time'];
            $event_booking->pickup_lat = $tourLeads['pickup_lat'];
            $event_booking->pickup_long = $tourLeads['pickup_long'];
            $event_booking->gst_amount = $gst_amount;
            $event_booking->admin_commission = $admin_commission;
            $event_booking->final_amount = $final_amount;
            $event_booking->payment_method = 'razor_pay';
            $event_booking->payment_platform = 'web';
            $event_booking->leads_id = $tourLeads['id'];
            $event_booking->use_date = $tourData['use_date'];
            $event_booking->part_payment = (($tourLeads['part_payment'] == 'part' || $tourLeads['part_payment'] == 'custom') ? $tourLeads['part_payment'] : 'full');

            $event_booking->traveller_id = ($tourData['created_id'] ?? 0);
            $event_booking->cab_assign = 0;
            $event_booking->booking_package = $tourLeads['booking_package'];

            $event_booking->pickup_otp = mt_rand(1000, 9999);
            $event_booking->drop_opt = mt_rand(1000, 9999);
            $event_booking->amount_status = 0;
            $event_booking->status = 1;
            // dd($event_booking);
            $event_booking->save();
            \App\Models\TourLeads::where('id', $tourLeads['id'])->update(['order_id' => $event_booking->id]);
            $additional_data = [
                'business_name' => \App\Models\BusinessSetting::where(['type' => 'company_name'])->first()->value,
                'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                'payment_mode' => 'web',
                'leads_id' => $tourLeads['id'],
                'package_id' => $tourLeads['package_id'],
                'customer_id' => $tourLeads['user_id'],
                "order_id" => $event_booking->id,
                "tour_id" => $request->tour_id,
                "amount" => $tourLeads['amount'],
                "user_name" => $user['name'],
                "user_email" => $user['email'],
                "user_phone" => $user['phone'],
            ];
            $currency_model = Helpers::get_business_settings('currency_model');
            if ($currency_model == 'multi_currency') {
                $currency_code = 'USD';
            } else {
                $default = \App\Models\BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                $currency_code = \App\Models\Currency::find($default)->code;
            }
            $customer = User::where("id", $tourLeads['user_id'])->first();
            $payer = new \App\Library\Payer(
                $customer['f_name'] . ' ' . $customer['l_name'],
                $customer['email'],
                $customer['phone'],
                ''
            );
            if (empty($customer['phone'])) {
                DB::rollBack();
                Toastr::error(translate('please_update_your_phone_number'));
                return redirect()->route('admin.tour_visits.leads');
            }

            $payment_info = new \App\Library\Payment(
                success_hook: 'digital_payment_success_custom',
                failure_hook: 'digital_payment_fail',
                currency_code: $currency_code,
                payment_method: 'razor_pay',
                payment_platform: 'web',
                payer_id: $customer['id'],
                receiver_id: '100',
                additional_data: $additional_data,
                payment_amount: (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0))),
                external_redirect_link: route('tour.tour-pay-success', [$tourData['slug'], 'lead' => ($tourLeads['id'] ?? '')]),
                attribute: 'tour_order',
                attribute_id: idate("U")
            );
            DB::commit();
            $receiver_info = new \App\Library\Receiver('receiver_name', 'example.png');
            $redirect_link = \App\Traits\Payment::generate_link($payer, $payment_info, $receiver_info);
            $parsed_url = parse_url($redirect_link);
            $query_string = $parsed_url['query'];
            parse_str($query_string, $query_params);

            $tourOrder = TourOrder::with(['Tour'])->where('id', $event_booking->id)->first();
            $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
            $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
            $message_data['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
            $message_data['time'] = ($tourOrder['pickup_time'] ?? '');
            $message_data['place_name'] = ($tourOrder['pickup_address'] ?? '');
            $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
            $message_data['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
            $message_data['payment_link'] = $redirect_link;
            $leads->paymant_link = $redirect_link;
            $leads->save();
            $message_data['customer_id'] = $user['id'];
            if ($tourOrder['Tour']['tour_image']) {
                $message_data['type'] = 'text-with-media';
                $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['tour_image'] ?? '');
            }
            $remain_amount = (($tourLeads['part_payment'] == 'custom') ? (($tourOrder['order_amount'] ?? 0) - ($request['custom_amount_payment'] ?? 0)) : (($tourOrder['part_payment'] == 'part') ?  ($tourOrder['amount'] ?? 0) : (0)));
            $message_data['remain_amount'] = webCurrencyConverter(amount: (float)$remain_amount ?? 0);
            if (($request['whatsapp_msg'] ?? 0) == 1) {
                Helpers::whatsappMessage('tour', 'Tour booking payment link', $message_data);
            }
            if (($request['itinerary_pdf_send'] ?? 0) == 1) {
                $message_data2['orderId'] = ($tourOrder['order_id'] ?? '');
                $message_data2['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
                $message_data2['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
                $message_data2['time'] = ($tourOrder['pickup_time'] ?? '');
                $message_data2['place_name'] = ($tourOrder['pickup_address'] ?? '');
                $message_data2['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
                $message_data2['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
                $message_data2['customer_id'] = $user['id'];
                $message_data2['link'] =  route('tour.tourvisit', ['id' => ($tourOrder['Tour']['slug'] ?? "")]);
                if ($tourOrder['Tour']['itineraryupload']) {
                    $message_data2['type'] = 'text-with-media';
                    $message_data2['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['itineraryupload'] ?? '');
                    Helpers::whatsappMessage('tour', 'share itinerary pdf', $message_data2);
                }
            }
            Toastr::success('Booking Success');
            return redirect()->route('admin.tour_visits.leads');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('An error occurred: ' . $e->getMessage());
            return redirect()->route('admin.tour_visits.leads');
        }
    }

    static public function CustomerTourRemainingPay(Request $request, $internal = false)
    {
        $tourOrder = TourOrder::with(['userData', 'Tour'])->where('id', ($request->id ?? 0))->where('amount_status', 1)->first();
        $leadUpdate = TourLeads::find(($tourOrder['leads_id'] ?? 0));
        parse_str(parse_url(($leadUpdate['paymant_link'] ?? ""), PHP_URL_QUERY), $queryParams);
        if ($tourOrder && (($tourOrder['part_payment'] ?? "") == 'custom' || ($tourOrder['part_payment'] ?? "") == 'part')) {
            $payOnlineAmount = ((($tourOrder['part_payment'] ?? "") == 'custom') ? ($tourOrder['order_amount'] - $tourOrder['amount']) : $tourOrder['amount']);
            $wallet_amount = 0;
            $total_amount = $payOnlineAmount;
            $onlinepay = $payOnlineAmount;
            $data = [
                'additional_data' => [
                    'business_name' => \App\Models\BusinessSetting::where(['type' => 'company_name'])->first()->value,
                    'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                    'payment_mode' => 'web',
                    'customer_id' => ($tourOrder['user_id'] ?? ""),
                    "order_id" => ($tourOrder['id'] ?? ""),
                    "tour_id" => ($tourOrder['tour_id'] ?? ""),
                    "leads_id" => $tourOrder['leads_id'],
                    "amount" => $payOnlineAmount,
                    "user_name" => $tourOrder['userData']['name'],
                    "user_email" => $tourOrder['userData']['email'],
                    "user_phone" => $tourOrder['userData']['phone'],
                    'total_amount' => $total_amount,
                    'wallet_amount' => $wallet_amount,
                    "online_pay" => $onlinepay,
                    'page_name' => 'tour_order_wallet',
                    'success_url' => route('tour.view-details', ['id' => ($tourOrder['id'] ?? "")]),
                ],
                'user_id' => $tourOrder['userData']['id'],
                'payment_method' => 'razor_pay',
                'payment_platform' => 'web',
                'payment_amount' => $onlinepay,
                'attribute' => "Tour Order",
                'external_redirect_link' => route('tour.tour-remaining-payment-success', ["id" => ($tourOrder['id'] ?? "")]),
            ];
            if (\App\Models\PaymentRequest::where('id', ($queryParams['payment_id'] ?? null))->where('is_paid', 0)->exists()) {
                $url_open = ($leadUpdate['paymant_link'] ?? "");
            } else {
                $url_open =  \App\Http\Controllers\Customer\PaymentController::Wallet_amount_add($data);
            }
            $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
            $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
            $message_data['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
            $message_data['time'] = ($tourOrder['pickup_time'] ?? '');
            $message_data['place_name'] = ($tourOrder['pickup_address'] ?? '');
            $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
            $message_data['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
            $message_data['payment_link'] = $url_open;
            $leadUpdate->paymant_link = $url_open;
            $leadUpdate->save();
            $message_data['customer_id'] = $tourOrder['user_id'];
            if ($tourOrder['Tour']['tour_image']) {
                $message_data['type'] = 'text-with-media';
                $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['tour_image'] ?? '');
            }
            $remain_amount = (($tourOrder['part_payment'] == 'custom') ? (($tourOrder['order_amount'] ?? 0) - ($tourOrder['amount'] ?? 0)) : (($tourOrder['part_payment'] == 'part') ?  ($tourOrder['amount'] ?? 0) : 0));
            $message_data['remain_amount'] = webCurrencyConverter(amount: (float)$remain_amount ?? 0);
            if (!$internal) {
                Helpers::whatsappMessage('tour', 'Tour booking payment link', $message_data);
            }
            Toastr::success('Send Link Successfully');
            return (($internal) ? ['status' => true, 'message' => 'Send Link Successfully'] : back());
        } else {
            Toastr::error('Not Send Link, Old Paymant Not Paid');
            return (($internal) ? ['status' => false, 'message' => 'Old Payment Not Paid'] : back());
        }
    }
}
