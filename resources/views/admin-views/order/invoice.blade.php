<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ 'Invoice' }}</title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/invoice.css') }}">
</head>

<body>
<div class="first">
    <table class="content-position mb-30">
        <tr>
            <th class="p-0 text-left fz-26">
                {{ 'Order Invoice' }}
            </th>
            <th>
                <img height="40" src="{{dynamicStorage(path: "storage/app/public/company/$companyWebLogo")}}" alt="">
            </th>
        </tr>
    </table>
    <table class="bs-0 mb-30 px-10">
        <tr>
            <th class="content-position-y text-left">
                <h4 class="text-uppercase mb-1 fz-14">
                    {{ 'Invoice' }} #{{ $order->id }} {{ ($order->order_type == 'POS' ? '(POS Order)' : '' ) }}
                </h4><br>
                <h4 class="text-uppercase mb-1 fz-14">
                    {{ 'Shop Name' }}
                    : {{ $order->seller_is == 'admin' ? $companyName : (isset($order->seller->shop) ? $order->seller->shop->name : 'Not Found') }}
                </h4>
                @if($order['seller_is']!='admin' && isset($order['seller']) && $order['seller']->gst != null)
                    <h4 class="text-capitalize fz-12">GST
                        : {{ $order['seller']->gst }}</h4>
                @endif
            </th>
            <th class="content-position-y text-right">
                <h4 class="fz-14">{{ 'Date' }} : {{date('d-m-Y h:i:s a',strtotime($order['created_at']))}}</h4>
            </th>
        </tr>
    </table>
</div>
<div class="">
    <section>
        <table class="content-position-y fz-12">
            <tr>
                <td class="font-weight-bold p-1">
                    <table>
                        <tr>
                            <td>
                                @if($order->shipping_address_data)
                                    @php
                                        $shipping_address = $order->shipping_address_data
                                    @endphp
                                    <span class="h2 mt-0">{{ 'Shipping to' }} </span>
                                    <div class="h4 montserrat-normal-600">
                                        <p class="mt-6 mb-0">{{$shipping_address->contact_person_name}}</p>
                                        @if($order->is_guest && isset($shipping_address->email))
                                            <p class="mt-6 mb-0">{{$shipping_address->email}}</p>
                                        @endif
                                        <p class="mt-6 mb-0">{{$shipping_address->phone}}</p>
                                        <p class="mt-6 mb-0">{{$shipping_address->address}}</p>
                                        <p class="mt-6 mb-0">{{ $shipping_address->city }} {{ $shipping_address->zip }} </p>
                                    </div>
                                @else
                                    <span class="h2 m-0">{{ 'Customer Info' }}</span>
                                    <div class="h4 montserrat-normal-600">
                                        @if($order->is_guest)
                                            <p class="mt-6 mb-0">{{ 'Guest User' }}</p>
                                        @else
                                            <p class="mt-6 mb-0">{{ $order->customer !=null? $order->customer['f_name'].' '.$order->customer['l_name']:'Name not found' }}</p>
                                        @endif

                                        @if (isset($order->customer) && $order->customer['id']!=0)
                                            <p class="mt-6 mb-0">{{$order->customer !=null? $order->customer['email']: 'Email not found' }}</p>
                                            <p class="mt-6 mb-0">{{$order->customer !=null? $order->customer['phone']: 'Phone not found' }}</p>
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
                                @if($order->billing_address_data)
                                    @php
                                        $billingAddress = $order->billing_address_data
                                    @endphp
                                    <span class="h2">{{ 'Billing Address' }} </span>
                                    <div class="h4 montserrat-normal-600">
                                        <p class="mt-6 mb-0">{{$billingAddress->contact_person_name}}</p>
                                        @if($order->is_guest && isset($billingAddress->email))
                                            <p class="mt-6 mb-0">{{$billingAddress->email}}</p>
                                        @endif
                                        <p class="mt-6 mb-0">{{$billingAddress->phone}}</p>
                                        <p class="mt-6 mb-0">{{$billingAddress->address}}</p>
                                        <p class="mt-6 mb-0">{{$billingAddress->city}} {{$billingAddress->zip}}</p>
                                    </div>
                                @elseif($order->billingAddress)
                                    <span class="h2">{{ 'Billing Address' }} </span>
                                    <div class="h4 montserrat-normal-600">
                                        <p class="mt-6 mb-0">{{$order->billingAddress ? $order->billingAddress['contact_person_name'] : ""}}</p>
                                        <p class="mt-6 mb-0">{{$order->billingAddress ? $order->billingAddress['phone'] : ""}}</p>
                                        <p class="mt-6 mb-0">{{$order->billingAddress ? $order->billingAddress['address'] : ""}}</p>
                                        <p class="mt-6 mb-0">{{$order->billingAddress ? $order->billingAddress['city'] : ""}} {{$order->billingAddress ? $order->billingAddress['zip'] : ""}}</p>
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

<br>

