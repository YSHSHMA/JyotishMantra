@php use App\Utils\Helpers; @endphp
@php
    use App\Enums\ViewPaths\Admin\BusinessSettings;
    use App\Enums\ViewPaths\Admin\PaymentMethod;
@endphp
<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        @if (Helpers::modules_permission_check('Business Setup', 'Business Setting', 'view'))
            <li class="{{ Request::is('admin/business-settings/web-config') ? 'active' : '' }}"><a
                    href="{{ route('admin.business-settings.web-config.index') }}">{{ translate('general') }}</a></li>
        @endif
        @if (Helpers::modules_permission_check('Business Setup', 'Business Setting', 'payment-view'))
            <li
                class="text-capitalize {{ Request::is('admin/business-settings/payment-method/' . PaymentMethod::PAYMENT_OPTION[URI]) ? 'active' : '' }}">
                <a
                    href="{{ route('admin.business-settings.payment-method.payment-option') }}">{{ translate('payment_options') }}</a>
            </li>
        @endif
        @if (Helpers::modules_permission_check('Business Setup', 'Business Setting', 'product-view'))
            <li class="{{ Request::is('admin/product-settings') ? 'active' : '' }}"><a
                    href="{{ route('admin.product-settings.index') }}">{{ translate('products') }}</a></li>
        @endif
        @if (Helpers::modules_permission_check('Business Setup', 'Business Setting', 'order-view'))
            <li
                class="{{ Request::is('admin/business-settings/order-settings/' . BusinessSettings::ORDER_VIEW[URI]) ? 'active' : '' }}">
                <a href="{{ route('admin.business-settings.order-settings.index') }}">{{ translate('orders') }}</a>
            </li>
        @endif
        @if (Helpers::modules_permission_check('Business Setup', 'Business Setting', 'vendor-view'))
            <li class="{{ Request::is('admin/business-settings/seller-settings') ? 'active' : '' }}"><a
                    href="{{ route('admin.business-settings.seller-settings.index') }}">{{ translate('vendors') }}</a>
            </li>
        @endif
        @if (Helpers::modules_permission_check('Business Setup', 'Business Setting', 'customer-view'))
            <li class="{{ Request::is('admin/customer/customer-settings') ? 'active' : '' }}"><a
                    href="{{ route('admin.customer.customer-settings') }}">{{ translate('customers') }}</a></li>
        @endif
        @if (Helpers::modules_permission_check('Business Setup', 'Business Setting', 'delivery-men-view'))
            <li
                class="text-capitalize {{ Request::is('admin/business-settings/delivery-man-settings') ? 'active' : '' }}">
                <a
                    href="{{ route('admin.business-settings.delivery-man-settings.index') }}">{{ translate('delivery_men') }}</a>
            </li>
        @endif
        @if (Helpers::modules_permission_check('Business Setup', 'Business Setting', 'shipping-method-view'))
            <li
                class="text-capitalize {{ Request::is('admin/business-settings/shipping-method/index') ? 'active' : '' }}">
                <a
                    href="{{ route('admin.business-settings.shipping-method.index') }}">{{ translate('shipping_Method') }}</a>
            </li>
        @endif
        @if (Helpers::modules_permission_check('Business Setup', 'Business Setting', 'delivery-restriction-view'))
            <li
                class="text-capitalize {{ Request::is('admin/business-settings/delivery-restriction') ? 'active' : '' }}">
                <a
                    href="{{ route('admin.business-settings.delivery-restriction.index') }}">{{ translate('delivery_restriction') }}</a>
            </li>
        @endif
    </ul>
</div>
