@php use App\Utils\Helpers; @endphp
@php use App\Enums\ViewPaths\Admin\FeaturesSection;use App\Enums\ViewPaths\Admin\Pages; @endphp
<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        @if (Helpers::modules_permission_check('Page & Media', 'Business Pages', 'view'))
        <li class="{{ Request::is('admin/business-settings/'.Pages::TERMS_CONDITION[URI]) ?'active':'' }}"><a
                href="{{route('admin.business-settings.terms-condition')}}">{{translate('terms_&_Conditions')}}</a></li>
                @endif
                @if (Helpers::modules_permission_check('Page & Media', 'Business Pages', 'privacy-policy-view'))
        <li class="{{ Request::is('admin/business-settings/'.Pages::PRIVACY_POLICY[URI]) ?'active':'' }}"><a
                href="{{route('admin.business-settings.privacy-policy')}}">{{translate('privacy_Policy')}}</a></li>
                @endif
                @if (Helpers::modules_permission_check('Page & Media', 'Business Pages', 'refund-policy-view'))
        <li class="{{ Request::is('admin/business-settings/'.Pages::VIEW[URI].'/refund-policy') ?'active':'' }}"><a
                href="{{route('admin.business-settings.page',['refund-policy'])}}">{{translate('refund_Policy')}}</a>
        </li>
        @endif

        @if (Helpers::modules_permission_check('Page & Media', 'Business Pages', 'return-policy-view'))
        <li class="{{ Request::is('admin/business-settings/'.Pages::VIEW[URI].'/return-policy') ?'active':'' }}"><a
                href="{{route('admin.business-settings.page',['return-policy'])}}">{{translate('return_Policy')}}</a>
        </li>
        @endif
        @if (Helpers::modules_permission_check('Page & Media', 'Business Pages', 'cancellation-policy-view'))
        <li class="{{ Request::is('admin/business-settings/'.Pages::VIEW[URI].'/cancellation-policy') ?'active':'' }}">
            <a href="{{route('admin.business-settings.page',['cancellation-policy'])}}">{{translate('cancellation_Policy')}}</a>
        </li>
        @endif
        @if (Helpers::modules_permission_check('Page & Media', 'Business Pages', 'about-us-view'))
        <li class="{{ Request::is('admin/business-settings/'.Pages::ABOUT_US[URI]) ?'active':'' }}"><a
                href="{{route('admin.business-settings.about-us')}}">{{translate('about_Us')}}</a></li>
                @endif
                @if (Helpers::modules_permission_check('Page & Media', 'Business Pages', 'faq-view'))
        <li class="{{ Request::is('admin/helpTopic/'.\App\Enums\ViewPaths\Admin\HelpTopic::LIST[URI]) ?'active':'' }}">
            <a href="{{route('admin.helpTopic.list')}}">{{translate('FAQ')}}</a></li>
            @endif

        @if(getWebConfig(name: 'react_setup')['status'] || theme_root_path() == 'theme_fashion')
            <li class="{{ Request::is('admin/business-settings/'.FeaturesSection::VIEW[URI]) ?'active':'' }}">
                <a href="{{route('admin.business-settings.features-section')}}">{{translate('features_Section')}}</a>
            </li>
        @endif
        @if (Helpers::modules_permission_check('Page & Media', 'Business Pages', 'company-reliability-view'))
        @if(theme_root_path() == 'default')
            <li class="{{ Request::is('admin/business-settings/'.FeaturesSection::COMPANY_RELIABILITY[URI]) ?'active':'' }}">
                <a href="{{route('admin.business-settings.company-reliability')}}"
                   class="text-capitalize">{{translate('company_reliability')}}</a></li>
        @endif
        @endif

    </ul>
</div>
