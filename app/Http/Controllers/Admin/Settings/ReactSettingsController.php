<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Contracts\Repositories\BusinessSettingRepositoryInterface;
use App\Enums\ViewPaths\Admin\ReactSetup;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ReactActivationRequest;
use App\Utils\Helpers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ReactSettingsController extends BaseController
{
    public function __construct(
        private readonly BusinessSettingRepositoryInterface $businessSettingRepo,
    ){}

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View
     */
    public function index(Request|null $request, string $type = null): View
    {
        return $this->getView();
    }

    private function getView(): View
    {
        if(env('APP_MODE') != 'demo'){
            reactDomainStatusCheck();
        }
        $reactData = getWebConfig(name: 'react_setup');
        return view(ReactSetup::VIEW[VIEW], compact('reactData'));
    }

    public function activation(ReactActivationRequest $request): RedirectResponse
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('This_option_is_disabled_for_demo'));
            return back();
        }

        if(activationSubmit($request['react_license_code'])){
            $value = json_encode([
                'status'=>1,
                'react_license_code'=>$request['react_license_code'],
                'react_domain'=>$request['react_domain'],
                'react_platform' => 'codecanyon'
            ]);
            $this->businessSettingRepo->updateOrInsert(type: 'react_setup', value: $value);

            Toastr::success(translate('react_data_updated'));
            Helpers::editDeleteLogs('React Website Configuration','React Website Configuration','Update');
            return back();
        }
        elseif(reactActivationCheck(reactDomain:$request['react_domain'],reactLicenseCode: $request['react_license_code'])){
            $value = json_encode([
                'status'=>1,
                'react_license_code'=>$request['react_license_code'],
                'react_domain'=>$request['react_domain'],
                'react_platform' => 'iss'
            ]);
            $this->businessSettingRepo->updateOrInsert(type: 'react_setup', value: $value);

            Toastr::success(translate('react_data_updated'));
            Helpers::editDeleteLogs('React Website Configuration','React Website Configuration','Update');
            return back();
        }
        Toastr::error(translate('Invalid_license_code_or_unregistered_domain'));
        return back();
    }
}
