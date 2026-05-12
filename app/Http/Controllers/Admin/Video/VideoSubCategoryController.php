<?php

namespace App\Http\Controllers\Admin\Video;

use App\Contracts\Repositories\VideoSubCategoryRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\VideoSubCategory;
use App\Models\VideoCategory;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\VideoSubCategoryAddRequest;
use App\Http\Requests\Admin\VideoSubCategoryUpdateRequest;
use App\Http\Resources\VideoSubCategoryResource;
use App\Traits\PaginatorTrait;
use App\Services\VideoSubCategoryService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VideoSubCategoryController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly VideoSubCategoryRepositoryInterface       $videosubcategoryRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
    ) {}

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
        $videosubcategorys = $this->videosubcategoryRepo->getList(dataLimit: 'all');
        return response()->json(VideoSubCategoryResource::collection($videosubcategorys));
    }

    public function getAddView(Request $request): View
    {
        $videosubcategorys = $this->videosubcategoryRepo->getListWhere(
            searchValue: $request->get('searchValue'),
            dataLimit: getWebConfig(name: 'pagination_limit')
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];

        $videoCategories = VideoCategory::all();

        // Determine the view to use based on conditions (if any)
        $view = VideoSubCategory::LIST[VIEW]; // Example view selection, adjust as needed

        return view($view, compact('videosubcategorys', 'language', 'defaultLanguage', 'videoCategories'));
    }


    public function getUpdateView(string|int $id): View
    {
        $videosubcategory = $this->videosubcategoryRepo->getFirstWhere(
            params: ['id' => $id],
            relations: ['translations']
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $videoCategories = VideoCategory::all(); // Fetch all video categories

        // Determine the view to use based on conditions (if any)
        $view = VideoSubCategory::UPDATE[VIEW]; // Example view selection, adjust as needed

        return view($view, compact('videosubcategory', 'language', 'defaultLanguage', 'videoCategories'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->videosubcategoryRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function add(VideoSubCategoryAddRequest $request, VideoSubCategoryService $videoSubCategoryService): RedirectResponse
    {
        $dataArray = $videoSubCategoryService->getAddData(request: $request);
        $savedAttributes = $this->videosubcategoryRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\VideoSubCategory', id: $savedAttributes->id);

        Toastr::success(translate('videosubcategory_added_successfully'));
        Helpers::editDeleteLogs('Youtube', 'Video Sub Category', 'Insert');
        return redirect()->route('admin.videosubcategory.view');
    }



    public function update(VideoSubCategoryUpdateRequest $request, $id, VideoSubCategoryService $videoSubCategoryService): RedirectResponse
    {
        $video = $this->videosubcategoryRepo->getFirstWhere(params: ['id' => $request['id']]);
        $dataArray = $videoSubCategoryService->getUpdateData(request: $request, data: $video);
        $this->videosubcategoryRepo->update(id: $request['id'], data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\VideoSubCategory', id: $request['id']);
        Helpers::editDeleteLogs('Youtube', 'Video Sub Category', 'Update');
        Toastr::success(translate('videosubcategory_updated_successfully'));
        return redirect()->route('admin.videosubcategory.view');
    }



    public function delete(Request $request, VideoSubCategoryService $videoSubCategoryService): JsonResponse
    {
        $videosubcategory = $this->videosubcategoryRepo->getFirstWhere(['id' => $request->input('id')]);

        if (!$videosubcategory) {
            return response()->json(['error' => translate('Video category not found')], 404);
        }
        $videoSubCategoryService->deleteImage(data: $videosubcategory);

        $this->videosubcategoryRepo->delete(['id' => $request->input('id')]);
        $this->translationRepo->delete(model: 'App\Models\VideoSubCategory', id: $request->input('id'));
        Helpers::editDeleteLogs('Youtube', 'Video Sub Category', 'Delete');
        return response()->json(['message' => translate('Video category deleted successfully')]);
    }
}