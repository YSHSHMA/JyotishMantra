<?php

namespace App\Http\Controllers\Admin\PanchangMoonImage;

use App\Contracts\Repositories\PanchangMoonImageRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\PanchangMoonImage;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\PanchangMoonImageAddRequest;
use App\Http\Requests\Admin\PanchangMoonImageUpdateRequest;
use App\Http\Resources\PanchangMoonImageResource;
use App\Traits\PaginatorTrait;
use App\Services\PanchangMoonImageService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PanchangMoonImageController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly PanchangMoonImageRepositoryInterface      
         $panchangmoonimageRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
    )
    {
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {    
        return $this->getAddView($request);
    }

    public function getList(): JsonResponse
    {
        $panchangmoonimages = $this->panchangmoonimageRepo->getList(dataLimit: 'all');
        return response()->json(PanchangMoonImageResource::collection($panchangmoonimages));
    }

public function getAddView(Request $request): View
{
    $panchangmoonimages = $this->panchangmoonimageRepo->getListWhere(
        searchValue: $request->get('searchValue'),
        dataLimit: getWebConfig(name: 'pagination_limit')
    );

    $language = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $language[0];


    // Determine the view to use based on conditions (if any)
    $view = PanchangMoonImage::LIST[VIEW]; // Example view selection, adjust as needed

    return view($view, compact('panchangmoonimages', 'language', 'defaultLanguage'));
}


 public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $panchangmoonimage = $this->panchangmoonimageRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(PanchangMoonImage::UPDATE[VIEW], compact('panchangmoonimage', 'language', 'defaultLanguage'));
    }

       public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->panchangmoonimageRepo->update(id: $request['id'], data: $data);
        
        return response()->json(['success' => true, 'message' => translate('status_updated_successfully')]);
    }

 public function add(PanchangMoonImageAddRequest $request, PanchangMoonImageService $panchangmoonimageService): RedirectResponse
    {
        $dataArray = $panchangmoonimageService->getAddData(request:$request);
        $savedAttributes = $this->panchangmoonimageRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\PanchangMoonImage', id:$savedAttributes->id);

        Toastr::success(translate('panchangmoonimage_added_successfully'));
        Helpers::editDeleteLogs('Panchang Moon','Panchang Moon Image','Insert');
        return redirect()->route('admin.panchangmoonimage.view');
    }



 public function update(PanchangMoonImageUpdateRequest $request, $id, PanchangMoonImageService $panchangMoonImageService): RedirectResponse
    {
        $panchang = $this->panchangmoonimageRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $panchangMoonImageService->getUpdateData(request: $request, data:$panchang);
        $this->panchangmoonimageRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\PanchangMoonImage', id:$request['id']);
        Helpers::editDeleteLogs('Panchang Moon','Panchang Moon Image','Update');
        Toastr::success(translate('panchangmoonimage_updated_successfully'));
        return redirect()->route('admin.panchangmoonimage.view');
    }



public function delete(Request $request): JsonResponse
{
    $panchangmoonimage = $this->panchangmoonimageRepo->getFirstWhere(['id' => $request->input('id')]);

    if (!$panchangmoonimage) {
        return response()->json(['error' => translate('panchangmoonimage not found')], 404);
    }

    $this->panchangmoonimageRepo->delete(['id' => $request->input('id')]);
    $this->translationRepo->delete(model: 'App\Models\PanchangMoonImage', id: $request->input('id'));
    Helpers::editDeleteLogs('Panchang Moon','Panchang Moon Image','Delete');
    return response()->json(['message' => translate('panchangmoonimage deleted successfully')]);
}


}
