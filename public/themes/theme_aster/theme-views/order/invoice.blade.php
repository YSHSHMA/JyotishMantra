<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ucwords('invoice')}}</title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ theme_asset('assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <style media="all">
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 600;
            font-display: swap;
            src: local('Montserrat'), url("{{ theme_asset('assets/css/fonts/Montserrat-Regular.woff2') }}") format('woff2');
        }
    </style>
    <link rel="stylesheet" media="all" href="{{ theme_asset('assets/css/invoice.css') }}">
</head>

<body>
@php
    use App\Models\BusinessSetting;
    use App\Utils\BackEndHelper;
    $companyPhone =BusinessSetting::where('type', 'company_phone')->first()->value;
    $companyEmail =BusinessSetting::where('type', 'company_email')->first()->value;
    $companyName =BusinessSetting::where('type', 'company_name')->first()->value;
    $companyWebLogo =BusinessSetting::where('type', 'company_web_logo')->first()->value;
    $companyMobileLogo =BusinessSetting::where('type', 'company_mobile_logo')->first()->value;
@endphp

<div class="first">
    <table class="content-position mb-30">
        <tr>
            <th class="p-0 text-left font-size-26px">
                {{ucwords('order invoice')}}
            </th>
            <th class="p-0 text-right">
                <img loading="lazy" height="40" src="{{dynamicAsset(path: "storage/app/public/company/".$companyWebLogo)}}"
                     alt="{{ $companyName }}">
            </th>
        </tr>
    </table>

    <table class="bs-0 mb-30 px-10">
        <tr>
            <th class="content-position-y text-left">
                <h4 class="text-uppercase mb-1 fz-14">
                    {{ucwords('invoice')}} #{{ $order->id }}
                </h4> <br>
                <h4 class="text-uppercase mb-1 fz-14">
                    {{ucwords('shop name')}}
                    : {{ $order->seller_is == 'admin' ? $companyName : (isset($order->seller->shop) ? $order->seller->shop->name : ucwords('not found')) }}
                </h4>
                @if($order['seller_is']!='admin' && isset($order['seller']->gst) != null)
                    <h4 class="text-capitalize fz-12">
                        {{ucwords('GST')}} : {{ $order['seller']->gst }}
                    </h4>
                @endif
            </th>
            <th class="content-position-y text-right">
                <h4 class="fz-14">{{ucwords('date')}} : {{date('d-m-Y h:i:s a',strtotime($order['created_at']))}}</h4>
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
                                        @php
                                            $shipping_address = $order->shipping_address_data
                                        @endphp
                                        <span class="h2 m-0">{{ucwords('shipping to')}} </span>
                                        <div class="h4 montserrat-normal-600">
                                            <p class="mt-6px mb-0">{{$shipping_address->contact_person_name}}</p>
                                            <p class="mt-6px mb-0">{{$shipping_address->phone}}</p>
                                            <p class="mt-6px mb-0">{{$shipping_address->address}}</p>
                                            <p class="mt-6px mb-0">{{ $shipping_address->city }} {{ $shipping_address->zip }} </p>
                                        </div>
                                    @else
                                        <span class="h2 m-0">{{ucwords('customer info')}} </span>
                                        <div class="h4 montserrat-normal-600">
                                            @if($order->is_guest)
                                                <p class="mt-6px mb-0">Guest User</p>
                                            @else
                                                <p class="mt-6px mb-0">{{ $order->customer !=null? $order->customer['f_name'].' '.$order->customer['l_name']:'Name not found' }}</p>
                                            @endif
                                            @if (isset($order->customer) && $order->customer['id']!=0)
                                                <p class="mt-6px mb-0">{{$order->customer !=null? $order->customer['email']:ucwords('email not found')}}</p>
                                                <p class="mt-6px mb-0">{{$order->customer !=null? $order->customer['phone']:ucwords('phone not found')}}</p>
                                            @endif
                                        </div>
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td>
                        <table>
                            <tr>
                                <td class="text-right">
                                    @if(!$order->is_guest && $order->billing_address_data)
                                        @php
                                            $billingAddress = $order->billing_address_data
                                        @endphp
                                        <span class="h2">{{ucwords('billing address')}} </span>
                                        <div class="h4 montserrat-normal-600">
                                            <p class="font-weight-normal mt-6px mb-0">{{$billingAddress->contact_person_name}}</p>
                                            <p class="font-weight-normal mt-6px mb-0">{{$billingAddress->phone}}</p>
                                            <p class="font-weight-normal mt-6px mb-0">{{$billingAddress->address}}</p>
                                            <p class="font-weight-normal margin-top: 6px; margin-bottom:0px;">{{$billingAddress->city}} {{$billingAddress->zip}}}</p>
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
                        <span class="h2 m-0">{{ucwords('POS order')}} </span>
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
                <th>{{ucwords('no.')}}</th>
                <th>{{ucwords('item description')}}</th>
                <th>{{ucwords('unit price')}}</th>
                <th>{{ucwords('qty')}}</th>
                <th class="text-right">{{ucwords('total')}}</th>
            </tr>
            </thead>
            @php
                $subtotal=0;
                $total=0;
                $sub_total=0;
                $total_tax=0;
                $total_shipping_cost=0;
                $total_discount_on_product=0;
                $extra_discount=0;
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
                            {{ucwords('variation')}} : {{$details['variant']}}
                        @endif
                    </td>
                    <td>{{BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($details['price']))}}</td>
                    <td>{{$details->qty}}</td>
                    <td class="text-right">{{BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($subtotal))}}</td>
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
    $extra_discount = ($sub_total / 100) * $order['extra_discount'];
} else {
    $extra_discount = $order['extra_discount'];
}

