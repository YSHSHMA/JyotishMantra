<html>
<head>
    <meta charset="UTF-8">
    <title>{{ ucwords('invoice')}}</title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/invoice.css') }}">
</head>

<body>

@php($companyName = getWebConfig(name: 'company_name'))
<div class="first">
    <table class="content-position mb-30">
        <tr>
            <th class="p-0 text-left font-size-26px">
                {{ ucwords('order Invoice')}}
            </th>
            <th class="p-0 text-right">
                <img height="40" src="{{dynamicStorage(path: "storage/app/public/company/".getWebConfig(name: 'company_web_logo'))}}"
                     alt="">
            </th>
        </tr>
    </table>

    <table class="bs-0 mb-30 px-10">
        <tr>
            <th class="content-position-y text-left">
                <h4 class="text-uppercase mb-1 fz-14">
                    {{ ucwords('Order')}} #{{ $order->id }}
                </h4>
                <br>
                <h4 class="text-uppercase mb-1 fz-14">
                    {{ ucwords('shop Name')}}
                    : {{ $order->seller_is == 'admin' ? $companyName : (isset($order->seller->shop) ? $order->seller->shop->name :  ucwords('not found')) }}
                </h4>
                @if($order['seller_is']!='admin' && isset($order['seller']->gst) != null)
                    <h4 class="text-capitalize fz-12">
                        {{ ucwords('GST')}} : {{ $order['seller']->gst }}
                    </h4>
                @endif
            </th>
            <th class="content-position-y text-right">
                <h4 class="fz-14">
                    {{ ucwords('date')}} : {{date('d-m-Y h:i:s a',strtotime($order['created_at']))}}
                </h4>
            </th>
        </tr>
    </table>
</div>
@if ($order->order_type == 'default_type')
    <div class="">
        <section>
            <table class="content-position-y fz-12">
                <tr>
                    <td class="font-weight-bold p-1">
                        <table>
                            <tr>
                                <td>
                                    @if($order->shipping_address_data)
                                            <?php
                                            $shipping_address = $order->shipping_address_data;
                                            ?>
                                        <span class="h2 m-0">{{ucwords('shipping to')}} </span>
                                        <div class="h4">
                                            <p class="mt-6px mb-0">{{$shipping_address->contact_person_name}}</p>
                                            <p class="mt-6px mb-0">{{$shipping_address->phone}}</p>
                                            <p class="mt-6px mb-0">{{$shipping_address->address}}</p>
                                            <p class="mt-6px mb-0">{{ $shipping_address->city }} {{ $shipping_address->zip }} </p>
                                        </div>
                                    @else
                                        <span class="h2 m-0">{{ ucwords('customer info')}} </span>
                                        <div class="h4">
                                            @if($order->is_guest)
                                                <p class="mt-6px mb-0">Guest User</p>
                                            @else
                                                <p class="mt-6px mb-0">
                                                    {{ $order->customer !=null? $order->customer['f_name'].' '.$order->customer['l_name']:'Name not found' }}
                                                </p>
                                            @endif

                                            @if (isset($order->customer) && $order->customer['id']!=0)
                                                <p class="mt-6px mb-0">
                                                    {{$order->customer !=null? $order->customer['email']: ucwords('email not found')}}
                                                </p>
                                                <p class="mt-6px mb-0">
                                                    {{$order->customer !=null? $order->customer['phone']: ucwords('phone not found')}}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td>
                        <table>
                            <tr>
                                <td class="text-right">
                                    @if(!$order->is_guest && $order->billing_address_data)
                                        <?php
                                            $billingAddress = $order->billing_address_data
                                        ?>
                                        <span class="h2">{{ ucwords('billing address')}} </span>
                                        <div class="h4">
                                            <p class="font-weight-normal mt-6px mb-0">
                                                {{$billingAddress->contact_person_name}}
                                            </p>
                                            <p class="font-weight-normal mt-6px mb-0">
                                                {{$billingAddress->phone}}
                                            </p>
                                            <p class="font-weight-normal mt-6px mb-0">
                                                {{$billingAddress->address}}
                                            </p>
                                            <p class="font-weight-normal mt-6px mb-0">
                                                {{$billingAddress->city}} {{$billingAddress->zip}}
                                            </p>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </section>
    </div>
@else
    <div class="row">
        <section>
            <table class="content-position-y w-100">
                <tr>
                    <td class="text-center" valign="top">
                        <span class="h2 m-0">{{ ucwords('POS order')}} </span>
                    </td>
                </tr>
            </table>
        </section>
    </div>
@endif

<br>

<div>
    <div class="content-position-y">
        <table class="customers bs-0">
            <thead>
            <tr>
                <th>{{ ucwords('no.')}}</th>
                <th>{{ ucwords('item description')}}</th>
                <th>
                    {{ ucwords('unit price')}}
                </th>
                <th>
                    {{ ucwords('qty')}}
                </th>
                <th class="text-right">
                    {{ ucwords('total')}}
                </th>
            </tr>
            </thead>
            <?php
            $total = 0;
            $subTotal = 0;
            $totalTax = 0;
            $totalShippingCost = 0;
            $totalDiscountOnProduct = 0;
            $extraDiscount = 0;
            ?>
            <tbody>
            @foreach($order->details as $key=>$details)
                    <?php $subTotal = ($details['price']) * $details->qty ?>
                <tr>
                    <td>{{$key+1}}</td>
                    <td>
                        {{$details['product']?$details['product']->name:''}}
                        @if($details['variant'])
                            <br>
                            {{ ucwords('variation')}} : {{$details['variant']}}
                        @endif
                    </td>
                    <td>{{ webCurrencyConverter(amount: $details['price']) }}</td>
                    <td>{{$details->qty}}</td>
                    <td class="text-right">{{ webCurrencyConverter(amount: $subTotal) }}</td>
                </tr>

                    <?php
                    $subTotal += $details['price'] * $details['qty'];
                    $totalTax += $details['tax'];
                    $totalShippingCost += $details->shipping ? $details->shipping->cost : 0;
                    $totalDiscountOnProduct += $details['discount'];
                    $total += $subTotal;
                    ?>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<?php
