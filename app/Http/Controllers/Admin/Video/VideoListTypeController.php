<?php

namespace App\Http\Controllers\Admin\Video;

use App\Contracts\Repositories\VideoListTypeRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\VideoListType;
//use App\Models\VideoListType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\VideoListTypeAddRequest;
use App\Http\Requests\Admin\VideoListTypeUpdateRequest;
use App\Http\Resources\VideoListTypeResource;
use App\Traits\PaginatorTrait;
use App\Services\VideoListTypeService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VideoListTypeController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly VideoListTypeRepositoryInterface       $videolisttypeRepo,
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
        $videolisttypes = $this->videolisttypeRepo->getList(dataLimit: 'all');
        return response()->json(VideoListTypeResource::collection($videolisttypes));
    }

public function getAddView(Request $request): View
{
    $videolisttypes = $this->videolisttypeRepo->getListWhere(
        searchValue: $request->get('searchValue'),
        dataLimit: getWebConfig(name: 'pagination_limit')
    );

    $language = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $language[0];


    // Determine the view to use based on conditions (if any)
    $view = VideoListType::LIST[VIEW]; // Example view selection, adjust as needed

    return view($view, compact('videolisttypes', 'language', 'defaultLanguage',));
}


public function getUpdateView(string|int $id): View
{
    $videolisttype = $this->videolisttypeRepo->getFirstWhere(
        params: ['id' => $id],
        relations: ['translations']
    );

    $language = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $language[0];

    // Determine the view to use based on conditions (if any)
    $view = VideoListType::UPDATE[VIEW]; // Example view selection, adjust as needed

    return view($view, compact('videolisttype', 'language', 'defaultLanguage'));
}

       public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->videolisttypeRepo->update(id: $request['id'], data: $data);
        
        return response()->json(['success' => true, 'message' => translate('status_updated_successfully')]);
    }

public function add(VideoListTypeAddRequest $request, VideoListTypeService $videoListTypeService): RedirectResponse
{
     $dataArray = $videoListTypeService->getAddData(request:$request);
        $savedAttributes = $this->videolisttypeRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\VideoListType', id:$savedAttributes->id);

    Toastr::success(translate('videolisttype_added_successfully'));
    Helpers::editDeleteLogs('Youtube','Video List Type','Insert');
    return redirect()->route('admin.videolisttype.view');
}



 public function update(VideoListTypeUpdateRequest $request, $id, VideoListTypeService $videoListTypeService): RedirectResponse
    {
        $video = $this->videolisttypeRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $videoListTypeService->getUpdateData(request: $request, data:$video);
        $this->videolisttypeRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\VideoListType', id:$request['id']);
        Helpers::editDeleteLogs('Youtube','Video List Type','Update');
        Toastr::success(translate('videolisttype_updated_successfully'));
        return redirect()->route('admin.videolisttype.view');
    }



public function delete(Request $request): JsonResponse
{
    $videolisttype = $this->videolisttypeRepo->getFirstWhere(['id' => $request->input('id')]);

    if (!$videolisttype) {
        return response()->json(['error' => translate('Video category not found')], 404);
    }

    $this->videolisttypeRepo->delete(['id' => $request->input('id')]);
    $this->translationRepo->delete(model: 'App\Models\VideoListType', id: $request->input('id'));
    Helpers::editDeleteLogs('Youtube','Video List Type','Delete');
    return response()->json(['message' => translate('Video category deleted successfully')]);
}

}


