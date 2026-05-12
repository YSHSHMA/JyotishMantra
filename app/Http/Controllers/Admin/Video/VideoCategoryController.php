<?php

namespace App\Http\Controllers\Admin\Video;

use App\Contracts\Repositories\VideoCategoryRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\VideoCategory;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\VideoCategoryRequest;
use App\Http\Resources\VideoCategoryResource;
use App\Traits\PaginatorTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VideoCategoryController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly VideoCategoryRepositoryInterface       $videocategoryRepo,
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
        $videocategorys = $this->videocategoryRepo->getList(dataLimit: 'all');
        return response()->json(VideoCategoryResource::collection($videocategorys));
    }

    public function getAddView(Request $request): View
    {
        $videocategorys = $this->videocategoryRepo->getListWhere(searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(VideoCategory::LIST[VIEW], compact('videocategorys', 'language', 'defaultLanguage'));
    }

    public function getUpdateView(string|int $id): View
    {
        $videocategory = $this->videocategoryRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(VideoCategory::UPDATE[VIEW], compact('videocategory', 'language', 'defaultLanguage'));
    }


       public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->videocategoryRepo->update(id: $request['id'], data: $data);
        
        return response()->json(['success' => true, 'message' => translate('status_updated_successfully')]);
    }

    public function add(VideoCategoryRequest $request): JsonResponse|RedirectResponse
{
    $englishName = $request['name'][array_search('en', $request['lang'])];

    $existingCategory = $this->videocategoryRepo->getFirstWhere(['name' => $englishName]);

    if ($existingCategory) {
        Toastr::error(translate('videocategory_already_exists'));
        return back();
    }

    $dataArray = [
        'name' => $englishName,
    ];

    $savedVideoCategory = $this->videocategoryRepo->add($dataArray);
    $this->translationRepo->add($request, 'App\Models\VideoCategory', $savedVideoCategory->id);

    Toastr::success(translate('videocategory_added_successfully'));
    Helpers::editDeleteLogs('Youtube','Video Category','Insert');
    return back();
}



    public function update(VideoCategoryRequest $request, $id): RedirectResponse
{
    $dataArray = [
        'name' => $request['name'][array_search('en', $request['lang'])],
    ];

    $this->videocategoryRepo->update(id: $id, data: $dataArray);
    $this->translationRepo->update(request: $request, model: 'App\Models\VideoCategory', id: $id);
    Helpers::editDeleteLogs('Youtube','Video Category','Update');
    Toastr::success(translate('videocategory_updated_successfully'));
    return redirect()->route('admin.videocategory.view'); 
}


public function delete(Request $request): JsonResponse
{
    $videocategory = $this->videocategoryRepo->getFirstWhere(['id' => $request->input('id')]);

    if (!$videocategory) {
        return response()->json(['error' => translate('Video category not found')], 404);
    }

    $this->videocategoryRepo->delete(['id' => $request->input('id')]);
    $this->translationRepo->delete(model: 'App\Models\VideoCategory', id: $request->input('id'));
    Helpers::editDeleteLogs('Youtube','Video Category','Delete');
    return response()->json(['message' => translate('Video category deleted successfully')]);
}


}
