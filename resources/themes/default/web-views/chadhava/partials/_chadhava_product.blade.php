{{-- List Show --}}
@if (isset($chadhava))
    @php($overallRating = getOverallRating($chadhava->reviews))
    <div class="product-single-hover style--category shadow-none">
        <div class="overflow-hidden position-relative">
            <div class="inline_product d-flex justify-content-center">
                <div class="d-flex">
                    @if ($chadhava->discount > 0)
                        <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13">
                            <span class="direction-ltr d-block">
                                @if ($chadhava->discount_type == 'percent')
                                    -{{ round($chadhava->discount, !empty($decimal_point_settings) ? $decimal_point_settings : 0) }}%
                                @elseif($chadhava->discount_type == 'flat')
                                    -{{ webCurrencyConverter(amount: $chadhava->discount) }}
                                @endif
                            </span>
                        </span>
                    @endif
                </div>
                <div class="d-block pb-0">
                    <div class="best-selleing-image">
                        <img alt=""
                            src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $chadhava['thumbnail'], type: 'product') }}"
                            class="rounded">
                    </div>
                </div>
            </div>
            <div class="single-product-details">
                @if ($overallRating[0] != 0)
                    <div class="rating-show justify-content-between">
                        <span class="d-inline-block font-size-sm text-body">
                            @for ($inc = 1; $inc <= 5; $inc++)
                                @if ($inc <= (int) $overallRating[0])
                                    <i class="tio-star text-warning"></i>
                                @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1 && $overallRating[0] > ((int) $overallRating[0]))
                                    <i class="tio-star-half text-warning"></i>
                                @else
                                    <i class="tio-star-outlined text-warning"></i>
                                @endif
                            @endfor
                            <label class="badge-style">( {{ count($chadhava->reviews) }} )</label>
                        </span>
                    </div>
                @endif
                <div class="product-name"> {{ Str::limit($chadhava['name'], 20) }}</div>
                <div class="justify-content-between">
                    <div class="product-price d-flex flex-wrap gap-8 align-items-center row-gap-0">
                        @if ($chadhava->discount > 0)
                            <del class="category-single-product-price">
                                {{ webCurrencyConverter(amount: $chadhava->unit_price) }}
                            </del>
                        @endif
                        <span class="text-accent text-dark">
                            {{ webCurrencyConverter(amount: $chadhava->unit_price - getProductDiscount(product: $chadhava, price: $chadhava->unit_price)) }}
                        </span>
                    </div>
                </div>
                {{-- <div id="qtyChadhava-{{$productChadhava->id}}" style="display:{{Session::has('chadhava_products') ? 'block' : 'none'}}">
                            <div class="d-flex justify-content-center align-items-center quantity-box border rounded border-base web-text-primary">
                                <span class="input-group-btn">
                                    <button class="btn btn-number __p-10 web-text-primary" type="button" data-type="minus" data-field="quantity" onclick='ChadhavaQuntityUpdate("{{ $productChadhava->id }}", -1, "{{ $chadhavaDetails->id }}", "{{ $productChadhava->unit_price }}")'>-</button>
                                </span>
                                <input type="text" name="quantity" id="chdhavaInput{{ $productChadhava->id }}"
                                    class="form-control input-number text-center cart-qty-field __inline-29 border-0"
                                    placeholder="{{ translate('1') }}"
                                        value="1"
                                    data-producttype="{{ $productChadhava->product_type }}"
                                    data-minimum-order='1'
                                    data-cart-id='{{ $productChadhava->id }}'
                                    data-increment="0"
                                        oninput='ChadhavaQuntityUpdate("{{ $productChadhava->id }}","{{ $chadhavaDetails->id }}" this.value)'
                                    data-chadhavaid="{{$chadhavaDetails->id}}"
                                    
                                    max="{{ $productChadhava['product_type'] == 'physical' ? $productChadhava->current_stock : 100 }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-number __p-10 web-text-primary" type="button" data-producttype="{{ $productChadhava->product_type }}" data-type="plus" data-field="quantity"  onclick='ChadhavaQuntityUpdate("{{ $productChadhava->id }}",1,"{{ $chadhavaDetails->id }}","{{ $productChadhava->unit_price }}")'>+</button>
                                </span>
                            </div>
                        </div> --}}
            </div>

        </div>
    </div>
@endif
