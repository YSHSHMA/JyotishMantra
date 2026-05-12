<?php

namespace App\Http\Controllers\AllController;

use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\AllPaths\SelfDrivingPath;
use App\Http\Controllers\Controller;
use App\Models\SelfDrivingCabs;
use App\Models\TourAndTravel;
use App\Models\TourCab;
use App\Models\TourVehicleCetagory;
use App\Services\SelfDrivingService;
use App\Traits\FileManagerTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Cast\Double;
use SimplePie\Cache\Redis;

class SelfDrivingController extends Controller
{
    use FileManagerTrait;
    protected $relationId;
    public function __construct(
        private readonly TranslationRepositoryInterface      $translationRepo,
    ) {
        $this->middleware(function ($request, $next) {
            if (auth('tour')->check()) {
                $this->relationId = auth('tour')->user()->relation_id;
            } elseif (auth('tour_employee')->check()) {
                $this->relationId = auth('tour_employee')->user()->relation_id;
            } else {
                $this->relationId = null;
            }

            return $next($request);
        });
    }

    public function AddVehicle()
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $googleMapsApiKey = config('services.google_maps.api_key');
        $typeList = TourVehicleCetagory::where('status', 1)->groupBy('type')->orderBy('id', 'desc')->get();
        $travelar_list = TourAndTravel::where('id', $this->relationId)->where('status', 1)->where('is_approve', 1)->first();
        return view(SelfDrivingPath::ADDCAB[VIEW], compact('typeList', 'travelar_list', 'googleMapsApiKey', 'languages', 'defaultLanguage'));
    }

    public function VehicleCategoryGet(Request $request)
    {
        $old_data = TourVehicleCetagory::find($request['id']);
        if ($old_data) {
            $getInfo = TourVehicleCetagory::where('type', $old_data['type'])->where('status', 1)->orderBy('id', 'desc')->get();
            return response()->json(['success' => 1, 'message' => "data Get Successfully", 'data' => $getInfo], 200);
        }
        return response()->json(['success' => 0, 'message' => "Not Found Data", 'data' => []], 200);
    }

    public function GetCabList(Request $request)
    {
        $old_data = TourCab::where('vehicle_category', $request['id'])->where('status', 1)->orderBy('id', 'desc')->get();
        if ($old_data) {
            return response()->json(['success' => 1, 'message' => "data Get Successfully", 'data' => $old_data], 200);
        }
        return response()->json(['success' => 0, 'message' => "Not Found Data", 'data' => []], 200);
    }

    public function StoreSelfDriving(Request $request, SelfDrivingService $service)
    {
        $getArray = $service->AddSelfDriving($request);
        $insert = SelfDrivingCabs::create($getArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\SelfDrivingCabs', id: $insert->id);
        Toastr::success(translate('Cancalltion_policy_updated_successfully'));
        return redirect()->route(SelfDrivingPath::SELFDRIVINGLIST[REDIRECT]);
    }

    public function SelfDrivingList(Request $request)
    {
        $typeList = TourVehicleCetagory::where('status', 1)->groupBy('type')->orderBy('id', 'desc')->get();
        $brand_name = TourVehicleCetagory::where('status', 1)->orderBy('id', 'desc')->get();
        $cab_list = TourCab::where('status', 1)->orderBy('id', 'desc')->get();
        return view(SelfDrivingPath::SELFDRIVINGLIST[VIEW], compact('typeList', 'brand_name', 'cab_list'));
    }

    public function SelfDrivingListFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('search_by_name', '');
        $searchByType = $request->input('search_by_type', '');
        $searchByCategory = $request->input('search_by_category', '');
        $searchByCabId = $request->input('search_by_cabid', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $columnName = $request->input("columns.$orderColumnIndex.data");
        $query = SelfDrivingCabs::query();
        $query->with(['getType', 'getCategory', 'getCabId'])
            ->when(isset($searchByType), function ($query) use ($searchByType) {
                return $query->where(['type' => $searchByType]);
            })->when(isset($searchByCategory), function ($query) use ($searchByCategory) {
                return $query->where(['category_id' => $searchByCategory]);
            })->when(isset($searchByCabId), function ($query) use ($searchByCabId) {
                return $query->where(['cab_id' => $searchByCabId]);
            })
        ;
        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where(function ($querys) use ($searchValue) {
                    $querys->where('basic_price', 'like', "%$searchValue%");
                    $querys->orWhereHas('getType', function ($q) use ($searchValue) {
                        $q->where('type', 'like', "%$searchValue%");
                    })
                        ->orWhereHas('getCategory', function ($q) use ($searchValue) {
                            $q->where('brand_name', 'like', "%$searchValue%");
                        })
                        ->orWhereHas('getCabId', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        });
                });
            });
        }
        $recordsTotal = SelfDrivingCabs::with(['getType', 'getCategory', 'getCabId'])->when(isset($searchByType), function ($query) use ($searchByType) {
            return $query->where(['type' => $searchByType]);
        })->when(isset($searchByCategory), function ($query) use ($searchByCategory) {
            return $query->where(['category_id' => $searchByCategory]);
        })->when(isset($searchByCabId), function ($query) use ($searchByCabId) {
            return $query->where(['cab_id' => $searchByCabId]);
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
            $routeUrl = route('tour-vendor.self-driving.self-status-update');
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
                    data-on-message="<p>If enabled, this vehicle Category will be available on the website and customer app</p>"
                    data-off-message="<p>If disabled, this vehicle Category will be hidden from the website and customer app</p>">
                <span class="switcher_control"></span>
            </label>
        </form>';

            $options = '<div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn" title="' . translate('edit') . '"
                                                href="' . route('tour-vendor.self-driving.self-driving-edit', [$item['id']]) . '">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="tourtravellers-' . $item['id'] . '" title="' . translate('delete') . '"><i class="tio-delete"></i>
                                            </a>
                                            <form action="' . route('tour-vendor.self-driving.self-driving-delete', [$item['id']]) . '" method="post" id="tourtravellers-' . $item['id'] . '">
                                                <input type="hidden" name="_token" value="' . $csrf . '">
                                            </form>
                                        </div>';
            return [
                'id' => $start + $key + 1,
                'type' => ($item['getType']['type'] ?? ''),
                'category' => ($item['getCategory']['brand_name'] ?? ''),
                'cab_name' => ($item['getCabId']['name'] ?? ''),
                'basic_price' => $item['basic_price'] ?? 0,
                'status' => $statusForm,
                'is_approve' => "<span class='badge badge-soft-".($item->is_approve == 1 ? 'success' : 'danger')."'>".($item->is_approve == 1 ? 'Live' : 'Off').'</span>',
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

    public function SelfDrivingEdit(Request $request)
    {
        $getData = SelfDrivingCabs::find($request['id']);
        if (!$getData) {
            Toastr::success(translate('not_found_data'));
            return redirect()->route(SelfDrivingPath::SELFDRIVINGLIST[REDIRECT]);
        }
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $typeList = TourVehicleCetagory::where('status', 1)->groupBy('type')->orderBy('id', 'desc')->get();
        $getTypes = TourVehicleCetagory::find($getData['type']);
        $categoryList = [];
        if ($getTypes) {
            $categoryList = TourVehicleCetagory::where('type', $getTypes['type'])->where('status', 1)->orderBy('id', 'desc')->get();
        }
        $getCabList = TourCab::where('status', 1)->where('vehicle_category', $getData['category_id'])->orderBy('id', 'desc')->get();
        $travelar_list = TourAndTravel::where('id', $this->relationId)->where('status', 1)->where('is_approve', 1)->first();
        return view(SelfDrivingPath::SELFDRIVINGEDIT[VIEW], compact('getData', 'typeList', 'categoryList', 'getCabList', 'travelar_list', 'languages', 'defaultLanguage'));
    }

    public function SelfDrivingUpdate(Request $request, SelfDrivingService $service)
    {
        $getArray = $service->UpdateSelfDriving($request);
        SelfDrivingCabs::where('id', $request['id'])->update($getArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\SelfDrivingCabs', id: $request->id);
        Toastr::success(translate('self_driving_cab_updated_successfully'));
        return redirect()->route(SelfDrivingPath::SELFDRIVINGLIST[REDIRECT]);
    }

    public function SelfDrivingDelete(Request $request)
    {
        $getData = SelfDrivingCabs::find($request['id']);
        if ($getData) {
            $getData->delete();
            $this->translationRepo->delete(model: 'App\Models\SelfDrivingCabs', id: $request->id);
        }
        Toastr::success(translate('self_driving_cab_deleted_successfully'));
        return redirect()->route(SelfDrivingPath::SELFDRIVINGLIST[REDIRECT]);
    }
    public function SelfDrivingImageRemove(Request $request, SelfDrivingService $service)
    {
        $getData  = SelfDrivingCabs::find($request['id']);
        if (empty($getData)) {
            return back();
        }
        $dataIMage = $service->ImageRemove($getData, $request['name']);
        SelfDrivingCabs::where('id', $request['id'])->update(['images' => json_encode($dataIMage)]);
        return back();
    }

    public function SelfDrivingStatusUpdate(Request $request)
    {
        $getData = SelfDrivingCabs::find($request['id']);
        if ($getData) {
            $getData->status = $request->get('status', 0);
            $getData->save();
        }
        Toastr::success(translate('status_updated_successfully'));
        return back();
    }
}
