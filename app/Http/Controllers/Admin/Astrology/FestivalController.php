<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\Contracts\Repositories\FestivalRepositoryInterface;
//use App\Contracts\Repositories\AstrologyRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ExportFileNames\Admin\Festival as FestivalExport;
use App\Enums\ViewPaths\Admin\Festival;
use App\Exports\FestivalListExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\FestivalAddRequest;
use App\Http\Requests\Admin\FestivalUpdateRequest;
use App\Services\FestivalService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\FestivalHindiMonth;
use App\Models\FastFestival;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;
use Image; // Import the Intervention Image facade
use Illuminate\Support\Str;

class FestivalController extends BaseController
{
    public function __construct(
        private readonly FestivalRepositoryInterface           $festivalRepo,
        //private readonly AstrologyRepositoryInterface           $astrologyRepo,
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
        $key = explode(' ', $request['searchValue']);
        $fastFestival=FastFestival::where(['status'=>1])->when(isset($key) , function ($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('event_name', 'like', "%{$value}%");
                    }
                });
            })
        ->latest()->paginate(getWebConfig('default_pagination'));
        //dd($fastFestival);
        return view('admin-views.festival.list',compact('fastFestival'));
    }

    public function getAddView(): View
    {
        $festivalMonth = FestivalHindiMonth::where('status',1)->get();
        //dd($festivalMonth);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Festival::ADD[VIEW], compact( 'language', 'defaultLanguage','festivalMonth'));
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $festival = $this->festivalRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Festival::UPDATE[VIEW], compact('festival', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->festivalRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(Request $request, FestivalService $festivalService): RedirectResponse
    {
        $this->astrologyRepo->updateByParams(params:['festival_id'=>$request['id']],data:['festival_id' =>$request['festival_id'],'sub_category_id'=>null,'sub_sub_category_id'=>null]);
        $festival = $this->festivalRepo->getFirstWhere(params:['id'=>$request['id']]);
        $festivalService->deleteImage(data:$festival);
        $this->translationRepo->delete(model:'App\Models\Festival', id:$request['id']);
        $this->festivalRepo->delete(params: ['id'=>$request['id']]);
        Toastr::success(translate('festival_deleted_successfully'));
        return redirect()->back();
    }


    public function add(FestivalAddRequest $request, FestivalService $festivalService): RedirectResponse
    {
        $dataArray = $festivalService->getAddData(request:$request);
        $savedAttributes = $this->festivalRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\Festival', id:$savedAttributes->id);

        Toastr::success(translate('festival_added_successfully'));
        return redirect()->route('admin.festival.list');
    }

    public function update(FestivalUpdateRequest $request, $id, FestivalService $festivalService): RedirectResponse
    {
        $festival = $this->festivalRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $festivalService->getUpdateData(request: $request, data:$festival);
        $this->festivalRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\FastFestival', id:$request['id']);

        Toastr::success(translate('festival_updated_successfully'));
        return redirect()->route('admin.festival.list');
    }

    public function newupdate(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'en_description' => 'required|string',
            'hi_description' => 'required|string',
            'image' => 'nullable|image|max:2048', // Image validation
        ]);

        // Find the record
        $record = FastFestival::findOrFail($id);

        // Check if a new image is uploaded
        if ($request->hasFile('image')) {
            // Delete the old image if exists
            if ($record->image) {
                Storage::delete($record->image);
            }

            // Handle the uploaded image
            $imageFile = $request->file('image');
            $currentDate = now()->format('Y-m-d');
            $uniqueId = Str::random(12);
            $webpName = "{$currentDate}-{$uniqueId}.webp";
            $webpPath = 'festival-images/' . $webpName;

            // Convert and save the image as WebP
            $image = Image::make($imageFile)->encode('webp', 90);
            Storage::put($webpPath, $image);

            // Update the record with new WebP image path
            $record->image = $webpPath;
        }

        // Update other fields
        $record->en_description = $request->input('en_description');
        $record->hi_description = $request->input('hi_description');

        // Save the updated record
        $record->save();

        // Redirect or return response
        Toastr::success(translate('festival_updated_successfully'));
        return redirect()->route('admin.festival.list');
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $festival = $this->festivalRepo->getListWhere(searchValue:$request->get('searchValue'), dataLimit: 'all');
        $active = $this->festivalRepo->getListWhere(filters:['status'=>1], dataLimit: 'all')->count();
        $inactive = $this->festivalRepo->getListWhere(filters:['status'=>0], dataLimit: 'all')->count();
        return Excel::download(new FestivalListExport(
            [
                'festival'=> $festival,
                'search' => $request['search'] ,
                'active' => $active,
                'inactive' => $inactive,
            ]), FestivalExport::EXPORT_XLSX) ;
    }
}
