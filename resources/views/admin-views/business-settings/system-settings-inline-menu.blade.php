@php use App\Utils\Helpers; @endphp
@php
    use App\Enums\ViewPaths\Admin\BusinessSettings;
    use App\Enums\ViewPaths\Admin\Currency;
    use App\Enums\ViewPaths\Admin\DatabaseSetting;
    use App\Enums\ViewPaths\Admin\EnvironmentSettings;
    use App\Enums\ViewPaths\Admin\SiteMap;
    use App\Enums\ViewPaths\Admin\SoftwareUpdate;
@endphp
<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        {{-- @if (Helpers::modules_permission_check('System Setup', 'System Setting', 'view'))
        <li class="{{ Request::is('admin/business-settings/web-config/'.EnvironmentSettings::VIEW[URI]) ?'active':'' }}">
            <a href="{{route('admin.business-settings.web-config.environment-setup')}}">{{translate('Environment_Settings')}}</a>
        </li>
        @endif --}}
        @if (Helpers::modules_permission_check('System Setup', 'System Setting', 'view'))
        <li class="{{ Request::is('admin/business-settings/web-config/'.BusinessSettings::APP_SETTINGS[URI]) ?'active':'' }}">
            <a href="{{route('admin.business-settings.web-config.app-settings')}}">{{translate('app_Settings')}}</a>
        </li>
        @endif
        @if (Helpers::modules_permission_check('System Setup', 'System Setting', 'software-view'))
        <li class="{{ Request::is('admin/system-settings/'.SoftwareUpdate::VIEW[URI]) ?'active':'' }}">
            <a href="{{route('admin.system-settings.software-update')}}">{{translate('software_Update')}}</a>
        </li>
        @endif
        @if (Helpers::modules_permission_check('System Setup', 'System Setting', 'language-view'))
        <li class="{{ Request::is('admin/business-settings/language') ?'active':'' }}">
            <a href="{{route('admin.business-settings.language.index')}}">{{translate('language')}}</a>
        </li>
        @endif
        @if (Helpers::modules_permission_check('System Setup', 'System Setting', 'currency-view'))
        <li class="{{ Request::is('admin/currency/'.Currency::LIST[URI]) ?'active':'' }}">
            <a href="{{route('admin.currency.view')}}">{{translate('Currency')}}</a>
        </li>
        @endif
        @if (Helpers::modules_permission_check('System Setup', 'System Setting', 'cookie-view'))
        <li class="{{ Request::is('admin/business-settings/'.BusinessSettings::COOKIE_SETTINGS[URI]) ? 'active':'' }}">
            <a href="{{ route('admin.business-settings.cookie-settings') }}">{{translate('cookies')}}</a>
        </li>
        @endif
        @if (Helpers::modules_permission_check('System Setup', 'System Setting', 'database-view'))
        <li class="{{ Request::is('admin/business-settings/web-config/'.DatabaseSetting::VIEW[URI]) ?'active':'' }}">
            <a href="{{route('admin.business-settings.web-config.db-index')}}">{{translate('Clean_Database')}}</a>
        </li>
        @endif
        @if (Helpers::modules_permission_check('System Setup', 'System Setting', 'site-map-view'))
        <li class="{{ Request::is('admin/business-settings/web-config/'.SiteMap::VIEW[URI]) ?'active':'' }}">
            <a href="{{route('admin.business-settings.web-config.mysitemap')}}">{{translate('site_Map')}}</a>
        </li>
        @endif
    </ul>
</div>
