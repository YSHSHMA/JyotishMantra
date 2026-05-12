@extends('layouts.front-end.app')

@section('title', translate('donate_order_list'))

@section('content')

<div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
    <div class="row">
        @include('web-views.partials._profile-aside')

        <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">
            <div class="card __card d-none d-lg-flex web-direction customer-profile-orders">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-0 mb-md-3">
                        <h5 class="font-bold mb-0 fs-16">{{ translate('Donate_order_list') }}</h5>
                    </div>

                    @if ($donateOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table __table __table-2 text-center">
                            <thead class="thead-light">
                                <tr>
                                    <td class="tdBorder">
                                        <div>
                                            <span class="d-block spandHeadO text-start text-capitalize"> {{ translate('Sno')}} </span>
                                        </div>
                                    </td>

                                    <td class="tdBorder">
                                        <div>
                                            <span class="d-block spandHeadO">
                                                {{ translate('name') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="tdBorder">
                                        <div>
                                            <span class="d-block spandHeadO">
                                                {{ translate('amount') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="tdBorder">
                                        <div>
                                            <span class="d-block spandHeadO">
                                                {{ translate('status') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="tdBorder">
                                        <div>
                                            <span class="d-block spandHeadO">
                                                {{ translate('action') }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($donateOrders as $donate)
                                <tr>
                                    <td>{{ ($loop->iteration) }}</td>
                                    <td class="bodytr">
                                        <div class="media-order">
                                            <a class="d-block position-relative">
                                                @if($donate['type'] == 'donate_trust')
                                                <img src="{{ getValidImage(path: 'storage/app/public/donate/trust/'.($donate['getTrust']['theme_image']??'') , type: 'counselling') }}" alt="{{ translate('donate') }}">
                                                @else
                                                <img src="{{ getValidImage(path: 'storage/app/public/donate/ads/'.($donate['adsTrust']['image']??'') , type: 'counselling') }}" alt="{{ translate('donate') }}">
                                                @endif
                                            </a>
                                            <div class="cont text-start">
                                                <span class="fs-12 font-weight-medium">
                                                    @if($donate['type'] == 'donate_trust')
                                                    {{($donate['getTrust']['trust_name']??"")}}
                                                    @else
                                                    {{($donate['adsTrust']['name']??"")}}
                                                    @endif
                                                </span>
                                                <div
                                                    class="text-secondary-50 fs-12 font-semibold mt-1">
                                                    {{ date('d M, Y h:i A', strtotime($donate['created_at'])) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td> {{ webCurrencyConverter(amount: ($donate['amount']??0)) }}</td>
                                    <td> <span class="status-badge rounded-pill __badge fs-12 font-semibold text-capitalize badge-soft-badge-soft-{{ (($donate['amount_status']== 1)?'success':'danger' )}}">{{ (($donate['amount_status']== 1)?'Success':'Pending' )}}</span></td>
                                    <td>
                                        <a href="{{ route('donate-order-details', [$donate['id']]) }}" title="view" class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('donate-create-pdf-invoice',[$donate['id']]) }}"
                                            title="{{ translate('download_donate_invoice') }}"
                                            class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                            <i class="tio-download-to"></i>
                                        </a>
                                        @if(!empty($donate['pan_card']) && preg_match('/^[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}$/', $donate['pan_card']))
                                        <a href="{{ url('api/v1/donate/twoal-a-certificate',[$donate['id']])}}" title="{{ translate('download_80g_certificate') }}" class="btn-outline-info text-info __action-btn btn-shadow rounded-full">
                                            <i class="tio-file_outlined">file_outlined</i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center pt-5 text-capitalize">
                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                            alt="" width="70">
                        <h5 class="mt-1 fs-14">{{ translate('no_donate_order_found') }}!</h5>
                    </div>
                    @endif
                    <div class="card-footer border-0">
                        {{ $donateOrders->links() }}
                    </div>
                </div>
            </div>
            <div class="bg-white d-lg-none web-direction">
                <div class="card-body d-flex flex-column gap-3 customer-profile-orders py-0">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-0 mb-md-3">
                        <h5 class="font-bold mb-0 fs-16">{{ translate('Donate_order_list') }}</h5>
                        <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                viewBox="0 0 15 15" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z" fill="white" />
                            </svg>
                        </button>
                    </div>
                    @if ($donateOrders->count() > 0)
                    @foreach ($donateOrders as $donate)
                    <div class="d-flex border-lighter rounded p-2 justify-content-between gap-1">
                        <div class="">
                            <div class="media-order">
                                <a class="d-block position-relative">
                                    @if($donate['type'] == 'donate_trust')
                                    <img src="{{ getValidImage(path: 'storage/app/public/donate/trust/'.($donate['getTrust']['theme_image']??'') , type: 'counselling') }}" alt="{{ translate('donate') }}">
                                    @else
                                    <img src="{{ getValidImage(path: 'storage/app/public/donate/ads/'.($donate['adsTrust']['image']??'') , type: 'counselling') }}" alt="{{ translate('donate') }}">
                                    @endif
                                </a>
                                <div class="cont text-start">
                                    <h6 class="font-weight-bold mb-1 fs-14">
                                        <a class="fs-14 font-semibold">
                                            {{ translate('order') }} #{{ $donate['trans_id'] }}
                                        </a>
                                    </h6>
                                    <span class="fs-12 font-weight-medium">
                                        @if($donate['type'] == 'donate_trust')
                                        {{($donate['getTrust']['trust_name']??"")}}
                                        @else
                                        {{($donate['adsTrust']['name']??"")}}
                                        @endif
                                    </span>
                                    <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                        {{ date('d M, Y h:i A', strtotime($donate['created_at'])) }}
                                    </div>
                                    <span>{{ webCurrencyConverter(amount: ($donate['amount']??0)) }}</span><br>
                                    <span class="status-badge rounded-pill __badge fs-12 font-semibold text-capitalize badge-soft-badge-soft-{{ (($donate['amount_status']== 1)?'success':'danger' )}}">{{ (($donate['amount_status']== 1)?'Success':'Pending' )}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="__btn-grp-sm">
                            <a href="{{ route('donate-create-pdf-invoice',[$donate['id']]) }}"
                                title="{{ translate('download_donate_invoice') }}"
                                class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                <i class="tio-download-to"></i>
                            </a>
                            @if(!empty($donate['pan_card']) && preg_match('/^[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}$/', $donate['pan_card']))
                            <a href="{{ url('api/v1/donate/twoal-a-certificate',[$donate['id']])}}" title="{{ translate('download_80g_certificate') }}" class="btn-outline-info text-info __action-btn btn-shadow rounded-full">
                                <i class="tio-file_outlined">file_outlined</i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center pt-5 text-capitalize">
                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                            alt="" width="70">
                        <h5 class="mt-1 fs-14">{{ translate('no_donate_order_found') }}!</h5>
                    </div>
                    @endif
                    <div class="card-footer border-0">
                        {{ $donateOrders->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>

</div>

@endsection