@extends('layouts.front-end.app')
@section('title', $leadsDetails->service_name)
@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/payment.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/home.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/theme.css') }}">
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
        <style>
        #productList{ 
            background-color: white;
            border-radius: 6px;
            box-shadow: 2px 2px 2px 2px #f3f3f3;
        }
    </style>
@endpush
@section('content')
    @php
        $final_price_val = 0;
    @endphp
    <div class="w-full h-full sticky md:top-[68px] top-0 z-20">
        <div class="bg-bar w-full">
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto" id="breadcrum-container-outer">
                <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                    @include('web-views.aushthan.partials.statusbar')
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-3 rtl px-0 px-md-3 text-align-direction" id="cart-summary">
       <!--  <h3 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">
            <a href="#"><span aria-hidden="true"><i class="fa fa-arrow-left"></i></span></a> 
        </h3> -->
        @php
            $selected_product_array = [];

        @endphp
        <div class="row g-3 mx-max-md-0">
            <section class="col-lg-6 px-max-md-0">
                <div class="cards">
                    <div class="card-header" id="">
                        <div class="details __h-100">
                            <span class="mb-2 __inline-24">{{ $leadsDetails->service_name }}</span>
                            <div class="d-flex justify-content-between"> {{ $leadsGet->package_name }}
                          
                            <span class=""><b>{{ webCurrencyConverter(amount: $leadsGet['package_price']) }} </b></span>
                           
                            </div>
                           
                            <hr class="my-2">
                            <div class="flex flex-col">
                                <div class="flex items-center space-x-1 pt-[16px] md:pt-2">
                                
                                    <span class="mb-2">
                                        <i class="fa fa-calendar" aria-hidden="true" style="color: var(--primary-clr);"></i>
                                    </span>
                                            {{date('d', strtotime($leadsGet->booking_date)) }},
                                            {{ translate(date('F', strtotime($leadsGet->booking_date))) }} ,
                                            {{ translate(date('l', strtotime($leadsGet->booking_date))) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="collapse{{ $leadsGet->id }}" class="collapse" aria-labelledby=""
                        data-parent="#accordionExample">
                        <div class="card-body">
                            {!! $leadsGet->detail !!}
                        </div>
                    </div>
                </div>
                <div class="" id="productList">
                    <table class='table table-borderless table-thead-bordered table-nowrap table-align-middle'>
                        <tbody>
                            @if (!empty($leadsGet->productleads))
                                @foreach ($leadsGet->productleads as $key => $pval)
                                    @php
                                        array_push($selected_product_array, $pval->product_id);
                                    @endphp

                                    <tr>
                                        <td class='__w-45'>
                                            <div class='d-flex gap-3'>
                                                <div class=''>
                                                    <a href='http://localhost/mahakal/product/crystal-bracelet-Z7HdvN'
                                                        class='position-relative overflow-hidden'>
                                                        <img class='rounded __img-62 '
                                                            src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $pval->productsData->thumbnail, type: 'product') }}"
                                                            id="Productimage" alt='Product'>
                                                    </a>
                                                </div>
                                                <div class='d-flex flex-column gap-1'>
                                                    <div class='text-break __line-2 __w-18rem '>
                                                        <a href='#' id='productName'>{{ $pval->product_name }}</a>
                                                    </div>
                                                    <div class='d-flex flex-wrap gap-2 '>
                                                        <div class='text-center'>
                                                            <div class='fw-semibold' id='productPrice'>
                                                                {{ $pval->qty }} X
                                                                {{ webCurrencyConverter(amount: $pval->product_price * $pval->qty) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class='__w-15p text-center'>
                                            <div class='qty d-flex justify-content-center align-items-center gap-3'>
                                                <span class="qty_minus" data-cart-id="{{ $pval->product_id }}"
                                                    onclick='QuantityUpdate("{{ $pval->product_id }}", -1, "{{ $pval->id }}", "{{ $pval->product_price }}","{{ $pval->leads_id }}","{{ $leadsGet->package_price }}")'
                                                    data-increment='{{ -1 }}'
                                                    data-event="{{ $pval->qty == 1 ? 'delete' : 'minus' }}"><i
                                                        class="{{ $pval->qty > 1 ? 'tio-remove' : 'tio-delete text-danger' }}"
                                                        id="DeleteIcon{{ $pval->product_id }}"></i>
                                                </span>

                                                <input type='text' class='qty_input cartQuantity{{ $pval->product_id }}'
                                                    value="{{ $pval->qty }}" name='quantity{{ $pval->product_id }}'
                                                    id='cart_quantity_web{{ $pval->product_id }}' data-minimum-order='1'
                                                    data-cart-id='{{ $pval->product_id }}'
                                                    data-increment="{{ '0' }}"
                                                    oninput='QuantityUpdate("{{ $pval->product_id }}","{{ $pval->id }}" this.value)'>

                                                <span class="qty_plus" data-cart-id="{{ $pval->product_id }}"
                                                    data-increment="{{ '1' }}"
                                                    onclick='QuantityUpdate("{{ $pval->product_id }}",1,"{{ $pval->id }}","{{ $pval->product_price }}","{{ $pval->leads_id }}","{{ $leadsGet->package_price }}")'><i
                                                        class='tio-add'></i> </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <aside class="col-lg-12 pt-2 pt-lg-2 px-max-md-0 order-summery-aside" id="price-load">
                    <div class="__cart-total __cart-total_sticky">
                        <div class="cart_total p-0">
                            <div class="pt-2 d-flex justify-content-between">
                                <span class="cart_value">Item</span>
                                <!-- <span class="cart_value">Qty</span> -->
                                <span class="cart_value">Price</span>
                           
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="cart_title">{{ $leadsGet->package_name }}</span>
                                <!-- <span class="cart_value" style="margin-right: 10rem;">X 1 </span> -->
                                <span class="cart_value">{{ webCurrencyConverter(amount:  $leadsGet['package_price']) }}
                                </span>
                            </div>
                            <div id="productCount">
                                <div class="finalProduct">
                                    @if (!empty($leadsGet->productleads))
                                        @foreach ($leadsGet->productleads as $pval)
                                        @php
                                            $final_price_val +=$pval->product_price * $pval->qty;
                                        @endphp
                                            <input type="hidden" name="final_price"   id="productCountFinal{{ $pval->product_id }}"
                                                value="{{ $pval->final_price }}.00">
                                            <div class="d-flex justify-content-between">
                                                <span class="cart_title">{{ $pval->product_name }}</span>
                                                <!-- <span class="cart_value" style="margin-right: 11rem;">X {{ $pval->qty  }}</span> -->
                                                <span class="cart_value totalProduct{{ $pval->product_id }}"> {{ webCurrencyConverter(amount:  $pval->product_price * $pval->qty)  }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <hr class="my-2">

                            @php
                                if (auth('customer')->check()) {
                                    $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                }
                                $couponDiscount = session()->has('coupon_discount_vippooja') ? session('coupon_discount_vippooja') : 0;
                                $productTotalAmount = $leadsGet['package_price'] + $final_price_val - $couponDiscount;
                                $totalAmount = $productTotalAmount - $customer->wallet_balance;
                                
                            @endphp
                            @if($customer->wallet_balance > 0)
                            <div id="productCounts">
                                <div class="finalProducts">
                                    <div class="d-flex">
                                        <span class="cart_title">{{ translate('wallet_balance') }} </span>
                                        <span class="cart_value text-success">  ({{ webCurrencyConverter(amount: $customer->wallet_balance) }})</span>
                                    </div>                            
                                    <div class="d-flex justify-content-between">
                                        <span class="cart_title">{{ translate('Amount_Paid_(via_Wallet)') }}</span>
                                        @if($customer->wallet_balance < $productTotalAmount)
                                        <span class="cart_value text-danger"> - {{ webCurrencyConverter(amount: $customer->wallet_balance) }}</span>
                                        @else
                                        <span class="cart_value text-danger"> - {{ webCurrencyConverter(amount: $productTotalAmount) }}</span>
                                        
                                        @endif
                                    </div>
                                    @if($customer->wallet_balance < $productTotalAmount)
                                        <div class="d-flex justify-content-between">
                                            <span class="cart_title">{{ translate('Remaining_Amount_to_Pay') }}</span>
                                            <span class="cart_value text-danger">{{ webCurrencyConverter($totalAmount) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                        </div>
                        <hr class="my-2">
                            @if ($leadsGet['package_id'] == 7)
                            @include('web-views.aushthan.partials._couponnvipaushthan')
                            @elseif($leadsGet['package_id'] == 8)
                            @include('web-views.aushthan.partials._couponintanceanushthan')
                            @endif
                        <hr class="my-2">
                    </div>
                </aside>
            </section>
            @php
                $productIds = json_decode($leadsGet->product_id);
                $products_data = is_array($productIds) ? \App\Models\Product::whereIn('id', $productIds)->get() : collect();
            @endphp
            <section class="col-lg-6 px-max-md-0">
                <div class="pt-3 pb-3">
                    <span class=" __text-16px font-bold text-capitalize">
                        {{ translate('Add_more_offering_items') }}
                    </span>
                </div>
                <div style="height: 440px; overflow-y: auto;">
                @if(!empty($products_data))
                    @foreach ($products_data as $product)
                        @if (!in_array($product->id, $selected_product_array))
                            @php($overallRating = getOverallRating($product->reviews))
                            <div class="flash_deal_product rtl cursor-pointer mb-2"
                                id="get-view-by-onclick{{ $product->id }}"
                                data-link="{{ route('product', $product->slug) }}" data-Pid="{{ $product->id }}"
                                data-qtyMin="{{ $product->minimum_order_qty }}" data-Pname=" {{ $product['name'] }}"
                                data-Pprice="{{ webCurrencyConverter(amount: $product->unit_price) }}">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center justify-content-center p-3">
                                        <div class="flash-deals-background-image image-default-bg-color">
                                            <img class="__img-125px" alt=""
                                                src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}">
                                        </div>
                                    </div>
                                    <div class="flash_deal_product_details pl-3 pr-3 pr-1 d-flex align-items-center">
                                        <div>
                                            <div>
                                                <h1 class="flash-product-title"
                                                    style="font-size: 18px;font-weight: 600;line-height: 14px;margin-bottom: 5px;">
                                                    {{ $product['name'] }}
                                                </h1>
                                            </div>
                                            <div class="widget-meta d-flex flex-wrap gap-8 align-items-center row-gap-0">
                                                <p>{!! Str::limit($product->details,200) !!}</p>
                                            </div>
                                            @if ($overallRating[0] != 0)
                                                <div class="flash-product-review">
                                                    @for ($inc = 1; $inc <= 5; $inc++)
                                                        @if ($inc <= (int) $overallRating[0])
                                                            <i class="tio-star text-warning">
                                                            </i>
                                                        @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1 && $overallRating[0] > ((int) $overallRating[0]))
                                                            <i class="tio-star-half text-warning"></i>
                                                        @else
                                                            <i class="tio-star-outlined text-warning"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            @endif
                                            <div class="d-flex flex-wrap gap-8 align-items-center row-gap-0">
                                                <span class="flash-product-price fw-semibold text-dark">
                                                    &#8377; {{ $product->unit_price }}
                                                </span>
                                                <button class="btn btn--primary rounded-pill text-uppercase py-1 fs-12"
                                                    type="button" onclick="addPoojaProduct(this)"
                                                    data-productid="{{ $product->id }}"
                                                    data-name=" {{ $product['name'] }}"
                                                    data-price="{{ $product->unit_price }}"
                                                    data-image="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}"
                                                    data-qtymin="{{ $product->minimum_order_qty }}"
                                                    data-event="{{ $product['quantity'] == $product->minimum_order_qty ? 'delete' : 'minus' }}"
                                                    data-poojaprice="{{ $leadsGet['package_price'] }}"
                                                    data-leadid="{{ $leadsGet['leadId'] }}"
                                                    data-serviceid="{{ $leadsGet['service_id'] }}"> <i
                                                        class="tio-add"></i> {{ translate('add')}} </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center p-4">
                        <img class="mb-3 w-160"  src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"    alt="">
                        <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                    </div>
                @endif
            </div>
            </section>
        </div>
    </div>
    {{-- Vishesh Prashadam --}}
      <section class="new-arrival-section">
        <div class="container rtl mt-4">
            @if ($prashadamList->count() >0 )
            <div class="section-header">
                <div class="arrival-title d-block">
                    <div class="text-capitalize">
                        {{ translate('Special_Prasadam_for_the_temple') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
   
        <div class="container rtl mb-3 overflow-hidden">
            <div class="py-2">
                <div class="new_arrival_product">
                    <div class="carousel-wrap">
                        <div class="owl-carousel owl-theme new-arrivals-product">
                            @foreach($prashadamList as $key=> $prashad)
                             @include('web-views.partials._prashadam',['prashad'=>$prashad])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Vishesh Prashadam --}}
@endsection
@push('script')
    <script src="{{theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js')}}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
    <script type="text/javascript">
        // Total Payment
        function addPoojaProduct(that) {
            var productid = $(that).data('productid');
            var name = $(that).data('name');
            var price = $(that).data('price');
            var image = $(that).data('image');
            var qtyMin = $(that).data('qtymin');
            var leadid = $(that).data('leadid');
            var serviceid = $(that).data('serviceid');
            var poojaprice = $(that).data('poojaprice');
            $.ajax({
                url: '{{ route('poojaproduct', $leadsGet->id) }}',
                method: 'POST',
                data: {
                    productid: productid,
                    name: name,
                    price: price,
                    image: image,
                    qtyMin: qtyMin,
                    lead: leadid,
                    serviceid: serviceid,
                    poojaprice: poojaprice,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $("#productList").load(location.href + " #productList", function() {
                        $(this).addClass("mt-2 pt-lg-2 mb-1 card");
                    });
                    // Check if there are no products
                    
                    var ProductCountList = `<div class="d-flex justify-content-between">
                                        <span class="cart_title">${name}</span>
                                        <span class="cart_value totalProduct${productid}">&#8377; ${price}.00</span>
                                      </div>`;
                    $('#productCount').find('div.finalProduct').append(ProductCountList);
                    $('#get-view-by-onclick' + productid).remove();
                    // var oldprice = parseInt($('#mainProductPrice').text());
                    // var newprice = parseInt(price) + oldprice;
                    // $('#mainProductPrice').text(newprice);
                    // $('#mainProductPriceInput').val(newprice);
                    $("#cart-summary").load(location.href + "#cart-summary");
                    $("#productList").load(location.href + " #productList");
                    $("#price-load").load(location.href + " #price-load");


                },
                error: function(xhr, status, error) {}
            });

        }
        // localStorage.clear(); // Assuming your data is stored under the key "products"
        // console.log("Item 'products' removed from localStorage!");
        function updateTotalPayment() {
            totalPayment = 0;
            products.forEach(function(product) {
                totalPayment += parseFloat(product.price);
            });
            $('#ProductPrice').text('Total Price: ' + totalPayment.toFixed(2));
        }

        function updateProductPrice() {
            $('#ProductPrice').val(totalPayment.toFixed(2));
        }
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/payment.js') }}"></script>

    <script>
        function QuantityUpdate(cartId, quantity, updateid, pprice, leadid, poojaprice) {
            var inputBox = $('#cart_quantity_web' + cartId);
            console.log(inputBox.val());

            if (quantity == -1) {
                if (inputBox.val() == 1) {
                    deleteQuantity(updateid, cartId,pprice);
                }
                if (inputBox.val() == 2) {
                    $('#DeleteIcon' + cartId).addClass('tio-delete text-danger');
                    $('#DeleteIcon' + cartId).removeClass('tio-remove');
                    $('#get-view-by-onclick' + cartId).append();
                    toastr.warning('Quantity not Applicable.');
                    var newQuantity = parseInt(inputBox.val()) + quantity;
                    inputBox.val(newQuantity);
                    ProductQuantity(cartId, quantity, updateid, pprice, leadid, poojaprice, newQuantity);
                } else {
                    var newQuantity = parseInt(inputBox.val()) + quantity;
                    inputBox.val(newQuantity);
                    ProductQuantity(cartId, quantity, updateid, pprice, leadid, poojaprice, newQuantity);
                }
            } else {
                $('#DeleteIcon' + cartId).removeClass('tio-delete text-danger');
                $('#DeleteIcon' + cartId).addClass('tio-remove');
                var newQuantity = parseInt(inputBox.val()) + quantity;
                inputBox.val(newQuantity);
                ProductQuantity(cartId, quantity, updateid, pprice, leadid, poojaprice, newQuantity);
            }

            //
        }

        function ProductQuantity(cartId, quantity, updateid, pprice, leadid, poojaprice, newQuantity) {
            $.ajax({
                url: '{{ route('updateCartQuantity') }}',
                method: 'POST',
                data: {
                    updateid: updateid,
                    price: pprice,
                    cartId: cartId,
                    quantity: newQuantity,
                    leadid: leadid,
                    poojaprice: poojaprice,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('.totalProduct' + cartId).text(response.data.final_price.final_price + '.00');
                    $('#productCountFinal' + cartId).val(response.data.final_price);
                    $('#mainProductPriceInput').val(parseInt(poojaprice) + parseInt(response.data
                    .total_amount));
                    var localPrice = parseInt(response.data.total_amount) + parseInt(poojaprice) + '.00';
                    $('#mainProductPrice').text(localPrice) + '.00';
                    $('#mainProductPriceInput').text(localPrice) + '.00';
                    $("#cart-summary").load(location.href + "#cart-summary");
                    $("#productList").load(location.href + " #productList");
                    $("#price-load").load(location.href + " #price-load");


                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
        // Delete Quantity
        function deleteQuantity(updateid, cartId,pprice) {
            $.ajax({
                url: '{{ route('deleteQuantity') }}',
                method: 'POST',
                data: {
                    pprice: pprice,
                    updateid: updateid,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(response.message);
                    $("#cart-summary").load(location.href + "#cart-summary");
                    $("#productList").load(location.href + " #productList");
                    $("#price-load").load(location.href + " #price-load");

                   
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
    
@endpush
