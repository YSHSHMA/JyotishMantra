<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralReview;
use Illuminate\Http\Request;
use App\Traits\FileManagerTrait;
use Brian2694\Toastr\Facades\Toastr;

class GeneralReviewController extends Controller
{
    use FileManagerTrait;

    public function add()
    {
        return view('admin-views.general-review.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'review_type' => 'required',
            'anonymous' => 'required',
            'star_rating' => 'required',
            'review_text' => 'required',
        ]);

        $imagePath = $this->upload(dir: 'general-review/', format: 'png', image: $request['image']);

        $reviewStore = new GeneralReview;
        $reviewStore->review_type = $request->review_type;
        $reviewStore->user_name = $request->user_name;
        $reviewStore->profile_image = $imagePath;
        $reviewStore->is_anonymous = $request->anonymous;
        $reviewStore->star_rating = $request->star_rating;
        $reviewStore->video_url = $request->video_url;
        $reviewStore->review_text = $request->review_text;
        if ($reviewStore->save()) {
            Toastr::success(translate('review_submitted_successfully'));
            return redirect()->route('admin.general.review.list');
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function list(Request $request)
    {
        $reviews = GeneralReview::query();
        $type = "";
        $status = "";
        if ($request->has('type') && $request->type != 'all') {
            $type = $request->type;
            $reviews = $reviews->where('review_type', $type);;
        }
        if ($request->has('status') && $request->status != 'all') {
            $status = $request->status;
            $reviews = $reviews->where('status', $status);
        }
        $reviews = $reviews->orderBy('created_at', 'desc')->paginate(10);
        return view('admin-views.general-review.list', compact('reviews', 'type', 'status'));
    }

    public function edit($id)
    {
        $edit = GeneralReview::where('id', $id)->first();
        return view('admin-views.general-review.edit', compact('edit'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'review_type' => 'required',
            'anonymous' => 'required',
            'star_rating' => 'required',
            'review_text' => 'required',
        ]);

        $reviewUpdate = GeneralReview::findOrFail($request->id);
        if ($request->hasFile('image')) {
            $imagePath = $this->upload(dir: 'general-review/', format: 'png',image: $request->file('image'));
            $reviewUpdate->profile_image = $imagePath;
        }

        $reviewUpdate->review_type   = $request->review_type;
        $reviewUpdate->user_name     = $request->user_name;
        $reviewUpdate->is_anonymous  = $request->anonymous;
        $reviewUpdate->star_rating   = $request->star_rating;
        $reviewUpdate->video_url     = $request->video_url;
        $reviewUpdate->review_text   = $request->review_text;

        if ($reviewUpdate->save()) {
            Toastr::success(translate('review_updated_successfully'));
            return redirect()->route('admin.general.review.list');
        }

        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function status(Request $request)
    {
        $status = GeneralReview::where('id', $request->id)->update(['status' => $request->status]);
        if ($status) {
            Toastr::success(translate('status_changed_successfully'));
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function delete($id)
    {
        $delete = GeneralReview::where('id', $id)->delete();
        if ($delete) {
            Toastr::success(translate('review_deleted_successfully'));
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function TourEventTempleAllReviewFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('search_by_name', '');
        $searchByType = $request->input('search_by_type', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $columnName = $request->input("columns.$orderColumnIndex.data");

        // ---------- TOUR REVIEWS ----------
        $tourQuery = \App\Models\TourReviews::with(['OrderTour.Tour', 'userData']);

        if (!empty($searchValue)) {
            $tourQuery->where(function ($q) use ($searchValue) {
                $q->whereHas('OrderTour.Tour', fn($q2) => $q2->where('tour_name', 'like', "%$searchValue%"))
                    ->orWhereHas('userData', fn($q2) => $q2->where('name', 'like', "%$searchValue%")
                        ->orWhere('phone', 'like', "%$searchValue%")
                        ->orWhere('email', 'like', "%$searchValue%"));
            });
        }
        $tourData = $tourQuery->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'service_name' => $item->OrderTour->Tour->tour_name ?? '',
                'user_name' => $item->userData->name ?? '',
                'user_phone' => $item->userData->phone ?? '',
                'user_email' => $item->userData->email ?? '',
                "status" => $item->status ?? 0,
                'star' => $item->star,
                'created_at' => $item->created_at,
                'type' => 'Tour',
            ]);

        // ---------- EVENT REVIEWS ----------
        $eventQuery = \App\Models\EventsReview::with(['event', 'userData']);

        if (!empty($searchValue)) {
            $eventQuery->where(function ($q) use ($searchValue) {
                $q->whereHas('event', fn($q2) => $q2->where('event_name', 'like', "%$searchValue%"))
                    ->orWhereHas('userData', fn($q2) => $q2->where('name', 'like', "%$searchValue%")
                        ->orWhere('phone', 'like', "%$searchValue%")
                        ->orWhere('email', 'like', "%$searchValue%"));
            });
        }
        $eventData = $eventQuery->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'service_name' => $item->event->event_name ?? '',
                'user_name' => $item->userData->name ?? '',
                'user_phone' => $item->userData->phone ?? '',
                'user_email' => $item->userData->email ?? '',
                'star' => $item->star,
                "status" => $item->status ?? 0,
                'created_at' => $item->created_at,
                'type' => 'Event',
            ]);

        // ---------- TEMPLE REVIEWS ----------
        $templeQuery = \App\Models\TempleReview::with(['templeinfo', 'userData']);

        if (!empty($searchValue)) {
            $templeQuery->where(function ($q) use ($searchValue) {
                $q->whereHas('templeinfo', fn($q2) => $q2->where('name', 'like', "%$searchValue%"))
                    ->orWhereHas('userData', fn($q2) => $q2->where('name', 'like', "%$searchValue%")
                        ->orWhere('phone', 'like', "%$searchValue%")
                        ->orWhere('email', 'like', "%$searchValue%"));
            });
        }
        $templeDatas = $templeQuery->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'service_name' => $item->templeinfo->name ?? '',
                'user_name' => $item->userData->name ?? '',
                'user_phone' => $item->userData->phone ?? '',
                'user_email' => $item->userData->email ?? '',
                'star' => $item->star,
                "status" => $item->status ?? 0,
                'created_at' => $item->created_at,
                'type' => 'Temple',
            ]);

        // ---------- City REVIEWS ----------
        $cityQuery = \App\Models\CitiesReview::with(['cities', 'userData']);

        if (!empty($searchValue)) {
            $cityQuery->where(function ($q) use ($searchValue) {
                $q->whereHas('cities', fn($q2) => $q2->where('city', 'like', "%$searchValue%"))
                    ->orWhereHas('userData', fn($q2) => $q2->where('name', 'like', "%$searchValue%")
                        ->orWhere('phone', 'like', "%$searchValue%")
                        ->orWhere('email', 'like', "%$searchValue%"));
            });
        }
        $CitiesDatas = $cityQuery->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'service_name' => $item->cities->city ?? '',
                'user_name' => $item->userData->name ?? '',
                'user_phone' => $item->userData->phone ?? '',
                'user_email' => $item->userData->email ?? '',
                'star' => $item->star,
                "status" => $item->status ?? 0,
                'created_at' => $item->created_at,
                'type' => 'City',
            ]);
        // ---------- Hotel REVIEWS ----------
        $hotelQuery = \App\Models\HotelReview::with(['hotelinfo', 'userData']);

        if (!empty($searchValue)) {
            $hotelQuery->where(function ($q) use ($searchValue) {
                $q->whereHas('hotelinfo', fn($q2) => $q2->where('hotel_name', 'like', "%$searchValue%"))
                    ->orWhereHas('userData', fn($q2) => $q2->where('name', 'like', "%$searchValue%")
                        ->orWhere('phone', 'like', "%$searchValue%")
                        ->orWhere('email', 'like', "%$searchValue%"));
            });
        }
        $HotelDatas = $hotelQuery->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'service_name' => $item->hotelinfo->hotel_name ?? '',
                'user_name' => $item->userData->name ?? '',
                'user_phone' => $item->userData->phone ?? '',
                'user_email' => $item->userData->email ?? '',
                'star' => $item->star,
                "status" => $item->status ?? 0,
                'created_at' => $item->created_at,
                'type' => 'Hotel',
            ]);
        // ---------- restaurant REVIEWS ----------
        $restaurantQuery = \App\Models\RestaurantReview::with(['restaurantinfo', 'userData']);
        if (!empty($searchValue)) {
            $restaurantQuery->where(function ($q) use ($searchValue) {
                $q->whereHas('restaurantinfo', fn($q2) => $q2->where('restaurant_name', 'like', "%$searchValue%"))
                    ->orWhereHas('userData', fn($q2) => $q2->where('name', 'like', "%$searchValue%")
                        ->orWhere('phone', 'like', "%$searchValue%")
                        ->orWhere('email', 'like', "%$searchValue%"));
            });
        }
        $RetaurantDatas = $restaurantQuery->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'service_name' => $item->restaurantinfo->restaurant_name ?? '',
                'user_name' => $item->userData->name ?? '',
                'user_phone' => $item->userData->phone ?? '',
                'user_email' => $item->userData->email ?? '',
                'star' => $item->star,
                "status" => $item->status ?? 0,
                'created_at' => $item->created_at,
                'type' => 'Restaurant',
            ]);
        // ---------- self Vehicle REVIEWS ----------
        $selfdrivingQuery = \App\Models\SelfVehicleReview::with(['selfvehicleinfo.getTraveller', 'userData']);
        if (!empty($searchValue)) {
            $selfdrivingQuery->where(function ($q) use ($searchValue) {
                $q->whereHas('selfvehicleinfo.getTraveller', fn($q2) => $q2->where('company_name', 'like', "%$searchValue%"))
                    ->orWhereHas('userData', fn($q2) => $q2->where('name', 'like', "%$searchValue%")
                        ->orWhere('phone', 'like', "%$searchValue%")
                        ->orWhere('email', 'like', "%$searchValue%"));
            });
        }
        $selfvehicleDatas = $selfdrivingQuery->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'service_name' => $item->selfvehicleinfo->getTraveller->company_name ?? '',
                'user_name' => $item->userData->name ?? '',
                'user_phone' => $item->userData->phone ?? '',
                'user_email' => $item->userData->email ?? '',
                'star' => $item->star,
                "status" => $item->status ?? 0,
                'created_at' => $item->created_at,
                'type' => 'Self Driving',
            ]);
        // ---------- MERGE DATA ----------
        $data = $tourData->merge($eventData)->merge($templeDatas)->merge($CitiesDatas)->merge($HotelDatas)->merge($RetaurantDatas)->merge($selfvehicleDatas);
        $data = $data->sortByDesc('created_at');
        $totalRecords = $data->count();
        $data = $data->slice($start, $length)->values();

        $formattedData = $data->map(function ($item, $key) use ($start) {
            $formId = 'temple-status' . $item['id'] . '-form';
            $inputId = 'temple-status' . $item['id'];
            $checked = $item['status'] == 1 ? 'checked' : '';
            $routeUrl = ''; 
            if($item['type'] == "Tour"){
                $routeUrl = route('admin.tour_visits.comment-status-update');
            }elseif ($item['type'] == "Event") {
               $routeUrl = route('admin.event-managment.event.comment-status-update');
            }elseif ($item['type'] == "Temple") {
                $routeUrl = route('admin.temple.review-status-update');
            }elseif ($item['type'] == "City") {
                $routeUrl = route('admin.cities.review-status-update');
            }elseif ($item['type'] == "Hotel") {
                $routeUrl = route('admin.temple.hotel.review-status-update');
            }elseif ($item['type'] == "Restaurant") {
                $routeUrl = route('admin.temple.restaurants.review-status-update');
            }elseif ($item['type'] == "Self Driving") {
                $routeUrl = '';
            }
            $csrf = csrf_token();
            $options = ' <div class="d-flex justify-content-center gap-2">
                                <form action="' . $routeUrl . '" method="post" id="' . $formId . '">
                                    <input type="hidden" name="_token" value="' . $csrf . '">
                                    <input type="hidden" name="id" value="' . $item['id'] . '">
                                    <label class="switcher mx-auto">
                                        <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                            id="' . $inputId . '" value="1" ' . $checked . '
                                            data-modal-id="toggle-status-modal"
                                            data-toggle-id="' . $inputId . '"
                                            data-on-title="Want to Turn ON cab status"
                                            data-off-title="Want to Turn OFF cab status"
                                            data-on-message="<p>If enabled, this Tour Visit will be available on the website and customer app</p>"
                                            data-off-message="<p>If disabled, this Tour Visit will be hidden from the website and customer app</p>">
                                        <span class="switcher_control"></span>
                                    </label>
                                </form>
                                            <a class="btn btn-outline-info btn-sm square-btn" title="' . translate('edit') . '"
                                                href="">
                                                <i class="tio-edit"></i>
                                            </a>
                                            ';
            $user_info = '<span class="font-weight-bolder">';
            for ($istar = 0; $istar < ($item['star'] ?? 1); $istar++) {
                $user_info .= '<i class="tio tio-star text-warning"></i>';
            }
            $user_info .= '</span><br>
            <span class="font-weight-bolder">' . ($item['user_name'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['user_email'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['user_phone'] ?? "") . '</span><br>';
            return [
                'id' => $start + $key + 1,
                'type' => $item['type'],
                'user_info' => $user_info,
                'details' => '<span title="' . e($item['service_name']) . '" data-toggle="tooltip">'
                    . e(\Illuminate\Support\Str::limit($item['service_name'], 20))
                    . '</span>',
                'create_by' => date('d M,Y h:i A', strtotime($item['created_at'])),
                'option' => $options,
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $formattedData
        ]);
    }
}
