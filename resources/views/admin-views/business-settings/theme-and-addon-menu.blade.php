@php use App\Utils\Helpers; @endphp
@php
    use App\Enums\ViewPaths\Admin\ThemeSetup;
@endphp
<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        @if (Helpers::modules_permission_check('System Setup', 'Themes & Addons', 'view'))
        <li class="{{ Request::is('admin/business-settings/web-config/theme/'.ThemeSetup::VIEW[URI]) ?'active':'' }}">
            <a href="{{route('admin.business-settings.web-config.theme.setup')}}">{{translate('theme_Setup')}}</a>
        </li>
        @endif
        @if (Helpers::modules_permission_check('System Setup', 'Themes & Addons', 'addon-view'))
        <li class="{{ Request::is('admin/addon') ?'active':'' }}">
            <a href="{{route('admin.addon.index')}}">{{translate('system_Addons')}}</a>
        </li>
        @endif
    </ul>
</div>
