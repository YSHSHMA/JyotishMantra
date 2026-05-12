@if ($featured_products->count() > 0)
@php($decimalPointSettings = !empty(getWebConfig(name: 'decimal_point_settings')) ? getWebConfig(name: 'decimal_point_settings') : 0)
<div class="container-fluid py-2 rtl px-0 px-md-3">
    <div class="__inline-62 pt-3">
        <div class="feature-product-title mt-0">
            {{ translate('featured_products') }}
            <h4 class="mt-2 height-10">
                <span class="divider">&nbsp;</span>
            </h4>
        </div>
        <div class="text-end px-3 d-none d-md-block">
            <a class="text-capitalize view-all-text web-text-primary"
            href="javascript:0" onclick="viewAll('featured')">
                {{ translate('view_all') }}
                <i class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
            </a>
        </div>
        <div class="feature-product">
            <div class="carousel-wrap p-1">
                <div class="featured-store-slider owl-carousel owl-theme" id="featured_products_list">
                    @foreach ($featured_products as $product)
                    <div>
                        @include('web-views.partials._feature-product', ['product' => $product,'decimal_point_settings' => $decimalPointSettings])
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="text-center pt-2 d-md-none">
                <a class="text-capitalize view-all-text web-text-primary"
                    href="javascript:0" onclick="viewAll('featured')">
                    {{ translate('view_all') }}
                    <i
                        class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endif
