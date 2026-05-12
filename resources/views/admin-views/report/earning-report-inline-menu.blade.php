@php use App\Utils\Helpers; @endphp
<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        @if (Helpers::modules_permission_check('Sales & Transaction Report', 'Earning Reports', 'admin-earnings'))
        <li class="{{ Request::is('admin/report/admin-earning') ?'active':'' }}"><a href="{{route('admin.report.admin-earning', ['date_type' => 'this_year'])}}">{{translate('admin_Earning')}}</a></li>
        @endif
        @if (Helpers::modules_permission_check('Sales & Transaction Report', 'Earning Reports', 'vendor-earnings'))
        <li class="{{ Request::is('admin/report/seller-earning') ?'active':'' }}"><a href="{{route('admin.report.seller-earning', ['date_type' => 'this_year'])}}">{{translate('vendor_Earning')}}</a></li>
        @endif
    </ul>
</div>
