@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app-trustees')

@section('title', translate('donated_info'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('donated_info') }}
        </h2>
    </div>
    <div class="refund-details-card--2 p-4">
        <div class="row gy-2">
            <div class="col-lg-8">
                <div class="card h-100 refund-details-card">
                    <div class="card-body">
                        <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                            <h4 class="d-flex gap-2">
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}" alt=""> Customer information
                            </h4>
                        </div>
                        <div class="media flex-wrap gap-3">
                            <div class="">
                                <img class="avatar rounded-circle avatar-70"
                                    src="{{ getValidImage(path: 'storage/app/public/profile/'.($getDonated['users']['image']??''), type: 'backend-profile') }}"
                                    alt="Image">
                            </div>
                            <div class="media-body d-flex flex-column gap-1">
                                <span class="title-color"><strong>{{ ($getDonated['users']['name']??'') }}</strong></span>
                                <span class="title-color break-all"><strong>{{ ($getDonated['users']['phone']??'') }}</strong></span>
                                <span class="title-color break-all"><strong>{{ ($getDonated['users']['email']??'') }}</strong></span>
                                <hr>
                                <span class="title-color break-all">Pan-Card: <strong>{{ ($getDonated['pan_card']??'') }}</strong></span>
                            </div>
                        </div>
                        <?php
                        $order_information = json_decode($getDonated['information'] ?? '[]', true);
                        $getadsnew = json_decode($getDonated['adsTrust']['set_json'] ?? '[]', true);
                        $newArray = [];
                        $product_amount = 0;
                        if($order_information && count($order_information) > 0) {
                            $adsData = $getadsnew['en'] ?? ($getadsnew['in'] ?? []);
                            $adsById = [];
                            foreach ($adsData as $adsItem) {
                                $adsById[$adsItem['id']] = $adsItem;
                            }
                            foreach ($order_information as $inlist) {
                                $id = $inlist['id'] ?? null;
                                if (!empty($id) || $id == '0') {
                                    $newItem = ['id' => $id];
                                    if (isset($adsById[$id])) {
                                        $adsItem = $adsById[$id];
                                        $newItem['name'] = $adsItem['set_title'] ?? '';
                                    } else {
                                        $newItem['name'] = $inlist['title'] ?? '';
                                    }
                                    $product_amount += (float)$inlist['fullamount'] ?? 0;
                                    $newItem['title'] = $inlist['subtitle'] ?? '';
                                    $newItem['amount'] = $inlist['amount'] ?? '';
                                    $newItem['qty'] = $inlist['qty'] ?? '';
                                    $newItem['fullamount'] = $inlist['fullamount'] ?? '';
                                    $newArray[] = $newItem;
                                }
                            }
                        }
                        $indkey = 1;
                        ?>
                        <div class="border-lg rounded payment mb-lg-3 table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Description</th>
                                        <th>Rate</th>
                                        <th>Tax</th>
                                        <th>Tax Amt</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($product_amount > 0)
                                    <tr>
                                        <td><?php echo $indkey++; ?></td>
                                        <td>
                                            @if ($getDonated['type'] == 'donate_trust')
                                            {{ $getDonated['getTrust']['trust_name'] ?? '' }}
                                            @else
                                            {{ $getDonated['adsTrust']['name'] ?? '' }}
                                            @endif
                                        </td>
                                        
                                        <td>{{ webCurrencyConverter(amount: ($getDonated['amount'] ?? 0) - $product_amount) }}
                                        </td>
                                        <td>Not Applicable</td>
                                        <td>{{ webCurrencyConverter(amount: 0) }}</td>
                                        <td>{{ webCurrencyConverter(amount: ($getDonated['amount'] ?? 0) - $product_amount) }}
                                        </td>
                                    </tr>
                                    @foreach ($newArray as $ky => $vl)
                                    <tr>
                                        <td><?php echo $indkey++; ?></td>
                                        <td>
                                            <span>{{ $vl['name'] }}</span><br>
                                            <span>{{ $vl['title'] }}</span><br>
                                            <span>{{ $vl['amount'] }} *
                                                {{ $vl['qty'] }}</span><br>
                                        </td>
                                        <td>{{ webCurrencyConverter(amount: $vl['fullamount'] ?? 0) }}
                                        </td>
                                        <td>Not Applicable</td>
                                        <td>{{ webCurrencyConverter(amount: 0) }}</td>
                                        <td>{{ webCurrencyConverter(amount: $vl['fullamount'] ?? 0) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="gap-3 mb-4 d-flex justify-content-between flex-wrap align-items-center">
                            <h4 class="">{{ translate('Trust_details') }}</h4>

                        </div>
                        <div class="refund-details">
                            <div class="img">
                                <div class="onerror-image border rounded">
                                @if($getDonated['ads_id'])
                                <img src="{{ getValidImage(path: 'storage/app/public/donate/ads/'.($getDonated['adsTrust']['image']??''), type: 'backend-product') }}" alt="">
                                @else 
                                <img src="{{ getValidImage(path: 'storage/app/public/donate/trust/'.($getDonated['getTrust']['theme_image']??''), type: 'backend-product') }}" alt="">
                                @endif
                                </div>
                            </div>
                            <div class="--content flex-grow-1">
                                <h4>
                                    <a>
                                        <span>Trust Name:&nbsp;{{ ($getDonated['getTrust']['name']??'Mahakal.com')}} </span>
                                    </a>
                                    <br>
                                    @if($getDonated['ads_id'])
                                    <span class="h6" style="font-weight: 500;">Ads Name :&nbsp;{{ ($getDonated['adsTrust']['name']??'') }} </span><br>
                                    @endif
                                </h4>


                            </div>
                            <ul class="dm-info p-0 m-0 w-l-115">

                                <li>
                                    <span class="left">{{ translate('total_price') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($getDonated['amount']??0)), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{ translate('admin_commission') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($getDonated['admin_commission']??0)), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{ translate('final_amount') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($getDonated['final_amount']??0)), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>


                            </ul>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100 refund-details-card">
                    <div class="card-body">
                        <?php $getData = []; ?>
                        <h4 class="mb-3">{{ translate('Payment_summary') }}</h4>
                        <ul class="dm-info p-0 m-0">
                            <li class="align-items-center">
                                <span class="left text-capitalize">{{ translate('donated_date') }}</span>
                                <span>:</span>
                                <span class="right">
                                    <strong>
                                        {{  date('d M,Y h:i A',strtotime($getDonated['created_at']??'')) }}
                                    </strong>
                                </span>
                            </li>
                            <li class="align-items-center">
                                <span class="left">{{ translate('transaction_id') }} </span> <span>:</span>
                                <span class="right">{{ ($getDonated['transaction_id']??'') }}</span>
                            </li>
                            <li class="align-items-center">
                                <span class="left">{{ translate('payment_method') }} </span> <span>:</span> <span
                                    class="right">{{ ((($getDonated['transaction_id']??'') == 'wallet')?'Wallet':'Online') }}</span>
                            </li>
                        </ul>


                        @if (($getData['advance_withdrawal_amount'] ?? 0) != 0)
                        <h4 class="mb-1 mt-3">{{ translate('advance_withdrawal_amount') }}</h4>
                        <ul class="dm-info p-0 m-0">
                            <li class="align-items-center">
                                <span class="left">{{ translate('amount') }} </span> <span>:</span> <span
                                    class="right"></span>
                            </li>

                            <li class="align-items-center">
                                <span class="left">{{ translate('status') }} </span> <span>:</span>
                                <span class="right">

                                    {{ translate('success') }}
                                    {{ translate('failed') }}
                                    {{ translate('pending') }}

                                </span>
                            </li>
                        </ul>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endpush