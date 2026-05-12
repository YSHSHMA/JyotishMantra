<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\TourCabRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\TourCabPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourPackageRequest;
use App\Models\TourVehicleCetagory;
use App\Services\TourPackageService;
use App\Traits\FileManagerTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class TourCabController extends Controller
{

    use FileManagerTrait;
    public function __construct(
        private readonly TranslationRepositoryInterface     $translationRepo,
        private readonly TourCabRepositoryInterface  $tourcab,
    ) {}

    public function VehicleListing(Request $request)
    {
        return view(TourCabPath::VEHICLELIST[VIEW]);
    }

    public function VehicleListingFilters(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '');
        $query = TourVehicleCetagory::query();

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('type', 'like', "%{$searchValue}%")
                    ->orWhere('brand_name', 'like', "%{$searchValue}%");
            });
        }
        $recordsTotal = TourVehicleCetagory::count();

        $recordsFiltered = $query->count();
        $data = $query->orderBy('id', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $formattedData = $data->map(function ($item, $key) use ($start) {
            $formId = 'temple-status' . $item->id . '-form';
            $inputId = 'temple-status' . $item->id;
            $checked = $item->status == 1 ? 'checked' : '';
            $routeUrl = route('admin.tour_vehicle_setting.status-update');
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
                    data-on-title="Want to Turn ON ' . e($item->brand_name) . ' status"
                    data-off-title="Want to Turn OFF ' . e($item->brand_name) . ' status"
                    data-on-message="<p>If enabled, this vehicle Category will be available on the website and customer app</p>"
                    data-off-message="<p>If disabled, this vehicle Category will be hidden from the website and customer app</p>">
                <span class="switcher_control"></span>
            </label>
        </form>';

            $options = '<div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn" title="' . translate('edit') . '"
                                                href="' . route('admin.tour_vehicle_setting.vehicel-edit', [$item['id']]) . '">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="tourtravellers-' . $item['id'] . '" title="' . translate('delete') . '"><i class="tio-delete"></i>
                                            </a>
                                            <form action="' . route('admin.tour_vehicle_setting.vehicle-delete', [$item['id']]) . '" method="post" id="tourtravellers-' . $item['id'] . '">
                                                <input type="hidden" name="_token" value="' . $csrf . '">
                                            </form>
                                        </div>';
            return [
                'id' => $start + $key + 1,
                'type' => ($item->type),
                'name' => ($item->brand_name),
                'status' => $statusForm,
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

    public function VehicleAdd(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(TourCabPath::VEHICLEADD[VIEW], compact('defaultLanguage', 'languages'));
    }

    public function VehicleStore(Request $request, TourPackageService $service)
    {
        $request->validate([
            'type' => 'required',
            'brand_name' => 'required|array',
            'brand_name.*' => 'required|max:255',
        ]);
        $dataArray = $service->getAddVehicleData($request);
        // dd($dataArray);
        foreach ($dataArray as $i => $data) {
            $inserted = TourVehicleCetagory::create($data);
            foreach ($request->lang as $index => $key) {
                if ($key != 'en') {
                    \App\Models\Translation::insert([
                        [
                            'translationable_type' => 'App\Models\TourVehicleCetagory',
                            'translationable_id' => $inserted->id,
                            'locale' => $key,
                            'key' => 'type',
                            'value' => $request['type'][$key]
                        ],
                        [
                            'translationable_type' => 'App\Models\TourVehicleCetagory',
                            'translationable_id' => $inserted->id,
                            'locale' => $key,
                            'key' => 'brand_name',
                            'value' => $request['brand_name'][$key][$i]
                        ]
                    ]);
                }
            }
        }
        Toastr::success(translate('Tour_vehicle_category_added_successfully'));
        return redirect()->route(TourCabPath::VEHICLELIST[REDIRECT]);
    }

    public function VehicleDelete(Request $request)
    {
        $old_data = TourVehicleCetagory::find($request['id']);
        if ($old_data) {
            $old_data->delete();
            $this->translationRepo->delete('App\Models\TourVehicleCetagory', $request['id']);
            Toastr::success(translate('Tour_vehicle_Category_Deleted_successfully'));
        } else {
            Toastr::error(translate('Tour_vehicle_Category_Deleted_Failed'));
        }
        return redirect()->route(TourCabPath::VEHICLELIST[REDIRECT]);
    }

    public function VehicleEdit(Request $request)
    {
        $getData = TourVehicleCetagory::with(['translations'])->find($request['id']);
        if (!$getData) {
            return back();
        }
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(TourCabPath::VEHICLEUPDATE[VIEW], compact('getData', 'defaultLanguage', 'languages'));
    }
    public function VehicleUpdate(Request $request, TourPackageService $service)
    {
        $request->validate([
            'type' => 'required|array',
            'type.*' => 'required|max:255',
            'brand_name' => 'required|array',
            'brand_name.*' => 'required|max:255',
        ]);
        $dataArray = $service->getUpdateVehicleData($request);
        $request['id'] = $request['id'];
        TourVehicleCetagory::updateOrInsert($dataArray);
        foreach ($request->lang as $index => $key) {
            if ($key != 'en') {
                \App\Models\Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\TourVehicleCetagory', 'translationable_id' => $request['id'], 'locale' => $key, 'key' => 'type',],
                    ['value' => $request['type'][$key]]
                );
                \App\Models\Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\TourVehicleCetagory', 'translationable_id' => $request['id'], 'locale' => $key, 'key' => 'brand_name',],
                    ['value' => $request['brand_name'][$key],]
                );
            }
        }
        Toastr::success(translate('Tour_vehicle_category_updated_successfully'));
        return redirect()->route(TourCabPath::VEHICLELIST[REDIRECT]);
    }

    public function VehicleStatusUpdate(Request $request)
    {
        $old_data = TourVehicleCetagory::find($request['id']);
        if ($old_data) {
            $old_data->status = $request->get('status', 0);
            $old_data->save();
        }
        Toastr::success(translate('status_updated_successfully'));
        return redirect()->route(TourCabPath::VEHICLELIST[REDIRECT]);
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

    public function CabList(Request $request)
    {
        $getData = $this->tourcab->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $vehicleList = TourVehicleCetagory::where('status', 1)->groupBy('type')->orderBy('id', 'desc')->get();
        return view(TourCabPath::ADDCAB[VIEW], compact('getData', 'vehicleList', 'defaultLanguage', 'languages'));
    }

    public function CabAdd(TourPackageRequest $request, TourPackageService $service)
    {
        $dataArray = $service->getAddCabData($request);
        $insert = $this->tourcab->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\TourCab', id: $insert->id);
        Toastr::success(translate('Tour_Cab_added_successfully'));
        return redirect()->route(TourCabPath::ADDCAB[REDIRECT]);
    }

    public function CabStatus(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->tourcab->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function CabUpdate($id)
    {
        $getData = $this->tourcab->getFirstWhere(params: ['id' => $id], relations: ['translations', 'vehicleCategory']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $vehicleList = TourVehicleCetagory::where('status', 1)->groupBy('type')->orderBy('id', 'desc')->get();
        $vehicleCategorys = TourVehicleCetagory::where('type', function ($query) use ($getData) {
            $query->select('type')
                ->from('tour_vehicle_category')
                ->where('id', $getData['vehicle_category'])
                ->limit(1);
        })
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();
        return view(TourCabPath::CABUPDATE[VIEW], compact('vehicleCategorys','vehicleList','getData', 'defaultLanguage', 'languages'));
    }

    public function CabEdit(TourPackageRequest $request, TourPackageService $service)
    {
        $dataArray = $service->getUpdateCabData($request);
        $this->tourcab->update(id: $request->id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\TourCab', id: $request->id);
        Toastr::success(translate('Tour_Cab_service_updated_successfully'));
        return redirect()->route(TourCabPath::ADDCAB[REDIRECT]);
    }

    public function CabDelete(Request $request, TourPackageService $service)
    {
        $old_data = $this->tourcab->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            $service->CapImageRemove($old_data);
            $this->tourcab->delete(params: ['id' => $request['id']]);
            $this->translationRepo->delete('App\Models\TourCab', $request['id']);
            Toastr::success(translate('Tour_Cab_service_Deleted_successfully'));
            return response()->json(['success' => 1, 'message' => translate('Tour_Cab_service_deleted_successfully')], 200);
        } else {
            Toastr::error(translate('Tour_Cab_service_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Not_found_data')], 400);
        }
    }
}
