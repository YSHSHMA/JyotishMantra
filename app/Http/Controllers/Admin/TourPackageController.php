<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\TourPackagesRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\TourPackagePath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourPackageRequest;
use App\Services\TourPackageService;
use App\Traits\FileManagerTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TourPackageController extends Controller
{

    use FileManagerTrait;
    public function __construct(
        private readonly TranslationRepositoryInterface      $translationRepo,
        private readonly TourPackagesRepositoryInterface  $tourpackage,
    ) {}

    public function PackageList(Request $request)
    {
        $getData = $this->tourpackage->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(TourPackagePath::ADDPACKAGE[VIEW], compact('getData', 'defaultLanguage', 'languages'));
    }

    public function PackageAdd(TourPackageRequest $request, TourPackageService $service)
    {
        $dataArray = $service->getAddTourData($request);
        $insert = $this->tourpackage->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\TourPackage', id: $insert->id);
        Toastr::success(translate('Tour_Package_added_successfully'));
        return redirect()->route(TourPackagePath::ADDPACKAGE[REDIRECT]);
    }

    public function PackageStatus(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->tourpackage->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function PackageUpdate($id)
    {
        $getData = $this->tourpackage->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(TourPackagePath::PACKAGEUPDATE[VIEW], compact('getData', 'defaultLanguage', 'languages'));
    }

    public function PackageEdit(TourPackageRequest $request, TourPackageService $service)
    {
        $getold = $this->tourpackage->getFirstWhere(params: ['id' => $request->id]);
        $dataArray = $service->getUpdateTourData($request, $getold);
        $this->tourpackage->update(id: $request->id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\TourPackage', id: $request->id);
        Toastr::success(translate('Tour_Package_updated_successfully'));
        return redirect()->route(TourPackagePath::ADDPACKAGE[REDIRECT]);
    }

    public function PackageDelete(Request $request, TourPackageService $service)
    {
        $old_data = $this->tourpackage->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            $service->deleteImage($old_data);
            $this->tourpackage->delete(params: ['id' => $request['id']]);
            $this->translationRepo->delete('App\Models\TourPackage', $request['id']);
            Toastr::success(translate('Tour_Package_Deleted_successfully'));
            return response()->json(['success' => 1, 'message' => translate('Tour_Package_deleted_successfully')], 200);
        } else {
            Toastr::error(translate('Tour_Package_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Not_found_data')], 400);
        }
    }

    public function HotelPackageAdd(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'hotel_name' => 'required|string|max:255|unique:tour_hotel_package,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        try {
            $id = DB::table('tour_hotel_package')->insertGetId([
                'name' => $request->hotel_name
            ]);

            return response()->json(['success' => true, 'message' => 'Hotel added successfully', 'id' => $id, 'name' => $request->hotel_name]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function getHotelspackage()
    {
        $hotels = DB::table('tour_hotel_package')->orderBy('id', 'desc')->get();
        return response()->json($hotels);
    }
    public function deleteHotelspackage(Request $request)
    {
        DB::table('tour_hotel_package')->where('id', $request['id'])->delete();
        return response()->json(['success' => true, 'message' => 'Hotel deleted']);
    }
}
