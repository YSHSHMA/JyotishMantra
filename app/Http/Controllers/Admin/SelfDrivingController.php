<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\SelfDrivingPath;
use App\Http\Controllers\Controller;
use App\Models\SelfCancellationPolicy;
use App\Models\SelfDrivingCabs;
use App\Models\SelfDrivingPolicy;
use App\Models\TourAndTravel;
use App\Models\TourCab;
use App\Models\TourVehicleCetagory;
use App\Services\SelfDrivingService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SelfDrivingController extends Controller
{
    public function __construct(
        private readonly TranslationRepositoryInterface      $translationRepo,
    ) {}

    public function AddDrivingPolicy(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(SelfDrivingPath::SELFDRIVINGADDPOLICY[VIEW], compact('languages', 'defaultLanguage'));
    }

    public function StoreDrivingPolicy(Request $request, SelfDrivingService $service)
    {
        $request->validate([
            'title' => 'required|array',
            'title.*' => 'required|string',
            'policy_name' => 'required|array',
            'policy_name.*' => 'required|string',
        ]);

        $getArray = $service->policyAddData($request);
        $insert = SelfDrivingPolicy::create($getArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\SelfDrivingPolicy', id: $insert->id);
        Toastr::success(translate('policy_added_successfully'));
        return redirect()->route(SelfDrivingPath::SELFDRIVINGADDPOLICY[REDIRECT]);
    }

    public function DrivingPolicyFilter(Request $request)
    {

        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '');
        $query = SelfDrivingPolicy::query();

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                    ->orWhere('policy_name', 'like', "%{$searchValue}%")
                    ->orWhere('message', 'like', "%{$searchValue}%");
            });
        }
        $recordsTotal = SelfDrivingPolicy::count();

        $recordsFiltered = $query->count();
        $data = $query->orderBy('id', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $formattedData = $data->map(function ($item, $key) use ($start) {
            $formId = 'temple-status' . $item->id . '-form';
            $inputId = 'temple-status' . $item->id;
            $checked = $item->status == 1 ? 'checked' : '';
            $routeUrl = route('admin.driving-policy.status-update');
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
                                                href="' . route('admin.driving-policy.policy-edit', [$item['id']]) . '">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="tourtravellers-' . $item['id'] . '" title="' . translate('delete') . '"><i class="tio-delete"></i>
                                            </a>
                                            <form action="' . route('admin.driving-policy.policy-delete', [$item['id']]) . '" method="post" id="tourtravellers-' . $item['id'] . '">
                                                <input type="hidden" name="_token" value="' . $csrf . '">
                                            </form>
                                        </div>';
            return [
                'id' => $start + $key + 1,
                'title' => ($item->title),
                'policy_name' => ($item->policy_name),
                'message' => Str::limit(strip_tags($item->message), 25),
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

    public function DrivingPolicyStatusUpdate(Request $request)
    {
        $getData = SelfDrivingPolicy::find($request['id']);
        if ($getData) {
            $getData->status = $request->get('status', 0);
            $getData->save();
        }
        Toastr::success(translate('status_updated_successfully'));
        return redirect()->route(SelfDrivingPath::SELFDRIVINGADDPOLICY[REDIRECT]);
    }

    public function DrivingPolicyDelete(Request $request)
    {
        $getData = SelfDrivingPolicy::find($request['id']);
        if ($getData) {
            $getData->delete();
            $this->translationRepo->delete(model: 'App\Models\SelfDrivingPolicy', id: $request->id);
        }
        Toastr::success(translate('policy_deleted_successfully'));
        return redirect()->route(SelfDrivingPath::SELFDRIVINGADDPOLICY[REDIRECT]);
    }

    public function DrivingPolicyEdit(Request $request)
    {
        $getData = SelfDrivingPolicy::with(['translations'])->find($request['id']);
        if (!$getData) {
            Toastr::success(translate('policy_not_found'));
            return redirect()->route(SelfDrivingPath::SELFDRIVINGADDPOLICY[REDIRECT]);
        }
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(SelfDrivingPath::SELFDRIVINGPOLICYDUPDATE[VIEW], compact('getData', 'languages', 'defaultLanguage'));
    }
    public function DrivingPolicyUpdate(Request $request, SelfDrivingService $service)
    {
        $request->validate([
            'title' => 'required|array',
            'title.*' => 'required|string',
            'policy_name' => 'required|array',
            'policy_name.*' => 'required|string',
        ]);

        $getArray = $service->policyAddData($request);
        SelfDrivingPolicy::where('id', $request['id'])->update($getArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\SelfDrivingPolicy', id: $request->id);
        Toastr::success(translate('policy_updated_successfully'));
        return redirect()->route(SelfDrivingPath::SELFDRIVINGADDPOLICY[REDIRECT]);
    }

    public function AddCancellationpolicy(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(SelfDrivingPath::CANCELLATIONPOLICY[VIEW], compact('languages', 'defaultLanguage'));
    }
    public function StoreCancellationpolicy(Request $request, SelfDrivingService $service)
    {
        $request->validate([
            'title' => 'required|array',
            'title.*' => 'required|string',
            'percentage' => 'required',
            'day' => 'required',
        ]);

        $getArray = $service->CancellationAddData($request);
        $insert = SelfCancellationPolicy::create($getArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\SelfCancellationPolicy', id: $insert->id);
        Toastr::success(translate('Cancalltion_policy_added_successfully'));
        return redirect()->route(SelfDrivingPath::CANCELLATIONPOLICY[REDIRECT]);
    }

    public function CancellationpolicyFilter(Request $request)
    {

        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $columnName = $request->input("columns.$orderColumnIndex.data");
        $query = SelfCancellationPolicy::query();

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                    ->orWhere('percentage', 'like', "%{$searchValue}%")
                    ->orWhere('day', 'like', "%{$searchValue}%")
                    ->orWhere('message', 'like', "%{$searchValue}%");
            });
        }
        $recordsTotal = SelfCancellationPolicy::count();

        $recordsFiltered = $query->count();
        $data = $query->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $formattedData = $data->map(function ($item, $key) use ($start) {
            $formId = 'temple-status' . $item->id . '-form';
            $inputId = 'temple-status' . $item->id;
            $checked = $item->status == 1 ? 'checked' : '';
            $routeUrl = route('admin.driving-cancellation-policy.status-update');
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
                                                href="' . route('admin.driving-cancellation-policy.cancellation-edit', [$item['id']]) . '">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="tourtravellers-' . $item['id'] . '" title="' . translate('delete') . '"><i class="tio-delete"></i>
                                            </a>
                                            <form action="' . route('admin.driving-cancellation-policy.cancellation-delete', [$item['id']]) . '" method="post" id="tourtravellers-' . $item['id'] . '">
                                                <input type="hidden" name="_token" value="' . $csrf . '">
                                            </form>
                                        </div>';
            $days = floor($item['day'] / 24);
            $remainingHours = $item['day'] % 24;
            $parts = [];
            if ($days > 0) {
                $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
            }
            if ($remainingHours > 0) {
                $parts[] = $remainingHours . ' hour' . ($remainingHours > 1 ? 's' : '');
            }
            $days_show = $parts ? implode(' ', $parts) : '0 hours';
            return [
                'id' => $start + $key + 1,
                'title' => ($item->title),
                'percentage' => ($item->percentage) . "%",
                'day' => ($days_show ?? ''),
                'message' => Str::limit(strip_tags($item->message), 25),
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

    public function CancellationpolicyStatusUpdate(Request $request)
    {
        $getData = SelfCancellationPolicy::find($request['id']);
        if ($getData) {
            $getData->status = $request->get('status', 0);
            $getData->save();
        }
        Toastr::success(translate('status_updated_successfully'));
        return redirect()->route(SelfDrivingPath::CANCELLATIONPOLICY[REDIRECT]);
    }

    public function CancellationpolicyDelete(Request $request)
    {
        $getData = SelfCancellationPolicy::find($request['id']);
        if ($getData) {
            $getData->delete();
            $this->translationRepo->delete(model: 'App\Models\SelfCancellationPolicy', id: $request->id);
        }
        Toastr::success(translate('cancellation_policy_deleted_successfully'));
        return redirect()->route(SelfDrivingPath::CANCELLATIONPOLICY[REDIRECT]);
    }

    public function CancellationpolicyEdit(Request $request)
    {
        $getData = SelfCancellationPolicy::with(['translations'])->find($request['id']);
        if (!$getData) {
            Toastr::success(translate('cancellation_policy_not_found'));
            return redirect()->route(SelfDrivingPath::CANCELLATIONPOLICY[REDIRECT]);
        }
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(SelfDrivingPath::CANCELLATIONPOLICYUPDATE[VIEW], compact('getData', 'languages', 'defaultLanguage'));
    }
    public function CancellationpolicyUpdate(Request $request, SelfDrivingService $service)
    {
        $request->validate([
            'title' => 'required|array',
            'title.*' => 'required|string',
            'percentage' => 'required',
            'day' => 'required',
        ]);

        $getArray = $service->CancellationAddData($request);
        SelfCancellationPolicy::where('id', $request['id'])->update($getArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\SelfCancellationPolicy', id: $request->id);
        Toastr::success(translate('Cancalltion_policy_updated_successfully'));
        return redirect()->route(SelfDrivingPath::CANCELLATIONPOLICY[REDIRECT]);
    }

    public function GetCabList(Request $request)
    {
        $old_data = TourCab::where('vehicle_category', $request['id'])->where('status', 1)->orderBy('id', 'desc')->get();
        if ($old_data) {
            return response()->json(['success' => 1, 'message' => "data Get Successfully", 'data' => $old_data], 200);
        }
        return response()->json(['success' => 0, 'message' => "Not Found Data", 'data' => []], 200);
    }

    public function AddSelfDriving(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $typeList = TourVehicleCetagory::where('status', 1)->groupBy('type')->orderBy('id', 'desc')->get();
        $travelar_list = TourAndTravel::where('status', 1)->where('is_approve', 1)->orderBy('id', 'desc')->get();
        return view(SelfDrivingPath::SELFDRIVINGADD[VIEW], compact('typeList', 'travelar_list', 'languages', 'defaultLanguage'));
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
            $routeUrl = route('admin.self-driving-management.status-update');
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
                                                href="' . route('admin.self-driving-management.self-driving-edit', [$item['id']]) . '">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="tourtravellers-' . $item['id'] . '" title="' . translate('delete') . '"><i class="tio-delete"></i>
                                            </a>
                                            <form action="' . route('admin.self-driving-management.self-driving-delete', [$item['id']]) . '" method="post" id="tourtravellers-' . $item['id'] . '">
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

    public function SelfDrivingStatusUpdate(Request $request)
    {
        $getData = SelfDrivingCabs::find($request['id']);
        if ($getData) {
            $getData->status = $request->get('status', 0);
            $getData->save();
        }
        Toastr::success(translate('status_updated_successfully'));
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
        $travelar_list = TourAndTravel::where('status', 1)->where('is_approve', 1)->orderBy('id', 'desc')->get();
        return view(SelfDrivingPath::SELFDRIVINGUPDATE[VIEW], compact('getData', 'typeList', 'categoryList', 'getCabList', 'travelar_list', 'languages', 'defaultLanguage'));
    }
    public function SelfDrivingUpdate(Request $request, SelfDrivingService $service)
    {
        $getArray = $service->UpdateSelfDriving($request);
        SelfDrivingCabs::where('id', $request['id'])->update($getArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\SelfDrivingCabs', id: $request->id);
        Toastr::success(translate('self_driving_cab_updated_successfully'));
        return redirect()->route(SelfDrivingPath::SELFDRIVINGLIST[REDIRECT]);
    }
    public function SelfVehicleLead(Request $request)
    {
        return view(SelfDrivingPath::SELFVEHICLELEAD[VIEW]);
    }
    public function SelfVehicleLeadFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('searchValue', '');
        $searchByCabId = $request->input('search_by_status', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $start_date = $request->get('start_date') ?? '';
        $end_date = $request->get('end_date') ?? '';
        $columnName = $request->input("columns.$orderColumnIndex.data");
        $querys = \App\Models\SelfVehicleLeads::query();
        $querys->with(['OrderInfo', 'SelfCabData', 'userData', 'followby'])->when($searchValue, function ($qu1) use ($searchValue) {
            $qu1->where('order_id', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            $qu1->orwhereHas('userData', function ($q2) use ($searchValue) {
                $q2->where('name', 'like', "%$searchValue%");
                $q2->orWhere('email', 'like', "%$searchValue%");
                $q2->orWhere('phone', 'like', "%$searchValue%");
            });
            $qu1->orWhereHas('OrderInfo', function ($q4) use ($searchValue) {
                $q4->where('order_id', 'like', "%$searchValue%");
            });
            $qu1->orWhereHas('SelfCabData', function ($q3) use ($searchValue) {
                $q3->where('slug', 'like', "%$searchValue%");
                $q3->where('car_type', 'like', "%$searchValue%");
            });
        })
            ->when(isset($searchByCabId), function ($query) use ($searchByCabId) {
                return $query->where('status', $searchByCabId);
            })->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('created_at', [$start_date, $end_date]);
            });
        $recordsTotal = \App\Models\SelfVehicleLeads::with(['OrderInfo', 'SelfCabData', 'userData', 'followby'])
            ->when(isset($searchByCabId), function ($query) use ($searchByCabId) {
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
                                            <a href="' . route('admin.self-driving-management.leads-delete', [$item['id']]) . '"
                                                class="btn btn-icon bg-label-danger waves-effect waves-light myactionbtn"
                                                onclick="return confirm(\'Are your sure, you want to delete\');"
                                                data-toggle="tooltip" aria-label="Delete"
                                                data-bs-original-title="Delete"><i class="tio-delete-outlined"></i></a>
                                            <a href="javascript:void(0)"
                                                class="btn btn-icon bg-label-success waves-effect waves-light myactionbtn"
                                                data-custId="' . $item['user_id'] . '" data-leadsId="' . $item['id'] . '"
                                                onclick="followUp(this)" data-toggle="tooltip" aria-label="Follow Up"
                                                data-bs-original-title="Follow Up">
                                                <i class="tio-message"></i>
                                            </a>
                                        </div>
                                        <div class="d-flex justify-content-center gap-2 pt-2">
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
                                        </div>
                                         <div class="d-flex justify-content-center gap-2 pt-2">
                                            <a href="' . route('admin.self-driving-management.self-vehicle-whatsapp-message', [$item['id']]) . '" class="btn btn-icon bg-label-success waves-effect waves-light myactionbtn" data-toggle="tooltip" aria-label="whatsapp" data-bs-original-title="whatsapp"><i class="tio-whatsapp" title="whatsapp"></i>
                                                <span class="btn-status btn-sm-status btn-status-danger">' . $item['whatsapp_hit'] . '</span>
                                            </a>
                                            <a href="' . route('admin.whatsapp.tours-template') . '"
                                                class="btn btn-icon bg-label-primary waves-effect waves-light myactionbtn"
                                                data-toggle="tooltip" aria-label="customise message"
                                                data-bs-original-title="customise message"><i class="tio-message" title="customise message"></i>
                                            </a>
                                        </div>
                    </div> ';

            $tour_info = '<span class="font-weight-bolder">' . ($item['SelfCabData']['getCategory']['brand_name'] ?? '') . ' | ' . ($item['SelfCabData']['getCabId']['name'] ?? '') . ' | ' . ($item['SelfCabData']['getCabId']['seats'] ?? '') . ' seats | ' . (ucwords($item['SelfCabData']['car_type'] ?? '')) . '</span>';
            if ($item['order_id']) {
                $tour_info .= '<br><span class="font-weight-bolder">Order Id: ' . ($item['OrderInfo']['order_id'] ?? '') . '</span>';
            }
            $tour_info .= '<br><span class="font-weight-bolder"><i class="tio-money_vs">money_vs</i>Amount</span> ' . setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($item['price'] ?? 0) + ($item['tax_amount'] ?? 0))), currencyCode: getCurrencyCode());
            $tour_info .= '<br><span class="font-weight-bolder"><i class="tio-money_vs">money_vs</i>coupon</span> ' . setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($item['coupan_amount'] ?? 0))), currencyCode: getCurrencyCode());
            $tour_info .= '<br><span class="font-weight-bolder"><i class="tio-password_open">password_open</i>Amount</span> ' . setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($item['security_amount'] ?? 0))), currencyCode: getCurrencyCode());
            $tour_info .= '<br><span class="font-weight-bolder"><i class="tio-saving"></i>Amount</span> ' . setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($item['price'] ?? 0) + ($item['tax_amount'] ?? 0) + ($item['security_amount'] ?? 0) - ($item['coupan_amount'] ?? 0))), currencyCode: getCurrencyCode());
            /////////////
            $user_info = '<span class="font-weight-bolder">' . ($item['userData']['name'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['userData']['email'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['userData']['phone'] ?? "") . '</span><br>';
            ///////////////////////
            $statusForm = '';
            if ($item['status'] == 0) {
                $statusForm = 'Pending';
            } elseif ($item['status'] == 1) {
                $statusForm = 'Success';
            } elseif ($item['status'] == 2) {
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
                'follow_by' => $item['followby']['follow_by'] ?? 'pending',
                'next_date' => $item['followby']['next_date'] ?? 'pending',
                'last_date' => $item['followby']['last_date'] ?? 'pending',
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

    public function SelfVehicleLeadDelete(Request $request)
    {
        $lead = \App\Models\SelfVehicleLeads::find($request['id']);
        if ($lead) {
            $lead->delete();
            \App\Models\SelfVehicleFollowup::where('lead_id', $request['id'])->delete();
            Toastr::success(translate('lead_Delete_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function SelfVehicleFollowUp(Request $request)
    {
        $follows = [
            'lead_id' => $request->input('lead_id'),
            'message' => $request->input('message'),
            'last_date' => $request->input('last_date'),
            'next_date' => $request->input('next_date'),
            'follow_by' => $request->input('follow_by'),
            'follow_by_id' => $request->input('follow_by_id'),
        ];
        \App\Models\SelfVehicleFollowup::create($follows);
        Toastr::success(translate('lead_follow_up_successfully'));
        return back();
    }
    public function SelfVehicleGetFollowUp(Request $request)
    {
        $followlist = \App\Models\SelfVehicleFollowup::where('lead_id', $request['id'])->get();
        if ($followlist) {
            return response()->json($followlist);
        } else {
            return response()->json([], 200);
        }
    }

    public function SelfVehicleWhatsappMessage(Request $request)
    {
        $lead = \App\Models\SelfVehicleLeads::where('id', $request['id'])->with(['SelfCabData', 'userData'])->first();
        if ($lead) {
            $message_data = [
                'title_name' => ($lead['SelfCabData']['getCategory']['brand_name'] ?? '') . ' | ' . ($lead['SelfCabData']['getCabId']['name'] ?? '') . ' | ' . ($lead['SelfCabData']['getCabId']['seats'] ?? '') . ' seats | ' . (ucwords($lead['SelfCabData']['car_type'] ?? '')),
                'customer_id' => ($lead['user_id'] ?? ""),
                'price' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($lead['price'] ?? 0) + ($lead['tax_amount'] ?? 0) - ($lead['coupan_amount'] ?? 0))), currencyCode: getCurrencyCode()),
                'type' => 'text-with-media',
                'attachment' =>  getValidImage(path: 'storage/app/public/tour_and_travels/self_driving' . $lead['SelfCabData']['thumbnail'], type: 'backend-product'),
                'link' => route('self-vehicle-details', ['slug' => $lead['SelfCabData']['slug']]),
            ];
            Helpers::whatsappMessage('tour', 'self_vehicle_leads_message', $message_data);
            \App\Models\SelfVehicleLeads::where('id', $request['id'])->increment('whatsapp_hit');
            Toastr::success(translate('message_sent_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function SelfVehiclePendingOrder(Request $request)
    {
        return view(SelfDrivingPath::SELFVEHICLELEORDERPENDING[VIEW]);
    }
    public function SelfVehiclePendingOrderFilter(Request $request)
    {

        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('searchValue', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $start_date = $request->get('start_date') ?? '';
        $end_date = $request->get('end_date') ?? '';
        $columnName = $request->input("columns.$orderColumnIndex.data");
        $querys = \App\Models\SelfVehicleOrder::query();
        $querys->with(['SelfCabData', 'userData','TravellerInfo'])->when($searchValue, function ($qu1) use ($searchValue) {
            $qu1->where('order_id', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            $qu1->orwhereHas('userData', function ($q2) use ($searchValue) {
                $q2->where('name', 'like', "%$searchValue%");
                $q2->orWhere('email', 'like', "%$searchValue%");
                $q2->orWhere('phone', 'like', "%$searchValue%");
            });
            $qu1->orWhereHas('SelfCabData', function ($q3) use ($searchValue) {
                $q3->where('slug', 'like', "%$searchValue%");
                $q3->where('car_type', 'like', "%$searchValue%");
            });
        })->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
            $query4->whereBetween('created_at', [$start_date, $end_date]);
        })->where('status', 1)->where('order_accept_status', 0);
        $recordsTotal = \App\Models\SelfVehicleOrder::with(['SelfCabData', 'userData','TravellerInfo'])
            ->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('created_at', [$start_date, $end_date]);
            })->where('status', 1)->where('order_accept_status', 0)->count();
        $recordsFiltered = $querys->count();
        $data = $querys->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $formattedData = $data->map(function ($item, $key) use ($start) {

            $options = '<div class="d-flex justify-content-center gap-2">                                            
                                            <a href="' . route('admin.self-driving-management.order-view-details', [$item['id']]) . '"
                                                class="btn btn-icon bg-label-danger waves-effect waves-light myactionbtn"
                                                data-toggle="tooltip" title="view Order"><i class="tio-invisible-outlined">invisible</i></a>                                            
                                                <a href="javascript:0"
                                                    class="btn btn-icon bg-label-info waves-effect waves-light myactionbtn"
                                                    data-leadsId="' . $item['id'] . '" onclick="followHistory(this)"
                                                    data-toggle="tooltip"
                                                    title="Refund Amount"><i class="tio-history"></i></a>
                                                <a href="tel:' . ($item['userData']['phone'] ?? '') . '"
                                                    class="btn btn-icon bg-label-warning waves-effect waves-light myactionbtn"
                                                    data-toggle="tooltip" aria-label="Call" title="Call"><i class="tio-call"></i></a>
                    </div> ';

            $vehicle_info = '<span class="font-weight-bolder">' . ($item['SelfCabData']['getCategory']['brand_name'] ?? '') . ' | ' . ($item['SelfCabData']['getCabId']['name'] ?? '') . ' <br> ' . ($item['SelfCabData']['getCabId']['seats'] ?? '') . ' seats | ' . (ucwords($item['SelfCabData']['car_type'] ?? '')) . '</span>';

            $Amount_info = '<span class="font-weight-bolder"><i class="tio-money_vs">money_vs</i>Amount</span> ' . setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($item['price'] ?? 0))), currencyCode: getCurrencyCode());
            $Amount_info .= '<br><span class="font-weight-bolder"><i class="tio-money_vs">money_vs</i>Tax</span> ' . setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($item['tax_amount'] ?? 0))), currencyCode: getCurrencyCode());
            $Amount_info .= '<br><span class="font-weight-bolder"><i class="tio-money_vs">money_vs</i>coupon</span> ' . setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($item['coupan_amount'] ?? 0))), currencyCode: getCurrencyCode());
            $Amount_info .= '<br><span class="font-weight-bolder"><i class="tio-password_open">password_open</i>Amount</span> ' . setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($item['security_amount'] ?? 0))), currencyCode: getCurrencyCode());
            $Amount_info .= '<br><span class="font-weight-bolder"><i class="tio-saving"></i>Amount</span> ' . setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($item['price'] ?? 0) + ($item['tax_amount'] ?? 0) + ($item['security_amount'] ?? 0) - ($item['coupan_amount'] ?? 0))), currencyCode: getCurrencyCode());
            /////////////
            $user_info = '<span class="font-weight-bolder">' . ($item['userData']['name'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['userData']['phone'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['userData']['email'] ?? "") . '</span><br>';
            $vendor_info = '<span class="font-weight-bolder">' . ($item['TravellerInfo']['company_name'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['TravellerInfo']['phone_no'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['TravellerInfo']['email'] ?? "") . '</span><br>';
            ///////////////////////
            $statusForm = '';
            if ($item['status'] == 0) {
                $statusForm = 'Pending';
            } elseif ($item['status'] == 1) {
                $statusForm = 'Success';
            } elseif ($item['status'] == 2) {
                $statusForm = 'Failed';
            }
            return [
                'id' => $start + $key + 1,
                'order_id' => $item['order_id'],
                'platform' => $item['platform'],
                'user_info' => $user_info,
                'vehicle_info' => $vehicle_info,
                'vendor_info' => $vendor_info,
                'create_by' => date('d M,Y h:i A', strtotime($item['created_at'])),
                'status' => $statusForm,
                'amount' => $Amount_info,
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
    public function SelfVehicleOrderView(Request $request){
        $getData = \App\Models\SelfVehicleOrder::where('id', $request['id'])->with(['SelfCabData', 'userData','TravellerInfo'])->first();
        return view(SelfDrivingPath::SELFVEHICLELEORDERVIEW[VIEW],compact('getData'));
    }
    public function SelfVehicleConfirmOrder(Request $request)
    {
        return view(SelfDrivingPath::SELFVEHICLELEORDERCONFIRM[VIEW]);
    }
    public function SelfVehiclePickUpOrder(Request $request)
    {
        return view(SelfDrivingPath::SELFVEHICLELEORDERPICKUP[VIEW]);
    }
    public function SelfVehicleDropOrder(Request $request)
    {
        return view(SelfDrivingPath::SELFVEHICLELEORDERDROP[VIEW]);
    }
}
