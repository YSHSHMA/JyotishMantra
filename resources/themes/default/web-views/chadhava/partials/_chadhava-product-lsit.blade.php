<style>
.qty_input {
    width: 68px;
    height: 30px;
}    
</style>
<div class="flash_deal_product">
    <div class=" d-flex">
        <div class="d-flex align-items-center justify-content-center p-12px">
            <div class="flash-deals-background-image">
                <img class="__img-125px" alt="" src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $productChadhava['thumbnail'], type: 'product') }}">
            </div>
        </div>
        <div class="flash_deal_product_details pl-3 pr-3 pr-1 d-flex mt-3">
            <div>
                <div>
                    <a href="#"
                        class="flash-product-title text-capitalize fw-semibold">
                        {{ $productChadhava->name }}
                    </a>
                    <div class="widget-meta d-flex flex-wrap gap-8 align-items-center row-gap-0">
                        {!! substr($productChadhava->details, 0, 40) !!}{!! strlen($productChadhava->details) > 10 ? '...' : '' !!}
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-8 align-items-center row-gap-0 pb-2">
                    <span class="flash-product-price fw-semibold text-dark">
                        &#8377; {{ $productChadhava->unit_price }}
                    </span>
                   
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
                   
                        <div id="addtoBtn-{{$productChadhava->id}}" style="display:block}}">
                            <input type="hidden" name="chdhavaPrice" id="chdhavaPrice{{ $productChadhava->id }}" value="{{ $productChadhava->unit_price }}">
                            <input type="hidden" name="chdhavaQty" id="chdhavaQty{{ $productChadhava->id }}" value="1">
                            <button class="btn btn--primary rounded-pill text-uppercase py-1 fs-12"
                            type="button" onclick="addChadhavaProduct(this)"
                            data-productid="{{ $productChadhava->id }}"
                            data-name=" {{ $productChadhava['name'] }}"
                            data-price="{{ $productChadhava->unit_price }}"
                            data-image="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $productChadhava['thumbnail'], type: 'product') }}"
                            data-qtymin="1"
                            data-event="{{ $productChadhava['quantity'] == $productChadhava->minimum_order_qty ? 'delete' : 'minus' }}"
                            data-chadhavaid="{{$chadhavaDetails->id}}"><i class="tio-add"></i> Add </button>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