?>
@php($shipping=$order['shipping_cost'])
<div class="content-position-y">
    <table class="fz-12">
        <tr>
            <th class="text-left w-60-in-100">
                <h4 class="fz-12 mb-1">{{ucwords('payment details')}}</h4>
                <h5 class="fz-12 mb-1 font-weight-normal">{{ str_replace('_',' ',$order->payment_method) }}</h5>
                <p class="fz-12 font-weight-normal">{{$order->payment_status}}
                    , {{date('y-m-d',strtotime($order['created_at']))}}</p>

                @if ($order->delivery_type)
                    <h4 class="fz-12 mb-1">{{ucwords('delivery info')}} </h4>
                    @if ($order->delivery_type == 'self_delivery' && $order->deliveryMan)
                        <p class="fz-12 font-normal">
                            <span class="font-weight-normal">
                                {{ucwords('self delivery')}}
                            </span>
                            <br>
                            <span class="font-weight-normal">
                                {{ucwords('deliveryman name')}} : {{$order->deliveryMan['f_name'].' '.$order->deliveryMan['l_name']}}
                            </span>
                            <br>
                            <span class="font-weight-normal">
                                {{ucwords('deliveryman phone')}} : {{$order->deliveryMan['phone']}}
                            </span>
                        </p>
                    @else
                        <p>
                        <span class="font-weight-normal">
                            {{$order->delivery_service_name}}
                        </span>
                            <br>
                            <span class="font-weight-normal">
                            {{ucwords('tracking id')}} : {{$order->third_party_delivery_tracking_id}}
                        </span>
                        </p>
                    @endif
                @endif
            </th>

            <th class="calc-table">
                <table>
                    <tbody>
                    <tr>
                        <td class="p-1 text-left"><b>{{ucwords('sub total')}}</b></td>
                        <td class="p-1 text-right">{{BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($sub_total))}}</td>

                    </tr>
                    <tr>
                        <td class="p-1 text-left"><b>{{ucwords('tax')}}</b></td>
                        <td class="p-1 text-right">{{BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($total_tax))}}</td>
                    </tr>
                    @if($order->order_type == 'default_type')
                        <tr>
                            <td class="p-1 text-left"><b>{{ucwords('shipping')}}</b></td>
                            <td class="p-1 text-right">{{BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($shipping - ($order->is_shipping_free ? $order->extra_discount : 0)))}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="p-1 text-left"><b>{{ucwords('coupon discount')}}</b></td>
                        <td class="p-1 text-right">
                            - {{BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($order->discount_amount))}}</td>
                    </tr>
                    <tr>
                        <td class="p-1 text-left"><b>{{ucwords('discount on product')}}</b></td>
                        <td class="p-1 text-right">
                            - {{BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($total_discount_on_product))}}</td>
                    </tr>
                    @if ($order->order_type != 'default_type')
                        <tr>
                            <td class="p-1 text-left"><b>{{ucwords('extra discount')}}</b></td>
                            <td class="p-1 text-right">
                                - {{BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($extra_discount))}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="border-dashed-top font-weight-bold text-left"><b>{{ucwords('total')}}</b></td>
                        <td class="border-dashed-top font-weight-bold text-right">
                            {{BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($order->order_amount))}}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </th>
        </tr>
    </table>
</div>

<br><br><br><br>

<div class="row">
    <section>
        <table>
            <tr>
                <th class="content-position-y bg-light py-4">
                    <div class="d-flex justify-content-center gap-2">
                        <div class="mb-2">
                            <i class="fa fa-phone"></i>
                            {{ucwords('phone')}}
                            : {{$companyPhone}}
                        </div>
                        <div class="mb-2">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            {{ucwords('email')}} : {{$companyEmail}}
                        </div>
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-globe" aria-hidden="true"></i>
                        {{ucwords('website')}} : {{url('/')}}
                    </div>
                    <div>
                        {{ucwords('all copyright reserved © '.date('Y').' ').$companyName}}
                    </div>
                </th>
            </tr>
        </table>
    </section>
</div>
</body>
</html>
