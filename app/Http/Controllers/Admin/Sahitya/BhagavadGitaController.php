<?php

namespace App\Http\Controllers\Admin\Sahitya;

use Illuminate\Http\Request;
use App\Contracts\Repositories\BhagavadGitaRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\BhagavadGita;
use App\Models\BhagavadGitaChapter;
use App\Models\BhagavadGitaDetails;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\BhagavadGitaAddRequest;
use App\Http\Requests\Admin\BhagavadGitaUpdateRequest;
use App\Services\BhagavadGitaService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BhagavadGitaImport;
use App\Exports\BhagavadGitaExport;
use App\Utils\Helpers;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BhagavadGitaController extends BaseController
{
    public function __construct(
        private readonly BhagavadGitaRepositoryInterface           $bhagavadgitaRepo,
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
        
        $bhagavadgitas = $this->bhagavadgitaRepo->getListWhere(
            orderBy: ['id' => 'asc'],
            searchValue: $searchValue,
            dataLimit: getWebConfig(name: 'pagination_limit')
        );

        $totalLanguages = $bhagavadgitas->pluck('language')->unique()->count();

        return view(BhagavadGita::LIST[VIEW], [
            'bhagavadgitas' => $bhagavadgitas,
            'totalLanguages' => $totalLanguages
        ]);
    }

    public function getAddView(): View
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(bhagavadgita::ADD[VIEW], compact( 'language', 'defaultLanguage'));
    }

    public function add(bhagavadgitaAddRequest $request, bhagavadgitaService $bhagavadgitaService): RedirectResponse
    {
        $dataArray = $bhagavadgitaService->getAddData(request:$request);
        $savedAttributes = $this->bhagavadgitaRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\BhagavadGitaChapter', id:$savedAttributes->id);

        Toastr::success(translate('bhagavadgita_added_successfully'));
        Helpers::editDeleteLogs('Sahitya','Bhagvad Geeta','Insert');
        return redirect()->route('admin.bhagavadgita.list');
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $bhagavadgita = $this->bhagavadgitaRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(BhagavadGita::UPDATE[VIEW], compact('bhagavadgita', 'language', 'defaultLanguage'));
    }

      public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->bhagavadgitaRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function update(BhagavadGitaUpdateRequest $request, $id, BhagavadGitaService $bhagavadgitaService): RedirectResponse
    {
        $bhagavadgita = $this->bhagavadgitaRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $bhagavadgitaService->getUpdateData(request: $request, data:$bhagavadgita);
        $this->bhagavadgitaRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\BhagavadGitaChapter', id:$request['id']);

        Toastr::success(translate('bhagavadgita_updated_successfully'));
        Helpers::editDeleteLogs('Sahitya','Bhagvad Geeta','Update');
        return redirect()->route('admin.bhagavadgita.list');
    }

    public function delete(string|int $id, BhagavadGitaService $bhagavadgitaService): RedirectResponse
    {
        $bhagavadgita = $this->bhagavadgitaRepo->getFirstWhere(params:['id'=>$id]);
        if($bhagavadgita){
            $this->translationRepo->delete(model:'App\Models\BhagavadGitaChapter', id:$id);
            $bhagavadgitaService->deleteImage(data:$bhagavadgita);
            $this->bhagavadgitaRepo->delete(params: ['id'=>$id]);
            Toastr::success(translate('bhagavadgita_deleted_successfully'));
            Helpers::editDeleteLogs('Sahitya','Bhagvad Geeta','Delete');
        }else {
            Toastr::error(translate('error occured'));
        }
        return redirect()->back();
    }


    public function add_verse($id)
    {
        // Fetch Bhagavad Gita details based on the ID
        $bhagavadgita = $this->bhagavadgitaRepo->getFirstWhere(['id' => $id]);

        // Fetch the languages and set the default language
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0]; // Set the first language as the default
        $bhagavadGitaVerse = BhagavadGitaDetails::all();
        $chapters = BhagavadGitaChapter::all();
        $currentChapter = BhagavadGitaChapter::find($id);

        // Return the view with the required variables
        return view('admin-views.sahitya.bhagavadgita.add-verse', compact('bhagavadgita', 'id', 'language', 'defaultLanguage', 'bhagavadGitaVerse', 'chapters', 'currentChapter'));
    }


    public function storeVerseDetails(Request $request)
    {
        // Validate the request
        $request->validate([
            'verse' => 'required|integer',
            // 'description.*' => 'required|string',
            'image' => 'image|mimes:jpg,png,jpeg,gif,bmp,tif,tiff,webp',
            'chapter_id' => 'required|integer' // Added validation for chapter_id
        ]);

        // Prepare data for insertion
        $data = [
            'verse' => $request->input('verse'),
            'description' => $request['description'][array_search('en', $request['lang'])] ?? '',
            'image' => $this->upload('sahitya/bhagavad-gita/', 'webp', $request->file('image')),
            'status' => 1,
            'chapter_id' => $request->input('chapter_id'), // Add chapter_id to data
        ];

        // Insert data into the database
        $firstInsertedId = BhagavadGitaDetails::create($data)->id;

        // Add translation data
        $this->translationRepo->add(request: $request, model: 'App\Models\BhagavadGitaDetails', id: $firstInsertedId);

        // Check if insertion was successful
        if ($firstInsertedId) {
            return redirect()->route('admin.bhagavadgita.details', ['chapter_id' => $request->input('chapter_id')])
                             ->with('success', 'Details saved successfully.');
        } else {
            return redirect()->route('admin.bhagavadgita.list')
                             ->with('error', 'Failed to save details. Please try again.');
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



    public function viewVerseDetails(Request $request, $chapter_id)
    {
        $perPage = 10;

        $bhagavadgitaDetails = BhagavadGitaDetails::where('chapter_id', $chapter_id)->paginate($perPage);

        $activeStatusCount = BhagavadGitaDetails::where('chapter_id', $chapter_id)
                                          ->where('status', 1)
                                          ->count();
        $currentChapter = BhagavadGitaChapter::find($chapter_id);

        return view('admin-views.sahitya.bhagavadgita.details', compact('bhagavadgitaDetails', 'activeStatusCount', 'currentChapter'));
    }

    public function showDetails($id)
    {
        $bhagavadgita = BhagavadGitaDetails::with('details')->findOrFail($id); 
        
        return view('admin.bhagavadgita.details', compact('bhagavadgita'));
    }

    public function viewAllDetails(Request $request, $id)
    {
        $perPage = 10;

        $bhagavadgitaDetails = BhagavadGitaDetails::where('id', $id)->paginate($perPage);

        $currentChapter = BhagavadGitaChapter::find($id);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];

        return view('admin-views.sahitya.bhagavadgita.all-details', compact('bhagavadgitaDetails', 'currentChapter', 'language', 'defaultLanguage'));
    }

    public function softDelete($id)
    {
        $bhagavadgita = BhagavadGitaDetails::find($id);
        $bhagavadgita->delete();
        return redirect()->back()->with('success', 'Sahitya deleted successfully!');
    }

    public function updateVerseDetails(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            // 'chapter' => 'required|integer',
            'verse' => 'required|integer',
            // 'description.*' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,bmp,tif,tiff,webp',
            'chapter_id' => 'required|integer' // Validation for chapter_id
        ]);

        // Find the existing verse details
        $bhagavadgitaDetail = BhagavadGitaDetails::findOrFail($id);

        // Prepare data for updating
        $data = [
            // 'chapter' => $request->input('chapter'),
            'verse' => $request->input('verse'),
            'description' => $request['description'][array_search('en', $request['lang'])] ?? $bhagavadgitaDetail->description,
            'chapter_id' => $request->input('chapter_id'), // Add chapter_id to data
        ];

        // Check if a new image has been uploaded
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($bhagavadgitaDetail->image) {
                \Storage::delete('public/sahitya/bhagavad-gita/' . $bhagavadgitaDetail->image);
            }

            // Upload new image
            $data['image'] = $this->upload('sahitya/bhagavad-gita/', 'webp', $request->file('image'));
        }

        // Update the verse details in the database
        $bhagavadgitaDetail->update($data);

        // Update translation data
        $this->translationRepo->update(request: $request, model: 'App\Models\BhagavadGitaDetails', id: $id);

        // Flash a success message
        Toastr::success(translate('Verse details updated successfully'));

        // Redirect to the verse details page
        return redirect()->route('admin.bhagavadgita.details', ['chapter_id' => $request->input('chapter_id')]);
    }



    public function editVerse($id)
    {
        $bhagavadgitaDetail = BhagavadGitaDetails::findOrFail($id);

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $currentChapter = BhagavadGitaChapter::find($bhagavadgitaDetail->chapter_id);
        return view('admin-views.sahitya.bhagavadgita.edit-verse', compact('bhagavadgitaDetail', 'language', 'defaultLanguage', 'currentChapter'));
    }

  public function updateDetailStatus(Request $request, $id)
    {
    $bhagavadgita = BhagavadGitaDetails::findOrFail($id);
    $bhagavadgita->status = $request->input('status') ? 1 : 0;
    $bhagavadgita->save();

    return response()->json([
        'status' => 'success',
        'message' => translate('Status updated successfully!')
    ]);
    }

    public function recover()
    {
        $bhagavadgitaDetails = BhagavadGitaDetails::onlyTrashed()->paginate(10);

        return view('admin-views.sahitya.bhagavadgita.recover', compact('bhagavadgitaDetails'));
    }


    public function restore($id)
    {
        BhagavadGitaDetails::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.bhagavadgita.recover')->with('success', 'Data restored successfully!');
    }

    public function json(Request $request)
    {
        $jsonData = file_get_contents(public_path('bhagavad-gita-json/bhagavad_gita.json'));
        $jsonData = json_decode($jsonData, true);
        foreach ($jsonData as $data)
        {
            $chapterId = $data['chapter'];
            $verse = $data['verse'];
            $description = $data['en_content'];
            $hiDescription = $data['hi_content'];
            $existingData = BhagavadGitaDetails::where('chapter_id', $chapterId)
            ->where('verse', $verse)
            ->first();
            if (!$existingData) {
                $newData = new BhagavadGitaDetails();
                $newData->chapter_id = $chapterId;
                $newData->verse = $verse;
                $newData->description = $description;
                $newData->status = 1;
                $newData->save();
                $newData->translations()->create([
                'translationable_type' => BhagavadGitaDetails::class,
                'translationable_id' => $newData->id,
                'locale' => 'in',
                'key' => 'description',
                'value' => $hiDescription,
                ]);
            }
        }
        return response()->json(['message' => 'Data imported successfully']);
    }
    


}