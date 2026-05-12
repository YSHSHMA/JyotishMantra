<div class="container-fluid rtl">
    @php($decimalPointSettings = !empty(getWebConfig(name: 'decimal_point_settings')) ? getWebConfig(name: 'decimal_point_settings') : 0)
    <div class="row g-4 pt-2 mt-0 pb-2 __deal-of align-items-start">
        <div class="col-12">
            <div class="latest-product-margin">
                <div class="d-flex justify-content-between mb-14px">
                    <div class="text-center">
                        <span class="for-feature-title __text-22px font-bold text-center">
                            {{ translate('latest_products')}}
                        </span>
                    </div>
                    <div class="mr-1">
                        <a class="text-capitalize view-all-text web-text-primary"
                        href="javascript:0" onclick="viewAll('latest')">
                            {{ translate('view_all')}}
                            <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left mr-1 ml-n1 mt-1 float-left' : 'right ml-1 mr-n1'}}"></i>
                        </a>
                    </div>
                </div>

                <div class="row mt-0 g-2">
                    @foreach($latest_products as $product)
                        <div class="col-xl-2 col-sm-4 col-md-3 col-lg-2 col-6">
                            <div>
                                @include('web-views.partials._inline-single-product',['product'=>$product,'decimal_point_settings'=>$decimalPointSettings])
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>