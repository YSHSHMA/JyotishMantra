<?php

namespace App\Http\Controllers\Admin\ThirdParty;

use App\Contracts\Repositories\BusinessSettingRepositoryInterface;
use App\Enums\ViewPaths\Admin\GoogleMapAPI;
use App\Http\Controllers\BaseController;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoogleMapAPIController extends BaseController
{

    public function __construct(
        private readonly BusinessSettingRepositoryInterface $businessSettingRepo,
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
        return $this->getView();
    }

    public function getView(): View
    {
        return view(GoogleMapAPI::VIEW[VIEW]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->businessSettingRepo->updateOrInsert(type: 'map_api_key', value: $request['map_api_key']);
        $this->businessSettingRepo->updateOrInsert(type: 'map_api_key_server', value: $request['map_api_key_server']);
        Toastr::success(translate('config_data_updated'));
        Helpers::editDeleteLogs('Third Party','Google Map Api','Update');
        return back();
    }
}
