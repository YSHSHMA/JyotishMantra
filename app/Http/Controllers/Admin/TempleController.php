<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use App\Contracts\Repositories\CitiesRepositoryInterface;
use App\Contracts\Repositories\CountryRepositoryInterface;
use App\Contracts\Repositories\StateRepositoryInterface;
use App\Contracts\Repositories\TempleReviewRepositoryInterface;
use App\Contracts\Repositories\TemplesRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\TemplePath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TemplesAddRequest;
use App\Models\Cities;
use App\Models\District;
use App\Models\Country;
use App\Models\TempleServicePackages;
use App\Models\TempleServicePrice;
use App\Models\TempleServiceSlot;
use App\Models\States;
use App\Models\TempleCategory;
use App\Services\TemplesService;
use App\Models\ServiceTax;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

use App\Models\DarshanFollowup;
use App\Models\DarshanOrder;
use App\Models\PanditTransectionHistory;
use App\Models\Temple;
use App\Models\TempleDarshanLead;


class TempleController extends Controller
{

    public function __construct(
        private readonly TemplesRepositoryInterface          $templeRepo,
        private readonly CitiesRepositoryInterface          $citiesRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
        private readonly TempleReviewRepositoryInterface        $templereviewRepo,
        private readonly CountryRepositoryInterface $countryRepo,
        private readonly StateRepositoryInterface $stateRepo,
    ) {}

