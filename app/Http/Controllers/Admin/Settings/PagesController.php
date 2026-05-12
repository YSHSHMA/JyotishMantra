<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Contracts\Repositories\BusinessSettingRepositoryInterface;
use App\Enums\ViewPaths\Admin\Pages;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AboutUsRequest;
use App\Http\Requests\Admin\PageUpdateRequest;
use App\Http\Requests\Admin\PrivacyPolicyRequest;
use App\Http\Requests\Admin\TermsConditionRequest;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PagesController extends BaseController
{

    public function __construct(
        private readonly BusinessSettingRepositoryInterface $businessSettingRepo,
    ) {}

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {
        return $this->getTermsConditionView();
    }

    public function getTermsConditionView(): View
    {
        $terms_condition = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'terms_condition']);
        $seller_terms_condition = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'seller_terms_condition']);
        $tour_terms_condition = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'tour_terms_condition']);
        $event_terms_condition = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'event_terms_condition']);
        $trustees_terms_condition = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'trustees_terms_condition']);
        return view(Pages::TERMS_CONDITION[VIEW], compact('terms_condition', 'seller_terms_condition', 'tour_terms_condition', 'event_terms_condition', 'trustees_terms_condition'));
    }

    public function updateTermsCondition(TermsConditionRequest $request): RedirectResponse
    {
        $this->businessSettingRepo->updateWhere(params: ['type' => 'terms_condition'], data: ['value' => $request['value']]);
        $this->businessSettingRepo->updateOrInsert('seller_terms_condition', $request['seller_value']);
        $this->businessSettingRepo->updateOrInsert('tour_terms_condition', $request['tour_value']);
        $this->businessSettingRepo->updateOrInsert('event_terms_condition', $request['event_value']);
        $this->businessSettingRepo->updateOrInsert('trustees_terms_condition', $request['trustees_value']);
        Toastr::success(translate('Terms_and_Condition_Updated_successfully'));
        Helpers::editDeleteLogs('Page & Media', 'Terms & Condition', 'Update');
        return back();
    }

    public function getPrivacyPolicyView(): View
    {
        $privacy_policy = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'privacy_policy']);
        $seller_privacy_policy = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'seller_privacy_policy']);
        $tour_privacy_policy = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'tour_privacy_policy']);
        $event_privacy_policy = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'event_privacy_policy']);
        $trustees_privacy_policy = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'trustees_privacy_policy']);
        return view(Pages::PRIVACY_POLICY[VIEW], compact('privacy_policy', 'seller_privacy_policy', 'tour_privacy_policy', 'event_privacy_policy', 'trustees_privacy_policy'));
    }

    public function updatePrivacyPolicy(PrivacyPolicyRequest $request): RedirectResponse
    {
        $this->businessSettingRepo->updateWhere(params: ['type' => 'privacy_policy'], data: ['value' => $request['value']]);
        $this->businessSettingRepo->updateOrInsert('seller_privacy_policy', $request['seller_value']);
        $this->businessSettingRepo->updateOrInsert('tour_privacy_policy', $request['tour_value']);
        $this->businessSettingRepo->updateOrInsert('event_privacy_policy', $request['event_value']);
        $this->businessSettingRepo->updateOrInsert('trustees_privacy_policy', $request['trustees_value']);
        Toastr::success(translate('Privacy_policy_Updated_successfully'));
        Helpers::editDeleteLogs('Page & Media', 'Privacy Policy', 'Update');
        return back();
    }


    public function getPageView($page): View|RedirectResponse
    {
        $pages = ['refund-policy', 'return-policy', 'cancellation-policy',];
        if (in_array($page, $pages)) {
            $data = $this->businessSettingRepo->getFirstWhere(params: ['type' => $page]);
            return view(Pages::VIEW[VIEW], compact('page', 'data'));
        }
        Toastr::error(translate('invalid_page'));
        return back();
    }

    public function updatePage(PageUpdateRequest $request, $page): RedirectResponse
    {
        $pages = ['refund-policy', 'return-policy', 'cancellation-policy',];
        if (in_array($page, $pages)) {
            $value = json_encode(['status' => $request->get('status', 0), 'content' => $request['value']]);
            $this->businessSettingRepo->updateWhere(params: ['type' => $page], data: ['value' => $value]);
            Toastr::success(translate('updated_successfully'));
            Helpers::editDeleteLogs('Page & Media', 'Pages', 'Update');
        } else {
            Toastr::error(translate('invalid_page'));
        }
        return back();
    }

    public function getAboutUsView(): View
    {
        $pageData = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'about_us']);
        return view(Pages::ABOUT_US[VIEW], compact('pageData'));
    }

    public function updateAboutUs(AboutUsRequest $request): RedirectResponse
    {
        $this->businessSettingRepo->updateWhere(params: ['type' => 'about_us'], data: ['value' => $request['about_us']]);
        Toastr::success(translate('about_us_updated_successfully'));
        Helpers::editDeleteLogs('Page & Media', 'About Us', 'Update');
        return back();
    }
}