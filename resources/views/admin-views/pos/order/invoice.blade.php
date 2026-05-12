<link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/pos-invoice.css') }}">
<div class="width-363px">
    <div class="text-center pt-4 mb-3">
        <h2 class="line-height-1">{{ getWebConfig('company_name') }}</h2>
        <h5 class="line-height-1 font-size-16px font-weight-lighter">
            {{ ucfirst('phone') }} : {{ getWebConfig('company_phone') }}
        </h5>
    </div>

    <span class="dashed-hr"></span>
    <div class="row mt-3">
        <div class="col-6">
            <h5>{{ ucfirst('order ID') }} : {{ $order['id'] }}</h5>
        </div>
        <div class="col-6">
            <h5 class="font-weight-lighter">
                {{ date('d/M/Y h:i a', strtotime($order['created_at'])) }}
            </h5>
        </div>
        @if($order->customer)
            <div class="col-12">
                <h5>{{ ucfirst('customer Name') }} : {{$order->customer['f_name'].' '.$order->customer['l_name']}}</h5>
                @if ($order->customer->id !=0)
                    <h5>{{ ucfirst('phone') }} : {{$order->customer['phone']}}</h5>
                @endif

            </div>
        @endif
    </div>
    <h5 class="text-uppercase"></h5>
    <span class="dashed-hr"></span>
    <table class="table table-bordered mt-3 text-left width-99">
        <thead>
        <tr>
            <th class="text-center">{{ ucfirst('QTY') }}</th>
            <th class="text-left">{{ ucfirst('DESC') }}</th>
            <th class="text-center">{{ ucfirst('price') }}</th>
        </tr>
        </thead>

        <tbody>
        @php($sub_total=0)
        @php($total_tax=0)
        @php($total_dis_on_pro=0)
        @php($product_price=0)
        @php($total_product_price=0)
        @php($ext_discount=0)
        @php($coupon_discount=0)
        @foreach($order->details as $detail)
            @if($detail->product)

                <tr>
                    <td class="text-left">
                        {{$detail['qty']}}
                    </td>
                    <td class="text-left">
                        <span> {{ Str::limit($detail->product['name'], 200) }}</span><br>
                        @if(count(json_decode($detail['variation'],true))>0)
                            <strong><u>{{ ucfirst('variation') }} : </u></strong>
                            @foreach(json_decode($detail['variation'],true) as $key1 =>$variation)
                                <div class="font-size-sm text-body color-black">
                                    <span>{{ ucfirst($key1) }} :  </span>
                                    <span
                                        class="font-weight-bold">{{$variation}} </span>
                                </div>
                            @endforeach
                        @endif

                        {{ ucfirst('discount') }}
                        : {{setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($detail['discount'],2)), currencyCode: getCurrencyCode()) }}
                    </td>
                    <td class="text-right">
                        @php($amount=($detail['price']*$detail['qty'])-$detail['discount'])
                        @php($product_price = $detail['price']*$detail['qty'])
                        {{setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($amount,2)), currencyCode: getCurrencyCode()) }}
                    </td>
                </tr>
                @php($sub_total+=$amount)
                @php($total_product_price+=$product_price)
                @php($total_tax+=$detail['tax'])

            @endif
        @endforeach
        </tbody>
    </table>
    <span class="dashed-hr"></span>
    <?php


    if ($order['extra_discount_type'] == 'percent') {
        $ext_discount = ($total_product_price / 100) * $order['extra_discount'];
    } else {
        $ext_discount = $order['extra_discount'];
    }
    if (isset($order['discount_amount'])) {
        $coupon_discount = $order['discount_amount'];
    }
    ?>
    <table class="w-100 color-black">
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('items Price') }}:</td>
            <td class="text-right">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($sub_total,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('tax') }} / {{ ucfirst('VAT') }}:</td>
            <td class="text-right">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($total_tax,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('subtotal') }}:</td>
            <td class="text-right">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($sub_total+$total_tax,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('extr discount') }}:</td>
            <td class="text-right">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($ext_discount,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{ ucfirst('coupon discount') }}:</td>
            <td class="text-right">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($coupon_discount,2)), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right font-size-20px">
                {{ ucfirst('total') }}:
            </td>
            <td class="text-right font-size-20px">
                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:round($order->order_amount,2)), currencyCode: getCurrencyCode()) }}
            </td>
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
