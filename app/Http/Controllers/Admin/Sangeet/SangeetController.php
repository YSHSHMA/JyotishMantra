<?php

namespace App\Http\Controllers\Admin\Sangeet;

use Illuminate\Http\Request;
use App\Contracts\Repositories\SangeetRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Sangeet;
use App\Models\SangeetCategory;
use App\Models\SangeetSubCategory;
use App\Models\SangeetLanguage;
use App\Models\SangeetDetails;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\SangeetAddRequest;
use App\Http\Requests\Admin\SangeetUpdateRequest;
use App\Services\SangeetService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SangeetImport;
use App\Exports\SangeetExport;
use App\Utils\Helpers;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SangeetController extends BaseController
{
    public function __construct(
        private readonly SangeetRepositoryInterface           $sangeetRepo,
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

    $searchValue = $request->get('searchValue');
    
    $sangeets = $this->sangeetRepo->getListWhere(
        orderBy: ['id' => 'desc'],
        searchValue: $searchValue,
        dataLimit: getWebConfig(name: 'pagination_limit')
    );

    $totalLanguages = $sangeets->pluck('language')->unique()->count();

    return view(Sangeet::LIST[VIEW], [
        'sangeets' => $sangeets,
        'totalLanguages' => $totalLanguages
    ]);
}

    public function getAddView(): View
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $sangeetCategories = SangeetCategory::all();
        $sangeetSubCategories = SangeetSubCategory::all();
        $sangeetLanguages = SangeetLanguage::all();
        return view(Sangeet::ADD[VIEW], compact( 'language', 'defaultLanguage', 'sangeetCategories', 'sangeetSubCategories', 'sangeetLanguages'));
    }

    public function add(sangeetAddRequest $request, SangeetService $sangeetService): RedirectResponse
    {
        $dataArray = $sangeetService->getAddData(request:$request);
        $savedAttributes = $this->sangeetRepo->add(data:$dataArray);
        //$this->translationRepo->add(request:$request, model:'App\Models\sangeet', id:$savedAttributes->id);

        Toastr::success(translate('sangeet_added_successfully'));
        Helpers::editDeleteLogs('Sangeet','Sangeet','Insert');
        return redirect()->route('admin.sangeet.list');
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $sangeet = $this->sangeetRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $sangeetCategories = SangeetCategory::all();
        $sangeetSubCategories = SangeetSubCategory::all();
        $sangeetLanguages = SangeetLanguage::all();
        return view(Sangeet::UPDATE[VIEW], compact('sangeet', 'language', 'defaultLanguage', 'sangeetCategories', 'sangeetSubCategories', 'sangeetLanguages'));
    }

      public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->sangeetRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function update(SangeetUpdateRequest $request, $id, SangeetService $sangeetService): RedirectResponse
    {
        $sangeet = $this->sangeetRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $sangeetService->getUpdateData(request: $request, data:$sangeet);
        $this->sangeetRepo->update(id:$request['id'], data:$dataArray);
       // $this->translationRepo->update(request:$request, model:'App\Models\Sangeet', id:$request['id']);

        Toastr::success(translate('sangeet_updated_successfully'));
        Helpers::editDeleteLogs('Sangeet','Sangeet','Update');
        return redirect()->route('admin.sangeet.list');
    }

    public function delete(string|int $id, SangeetService $sangeetService): RedirectResponse
    {
        $sangeet = $this->sangeetRepo->getFirstWhere(params:['id'=>$id]);
        if($sangeet){
            $this->translationRepo->delete(model:'App\Models\Sangeet', id:$id);
            $sangeetService->deleteImage(data:$sangeet);
            $this->sangeetRepo->delete(params: ['id'=>$id]);
            Toastr::success(translate('sangeet_deleted_successfully'));
            Helpers::editDeleteLogs('Sangeet','Sangeet','Delete');
        }else {
            Toastr::error(translate('error occured'));
        }
        return redirect()->back();
    }


       public function getSubcategories(Request $request)
    {
        $category_id = $request->input('category_id');
        $subcategories = SangeetSubCategory::where('category_id', $category_id)->get();
        
        $options = '<option value="">' . translate("Select SubCategory") . '</option>';
        
        foreach ($subcategories as $subcategory) {
            $options .= '<option value="' . $subcategory->id . '">' . $subcategory->name . '</option>';
        }
        
        return $options;
    }

    public function add_details($id)
{

    $sangeet = $this->sangeetRepo->getFirstWhere(['id' => $id]);
  //  dd($video);

  return view('admin-views.sangeet.sangeet.add-details', compact('sangeet','id'));
}


