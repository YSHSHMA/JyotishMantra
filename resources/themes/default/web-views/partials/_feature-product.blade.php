@php($overallRating = getOverallRating($product->reviews))
<div class="product-single-hover rtl">
    <div class="overflow-hidden position-relative">
        <div class="inline_product clickable">
            @if($product->discount > 0)
                <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13">
                    <span class="direction-ltr d-block">
                        @if ($product->discount_type == 'percent')
                            -{{round($product->discount,(!empty($decimal_point_settings) ? $decimal_point_settings: 0))}}%
                        @elseif($product->discount_type =='flat')
                            -{{ webCurrencyConverter(amount: $product->discount) }}
                        @endif
                    </span>
                </span>
            @endif
            <a href="{{route('product',$product->slug)}}">
                <img src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/'.$product['thumbnail'], type: 'product') }}" alt="" style="width:100%; height:auto;">
            </a>

            <div class="quick-view">
                <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:" data-product-id="{{ $product->id }}">
                    <i class="czi-eye align-middle"></i>
                </a>
            </div>
            @if($product->product_type == 'physical' && $product->current_stock <= 0)
                <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
            @endif
        </div>
        <div class="single-product-details" style="padding:0;">
            <div style="padding: 10px 10px 0 10px">
                  <div >
                <a href="{{route('product',$product->slug)}}" class="text-capitalize fw-semibold">
                    {{ Str::limit($product['name'], 23) }}
                </a>
            </div>
            
            
            <div class="justify-content-between">
                <div class="product-price">
                    <span class="text-accent text-dark">
                        {{ webCurrencyConverter(amount:
                            $product->unit_price-(getProductDiscount(product: $product, price: $product->unit_price))
                        ) }}
                    </span>
                    @if($product->discount > 0)
                        <del class="category-single-product-price" style="color:red">
                            {{ webCurrencyConverter(amount: $product->unit_price) }}
                        </del>
                    @endif
                    
                </div>
            </div>
            </div>
          

            <!-- @if($overallRating[0] != 0 )
                <div class="rating-show justify-content-between">
                    <span class="d-inline-block font-size-sm text-body">
                        @for($inc=1;$inc<=5;$inc++)
                            @if ($inc <= (int)$overallRating[0])
                                <i class="tio-star text-warning"></i>
                            @elseif ($overallRating[0] != 0 && $inc <= (int)$overallRating[0] + 1.1 && $overallRating[0] > ((int)$overallRating[0]))
                                <i class="tio-star-half text-warning"></i>
                            @else
                                <i class="tio-star-outlined text-warning"></i>
                            @endif
                        @endfor
                        <label class="badge-style">( {{ count($product->reviews) }} )</label>
                    </span>
                </div>
            @endif -->
            <div class="d-flex border-top mt-2" style="height: 2rem;">
    <button type="button" 
            class="w-100 d-flex align-items-center justify-content-center text-white fw-bold border-0"
            style="background-color: #FF7722;"
            onclick="window.location.href='{{ route('product', $product->slug) }}'">
        {{ translate('Add Cart') }}
    </button>
</div>

        </div>
    </div>
</div>