    public function index()
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $stateList = states::orderBy('name', 'asc')->get();
        $countryList = Country::orderBy('name', 'asc')->get();
        $templecategory = TempleCategory::where('status', 1)->orderBy('name', 'asc')->get();
        $citiesList = cities::orderBy('city', 'asc')->get();
        $districtList = District::orderBy('name', 'asc')->get();
        $googleMapsApiKey =  config('services.google_maps.api_key');
        return view(TemplePath::ADD[VIEW], compact('languages', 'countryList', 'defaultLanguage', 'templecategory', 'stateList', 'googleMapsApiKey', 'citiesList','districtList'));
    }

    public function add_temple(TemplesAddRequest $request, TemplesService $templeService): RedirectResponse
    {
        if (!empty($request['country_id'])) {
            if (is_numeric($request['country_id'])) {
                $request['country_id'] = (int) $request['country_id'];
            } else {
                $getcountry = $this->countryRepo->getFirstWhere(params: ['name' => (trim($request['country_id']))]);
                if (!$getcountry) {
                    $insert = $this->countryRepo->add(data: ['name' => (trim($request['country_id'])), 'sortname' => $request['country_id_short_name']]);
                    $request['country_id'] = $insert->id;
                } else {
                    $request['country_id'] = $getcountry->id;
                }
            }
        }

        if (!empty($request['state_id'])) {
            if (is_numeric($request['state_id'])) {
                $request['state_id'] = (int) $request['state_id'];
            } else {
                $getstate = $this->stateRepo->getFirstWhere(params: ['name' => strtoupper(trim($request['state_id']))]);
                if (!$getstate) {
                    $insert = $this->stateRepo->add(data: ['name' => strtoupper(trim($request['state_id'])), 'country_id' => $request['country_id']]);
                    $request['state_id'] = $insert->id;
                } else {
                    $request['state_id'] = $getstate->id;
                }
            }
        }


        if (!empty($request['city_id'])) {
            if (is_numeric($request['city_id'])) {
                $request['city_id'] = (int) $request['city_id'];
            } else {
                $cityName = ucwords(trim($request['city_id']));
                $getcities = $this->citiesRepo->getFirstWhere(params: ['city' => $cityName]);
                if (!$getcities) {
                    $insert = $this->citiesRepo->add(data: ['city' => $cityName, 'country_id'  => $request['country_id'], 'state_id' => $request['state_id'], 'short_desc'  => '', 'description' => '', 'images'      => '', 'famous_for'  => '', 'latitude'    => $request['city_latitude'] ?? '', 'longitude'   => $request['city_longitude'] ?? '']);
                    $request['city_id'] = $insert->id;
                } else {
                    $request['city_id'] = $getcities->id;
                }
            }
        }

        $dataArray = $templeService->getAddTemplesData($request, addedBy: 'admin');
        $savedTemple = $this->templeRepo->add(data: $dataArray);
        // $this->templeRepo->addTemplesTags(request: $request, temple: $savedTemple);
        $this->translationRepo->add(request: $request, model: 'App\Models\Temple', id: $savedTemple->id);
        Helpers::editDeleteLogs('Temple', 'Temple', 'Insert');
        Toastr::success(translate('temple_added_successfully'));
        return redirect()->route('admin.temple.list');
    }

    public function getCities(Request $request, TemplesService $templeService): JsonResponse
    {
        $parentId = $request['id'];
        $filter = ['id' => $parentId];
        $citiesList = cities::where('state_id', $parentId)->get();

        $dropdown = $templeService->getStatesDropdown(request: $request, cities: $citiesList);


        $childStates = '';
        if (count($citiesList) == 1) {
            $subCities = $this->citiesRepo->getListWhere(filters: ['state_id' => $citiesList[0]['id']], dataLimit: 'all');
            $childStates = $templeService->getStatesDropdown(request: $request, cities: $subCities);
        }

        $districtList = District::where('state_id', $parentId)->get();
        $districtDropdown = '<option selected disabled>Select District</option>';
        foreach ($districtList as $district) {
            $districtDropdown .= '<option value="'.$district->id.'">'.$district->name.'</option>';
        }
        return response()->json([
            'select_tag' => $dropdown,
            'district_tag' => $districtDropdown,
            'sub_cities' => count($citiesList) == 1 ? $childStates : '',

        ], 200);
    }
    
    public function getDistricts(Request $request): JsonResponse
    {
        $stateId = $request->state_id;

        $districts = District::where('state_id', $stateId)->get();

        $html = '<option selected disabled>'.translate('select_District').'</option>';

        foreach ($districts as $district) {
            $html .= '<option value="'.$district->id.'">'.$district->name.'</option>';
        }

        return response()->json([
            'select_tag' => $html
        ]);
    }

    public function list(Request $request): Application|Factory|View
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $temple = $this->templeRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'), relations: ['cities', 'states']);
        // dd($temple);
        return view(TemplePath::LIST[VIEW], compact('temple', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data['status'] = $request->get('status', 0);
        $this->templeRepo->update(id: $request['id'], data: $data);

        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function updatePackageStatus(Request $request): JsonResponse
    {
        $data['package_status'] = $request->get('package_status', 0);
        $this->templeRepo->update(id: $request['id'], data: $data);

        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(string|int $id, TemplesService $templeService): RedirectResponse
    {
        $temple = $this->templeRepo->getFirstWhere(params: ['id' => $id]);
        if ($temple) {
            $this->translationRepo->delete(model: 'App\Models\Temple', id: $id);
            $templeService->deleteImages(temple: $temple);
            $this->templeRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('temple_removed_successfully'));
            Helpers::editDeleteLogs('Temple', 'Temple', 'Delete');
        } else {
            Toastr::error(translate('invalid_product'));
        }

        return back();
    }

    public function getUpdateView(Request $request, string|int $id): View|RedirectResponse
    {
        $temple = $this->templeRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);

        $citiesList = Cities::orderBy('city', 'asc')->get();
        $stateList = States::orderBy('name', 'asc')->get();
        $countryList = Country::orderBy('name', 'asc')->get();
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $districtList = District::orderBy('name', 'asc')->get();
        $defaultLanguage = $languages[0];
        $templecategory = TempleCategory::where('status', 1)->orderBy('name', 'asc')->get();
        $googleMapsApiKey =  config('services.google_maps.api_key');
        return view(TemplePath::UPDATE[VIEW], compact('temple', 'countryList', 'templecategory', 'googleMapsApiKey', 'citiesList', 'stateList', 'districtList','languages', 'defaultLanguage'));
    }

    // public function update(TemplesAddRequest $request, TemplesService $templeService, $id): RedirectResponse
    // {
    //     $temple = $this->templeRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
    //     if (!empty($request['country_id'])) {
    //         if (is_numeric($request['country_id'])) {
    //             $request['country_id'] = (int) $request['country_id'];
    //         } else {
    //             $getcountry = $this->countryRepo->getFirstWhere(params: ['name' => (trim($request['country_id']))]);
    //             if (!$getcountry) {
    //                 $insert = $this->countryRepo->add(data: ['name' => (trim($request['country_id'])), 'sortname' => $request['country_id_short_name']]);
    //                 $request['country_id'] = $insert->id;
    //             } else {
    //                 $request['country_id'] = $getcountry->id;
    //             }
    //         }
    //     }

    //     if (!empty($request['state_id'])) {
    //         if (is_numeric($request['state_id'])) {
    //             $request['state_id'] = (int) $request['state_id'];
    //         } else {
    //             $getstate = $this->stateRepo->getFirstWhere(params: ['name' => strtoupper(trim($request['state_id']))]);
    //             if (!$getstate) {
    //                 $insert = $this->stateRepo->add(data: ['name' => strtoupper(trim($request['state_id'])), 'country_id' => $request['country_id']]);
    //                 $request['state_id'] = $insert->id;
    //             } else {
    //                 $request['state_id'] = $getstate->id;
    //             }
    //         }
    //     }


    //     if (!empty($request['city_id'])) {
    //         if (is_numeric($request['city_id'])) {
    //             $request['city_id'] = (int) $request['city_id'];
    //         } else {
    //             $cityName = ucwords(trim($request['city_id']));
    //             $getcities = $this->citiesRepo->getFirstWhere(params: ['city' => $cityName]);
    //             if (!$getcities) {
    //                 $insert = $this->citiesRepo->add(data: ['city' => $cityName, 'country_id'  => $request['country_id'], 'state_id' => $request['state_id'], 'short_desc'  => '', 'description' => '', 'images'      => '', 'famous_for'  => '', 'latitude'    => $request['city_latitude'] ?? '', 'longitude'   => $request['city_longitude'] ?? '']);
    //                 $request['city_id'] = $insert->id;
    //             } else {
    //                 $request['city_id'] = $getcities->id;
    //             }
    //         }
    //     }
    //     $vipDarshan = [];

    //     $serviceTax = \App\Models\ServiceTax::first();
    //     $platform_gst_percent = $serviceTax ? $serviceTax->platform_fee : 0;

    //     if (isset($request['vipdarshan']) && $request['vipdarshan'] && count($request['vipdarshan']) > 0) {
    //         $vi_id = 0;
    //         foreach ($request['vipdarshan'] as $vips) {
    //             if ($vips['name']) {
    //                 $vipDarshan[$vi_id]['id'] = $vi_id + 1;
    //                 $vipDarshan[$vi_id]['name'] = $vips['name'];
    //                 $vipDarshan[$vi_id]['description'] = $vips['description'];
    //                 if (isset($vips['children']) && $vips['children'] && count($vips['children']) > 0) {
    //                     $ch1 = 0;
    //                     foreach ($vips['children'] as $vi_ch) {
    //                         if ($vi_ch['name'] && ($vi_ch['price'] >= 0)) {
    //                             $vipDarshan[$vi_id]['package'][$ch1]['id'] = $ch1 + 1;
    //                             $vipDarshan[$vi_id]['package'][$ch1]['name'] = $vi_ch['name'];
    //                             $vipDarshan[$vi_id]['package'][$ch1]['price'] = $vi_ch['price'];
    //                             $vipDarshan[$vi_id]['package'][$ch1]['limit'] = $vi_ch['limit']??0;
    //                             $vipDarshan[$vi_id]['package'][$ch1]['today_price'] = $vi_ch['today_price'] ?? ($vi_ch['price']??0);
    //                             $vipDarshan[$vi_id]['package'][$ch1]['receipt_price'] = $vi_ch['receipt_price']??0;
    //                             $vipDarshan[$vi_id]['package'][$ch1]['platform_fee'] = $vi_ch['platform_fee']??0;

    //                             $vipDarshan[$vi_id]['package'][$ch1]['platform_fee'] = (string) ($vi_ch['platform_fee'] ?? 0);
    //                             $platform_fee_value = (float) $vipDarshan[$vi_id]['package'][$ch1]['platform_fee'];

    //                             $platform_gst_amount   = round(($platform_fee_value * $platform_gst_percent) / 100, 2);
    //                             $platform_base_price   = round($platform_fee_value - $platform_gst_amount, 2);

    //                             $vipDarshan[$vi_id]['package'][$ch1]['platform_gst']        = number_format($platform_gst_amount, 2, '.', '');
    //                             $vipDarshan[$vi_id]['package'][$ch1]['platform_base_price'] = number_format($platform_base_price, 2, '.', '');

    //                             if (isset($vi_ch['include']) && $vi_ch['include'] && count($vi_ch['include']) > 0) {
    //                                 $ch_inc1 = 0;
    //                                 foreach ($vi_ch['include'] as $vi_inc) {
    //                                     if ($vi_inc['name']) {
    //                                         $vipDarshan[$vi_id]['package'][$ch1]['include'][$ch_inc1]['id'] = $ch_inc1 + 1;
    //                                         $vipDarshan[$vi_id]['package'][$ch1]['include'][$ch_inc1]['name'] = $vi_inc['name'];
    //                                         $ch_inc1++;
    //                                     }
    //                                 }
    //                             }
    //                             if (isset($vi_ch['subchildren']) && count($vi_ch['subchildren']) > 0) {
    //                                 $ch_last = 0;
    //                                 foreach ($vi_ch['subchildren'] as $vi_onl) {
    //                                     if ($vi_onl['start_time'] && $vi_onl['end_time']) {
    //                                         $vipDarshan[$vi_id]['package'][$ch1]['date'][$ch_last]['id'] = $ch_last + 1;
    //                                         $vipDarshan[$vi_id]['package'][$ch1]['date'][$ch_last]['time'] = date('h:i A', strtotime($vi_onl['start_time'])) . ' - ' . date('h:i A', strtotime($vi_onl['end_time']));
    //                                         $ch_last++;
    //                                     }
    //                                 }
    //                             }
    //                             $ch1++;
    //                         }
    //                     }
    //                 }
    //                 $vi_id++;
    //             }
    //         }
    //     }
    //     $dataArray = $templeService->getUpdateTempleData(request: $request, temple: $temple, updateBy: 'admin');
    //     $dataArray['vip_plans'] = json_encode($vipDarshan);
    //     $this->templeRepo->update(id: $id, data: $dataArray);
    //     $this->translationRepo->update(request: $request, model: 'App\Models\Temple', id: $id);
    //     Helpers::editDeleteLogs('Temple', 'Temple', 'Update');
    //     Toastr::success(translate('temple_updated_successfully'));
    //     return redirect()->route('admin.temple.list');
    // }

    public function update(TemplesAddRequest $request, TemplesService $templeService, $id): RedirectResponse
    {
        $temple = $this->templeRepo->getFirstWhereWithoutGlobalScope(
            params: ['id' => $id],
            relations: ['translations']
        );

        // Country
        if (!empty($request['country_id'])) {
            if (is_numeric($request['country_id'])) {
                $request['country_id'] = (int) $request['country_id'];
            } else {
                $getcountry = $this->countryRepo->getFirstWhere(params: ['name' => trim($request['country_id'])]);
                if (!$getcountry) {
                    $insert = $this->countryRepo->add(data: [
                        'name'     => trim($request['country_id']),
                        'sortname' => $request['country_id_short_name']
                    ]);
                    $request['country_id'] = $insert->id;
                } else {
                    $request['country_id'] = $getcountry->id;
                }
            }
        }

        // State
        if (!empty($request['state_id'])) {
            if (is_numeric($request['state_id'])) {
                $request['state_id'] = (int) $request['state_id'];
            } else {
                $getstate = $this->stateRepo->getFirstWhere(params: ['name' => strtoupper(trim($request['state_id']))]);
                if (!$getstate) {
                    $insert = $this->stateRepo->add(data: [
                        'name'       => strtoupper(trim($request['state_id'])),
                        'country_id' => $request['country_id']
                    ]);
                    $request['state_id'] = $insert->id;
                } else {
                    $request['state_id'] = $getstate->id;
                }
            }
        }

        // City
        if (!empty($request['city_id'])) {
            if (is_numeric($request['city_id'])) {
                $request['city_id'] = (int) $request['city_id'];
            } else {
                $cityName  = ucwords(trim($request['city_id']));
                $getcities = $this->citiesRepo->getFirstWhere(params: ['city' => $cityName]);
                if (!$getcities) {
                    $insert = $this->citiesRepo->add(data: [
                        'city'       => $cityName,
                        'country_id' => $request['country_id'],
                        'state_id'   => $request['state_id'],
                        'short_desc' => '',
                        'description' => '',
                        'images'     => '',
                        'famous_for' => '',
                        'latitude'   => $request['city_latitude'] ?? '',
                        'longitude'  => $request['city_longitude'] ?? ''
                    ]);
                    $request['city_id'] = $insert->id;
                } else {
                    $request['city_id'] = $getcities->id;
                }
            }
        }

        $vipDarshan = [];

        // Platform GST % from service_tax table
        $serviceTax            = \App\Models\ServiceTax::first();
        $platform_gst_percent  = $serviceTax ? $serviceTax->platform_fee : 0;

        if (!empty($request['vipdarshan']) && count($request['vipdarshan']) > 0) {
            $vi_id = 0;
            foreach ($request['vipdarshan'] as $vips) {
                if ($vips['name']) {
                    $vipDarshan[$vi_id]['id']          = $vi_id + 1;
                    $vipDarshan[$vi_id]['name']        = $vips['name'];
                    $vipDarshan[$vi_id]['description'] = $vips['description'] ?? null;

                    if (!empty($vips['children'])) {
                        $ch1 = 0;
                        foreach ($vips['children'] as $vi_ch) {
                            if ($vi_ch['name'] && isset($vi_ch['price']) && $vi_ch['price'] >= 0) {
                                $vipDarshan[$vi_id]['package'][$ch1]['id']            = $ch1 + 1;
                                $vipDarshan[$vi_id]['package'][$ch1]['name']          = $vi_ch['name'];
                                $vipDarshan[$vi_id]['package'][$ch1]['price']         = $vi_ch['price'];
                                $vipDarshan[$vi_id]['package'][$ch1]['limit']         = $vi_ch['limit'] ?? 0;
                                $vipDarshan[$vi_id]['package'][$ch1]['today_price']   = $vi_ch['today_price'] ?? $vi_ch['price'];
                                $vipDarshan[$vi_id]['package'][$ch1]['receipt_price'] = $vi_ch['receipt_price'] ?? 0;

                                // Platform Fee calculation
                                $platform_fee_value  = (float) ($vi_ch['platform_fee'] ?? 0);
                                $platform_gst_amount = round(($platform_fee_value * $platform_gst_percent) / 100, 2);
                                $platform_base_price = round($platform_fee_value - $platform_gst_amount, 2);

                                $vipDarshan[$vi_id]['package'][$ch1]['platform_fee']        = number_format($platform_fee_value, 2, '.', '');
                                $vipDarshan[$vi_id]['package'][$ch1]['platform_gst']        = number_format($platform_gst_amount, 2, '.', '');
                                $vipDarshan[$vi_id]['package'][$ch1]['platform_base_price'] = number_format($platform_base_price, 2, '.', '');

                                // Include
                                if (!empty($vi_ch['include'])) {
                                    $ch_inc1 = 0;
                                    foreach ($vi_ch['include'] as $vi_inc) {
                                        if ($vi_inc['name']) {
                                            $vipDarshan[$vi_id]['package'][$ch1]['include'][$ch_inc1]['id']   = $ch_inc1 + 1;
                                            $vipDarshan[$vi_id]['package'][$ch1]['include'][$ch_inc1]['name'] = $vi_inc['name'];
                                            $ch_inc1++;
                                        }
                                    }
                                }

                                // Dates
                                if (!empty($vi_ch['subchildren'])) {
                                    $ch_last = 0;
                                    foreach ($vi_ch['subchildren'] as $vi_onl) {
                                        if ($vi_onl['start_time'] && $vi_onl['end_time']) {
                                            $vipDarshan[$vi_id]['package'][$ch1]['date'][$ch_last]['id']   = $ch_last + 1;
                                            $vipDarshan[$vi_id]['package'][$ch1]['date'][$ch_last]['time'] = date('h:i A', strtotime($vi_onl['start_time'])) . ' - ' . date('h:i A', strtotime($vi_onl['end_time']));
                                            $ch_last++;
                                        }
                                    }
                                }
                                $ch1++;
                            }
                        }
                    }
                    $vi_id++;
                }
            }
        }

        $dataArray                = $templeService->getUpdateTempleData(request: $request, temple: $temple, updateBy: 'admin');
        $dataArray['vip_plans']   = json_encode($vipDarshan);

        $this->templeRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\Temple', id: $id);

        Helpers::editDeleteLogs('Temple', 'Temple', 'Update');
        Toastr::success(translate('temple_updated_successfully'));

        return redirect()->route('admin.temple.list');
    }

    public function review_list(Request $request)
    {
        $getData = $this->templereviewRepo->getListWhere(relations: ['userData', 'templeData'], orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TemplePath::REVIEW[VIEW], compact('getData'));
    }

    public function review_status(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->templereviewRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function review_delete(TemplesService $service, $id)
    {
        $old_data = $this->templereviewRepo->getFirstWhere(params: ['id' => $id]);
        if (!empty($old_data['image'])) {
            $service->locationRemove($old_data['image']);
        }
        $savedCities = $this->templereviewRepo->delete(params: ['id' => $id]);
        Toastr::success(translate('Review_Deleted_successfully'));
        return redirect()->route(TemplePath::REVIEW[REDIRECT]);
    }


    public function DarshanLeads(Request $request)
    {
        $darshanlist = TempleDarshanLead::where('status', 0)->with(['Temple', 'userData', 'followby'])
            ->when($request->searchValue, function ($query) use ($request) {
                $query->where('lead_id', 'like', "%$request->searchValue%");
                $query->orWhere('name', 'like', "%$request->searchValue%");
                $query->orWhere('phone', 'like', "%$request->searchValue%");
                $query->orWhere('title', 'like', "%$request->searchValue%");
                $query->orWhere('package_name', 'like', "%$request->searchValue%");
                $query->orWhereHas('Temple', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                });
                $query->orWhereHas('userData', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                    $q->orWhere('phone', 'like', "%$request->searchValue%");
                });
            })->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));
        return view(TemplePath::DARSHANLEAD[VIEW], compact('darshanlist'));
    }

    public function DarshanLeadDelete(Request $request)
    {
        $lead = TempleDarshanLead::find($request['id']);
        if ($lead) {
            $lead->delete();
            Toastr::success(translate('lead_Delete_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }
    public function DarshanLeadsFollow(Request $request)
    {
        $followlist = DarshanFollowup::where('lead_id', $request['id'])->get();
        if ($followlist) {
            return response()->json($followlist);
        } else {
            return response()->json([], 200);
        }
    }
    public function DarshanLeadsFollowUp(Request $request)
    {
        $follows = [
            'lead_id' => $request->input('lead_id'),
            'message' => $request->input('message'),
            'last_date' => $request->input('last_date'),
            'next_date' => $request->input('next_date'),
            'follow_by' => $request->input('follow_by'),
            'follow_by_id' => $request->input('follow_by_id'),
        ];
        DarshanFollowup::create($follows);
        Toastr::success(translate('lead_follow_up_successfully'));
        return back();
    }

    public function DarshanSendWhatsappLead(Request $request)
    {
        $lead = TempleDarshanLead::where('id', $request['id'])->first();
        if ($lead) {
            $poojaName = Temple::where('id', $lead['temple_id'])->first();
            $message_data = [
                'title' => $lead['title'],
                'package_name' => $lead['package_name'],
                'price' => $lead['price'],
                'type' => 'text-with-media',
                'attachment' =>  getValidImage(path: 'storage/app/public/temple/thumbnail/' . $poojaName['thumbnail'], type: 'backend-product'),
                'link' => route('temple-details', ['slug' => $poojaName['slug']]),
                'customer_id' => ($lead['user_id'] ?? ""),
            ];
            $messages =  Helpers::whatsappMessage('vipdarshan', 'Lead Message', $message_data);
            TempleDarshanLead::where('id', $request['id'])->increment('whatsapp_hit');
            Toastr::success(translate('message_sent_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function VipDarshanBooking(Request $request)
    {
        $darshanlist = DarshanOrder::where('status', 1)->with(['Temple', 'userData'])
            ->when($request->searchValue, function ($query) use ($request) {
                $query->orWhere('order_id', 'like', "%$request->searchValue%");
                $query->orWhere('title', 'like', "%$request->searchValue%");
                $query->orWhere('package_name', 'like', "%$request->searchValue%");
                $query->orWhereHas('Temple', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                });
                $query->orWhereHas('userData', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                    $q->orWhere('phone', 'like', "%$request->searchValue%");
                });
            })->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));


        return view(TemplePath::VIPDARSHANBOOKING[VIEW], compact('darshanlist'));
    }

    public function VipDarshanBookingInfo(Request $request)
    {
        $getData = DarshanOrder::where('id', $request['id'])->with(['Temple', 'userData', 'Members'])->first();
        return view(TemplePath::VIPDARSHANBOOKINGINFO[VIEW], compact('getData'));
    }

    public function temple_package(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $packages = TempleServicePackages::latest()->get();
        $editPackage = null;
        if ($request->id) {
            $editPackage = TempleServicePackages::find($request->id);
        }
        return view('admin-views.temple.packages.temple-package', compact('languages', 'defaultLanguage', 'packages', 'editPackage'));
    }

    public function store_package_master(Request $request, TemplesService $templeService): RedirectResponse
    {
        if ($request->id) {
            $dataArray = $templeService->getUpdateTemplePackageData(
                request: $request,
                updateBy: 'admin'
            );
            $this->templeRepo->editpackage(id: $request->id, data: $dataArray);

            $this->translationRepo->update(
                request: $request,
                model: 'App\Models\TempleServicePackages',
                id: $request->id
            );
            Toastr::success(translate('temple_updated_successfully'));
        } else {
            //  Create Case
            $dataArray = $templeService->getAddTemplesPackageData($request, addedBy: 'admin');
            $savedTemple = $this->templeRepo->addpackage(data: $dataArray);

            $this->translationRepo->add(
                request: $request,
                model: 'App\Models\TempleServicePackages',
                id: $savedTemple->id
            );

            Toastr::success(translate('temple_package_successfully'));
        }

        return redirect()->route('admin.temple.templepackage');
    }

    public function delete_package_master($id): RedirectResponse
    {
        try {
            $package = TempleServicePackages::findOrFail($id);
            $package->status = 0;
            $package->save();
            Toastr::success(translate('temple_package_deleted_successfully'));
        } catch (\Exception $e) {
            Toastr::error(translate('something_went_wrong'));
        }

        return redirect()->route('admin.temple.templepackage');
    }

    public function update_package_status(Request $request, $id): RedirectResponse
    {
        try {
            $package = TempleServicePackages::findOrFail($id);

            // Get value from request (0 or 1)
            $package->status = $request->input('status');
            $package->save();

            Toastr::success(translate('temple_package_status_updated_successfully'));
        } catch (\Exception $e) {
            Toastr::error(translate('something_went_wrong'));
        }

        return redirect()->back();
    }

    public function update_variant_status(Request $request, $id): RedirectResponse
    {
        try {
            $variant = TempleServicePrice::findOrFail($id);

            // Get value from request (0 or 1)
            $variant->status = $request->input('status');
            $variant->save();

            Toastr::success(translate('temple_variant_status_updated_successfully'));
        } catch (\Exception $e) {
            Toastr::error(translate('something_went_wrong'));
        }

        return redirect()->back();
    }
    public function package_temple_create($id, Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $temple = Temple::findOrFail($id);
        $allServices = TempleServicePackages::where('status', 1)->get();
        $selectedServices = $temple->package_service ? json_decode($temple->package_service, true) : [];
        $editPackage = null;
        if ($request->id) {
            $editPackage = TempleServicePackages::find($request->id);
        }
        $selectedIds = collect($selectedServices)->pluck('id')->toArray();
        $filteredServices = $allServices->whereIn('id', $selectedIds);
        $gstRate = ServiceTax::value('temple_service_tax') ?? 0;
        return view('admin-views.temple.packages.add-package', compact('gstRate', 'filteredServices', 'languages', 'defaultLanguage', 'allServices', 'selectedServices', 'temple', 'editPackage', 'selectedIds'));
    }

    public function savepackage_services(Request $request, $id)
    {
        $temple = Temple::findOrFail($id);
        $newServices = [];
        if ($request->has('package_service') && is_array($request->package_service)) {
            foreach ($request->package_service as $serviceId) {
                $service = \App\Models\TempleServicePackages::find($serviceId);
                if ($service) {
                    $newServices[] = [
                        'id' => $service->id,
                        'name' => $service->name,
                        'status' => 1
                    ];
                }
            }
        }
        $temple->update([
            'package_service' => json_encode($newServices)
        ]);
        Toastr::success(translate('services_updated_successfully'));
        return redirect()->back();
    }

    public function savepackage_price(Request $request, TemplesService $templeService): RedirectResponse
    {
        // 1. save temple_service_prices
        $dataArray = $templeService->getAddTemplesPackagePriceData($request, addedBy: 'admin');
        $savedTemplePrice = $this->templeRepo->addpackageprice(data: $dataArray);

        // 2. translations
        $this->translationRepo->add(
            request: $request,
            model: 'App\Models\TempleServicePrice',
            id: $savedTemplePrice->id
        );

        // 3. if slots are available
        if ($request->is_available == 1 && $request->has('day_of_week')) {
            foreach ($request->day_of_week as $weekIndex => $day) {
                if (!empty($day)) {
                    $startTimes = $request->start_time[$weekIndex] ?? [];
                    $endTimes   = $request->end_time[$weekIndex] ?? [];
                    $capacities = $request->slots_limi_capacity[$weekIndex] ?? [];
                    foreach ($startTimes as $i => $st) {
                        if (!empty($st) && !empty($endTimes[$i]) && !empty($capacities[$i])) {
                            \App\Models\TempleServiceSlot::create([
                                'temple_service_prices_id' => $savedTemplePrice->id,
                                'day_of_week'              => $day,
                                'start_time'               => $st,
                                'end_time'                 => $endTimes[$i],
                                'slots_limi_capacity'      => $capacities[$i],
                            ]);
                        }
                    }
                }
            }
        }

        Toastr::success(translate('package_services_price_create_successfully'));
        return redirect()->back();
    }

    public function temple_package_price()
    {
        $temples = Temple::with(['servicePrices.slots', 'servicePrices.servicePackage', 'Trust'])->get();
        return view('admin-views.temple.packages.temple-package-price', compact('temples'));
    }
    public function package_editprice($priceId, $templeId, Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? [];
        $defaultLanguage = $languages[0] ?? null;
        $temple = Temple::findOrFail($templeId);
        $packagePrice = TempleServicePrice::findOrFail($priceId);
        $packageSlotEditData = TempleServiceSlot::where('temple_service_prices_id', $priceId)
            ->where('status', 1)
            ->get();
        $editPackage = null;
        if (!empty($packagePrice->package_id)) {
            $editPackage = TempleServicePackages::find($packagePrice->package_id);
        }
        $selectedIds = $packagePrice->package_id ? [$packagePrice->package_id] : [];
        $filteredServices = TempleServicePackages::where('status', 1)->get();
        $gstRate = ServiceTax::value('temple_service_tax') ?? 0;
        return view('admin-views.temple.packages.edit-package', compact(
            'gstRate',
            'filteredServices',
            'languages',
            'defaultLanguage',
            'packagePrice',
            'packageSlotEditData',
            'temple',
            'editPackage',
            'selectedIds'
        ));
    }


    public function editpackage_price($priceId, $templeId, Request $request, TemplesService $templeService): RedirectResponse
    {
        // 1. Data prepare karo
        $dataArray = $templeService->getEditTemplesPackagePriceData($request, addedBy: 'admin');

        // 2. Repo se update + model return
        $savedTemplePrice = $this->templeRepo->editpackageprice(id: $priceId, data: $dataArray);

        if ($savedTemplePrice) {
            // 3. Translations update
            $this->translationRepo->update(
                request: $request,
                model: 'App\Models\TempleServicePrice',
                id: $savedTemplePrice->id
            );

            // 4. Slots update if available
            if ($request->is_available == 1 && $request->has('day_of_week')) {

                // Purane slots fetch
                $existingSlots = TempleServiceSlot::where('temple_service_prices_id', $savedTemplePrice->id)->get();
                $existingIds   = $existingSlots->pluck('id')->toArray();

                $requestIds = [];

                foreach ($request->day_of_week as $weekIndex => $day) {
                    if (!empty($day)) {
                        $startTimes = $request->start_time[$weekIndex] ?? [];
                        $endTimes   = $request->end_time[$weekIndex] ?? [];
                        $capacities = $request->slots_limi_capacity[$weekIndex] ?? [];
                        $slotIds    = $request->slot_id[$weekIndex] ?? [];

                        foreach ($startTimes as $i => $st) {
                            if (!empty($st) && !empty($endTimes[$i]) && !empty($capacities[$i])) {
                                $slotId = $slotIds[$i] ?? null;

                                if ($slotId && in_array($slotId, $existingIds)) {
                                    $slot = TempleServiceSlot::find($slotId);
                                    if ($slot) {
                                        $slot->update([
                                            'day_of_week'         => $day,
                                            'start_time'          => $st,
                                            'end_time'            => $endTimes[$i],
                                            'slots_limi_capacity' => $capacities[$i],
                                        ]);
                                        $requestIds[] = $slotId;
                                    }
                                } else {
                                    //  Create new slot
                                    $newSlot = TempleServiceSlot::create([
                                        'temple_service_prices_id' => $savedTemplePrice->id,
                                        'day_of_week'              => $day,
                                        'start_time'               => $st,
                                        'end_time'                 => $endTimes[$i],
                                        'slots_limi_capacity'      => $capacities[$i],
                                    ]);
                                    $requestIds[] = $newSlot->id;
                                }
                            }
                        }
                    }
                }

                //  Delete slots jo request me nahi aaye
                $toDelete = array_diff($existingIds, $requestIds);
                if (!empty($toDelete)) {
                    TempleServiceSlot::whereIn('id', $toDelete)->delete();
                }
            }

            Toastr::success(translate('package_services_price_updated_successfully'));
        } else {
            Toastr::error(translate('package_services_price_update_failed'));
        }
        return redirect()->route('admin.temple.templepackageprice');
    }


    public function TemplePanditWithdrawal()
    {
        return view('admin-views.temple.pandit.withdrawal');
    }

    public function TemplePanditWithdrawalFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('searchValue', '');
        $temple_id = $request->input('temple_id', '');
        $pandit_id = $request->input('pandit_id', '');
        $trust_id = $request->input('trust_id', '');
        $status = $request->input('status', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $columnName = $request->input("columns.$orderColumnIndex.data");
        $querys = PanditTransectionHistory::with(['Trusts', 'Temple', 'Pandit']);
        $querys->when(!empty($searchValue), function ($qu1) use ($searchValue) {
            $qu1->whereHas('Trusts', function ($q2) use ($searchValue) {
                $q2->where('trust_name', 'like', "%$searchValue%");
            });
            $qu1->orWhereHas('Temple', function ($q3) use ($searchValue) {
                $q3->where('name', 'like', "%$searchValue%");
            });
            $qu1->orWhereHas('Pandit', function ($q4) use ($searchValue) {
                $q4->where('name', 'like', "%$searchValue%");
            });
        })
            ->when(!empty($temple_id), function ($query) use ($temple_id) {
                $query->where('temple_id', $temple_id);
            })
            ->when(!empty($trust_id), function ($query) use ($trust_id) {
                $query->where('trust_id', $trust_id);
            })
            ->when(!empty($pandit_id), function ($query) use ($pandit_id) {
                $query->where('purohit_id', $pandit_id);
            })
            ->when(!empty($status), function ($query) use ($status) {
                $query->where('status', $status);
            });
        $recordsTotal =  PanditTransectionHistory::with(['Trusts', 'Temple', 'Pandit'])
            ->when(!empty($temple_id), function ($query) use ($temple_id) {
                $query->where('temple_id', $temple_id);
            })
            ->when(!empty($trust_id), function ($query) use ($trust_id) {
                $query->where('trust_id', $trust_id);
            })
            ->when(!empty($pandit_id), function ($query) use ($pandit_id) {
                $query->where('purohit_id', $pandit_id);
            })
            ->when(!empty($status), function ($query) use ($status) {
                $query->where('status', $status);
            })->count();

        $recordsFiltered = $querys->count();
        $data = $querys->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $formattedData = $data->map(function ($item, $key) use ($start) {

            $options = '<div class="d-flex justify-content-center gap-2">                                            
                                            <a href="' . route('admin.temple.temple-pandit-withdrawal-view', ['id' => $item['id']]) . '"
                                                class="btn btn--primary btn-sm"
                                                data-toggle="tooltip" aria-label="Edit"
                                                data-bs-original-title="Edit"><i class="tio-invisible"></i></a>';

            $status = '';
            if ($item['status'] == 0) {
                $status = '<span class="badge badge-soft-warning">Processing..</span>';
            } elseif ($item['status'] == 1) {
                $status = '<span class="badge badge-soft-success">Complete</span>';
            } else {
                $status = '<span class="badge badge-soft-danger">Failed</span>';
            }

            return [
                'id' => $start + $key + 1,
                'temple_name' => "<span class='font-weight-bolder'>" . $item['Trusts']['trust_name'] . "</span> <br>" . $item['Temple']['name'],
                'pandit_name' =>  $item['Pandit']['name'],
                'create_by' => date('d M,Y h:i A', strtotime($item['created_at'])),
                'request_amount' => $item['debit'],
                'option' => $options,
                'status' => $status,
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData
        ]);
    }

    public function TemplePanditWithdrawalView(Request $request)
    {
        $withdrawRequests = \App\Models\PanditTransectionHistory::with(['Trusts', 'Temple', 'Pandit'])->where('id', $request['id'])->first();
        return view('admin-views.temple.pandit.view', compact('withdrawRequests'));
    }

    public function TemplePanditWithdrawalReject(Request $request)
    {
        $withdrawRequests = \App\Models\PanditTransectionHistory::where('id', $request['id'])->first();
        if ($withdrawRequests) {
            \App\Models\Purohit::where('id', $request['id'])->update(['requested_amount' => 0]);
            \App\Models\PanditTransectionHistory::where('id', $request['id'])->update(['status' => 2]);
            Toastr::success('pay_Request_Reject Successfully');
            return back();
        }
        Toastr::success('pay_Request_Reject Failed');
        return back();
    }

    public function TemplePanditWithdrawalapproval(Request $request, $id, $type)
    {

        $get_Razorpay = \App\Models\Setting::where(['key_name' => 'razor_pay'])->first();
        $RAZORPAY_KEY_ID = '';
        $RAZORPAY_KEY_SECRET = '';
        $RAZORPAY_ACCOUNT_NUMBER = '';
        if ($get_Razorpay['mode'] == 'live') {
            $RAZORPAY_KEY_ID = $get_Razorpay['live_values']['api_key'];
            $RAZORPAY_KEY_SECRET = $get_Razorpay['live_values']['api_secret'];
            $RAZORPAY_ACCOUNT_NUMBER = $get_Razorpay['live_values']['account_number'] ?? '';
        } else {
            $RAZORPAY_KEY_ID = $get_Razorpay['live_values']['api_key'];
            $RAZORPAY_KEY_SECRET = $get_Razorpay['live_values']['api_secret'];
            $RAZORPAY_ACCOUNT_NUMBER = $get_Razorpay['live_values']['account_number'] ?? '';
        }
        $api = new \Razorpay\Api\Api($RAZORPAY_KEY_ID, $RAZORPAY_KEY_SECRET);
        $getWithdrawal_recode = \App\Models\PanditTransectionHistory::with(['Trusts', 'Temple', 'Pandit'])->where('id', $id)->first();
        $email = $getWithdrawal_recode['Pandit']['email'] ?? "";
        $contact = $getWithdrawal_recode['Pandit']['mobile'];
        $url = "https://api.razorpay.com/v1/contacts";
        $data = [
            "name" => $getWithdrawal_recode['Pandit']['name'],
            "email" => $email,
            "contact" => $contact,
            "type" => "pandit"
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
                    "name" => $getWithdrawal_recode['bank_holder_name'],
                    "ifsc" => $getWithdrawal_recode['ifsc_code'],
                    "account_number" => $getWithdrawal_recode['account_number']
                ]
            ]);
        } elseif ($type == 'manual') {
            \App\Models\Purohit::where('id', $getWithdrawal_recode['purohit_id'])->update(['requested_amount' => 0, 'collected_amount' => \Illuminate\Support\Facades\DB::raw('collected_amount + ' . $getWithdrawal_recode['debit']), "withdrawal_amount" => \Illuminate\Support\Facades\DB::raw('withdrawal_amount - ' . $getWithdrawal_recode['debit'])]);

            \App\Models\PanditTransectionHistory::where('id', $id)->update([
                'status' => 1,
                'pay_transactionid' => $request['transcation_id'] ?? '',
                'transfer_by' => 'admin',
                'transaction_type' => 'manual'
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
            'account_number' => $RAZORPAY_ACCOUNT_NUMBER,
            'fund_account_id' => $fund_account_id,
            'amount' => 100,
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
        }
        curl_close($ch);
        if ($httpCode == 200 || $httpCode == 201) {
            \App\Models\Purohit::where('id', $getWithdrawal_recode['purohit_id'])->update(['requested_amount' => 0, 'collected_amount' => \Illuminate\Support\Facades\DB::raw('collected_amount + ' . $getWithdrawal_recode['debit']), "withdrawal_amount" => \Illuminate\Support\Facades\DB::raw('withdrawal_amount - ' . $getWithdrawal_recode['debit'])]);
            \App\Models\PanditTransectionHistory::where('id', $id)->update(['status' => 1]);
            Toastr::success('Payment transferred successfully');
            return back();
        } else {
             \App\Models\Purohit::where('id', $getWithdrawal_recode['purohit_id'])->update(['requested_amount' => 0]);           
            \App\Models\PanditTransectionHistory::where('id', $id)->update(['status' => 2]);
            Toastr::error('Failed to payouts');
            return back();
        }
    }
}
