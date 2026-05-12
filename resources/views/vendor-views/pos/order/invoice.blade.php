<link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/pos-invoice.css') }}">

<div class="width-363px">
    <div class="text-center pt-4 mb-3">
        <h2 class="line-height-1">{{ $shop->name }}</h2>
        <h5 class="line-height-1">
            {{ ucfirst('phone') }} : {{ $shop->contact}}
        </h5>
    </div>

    <span class="dashed-hr"></span>
    <div class="row mt-3">
        <div class="col-6">
            <h5>
                {{ ucfirst('order ID') }} : {{ $order['id'] }} {{ ($order->order_type == 'POS' ? '(POS Order)':'' ) }}
            </h5>
        </div>
        <div class="col-6">
            <h5 class="font-weight-lighter">
                {{ date('d/M/Y h:i a', strtotime($order['created_at'])) }}
            </h5>
        </div>
        @if($order->customer)
            <div class="col-12">
                <h5>{{ ucfirst('customer Name') }} : {{ $order->customer['f_name'].' '.$order->customer['l_name'] }}</h5>
                @if ($order->customer->id !=0)
                    <h5>{{ ucfirst('phone') }} : {{ $order->customer['phone'] }}</h5>
                @endif

            </div>
        @endif
    </div>
    <h5 class="text-uppercase"></h5>
    <span class="dashed-hr"></span>
    <table class="table table-bordered mt-3 text-left width-99">
        <thead>
        <tr>
            <th class="pl--0">{{ ucfirst('QTY') }}</th>
            <th class="text-left">{{ ucfirst('DESC') }}</th>
            <th class="text-right pr--0">{{ ucfirst('price') }}</th>
        </tr>
        </thead>

        <tbody>
        @php($subTotal=0)
        @php($totalTax=0)
        @php($productPrice=0)
        @php($totalProductPrice=0)
        @php($extraDiscount=0)
        @php($couponDiscount=0)
        @foreach($order->details as $detail)
            @if($detail->product)

                <tr>
                    <td class="pl--0">
                        {{ $detail['qty'] }}
                    </td>
                    <td class="text-left">
                        <span> {{ Str::limit($detail->product['name'], 200) }}</span><br>
                        @if(count(json_decode($detail['variation'],true))>0)
                            <strong><u>{{ ucfirst('variation') }} : </u></strong>
                            @foreach(json_decode($detail['variation'],true) as $key1 =>$variation)
                                <div class="font-size-sm text-body">
                                    <span>{{ $key1}} :  </span>
                                    <span
                                        class="font-weight-bold">{{ $variation }} </span>
                                </div>
                            @endforeach
                        @endif

                        {{ ucfirst('discount') }}
                        : {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: round($detail['discount'],2)), currencyCode: getCurrencyCode()) }}
                    </td>
                    <td class="text-right pr--0">
                        @php($amount=($detail['price']*$detail['qty'])-$detail['discount'])
                        @php($productPrice = $detail['price']*$detail['qty'])
                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($amount,2)), currencyCode: getCurrencyCode()) }}
                    </td>
                </tr>
                @php($subTotal+=$amount)
                @php($totalProductPrice+=$productPrice)
                @php($totalTax+=$detail['tax'])

            @endif
        @endforeach
        </tbody>
    </table>
    <span class="dashed-hr"></span>
    <?php


    if ($order['extra_discount_type'] == 'percent') {
        $extraDiscount = ($totalProductPrice / 100) * $order['extra_discount'];
    } else {
        $extraDiscount = $order['extra_discount'];
    }
    if (isset($order['discount_amount'])) {
        $couponDiscount = $order['discount_amount'];
    }
    ?>
    <table class="width-100">
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('items Price') }}:</td>
            <td class="text-right">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($subTotal,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('tax') }} / {{ ucfirst('VAT') }}:</td>
            <td class="text-right">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($totalTax,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('subtotal') }}:</td>
            <td class="text-right">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($subTotal+$totalTax,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('extra discount') }}:</td>
            <td class="text-right">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($extraDiscount,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('coupon discount') }}:</td>
            <td class="text-right">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($couponDiscount,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('total') }}:</td>
            <td class="text-right">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($order->order_amount,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
    </table>


    <div class="d-flex flex-row justify-content-between border-top">
        <span>{{ ucfirst('paid by') }}: {{ ucfirst($order->payment_method) }}</span>
    </div>
    <span class="dashed-hr"></span>
    <h5 class="text-center pt-3">
        """{{ ucfirst('THANK YOU') }}"""
    </h5>
    <span class="dashed-hr"></span>
</div>
