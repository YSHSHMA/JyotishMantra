<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\TourTypeRepositoryInterface;
use App\Contracts\Repositories\TourVisitRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\TourTypePath;
use App\Http\Controllers\Controller;
use App\Models\TourType;
use App\Models\TourVisits;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class TourTypeController extends Controller
{
    public function __construct(
        private readonly TranslationRepositoryInterface     $translationRepo,
        private readonly TourTypeRepositoryInterface  $tourtypeRepo,
        private readonly TourVisitRepositoryInterface  $tourvisit,
    ) {}

    public function TypeList(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $getDatalist = $this->tourtypeRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourTypePath::ADDTYPE[VIEW], compact('getDatalist', 'languages', 'defaultLanguage'));
    }

    public function TypeAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', function ($attribute, $value, $fail) {
                if (TourType::where('name', $value)->exists()) {
                    $fail('The name has already been inserted.');
                }
            },],
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $dataArray = ['name' => $request['name'][array_search('en', $request['lang'])], 'slug' => \Illuminate\Support\Str::slug($request['name'][array_search('en', $request['lang'])], '_')];
        $insert = $this->tourtypeRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\TourType', id: $insert->id);
        Toastr::success(translate('Tour_Visit_added_successfully'));
        return redirect()->route(TourTypePath::ADDTYPE[REDIRECT]);
    }

    public function TypeStatus(Request $request)
    {

        $getData  = $this->tourtypeRepo->getFirstWhere(params: ['id' => $request['id']]);
        $getDataVisit  = $this->tourvisit->getListWhere(filters: ['tour_type' => $getData['slug'], 'status' => 1])->pluck('id');
        if ($getDataVisit->isEmpty()) {
            $data['status'] = $request->get('status', 0);
            $this->tourtypeRepo->update(id: $request['id'], data: $data);
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
        } else if ($getDataVisit->isNotEmpty()) {
            $data['status'] = $request->get('status', 0);
            $this->tourtypeRepo->update(id: $request['id'], data: $data);
            if ($data['status'] == 0) {
                TourVisits::whereIn('id', $getDataVisit)->update(['status' => 0]);
                return response()->json(['success' => 1, 'message' => translate('status_updated_successfully_and_relational_tour_off')], 200);
            } else {
                return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
            }
        } else {
            return response()->json(['success' => 0, 'message' => translate('Status_is_not_updated_because_the_visit_is_active')], 200);
        }
    }

    public function TypeUpdate(Request $request, $id)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $getDatalist = $this->tourtypeRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        return view(TourTypePath::TYPEUPDATE[VIEW], compact('getDatalist', 'languages', 'defaultLanguage'));
    }

    public function TypeEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:tour_type,name,' . $id,
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $getData  = $this->tourtypeRepo->getFirstWhere(params: ['id' => $id]);
        $getDataVisit  = $this->tourvisit->getFirstWhere(params: ['tour_type' => $getData['slug']]);
        if (empty($getDataVisit)) {
            $dataArray = ['name' => $request['name'][array_search('en', $request['lang'])], 'slug' => \Illuminate\Support\Str::slug($request['name'][array_search('en', $request['lang'])], '_')];
            $this->tourtypeRepo->update(id: $id, data: $dataArray);
            $this->translationRepo->update(request: $request, model: 'App\Models\TourType', id: $id);
            Toastr::success(translate('name_updated_successfully'));
        } elseif (!empty($getDataVisit)) {
            $dataArray = ['name' => $request['name'][array_search('en', $request['lang'])]];
            $this->tourtypeRepo->update(id: $id, data: $dataArray);
            $this->translationRepo->update(request: $request, model: 'App\Models\TourType', id: $id);
            Toastr::success(translate('name_updated_successfully'));
        } else {
            Toastr::error(translate('name_is_not_updated_because_the_visit_is_active'));
        }
        return redirect()->route(TourTypePath::ADDTYPE[REDIRECT]);
    }

    public function TypeDelete(Request $request)
    {
        $old_data = $this->tourtypeRepo->getFirstWhere(params: ['id' => $request->id]);
        $getDataVisit  = $this->tourvisit->getFirstWhere(params: ['tour_type' => $old_data['slug']]);
        if (empty($getDataVisit)) {
            $this->tourtypeRepo->delete(params: ['id' => $request->id]);
            $this->translationRepo->delete(model: 'App\Models\TourType', id: $request->id);
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
        } else {
            return response()->json(['success' => 0, 'message' => translate('Status_is_not_updated_because_the_visit_is_active')], 200);
        }
    }
}
