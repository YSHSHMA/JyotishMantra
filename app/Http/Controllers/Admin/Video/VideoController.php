<?php

namespace App\Http\Controllers\Admin\Video;

use Illuminate\Http\Request;
use App\Contracts\Repositories\VideoRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Video;
use App\Models\VideoCategory;
use App\Models\VideoSubCategory;
use App\Models\VideoListType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\VideoAddRequest;
use App\Http\Requests\Admin\VideoUpdateRequest;
use App\Services\VideoService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VideoController extends BaseController
{
    public function __construct(
        private readonly VideoRepositoryInterface           $videoRepo,
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
        return $this->getList($request);
    }


    public function getList(Request $request): Application|Factory|View
    {
        $videos = $this->videoRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(Video::LIST[VIEW], compact('videos'));
    }

  public function getAddView(): View
{
    $language = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $language[0];
    $videoCategories = VideoCategory::all();
    $videoSubCategories = VideoSubCategory::all();
    $videoListType = VideoListType::all();
    return view(Video::ADD[VIEW], compact('language', 'defaultLanguage', 'videoCategories', 'videoSubCategories','videoListType'));
}

public function add(VideoAddRequest $request, VideoService $videoService): RedirectResponse
{
    $dataArray = $videoService->getAddData(request:$request);


    $savedVideo  = $this->videoRepo->add(data:$dataArray);
   // $this->translationRepo->add(request:$request, model:'App\Models\video', id:$savedVideo->id);

    Toastr::success(translate('video_added_successfully'));
    Helpers::editDeleteLogs('Youtube','Youtube','Insert');
    return redirect()->route('admin.video.list');
}

public function getUpdateView(string|int $id): View
{
    $video = $this->videoRepo->getFirstWhere(['id' => $id], ['translations']);
    $language = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $language[0];
    $videoCategories = VideoCategory::all();
    $videoSubCategories = VideoSubCategory::all();
    $videoListType = VideoListType::all();
    
    // Fetch all categories for the dropdown
    $allVideoCategories = VideoCategory::all();

    return view(Video::UPDATE[VIEW], compact('video', 'language', 'defaultLanguage', 'videoCategories', 'videoSubCategories', 'allVideoCategories', 'videoListType'));
}

     public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->videoRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => true, 'message' => translate('status_updated_successfully')]);
    }


    public function update(VideoUpdateRequest $request, $id, VideoService $videoService): RedirectResponse
    {
        $video = $this->videoRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $videoService->getUpdateData(request: $request, data:$video);
        $this->videoRepo->update(id:$request['id'], data:$dataArray);
       // $this->translationRepo->update(request:$request, model:'App\Models\Video', id:$request['id']);
        Helpers::editDeleteLogs('Youtube','Youtube','Update');
        Toastr::success(translate('video_updated_successfully'));
        return redirect()->route('admin.video.list');
    }


    public function delete(string|int $id, VideoService $videoService): RedirectResponse
    {
        $video = $this->videoRepo->getFirstWhere(params:['id'=>$id]);
        if($video){
            $this->translationRepo->delete(model:'App\Models\Video', id:$id);
            $this->videoRepo->delete(params: ['id'=>$id]);
            Helpers::editDeleteLogs('Youtube','Youtube','Delete');
            Toastr::success(translate('video_deleted_successfully'));
        }else {
            Toastr::error(translate('error occured'));
        }
        return redirect()->back();
    }
   
      public function getSubcategories(Request $request)
    {
        $category_id = $request->input('category_id');
        $subcategories = VideoSubCategory::where('category_id', $category_id)->get();
        
        $options = '<option value="">' . translate("Select SubCategory") . '</option>';
        
        foreach ($subcategories as $subcategory) {
            $options .= '<option value="' . $subcategory->id . '">' . $subcategory->name . '</option>';
        }
        
        return $options;
    }


    public function list_view_details($id)
    {    

        $video = $this->videoRepo->getFirstWhere(['id' => $id]);

      return view('admin-views.video.video.list_details', compact('video'));
    }


public function updateUrlStatus(Request $request): JsonResponse
{
    $videoId = $request->input('id');
    $index = $request->input('index');
    $status = $request->input('status');

    $video = $this->videoRepo->getFirstWhere(['id' => $videoId]);

    if ($video) {
        $urlStatuses = json_decode($video->url_status, true);
        $urlStatuses[$index] = (int) $status;
        $video->url_status = json_encode($urlStatuses);
        $video->save();

        return response()->json(['success' => true, 'message' => translate('URL status updated successfully')]);
    }

    return response()->json(['success' => false, 'message' => translate('Error occurred')]);
}


}