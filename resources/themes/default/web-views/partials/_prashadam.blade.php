@if(isset($prashad))
    @php($overallRating = getOverallRating($prashad->reviews))
    <div class="flash_deal_product get-view-by-onclick" data-link="{{ route('product',$prashad->slug) }}">
        @if($prashad->discount > 0)
            <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13">
                <span class="direction-ltr d-block">
                    @if ($prashad->discount_type == 'percent')
                        -{{round($prashad->discount,(!empty($decimal_point_settings) ? $decimal_point_settings: 0))}}%
                    @elseif($prashad->discount_type =='flat')
                        -{{ webCurrencyConverter(amount: $prashad->discount) }}
                    @endif
                </span>
            </span>
        @endif
        <div class=" d-flex">
            <div class="d-flex align-items-center justify-content-center p-12px">
                <div class="flash-deals-background-image">
                    <img class="__img-125px" style="width: 110px !important;" alt="" src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/'.$prashad['thumbnail'], type: 'product') }}">
                </div>
            </div>
            <div class="flash_deal_product_details pl-3 pr-3 pr-1 d-flex mt-3">
                <div>
                    <div>
                        <a href="{{route('product',$prashad->slug)}}"
                           class="flash-product-title text-capitalize fw-semibold">
                            {{ Str::limit($prashad['name'], 80) }}
                        </a>
                    </div>
                    @if($overallRating[0] != 0 )
                        <div class="flash-product-review">
                            @for($inc=1;$inc<=5;$inc++)
                                @if ($inc <= (int)$overallRating[0])
                                    <i class="tio-star text-warning"></i>
                                @elseif ($overallRating[0] != 0 && $inc <= (int)$overallRating[0] + 1.1 && $overallRating[0] > ((int)$overallRating[0]))
                                    <i class="tio-star-half text-warning"></i>
                                @else
                                    <i class="tio-star-outlined text-warning"></i>
                                @endif
                            @endfor
                            <label class="badge-style2">
                                ( {{ count($prashad->reviews) }} )
                            </label>
                        </div>
                    @endif
                    <div class="d-flex flex-wrap gap-8 align-items-center row-gap-0">
                        @if($prashad->discount > 0)
                            <del class="category-single-product-price">
                                {{ webCurrencyConverter(amount: $prashad->unit_price) }}
                            </del>
                        @endif
                        <span class="flash-product-price text-dark fw-semibold">
                            {{ webCurrencyConverter(amount: $prashad->unit_price - getProductDiscount(product: $prashad, price: $prashad->unit_price)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
