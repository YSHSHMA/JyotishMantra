<?php

namespace App\Http\Controllers\Admin\Bhagwan;

use Illuminate\Http\Request;
use App\Contracts\Repositories\BhagwanRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Bhagwan;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\BhagwanAddRequest;
use App\Http\Requests\Admin\BhagwanUpdateRequest;
use App\Models\BhagwanLogs;
use App\Services\BhagwanService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use DateTime;
use DateTimeZone;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BhagwanController extends BaseController
{


    use FileManagerTrait {
        delete as deleteFile;
        update as updateFile;
    }

    public function __construct(
        private readonly BhagwanRepositoryInterface           $bhagwanRepo,
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
        return $this->getList($request);
    }

    public function getList(Request $request): Application|Factory|View
    {

        $searchValue = $request->get('searchValue');

        $bhagwans = $this->bhagwanRepo->getListWhere(
            orderBy: ['id' => 'asc'],
            searchValue: $searchValue,
            dataLimit: getWebConfig(name: 'pagination_limit')
        );

        $totalLanguages = $bhagwans->pluck('language')->unique()->count();

        return view(Bhagwan::LIST[VIEW], [
            'bhagwans' => $bhagwans,
            'totalLanguages' => $totalLanguages
        ]);
    }

    public function getAddView(): View
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(bhagwan::ADD[VIEW], compact('language', 'defaultLanguage'));
    }

    public function add(bhagwanAddRequest $request, bhagwanService $bhagwanService): RedirectResponse
    {
        $dataArray = $bhagwanService->getAddData(request: $request);
        $savedAttributes = $this->bhagwanRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\Bhagwan', id: $savedAttributes->id);

        Toastr::success(translate('added_successfully'));
        Helpers::editDeleteLogs('Bhagwan', 'Bhagwan', 'Insert');
        return redirect()->route('admin.bhagwan.list');
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $bhagwan = $this->bhagwanRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Bhagwan::UPDATE[VIEW], compact('bhagwan', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->bhagwanRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function update(BhagwanUpdateRequest $request, $id, BhagwanService $bhagwanService): RedirectResponse
    {
        $bhagwan = $this->bhagwanRepo->getFirstWhere(params: ['id' => $request['id']]);
        $dataArray = $bhagwanService->getUpdateData(request: $request, data: $bhagwan);
        $this->bhagwanRepo->update(id: $request['id'], data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\Bhagwan', id: $request['id']);

        Toastr::success(translate('updated_successfully'));
        Helpers::editDeleteLogs('Bhagwan', 'Bhagwan', 'Update');
        return redirect()->route('admin.bhagwan.list');
    }

    public function delete(string|int $id, BhagwanService $bhagwanService): RedirectResponse
    {
        $bhagwan = $this->bhagwanRepo->getFirstWhere(params: ['id' => $id]);
        if ($bhagwan) {
            $this->translationRepo->delete(model: 'App\Models\Bhagwan', id: $id);
            $bhagwanService->deleteAllImage(data: $bhagwan);
            $this->bhagwanRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('deleted_successfully'));
            Helpers::editDeleteLogs('Bhagwan', 'Bhagwan', 'Delete');
        } else {
            Toastr::error(translate('error occured'));
        }
        return redirect()->back();
    }


    public function deleteImage(Request $request, BhagwanService $bhagwanService): RedirectResponse
    {
        $service = $this->bhagwanRepo->getFirstWhere(params: ['id' => $request['id']]);

        $field = 'images';
        $storagePath = '/bhagwan/';
    
        if (in_array($request['name'], json_decode($service['wallpapers'] ?? '[]', true))) {
            $field = 'wallpapers';
            $storagePath = '/bhagwan/wallpaper/';
        }
    
        if ($field === 'images' && count(json_decode($service[$field] ?? '[]', true)) < 2) {
            Toastr::warning(translate('you_can_not_delete_all_images'));
            return back();
        }
    
        $this->deleteFile(filePath: $storagePath . $request['name']);
        $imageProcessing = $bhagwanService->deleteImage(request: $request, service: $service);
    
        $updateData = [
            $field => json_encode($imageProcessing[$field]),
        ];
        $this->bhagwanRepo->update(id: $request['id'], data: $updateData);
    
        Toastr::success(translate('image_removed_successfully'));
        return back();
    }
    


    public function storeEventImage(Request $request): RedirectResponse
    {
        $bhagwan = $this->bhagwanRepo->getFirstWhere(['id' => $request->id]);

        if (!$bhagwan) {
            Toastr::error(translate('record_not_found'));
            return redirect()->route('admin.bhagwan.list');
        }

        try {

            $imagePath = $this->upload('bhagwan/event-img/', 'webp', $request->file('event_image'));

            $selectedDate = $request->input('date');

            $bhagwan->update([
                'date' => $selectedDate,
                'event_image' => $imagePath,
            ]);

            Toastr::success(translate('added_successfully'));
        } catch (\Exception $e) {
            Toastr::error(translate('image_upload_failed'));
            return back()->withInput();
        }

        return redirect()->route('admin.bhagwan.list');
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


    public function updateEvent(Request $request): RedirectResponse
    {
        $bhagwan = $this->bhagwanRepo->getFirstWhere(['id' => $request->id]);

        if (!$bhagwan) {
            Toastr::error(translate('record_not_found'));
            return redirect()->route('admin.bhagwan.list');
        }

        $selectedDate = $request->input('date');

        if ($request->hasFile('event_image')) {
            if (!empty($bhagwan->event_image)) {
                $this->deleteFile('/bhagwan/event-img/' . $bhagwan->event_image);
            }

            $imagePath = $this->upload('bhagwan/event-img/', 'webp', $request->file('event_image'));
        } else {
            $imagePath = $bhagwan->event_image;
        }

        $bhagwan->update([
            'date' => $selectedDate,
            'event_image' => $imagePath,
        ]);

        Toastr::success(translate('updated_successfully'));
        return redirect()->route('admin.bhagwan.list');
    }

    public function BhagwanLogsList()
    {
        $logs = BhagwanLogs::with('customer')->orderBy('id', 'desc')->paginate(10);
        return view(Bhagwan::BHAGWANLOGS[VIEW], compact('logs'));
    }
}