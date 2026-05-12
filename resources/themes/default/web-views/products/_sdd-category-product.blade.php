<div class="container-fluid rtl my-2">
    @php($decimalPointSettings = getWebConfig(name: 'decimal_point_settings') ?? 0)

    <div class="row mb-3">
        @foreach ($categoryProducts as $categoryId => $categoryProduct)
            <div class="card __shadow h-100 my-2">
                <div class="card-body">
                    <div class="col-12 category-products">
                        <h5>{{ $categoryProduct->first()->category->name }}</h5>
                        <div class="feature-product">
                            <div class="carousel-wrap p-1">
                                <div class="featured-store-slider owl-carousel owl-theme" id="featured_products_list">
                                    @foreach ($categoryProduct as $product)
                                        {{-- <div class="col-xl-2 col-sm-4 col-md-3 col-lg-2 col-6"> --}}
                                            @include('web-views.partials._inline-single-product', [
                                                'product' => $product,
                                                'decimal_point_settings' => $decimalPointSettings,
                                            ])
                                        {{-- </div> --}}
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
