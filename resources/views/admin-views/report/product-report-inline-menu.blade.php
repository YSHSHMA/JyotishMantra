@php use App\Utils\Helpers; @endphp
<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        @if (Helpers::modules_permission_check('Product Report', 'Product Report', 'all-product'))
        <li class="{{ Request::is('admin/report/all-product') ?'active':'' }}"><a href="{{route('admin.report.all-product')}}">{{translate('all_Products')}}</a></li>
        @endif
        @if (Helpers::modules_permission_check('Product Report', 'Product Report', 'stock-product'))
        <li class="{{ Request::is('admin/stock/product-stock') ?'active':'' }}"><a href="{{route('admin.stock.product-stock')}}">{{translate('product_Stock')}}</a></li>
        @endif
        @if (Helpers::modules_permission_check('Product Report', 'Product Report', 'wish-listed-product'))
        <li class="{{ Request::is('admin/stock/product-in-wishlist') ?'active':'' }}"><a href="{{route('admin.stock.product-in-wishlist')}}">{{translate('wish_Listed_Products')}}</a></li>
        @endif
    </ul>
</div>
