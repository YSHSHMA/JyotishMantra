<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\HotelsRepositoryInterface;
use App\Contracts\Repositories\HotelsReviewRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\HotelsEnums;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HotelAddRequest;
use App\Http\Requests\Admin\HotelupdateRequest;
use App\Http\Requests\Admin\TempleCategoryAddRequest;
use App\Models\Cities;
use App\Models\Country;
use App\Models\States;
use App\Services\HotelService;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HotelController extends Controller
{

    use FileManagerTrait;
    public function __construct(
        private readonly HotelsRepositoryInterface       $hotelRepo,
        private readonly TranslationRepositoryInterface      $translationRepo,
        private readonly HotelsReviewRepositoryInterface      $hotelreviewRepo,
    ) {
    }

    public function index(): View
    {
        $country = Country::orderBy('name', 'asc')->get();
        $citiesList = Cities::orderBy('city', 'asc')->get();
        $stateList = States::orderBy('name', 'asc')->get();
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(HotelsEnums::ADD[VIEW], compact('citiesList', 'country', 'googleMapsApiKey', 'stateList', 'language', 'defaultLanguage'));
    }

    public function store(HotelAddRequest $request, HotelService $service)
    {
        $request->validated();
        $dataArray = $service->getAddData($request);
        $insert = $this->hotelRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\Hotels', id: $insert->id);
        Helpers::editDeleteLogs('Temple','Hotel','Insert');
        Toastr::success(translate('Hotel_added_successfully'));
        return redirect()->route(HotelsEnums::LIST[REDIRECT]);
    }

    public function list(Request $request)
    {
        $getData = $this->hotelRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'),dataLimit: getWebConfig(name: 'pagination_limit'),relations: ['states', 'cities']);
        return view(HotelsEnums::LIST[VIEW], compact('getData'));
    }

    public function update(Request $request, $id)
    {
        $getData = $this->hotelRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $country = Country::orderBy('name', 'asc')->get();
        $citiesList = cities::orderBy('city', 'asc')->get();
        $stateList = states::orderBy('name', 'asc')->get();
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(HotelsEnums::UPDATE[VIEW], compact('getData', 'country', 'citiesList', 'stateList', 'googleMapsApiKey', 'language', 'defaultLanguage'));
    }

    public function edit(HotelupdateRequest $request, HotelService $service, $id)
    {
        $request->validated();
        $old_data = $this->hotelRepo->getFirstWhere(params: ['id' => $id]);
        $dataArray =  $service->updateData($request,$old_data);
        $this->hotelRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\Hotels', id: $id);
        Toastr::success(translate('Hotel_updated_successfully'));
        Helpers::editDeleteLogs('Temple','Hotel','Update');
        return redirect()->route(HotelsEnums::LIST[REDIRECT]);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data['status'] = $request->get('status', 0);
        $this->hotelRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(Request $request, HotelService $service, $id): RedirectResponse
    {
        $old_data = $this->hotelRepo->getFirstWhere(params: ['id' => $id]);
        if ($old_data) {
            $service->removeImage($old_data);
            $this->hotelRepo->delete(params: ['id' => $id]);
            $this->translationRepo->delete('App\Models\Hotels', $id);
            Toastr::success(translate('Hotel_Deleted_successfully'));
            Helpers::editDeleteLogs('Temple','Hotel','Delete');
        } else {
            Toastr::error(translate('Hotel_Deleted_Failed'));
        }
        return redirect()->route(HotelsEnums::LIST[REDIRECT]);
    }

    //gallery

    public function gallery(Request $request, $id)
    {
        $hotel_id = $id;
        $getData = $this->hotelRepo->getFirstWhere(params: ['id' => $id]);
        return view(HotelsEnums::GALLERY[VIEW], compact('hotel_id', 'getData'));
    }

    public function gallery_add(Request $request, HotelService $service): JsonResponse
    {
        $getData = $this->hotelRepo->getFirstWhere(params: ['id' => $request['hotel_id']]);
        $dataArray = $service->addgalleryImages($request, $getData);
        $savedAttributes = $this->hotelRepo->update(id: $request['hotel_id'], data: $dataArray);
        Toastr::success(translate('gallery_added_successfully'));
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function deleteImage(HotelService $service, $id, $name)
    {
        $getData = $this->hotelRepo->getFirstWhere(params: ['id' => $id]);
        $datacoupon_questionArray =  $service->deletes(old_data: $getData, name: $name);
        $savedAttributes = $this->hotelRepo->update(id: $id, data: $datacoupon_questionArray);
        Toastr::success(translate('gallery_Image_remove_successfully'));
        return redirect()->route(HotelsEnums::GALLERY[REDIRECT], [$id]);
    }

    public function review_list(Request $request){
        $getData = $this->hotelreviewRepo->getListWhere(relations:['userData','hotelData'],orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'),dataLimit: getWebConfig(name: 'pagination_limit')); 
        return view(HotelsEnums::REVIEW[VIEW],compact('getData')); 
    }

    public function review_status(Request $request){
        $data['status'] = $request->get('status',0);
        $this->hotelreviewRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function review_delete(HotelService $service,$id){
        $old_data = $this->hotelreviewRepo->getFirstWhere(params:['id'=>$id]);
        if(!empty($old_data['image'])){
            $service->locationRemove($old_data['image']);
        }
        $savedCities = $this->hotelreviewRepo->delete(params:['id'=>$id]);
        Toastr::success(translate('Review_Deleted_successfully'));
        Helpers::editDeleteLogs('Temple','Hotel Review','Delete');
        return redirect()->route(HotelsEnums::REVIEW[REDIRECT]);
    }  
}