public function storeSangeetDetails(Request $request)
{
    $details = [];
    $sangeet_ids = $request->input('sangeet_id', []);
    $firstInsertedId = null;
    $firstSangeetId = null;

    foreach ($request->input('title', []) as $index => $title) {
        if (!isset($sangeet_ids[$index])) {
            continue;
        }

        $data = [
            'sangeet_id' => $sangeet_ids[$index],
            'title' => $title,
            'singer_name' => $request->input("singer_name.{$index}"),
            'audio' => $request->file("audio.{$index}") ? $this->upload('sangeet-audio/', 'mp3', $request->file("audio.{$index}")) : null,
            'lyrics' => $request->input("lyrics.{$index}"),
            'image' => $this->upload('sangeet-img/', 'webp', $request->file("image.{$index}")),
            'background_image' => $this->upload('sangeet-background-img/', 'webp', $request->file("background_image.{$index}")),
            'famous' => $request->has("famous") ? 1 : 0,
        ];

        $sangeetDetail = SangeetDetails::create($data);

        if ($firstInsertedId === null) {
            $firstInsertedId = $sangeetDetail->id;
            $firstSangeetId = $sangeet_ids[$index];
        }
    }

    if ($firstInsertedId) {
        return redirect()->route('admin.sangeet.details', ['sangeet_id' => $firstSangeetId]);
    } else {
        return redirect()->route('admin.sangeet.list')->with('error', 'Failed to save Sangeet details. Please try again.');
    }
}

   private function upload($path, $extension, $file)
    {
        if ($file) {
            $filename = uniqid() . '.' . $extension;
            $file->storeAs($path, $filename, 'public');
            return $filename;
        }
        return null;
    }

public function viewSangeetDetails(Request $request, $sangeet_id)
{
    $perPage = 10;

    $sangeetDetails = SangeetDetails::where('sangeet_id', $sangeet_id)->paginate($perPage);

      $activeStatusCount = SangeetDetails::where('sangeet_id', $sangeet_id)
                                      ->where('status', 1)
                                      ->count();

    return view('admin-views.sangeet.sangeet.details', compact('sangeetDetails', 'activeStatusCount'));
}

public function showDetails($id)
{
    $sangeet = Sangeet::with('details')->findOrFail($id); 
    
    return view('admin.sangeet.details', compact('sangeet'));
}

public function viewAllDetails(Request $request)
{
    $perPage = 10;

    $sangeetDetails = SangeetDetails::paginate($perPage);
    
    return view('admin-views.sangeet.sangeet.details', compact('sangeetDetails'));
}


public function updateSangeetDetails(Request $request, $id)
{
    $sangeetDetail = SangeetDetails::findOrFail($id);

    $data = $request->only(['sangeet_id', 'title', 'singer_name', 'lyrics']);
    $data['famous'] = $request->has('famous') ? 1 : 0;

    if ($request->hasFile('audio')) {
        $data['audio'] = $this->upload('sangeet-audio/', 'mp3', $request->file('audio'));
    }

    if ($request->hasFile('image')) {
        $data['image'] = $this->upload('sangeet-img/', 'webp', $request->file('image'));
    }

    if ($request->hasFile('background_image')) {
        $data['background_image'] = $this->upload('sangeet-background-img/', 'webp', $request->file('background_image'));
    }

    // Debugging output
    \Log::info('Updating Sangeet Details with data: ', $data);

    $sangeetDetail->update($data);

    Toastr::success(translate('sangeet_details_updated_successfully'));

    return redirect()->route('admin.sangeet.details', ['sangeet_id' => $sangeetDetail->sangeet_id]);
}


public function editDetails($id)
{
    $sangeetDetail = SangeetDetails::findOrFail($id);
    
    return view('admin-views.sangeet.sangeet.edit-details', compact('sangeetDetail'));
}



  public function updateDetailStatus(Request $request, $id)
    {
    $sangeet = SangeetDetails::findOrFail($id);
    $sangeet->status = $request->input('status') ? 1 : 0;
    $sangeet->save();

    return response()->json([
        'status' => 'success',
        'message' => translate('Status updated successfully!')
    ]);
}

public function softDelete($id)
{
    $sangeet = SangeetDetails::find($id);
    $sangeet->delete();
    return redirect()->back()->with('success', 'Sangeet deleted successfully!');
}

public function recover()
{
    $sangeetDetails = SangeetDetails::onlyTrashed()->paginate(10);

    return view('admin-views.sangeet.sangeet.recover', compact('sangeetDetails'));
}


public function restore($id)
{
    SangeetDetails::withTrashed()->findOrFail($id)->restore();
    return redirect()->route('admin.sangeet.recover')->with('success', 'Sangeet restored successfully!');
}


// public function export(Request $request)
// {
//     $fields = $request->input('export_fields');
//     return Excel::download(new SangeetExport($fields), 'sangeet_data.xlsx');
// }


// public function import(Request $request)
// {
//     // Validate the import file
//     $request->validate([
//         'import_file' => 'required|file|mimes:csv,txt|max:2048', // Adjust max size as needed
//     ]);

//     // Import the file using Excel
//     Excel::import(new SangeetImport, $request->file('import_file'));

    
//     Toastr::success(translate('data_uploaded_successfully'));
//     return redirect()->back();
// }



}