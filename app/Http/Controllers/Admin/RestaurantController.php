<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Contracts\Repositories\RestaurantReviewRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\RestaurantsPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RestaurantAddRequest;
use App\Models\Cities;
use App\Models\Country;
use App\Models\States;
use App\Services\RestaurantService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(
        private readonly RestaurantRepositoryInterface      $RestaurantRepo,
        private readonly RestaurantReviewRepositoryInterface      $RestaurantReviewRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,

    ) {
    }

    public function index(Request $request)
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $googleMapsApiKey =  config('services.google_maps.api_key');
        $country = Country::orderBy('name', 'asc')->get();
        $citiesList = Cities::orderBy('city', 'asc')->get();
        $stateList = States::orderBy('name', 'asc')->get();
        return view(RestaurantsPath::ADD[VIEW], compact('language', 'country', 'stateList', 'citiesList', 'defaultLanguage', 'googleMapsApiKey'));
    }

    public function store(RestaurantAddRequest $request, RestaurantService $service): RedirectResponse
    {
        $checkData = $this->RestaurantRepo->getFirstWhere(['restaurant_name' => $request['restaurant_name'][array_search('en', $request['lang'])]]);
        if (!$checkData) {
            $dataArray = $service->getAddData(request: $request);
            $insertID =  $this->RestaurantRepo->add(data: $dataArray);
            $this->translationRepo->add(request: $request, model: 'App\Models\Restaurant', id: $insertID->id);
            Toastr::success(translate('Restaurant_added_successfully'));
            Helpers::editDeleteLogs('Temple','Restaurant','Insert');
            return redirect()->route(RestaurantsPath::LIST[REDIRECT]);
        } else {
            Toastr::error(translate('already_exists_Restaurant_name'));
            return redirect()->route(RestaurantsPath::ADD[REDIRECT]);
        }
    }

    public function list(Request $request)
    {
        $all_data = $this->RestaurantRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'),dataLimit: getWebConfig(name: 'pagination_limit'), relations: ['cities', 'country', 'states']);
        return view(RestaurantsPath::LIST[VIEW], compact('all_data'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data['status'] = $request->get('status', 0);
        $this->RestaurantRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(Request $request, RestaurantService $service, $id)
    {
        $old_data = $this->RestaurantRepo->getFirstWhere(params: ['id' => $id]);
        if ($old_data) {
            $service->removeImage($old_data);
            $this->RestaurantRepo->delete(params: ['id' => $id]);
            $this->translationRepo->delete('App\Models\Restaurant', $id);
            Toastr::success(translate('Restaurant_Deleted_successfully'));
            Helpers::editDeleteLogs('Temple','Restaurant','Delete');
        } else {
            Toastr::error(translate('Restaurant_Deleted_Failed'));
        }
        return redirect()->route(RestaurantsPath::LIST[REDIRECT]);
    }

    public function update(Request $request, $id)
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $googleMapsApiKey =  config('services.google_maps.api_key');
        $country = Country::orderBy('name', 'asc')->get();
        $citiesList = cities::orderBy('city', 'asc')->get();
        $stateList = states::orderBy('name', 'asc')->get();
        $getData = $this->RestaurantRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        
        return view(RestaurantsPath::UPDATE[VIEW], compact('getData', 'language', 'country', 'stateList', 'citiesList', 'defaultLanguage', 'googleMapsApiKey'));
    }

    public function edit(RestaurantAddRequest $request, RestaurantService $service, $id): RedirectResponse
    {
        $old_data = $this->RestaurantRepo->getFirstWhere(params: [['id', '!=', $id], 'restaurant_name' => $request['restaurant_name'][array_search('en', $request['lang'])]]);
        if (!$old_data) {
            $old_data1 = $this->RestaurantRepo->getFirstWhere(params: [['id', $id]]);
            $updateArray = $service->updateData($request,$old_data1);
            $this->RestaurantRepo->update(id: $id, data: $updateArray);
            $this->translationRepo->update(request: $request, id: $id, model: "App\Models\Restaurant");
            Toastr::success(translate('Restaurant_Updated_successfully'));
            Helpers::editDeleteLogs('Temple','Restaurant','Update');
            return redirect()->route(RestaurantsPath::LIST[REDIRECT]);
        } else {
            Toastr::error(translate('already_exists_Restaurant_name'));
            return redirect()->route(RestaurantsPath::UPDATE[REDIRECT], [$id]);
        }
    }

    public function gallery(Request $request, $id)
    {
        $gallery_list = $this->RestaurantRepo->getFirstWhere(params: ['id' => $id]);
        return view(RestaurantsPath::GALLERY[VIEW], compact('gallery_list'));
    }

    public function gallery_add(Request $request, RestaurantService $service, $id)
    {
            $gallery_list = $this->RestaurantRepo->getFirstWhere(params: ['id' => $id]);
            $array = $service->imageAdd($request,$gallery_list);
           $datas = $this->RestaurantRepo->update(id: $id, data: $array);
           if($datas){
            return response()->json(['success' => 1, 'message' => translate('added_gallery_successfully')], 200);
           }
        return response()->json(['success' => 0, 'message' => translate('added_gallery_Failed')], 200);
    }


    public function deleteImage(RestaurantService $service, $id, $name)
    {
        $gallery_list = $this->RestaurantRepo->getFirstWhere(params: ['id' => $id]);
        if ($gallery_list) {
          $array = $service->deleteImage($name, $gallery_list);
          $this->RestaurantRepo->update(id: $id, data: $array);
          Toastr::success(translate('Gallery_Deleted_successfully'));
        } else {
            Toastr::success(translate('Gallery_Deleted_Failed'));           
        }
        return redirect()->route(RestaurantsPath::GALLERY[REDIRECT], [$id]);
    }


    public function review_list(Request $request){
        $getData =  $all_data = $this->RestaurantReviewRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'),dataLimit: getWebConfig(name: 'pagination_limit'), relations: ['userData', 'restaurantData']);
        return view(RestaurantsPath::REVIEW[VIEW],compact('getData'));
    }

    public function review_status(Request $request){
        $data['status'] = $request->get('status',0);
        $this->RestaurantReviewRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function review_delete(RestaurantService $service,$id){
        $old_data = $this->RestaurantReviewRepo->getFirstWhere(params:['id'=>$id]);
        if(!empty($old_data['image'])){
            $service->locationRemove($old_data['image']);
        }
        $savedCities = $this->RestaurantReviewRepo->delete(params:['id'=>$id]);
        Helpers::editDeleteLogs('Temple','Restaurant Review','Delete');
        Toastr::success(translate('Review_Deleted_successfully'));
        return redirect()->route(RestaurantsPath::REVIEW[REDIRECT]);
    }  

}