<div class="">
    <div class="content-position-y">
        <table class="customers bs-0">
            <thead>
            <tr>
                <th>{{ 'SL' }}</th>
                <th>{{ 'Item Description' }}</th>
                <th>{{ 'Unit Price' }}</th>
                <th>{{' Qty'}}</th>
                <th class="text-right">{{ 'Total' }}</th>
            </tr>
            </thead>
            @php
                $subtotal=0;
                $total=0;
                $sub_total=0;
                $total_tax=0;
                $total_shipping_cost=0;
                $total_discount_on_product=0;
                $ext_discount=0;
            @endphp
            <tbody>
            @foreach($order->details as $key=>$details)
                @php $subtotal=($details['price'])*$details->qty @endphp
                <tr>
                    <td>{{$key+1}}</td>
                    <td>
                        {{$details['product']?$details['product']->name:''}}
                        @if($details['variant'])
                            <br>
                            {{ 'Variation' }} : {{$details['variant']}}
                        @endif
                    </td>
                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details['price']), currencyCode: getCurrencyCode(type: 'default')) }}</td>
                    <td>{{$details->qty}}</td>
                    <td class="text-right">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $subtotal), currencyCode: getCurrencyCode(type: 'default')) }}</td>
                </tr>

                @php
                    $sub_total+=$details['price']*$details['qty'];
                    $total_tax+=$details['tax'];
                    $total_shipping_cost+=$details->shipping ? $details->shipping->cost :0;
                    $total_discount_on_product+=$details['discount'];
                    $total+=$subtotal;
                @endphp
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<?php
if ($order['extra_discount_type'] == 'percent') {
    $ext_discount = ($sub_total / 100) * $order['extra_discount'];
} else {
    $ext_discount = $order['extra_discount'];
}
?>
@php($shipping=$order['shipping_cost'])
<div class="content-position-y">
    <table class="fz-12">
        <tr>
            <th class="text-left">
                <h4 class="fz-12 mb-1">{{ 'Payment Details' }}</h4>
                <p class="fz-12 font-normal">
                    {{$order->payment_status}}
                    , {{date('y-m-d',strtotime($order['created_at']))}}
                </p>

                @if ($order->delivery_type !=null)
                    <h4 class="fz-12 mb-1">{{ 'Delivery Info' }} </h4>
                    @if ($order->delivery_type == 'self_delivery')
                        <p class="fz-12 font-normal">
                            <span>
                                {{ 'Self Delivery' }}
                            </span>
                            @if($order->deliveryMan)
                                <br>
                                <span>
                                    {{ 'Delivery Man Name' }} : {{$order->deliveryMan['f_name'].' '.$order->deliveryMan['l_name']}}
                                </span>
                                <br>
                                <span>
                                    {{ 'Delivery Man Phone' }} : {{$order->deliveryMan['phone']}}
                                </span>
                            @else
                                {{ 'Delivery Man not found!' }}
                            @endif
                        </p>
                    @else
                        <p>
                        <span>
                            {{$order->delivery_service_name}}
                        </span>
                            <br>
                            <span>
                            Tracking Id : {{$order->third_party_delivery_tracking_id}}
                        </span>
                        </p>
                    @endif
                @endif

            </th>

            <th>
                <table class="calc-table">
                    <tbody>
                    <tr>
                        <td class="p-1 text-left">{{ 'Sub Total' }}</td>
                        <td class="p-1">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $sub_total), currencyCode: getCurrencyCode(type: 'default')) }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 text-left">{{ 'Tax' }}</td>
                        <td class="p-1">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $total_tax), currencyCode: getCurrencyCode(type: 'default')) }}</td>
                    </tr>
                    @if ($order->order_type=='default_type')
                        <tr>
                            <td class="p-1 text-left">{{ 'Shipping' }}</td>
                            <td class="p-1">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $shipping - ($order['is_shipping_free'] ? $order['extra_discount'] : 0)), currencyCode: getCurrencyCode(type: 'default')) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="p-1 text-left">{{ 'Promotion Discount' }}</td>
                        <td class="p-1">
                            - {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->discount_amount), currencyCode: getCurrencyCode(type: 'default')) }} </td>
                    </tr>
                    @if ($order->order_type=='POS')
                        <tr>
                            <td class="p-1 text-left">{{ 'Extra Discount' }}</td>
                            <td class="p-1">
                                - {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $ext_discount), currencyCode: getCurrencyCode(type: 'default')) }} </td>
                        </tr>
                    @endif
                    <tr>
                        <td class="p-1 text-left">{{ 'Discount On Product' }}</td>
                        <td class="p-1">
                            - {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $total_discount_on_product), currencyCode: getCurrencyCode(type: 'default')) }}</td>
                    </tr>
                    <tr>
                        <td class="border-dashed-top font-weight-bold text-left"><b>{{ 'Total' }}</b></td>
                        <td class="border-dashed-top font-weight-bold">
                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->order_amount), currencyCode: getCurrencyCode(type: 'default')) }}
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
        <table class="">
            <tr>
                <th class="fz-12 font-normal pb-3">
                    {{ 'If you require any assistance or have feedback or suggestions about our site you can email us at' }} <a
                            href="mailto:{{ $companyEmail }}">({{ $companyEmail }})</a>
                </th>
            </tr>
            <tr>
                <th class="content-position-y bg-light py-4">
                    <div class="d-flex justify-content-center gap-2">
                        <div class="mb-2">
                            <i class="fa fa-phone"></i>
                            {{ 'Phone' }} : {{ $companyPhone }}
                        </div>
                        <div class="mb-2">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            {{ 'Email' }} : {{$companyEmail}}
                        </div>
                    </div>
                    <div class="mb-2">
                        {{url('/')}}
                    </div>
                    <div>
                        {{ 'All Copyright Reserved ©' }} {{ date('Y') }} {{ $companyName }}
                    </div>
                </th>
            </tr>
        </table>
    </section>
</div>

</body>
</html>