if ($order['extra_discount_type'] == 'percent') {
    $extraDiscount = ($subTotal / 100) * $order['extra_discount'];
} else {
    $extraDiscount = $order['extra_discount'];
}
?>
@php($shipping=$order['shipping_cost'])
<div class="content-position-y">
    <table class="fz-12">
        <tr>
            <th class="text-left width-60">
                <h4 class="fz-12 mb-1">{{ ucwords('payment details')}}</h4>
                <h5 class="fz-12 mb-1 font-weight-normal">{{ str_replace('_',' ',$order->payment_method) }}</h5>
                <p class="fz-12 font-weight-normal">{{$order->payment_status}}
                    , {{date('y-m-d',strtotime($order['created_at']))}}</p>

                @if ($order->delivery_type)
                    <h4 class="fz-12 mb-1">{{ ucwords('delivery_info')}} </h4>
                    @if ($order->delivery_type == 'self_delivery' && $order->deliveryMan)
                        <p class="fz-12 font-normal">
                            <span class="font-weight-normal">
                                {{ ucwords('self delivery')}}
                            </span>
                            <br>
                            <span class="font-weight-normal">
                                {{ ucwords('deliveryman name')}} : {{$order->deliveryMan['f_name'].' '.$order->deliveryMan['l_name']}}
                            </span>
                            <br>
                            <span class="font-weight-normal">
                                {{ ucwords('deliveryman phone')}} : {{$order->deliveryMan['phone']}}
                            </span>
                        </p>
                    @else
                        <p>
                        <span class="font-weight-normal">
                            {{$order->delivery_service_name}}
                        </span>
                            <br>
                            <span class="font-weight-normal">
                            {{ ucwords('tracking id')}} : {{$order->third_party_delivery_tracking_id}}
                        </span>
                        </p>
                    @endif
                @endif
            </th>

            <th class="calc-table">
                <table>
                    <tbody>

                    <tr>
                        <td class="p-1 text-left"><b>{{ ucwords('sub total')}}</b></td>
                        <td class="p-1 text-right">{{ webCurrencyConverter(amount: $subTotal) }}</td>

                    </tr>
                    <tr>
                        <td class="p-1 text-left"><b>{{ ucwords('tax')}}</b></td>
                        <td class="p-1 text-right">{{ webCurrencyConverter(amount: $totalTax) }}</td>
                    </tr>
                    @if($order->order_type == 'default_type')
                        <tr>
                            <td class="p-1 text-left"><b>{{ ucwords('shipping')}}</b></td>
                            <td class="p-1 text-right">{{webCurrencyConverter(amount: $shipping - ($order->is_shipping_free ? $order->extra_discount : 0)) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="p-1 text-left"><b>{{ ucwords('coupon discount')}}</b></td>
                        <td class="p-1 text-right">
                            - {{ webCurrencyConverter(amount: $order->discount_amount) }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 text-left"><b>{{ ucwords('discount on product')}}</b></td>
                        <td class="p-1 text-right">
                            - {{ webCurrencyConverter(amount: $totalDiscountOnProduct) }}</td>
                    </tr>
                    @if ($order->order_type != 'default_type')
                        <tr>
                            <td class="p-1 text-left"><b>{{ ucwords('extra discount')}}</b></td>
                            <td class="p-1 text-right">
                                - {{ webCurrencyConverter(amount: $extraDiscount) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="border-dashed-top font-weight-bold text-left"><b>{{ ucwords('total')}}</b></td>
                        <td class="border-dashed-top font-weight-bold text-right">
                            {{ webCurrencyConverter(amount: $order->order_amount) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </th>
        </tr>
    </table>
</div>
<br>
<br><br><br>

<div class="row">
    <section>
        <table>
            <tr>
                <th class="content-position-y bg-light py-4">
                    <div class="d-flex justify-content-center gap-2">
                        <div class="mb-2">
                            <img height="10" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/telephone.png') }}"
                                 alt="">
                            {{ ucwords('phone')}}
                            : {{ getWebConfig(name: 'company_phone') }}
                        </div>
                        <div class="mb-2">
                            <img height="10" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/email.png') }}" alt="">
                            {{ ucwords('email')}}
                            : {{ getWebConfig(name: 'company_email') }}
                        </div>
                    </div>
                    <div class="mb-2">
                        <img height="10" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/web.png') }}" alt="">
                        {{ ucwords('website')}}
                        : {{url('/')}}
                    </div>
                    <div>
                        {{ ucwords('all copy right reserved © '.date('Y').' ').$companyName}}
                    </div>
                </th>
            </tr>
        </table>
    </section>
</div>

</body>
</html>
