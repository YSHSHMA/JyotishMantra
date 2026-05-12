@php use App\Utils\Helpers; @endphp
<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        @if (Helpers::modules_permission_check('3rd Party', 'Payment Method', 'view'))
        <li class="{{ Request::is('admin/business-settings/payment-method') ?'active':'' }}"><a class="text-capitalize" href="{{route('admin.business-settings.payment-method.index')}}">{{translate('digital_payment_methods')}}</a></li>
        @endif
        @if (Helpers::modules_permission_check('3rd Party', 'Payment Method', 'offline-payment-view'))
        <li class="{{ Request::is('admin/business-settings/offline-payment-method/*') ?'active':'' }}"><a class="text-capitalize" href="{{route('admin.business-settings.offline-payment-method.index')}}">{{translate('offline_payment_methods')}}</a></li>
        @endif
    </ul>
</div>
