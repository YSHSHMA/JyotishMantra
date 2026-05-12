@extends('layouts.front-end.app')

@section('title', translate('my_Order_List'))
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/social-icon.css') }}">
<style>
    .star-rating {
        display: block;
        gap: 5px;
        font-size: 30px;
        cursor: pointer;
    }

    .star-rating i {
        color: #fe9802;
        transition: color 0.2s;
    }

    .star-rating i.filled {
        color: #fe9802;
    }

    .star-rating-display-contents {
        display: contents;
    }

    @media (max-width: 767px) {
        .chat-container {
            height: 400px;
        }

        .chat-header,
        .chat-box,
        .chat-input {
            padding: 8px;
        }

        .user-message,
        .admin-message {
            font-size: 14px;
            padding: 8px;
        }

        .order_table_td {
            display: block;
            width: 100%;
        }

        .order_table_tr {
            display: block;
            margin-bottom: 20px;
        }

        .payment .table {
            min-width: 100%;
        }

        .mobile-full {
            width: 100% !important;
        }

        .customer-profile-orders .card-body {
            padding: 15px;
        }

        .payment .min-width-600px {
            min-width: auto !important;
        }
    }

    @media (max-width: 991px) {
        .customer-profile-wishlist {
            margin-top: 20px;
        }

        .d-lg-flex {
            display: block !important;
        }
    }

    .cancellation-policy-table td,
    .cancellation-policy-table th {
        font-size: 16px;
    }

    @media (max-width: 991px) {

        .cancellation-policy-table td,
        .cancellation-policy-table th {
            font-size: 14px;
        }
    }

    @media (max-width: 767px) {

        .cancellation-policy-table td,
        .cancellation-policy-table th {
            font-size: 13px;
        }
    }

    @media (max-width: 575px) {

        .cancellation-policy-table td,
        .cancellation-policy-table th {
            font-size: 12px;
        }
    }
</style>


<style>
    .chat-container {
        margin: 0 auto;
        border: 1px solid #ccc;
        border-radius: 10px;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 500px;
    }

    .chat-header {
        display: flex;
        align-items: center;
        padding: 10px;
        background-color: #fff;
        border-bottom: 1px solid #ccc;
    }


    .chat-box {
        padding: 10px;
        flex-grow: 1;
        overflow-y: auto;
        background-color: #f1f1f1;
    }

    .chat-input {
        display: flex;
        border-top: 1px solid #ccc;
        padding: 10px;
    }

    .chat-input input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 20px;
        outline: none;
    }

    .chat-input button {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        padding: 10px;
        margin-left: 10px;
        cursor: pointer;
    }

    .chat-input button i {
        font-size: 16px;
    }

    .chat-message {
        margin-bottom: 10px;
        padding: 10px;
        /* border-radius: 10px;
        max-width: 60%; */
        word-wrap: break-word;
    }

    .user-message {
        background-color: #ff9200;
        color: white;
        align-self: flex-end;
        text-align: right;
        border-radius: 8px;
    }

    .admin-message {
        background-color: #f1f1f1;
        color: black;
        align-self: flex-start;
        text-align: left;
    }
</style>
@endpush
@section('content')

<div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
    <div class="row">
        @include('web-views.partials._profile-aside')
        <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">
            <!-- <div class="card __card d-lg-flex web-direction customer-profile-orders"> -->
            <div class="card __card customer-profile-orders shadow-sm rounded">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2 text-capitalize">
                                <h4 class="text-capitalize mb-0 mobile-fs-14 fs-18 font-bold">{{ translate('order') }} #{{ $darshanOrders['order_id'] ?? '' }} </h4>
                                <?php
                                if ($darshanOrders['status'] == 0) {
                                    $showClass = 'primary';
                                    $showName = 'Processing';
                                } elseif ($darshanOrders['status'] == 1) {
                                    $showClass = 'success';
                                    $showName = 'Success';
                                } elseif ($darshanOrders['status'] == 3) {
                                    $showClass = 'danger';
                                    $showName = 'Paymant Failed';
                                } else {
                                    $showClass = 'danger';
                                    $showName = 'Refund';
                                }
                                ?>
                                <span
                                    class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $showClass }} fs-12 font-semibold text-capitalize">
                                    {{ $showName }}
                                </span>
                            </div>
                            <div class="date fs-12 font-semibold text-secondary-50 text-body mb-3 mt-2">
                                {{ date('d M, Y h:i A', strtotime($darshanOrders['created_at'])) }}
                            </div>
                        </div>
                        <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1 d-lg-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                                fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z" fill="white" />
                            </svg>
                        </button>
                    </div>
                    <ul class="nav nav-tabs nav--tabs d-flex justify-content-start mt-3 border-top border-bottom py-2"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link __inline-27 active" href="#all_order" data-toggle="tab" role="tab">
                                {{ translate('order_summary') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#entry_pass" data-toggle="tab" role="tab">
                                {{ translate('entry_pass') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#reviews" data-toggle="tab" role="tab">
                                {{ translate('reviews') }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content px-lg-3">
                        <div class="tab-pane fade show active text-justify" id="all_order" role="tabpanel">
                            <div class="bg-white border-lg rounded mobile-full">
                                <div class="p-lg-3 p-0">
                                    <div class="card border-sm">
                                        <div class="p-lg-3">
                                            <div>
                                                <a href="{{ url('api/v1/darshan/vip-invoice', [$darshanOrders['id']]) }}"
                                                    title="Download Vip Darshan invoice"
                                                    class="btn btn--primary btn-sm float-end my-2">
                                                    <i class="tio-download-to"></i>invoice
                                                </a>
                                            </div>
                                            <div class="border-lg rounded payment mb-lg-3 table-responsive">
                                                <table class="table table-borderless mb-0">
                                                    <thead>
                                                        <tr class="order_table_tr">

                                                            <td class="order_table_td">
                                                                <div class="">
                                                                    <div class="_1 py-2 d-flex justify-content-between align-items-center">
                                                                        <h6 class="fs-13 font-bold text-capitalize">
                                                                            {{ translate('payment_info') }}
                                                                        </h6>
                                                                    </div>
                                                                    <div class="fs-12">
                                                                        <span
                                                                            class="text-muted text-capitalize">{{ translate('payment_status') }}</span>:
                                                                        <?php if ($darshanOrders['status'] == 1) { ?>
                                                                            <span class="text-success text-capitalize">{{ translate('paid') }}</span>
                                                                        <?php } else { ?>
                                                                            <span class="text-darnger text-capitalize">{{ translate('unpaid') }}</span>
                                                                        <?php }  ?>

                                                                    </div>
                                                                    <div class="mt-2 fs-12">
                                                                        <span
                                                                            class="text-muted text-capitalize">{{ translate('payment_method') }}</span>
                                                                        :<span class="text-primary text-capitalize">
                                                                            @if ($darshanOrders['transaction_id'] == 'wallet')
                                                                            {{ translate('Wallet') }}
                                                                            @else
                                                                            {{ translate('online') }}
                                                                            @endif
                                                                        </span>
                                                                    </div>
                                                                    <div class="mt-2 fs-12">

                                                                        <small class="fs-13 font-bold text-capitalize">{{ translate('Temple_name') }}</small>
                                                                        :
                                                                        <span>{{ $darshanOrders['Temple']['name'] ?? '' }}</span>
                                                                        <br>
                                                                        <small
                                                                            class="fs-13 font-bold text-capitalize">{{ translate('booking_date') }}</small>
                                                                        :
                                                                        <span>{{ date('d M, Y', strtotime($darshanOrders['date'])) }}</span><br>
                                                                        <small
                                                                            class="fs-13 font-bold text-capitalize">{{ translate('time_slot') }}</small>
                                                                        :
                                                                        <span>{{ ($darshanOrders['time']) }}</span><br>
                                                                    </div>
                                                                </div>
                                                                <!--  -->
                                                            </td>
                                                            <td class="order_table_td">
                                                                <div class="">
                                                                    <div class="py-2">
                                                                        <h6 class="fs-13 font-bold text-capitalize">
                                                                            {{ translate('User_info') }}:
                                                                        </h6>
                                                                    </div>
                                                                    <div class="">
                                                                        <span class="text-capitalize fs-12">
                                                                            <span class="text-capitalize">
                                                                                <span
                                                                                    class="min-w-60px">{{ translate('name') }}</span>
                                                                                :
                                                                                &nbsp;{{ $darshanOrders['userData']['name'] ?? '' }}
                                                                            </span>
                                                                            <br>
                                                                            <span class="text-capitalize">
                                                                                <span
                                                                                    class="min-w-60px">{{ translate('phone') }}</span>
                                                                                :
                                                                                &nbsp;{{ $darshanOrders['userData']['phone'] ?? '' }},
                                                                            </span>
                                                                            <br>
                                                                            <span style="text-transform: lowercase;">
                                                                                <span class="min-w-60px">{{ translate('Email') }}</span>:
                                                                                &nbsp;<span>{{ $darshanOrders['userData']['email'] ?? '' }}</span>,
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div class="payment mb-3 table-responsive d-none d-lg-block">
                                                <table class="table table-borderless min-width-600px">
                                                    <thead class="thead-light text-capitalize">
                                                        <tr class="fs-13 font-semibold">
                                                            <th class="px-5">{{ translate('darshan_name') }}</th>
                                                            <th>{{ translate('person_qty') }}</th>
                                                            <th>{{ translate('price') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <span> {{ $darshanOrders['title'] }}</span><br>
                                                                <span> {{ $darshanOrders['package_name'] }}</span><br>
                                                            </td>
                                                            <td> <span> {{ $darshanOrders['people_qty'] }}</span></td>
                                                            <td>
                                                                @if(((float) $darshanOrders['price'] ?? 0) > 0)
                                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) $darshanOrders['price'] ?? 0) - ((float) $darshanOrders['gst_amount'] ?? 0) ), currencyCode: getCurrencyCode()) }}
                                                                @else
                                                                Free
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row d-flex justify-content-end mt-2">
                                        <div class="col-md-8 col-lg-5">
                                            <div class="bg-white border-sm rounded">
                                                <div class="card-body ">
                                                    <table class="calculation-table table table-borderless mb-0">
                                                        <tbody class="totals">
                                                            <tr>
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span
                                                                            class="font-semibold">{{ translate('item') }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span
                                                                            class="font-semibold">{{ translate('Price') }}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span
                                                                            class="product-qty">{{ translate('subtotal') }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>

                                                                    <div class="text-end">
                                                                        <span class="fs-15 font-semibold">
                                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) $darshanOrders['price'] ?? 0) - ((float) $darshanOrders['gst_amount'] ?? 0) ), currencyCode: getCurrencyCode()) }}

                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span class="product-qty" style="font-size: 13px;">{{ translate('total_tax') }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>

                                                                    <div class="text-end">
                                                                        <span
                                                                            class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) $darshanOrders['gst_amount'] ?? 0)), currencyCode: getCurrencyCode()) }}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span class="font-weight-bold">
                                                                            <strong>{{ translate('Paid_Amount') }}</strong>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span class="font-weight-bold amount">
                                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $darshanOrders['price'] ?? 0), currencyCode: getCurrencyCode()) }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade text-justify" id="entry_pass" role="tabpanel">
                            <div class="bg-white border-lg rounded mobile-full">
                                <div class="p-lg-3 p-0">
                                    <div class="card border-sm">
                                        <div class="card-header">
                                            <span>
                                                {{ translate('passes_info') }}
                                            </span>
                                        </div>
                                        <div class="p-lg-3">
                                            <div class="border-lg rounded payment mb-lg-3 table-responsive">
                                                <table class="table table-borderless mb-0 cancellation-policy-table">
                                                    <thead>
                                                        <tr>
                                                            <td class="font-weight-bolder">
                                                                {{ translate('User_info') }}
                                                            </td>
                                                            <td class="font-weight-bolder">
                                                                {{ translate('Download') }}
                                                            </td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if($darshanOrders['Members'] && count($darshanOrders['Members']) > 0)
                                                        @foreach($darshanOrders['Members'] as $k=>$val)
                                                        <tr>
                                                            <td>
                                                                <span>Name : {{ ucwords($val['name']) }}</span><br>
                                                                @if($val['phone'])<span>Phone : {{ $val['phone'] }}</span><br>@endif
                                                                <span>Aadhar No. : {{ $val['aadhar'] }}</span><br>
                                                            </td>
                                                            <td>
                                                                <a href="{{ url('api/v1/darshan/vip-pass',['barcode'=>base64_encode($val['barcode'])]) }} " class="btn btn-sm btn--primary rounded-pill"><i class="tio-arrow_large_downward">arrow_large_downward</i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade  text-justify" id="reviews" role="tabpanel">
                            <div class="col-12">
                                <div class="card-body bg-white border-lg rounded mobile-full">
                                    @php
                                    $getTourReview = \App\Models\TempleReview::where([
                                    'user_id' => $darshanOrders['user_id'],
                                    'temple_id' => $darshanOrders['temple_id'],
                                    "order_id" => $darshanOrders['id'],
                                    ])->first();
                                    @endphp
                                    @if (!$getTourReview || ($getTourReview['is_edited']??0) == 0)
                                    <form action="{{ route('vip-darshan-add-review') }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group text-center">
                                                <label>{{ translate('Give_Your_Rating_&_Feedback') }}</label>
                                                <div class="star-rating" id="starRating">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="far fa-star" data-index="{{ $i }}"></i>
                                                        @endfor
                                                </div>
                                                <input type="hidden" name="rating" id="ratingInput" value="0">
                                            </div>

                                            <div class="form-group">
                                                <label for="exampleInputEmail1">{{ translate('comment') }}</label>
                                                <input type="hidden" name="temple_id" value="{{ $darshanOrders['temple_id'] }}">
                                                <input type="hidden" name="order_id" value="{{ $darshanOrders['id'] }}" hidden>
                                                <textarea class="form-control" name="comment">{{ $getTourReview['comment'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ URL::previous() }}"
                                                class="btn btn-secondary">{{ translate('back') }}</a>
                                            <button type="submit"
                                                class="btn btn--primary">{{ translate('submit') }}</button>
                                        </div>
                                    </form>
                                    @else
                                    <section class="rating__card text-center">
                                        <blockquote class="rating__card__quote">“{{ $getTourReview['comment'] }}”
                                        </blockquote>
                                        <div class="rating__card__stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <=$getTourReview['star'])
                                                <i class="fa fa-star star-rating text-warning star-rating-display-contents"></i>
                                                @else
                                                <i class="fa fa-star-o star-rating star-rating-display-contents"></i>
                                                @endif
                                                @endfor
                                                <br>
                                                <span
                                                    class="rating__card__stars__name">{{ $darshanOrders['userData']['name'] }}</span>
                                        </div>
                                        <p class="rating__card__bottomText">
                                            {{ date('h:i A, d M Y', strtotime($getTourReview['created_at'])) }}
                                        </p>
                                    </section>

                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </section>
    </div>

</div>



@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('#starRating i');
        const ratingInput = document.getElementById('ratingInput');

        let currentRating = 0;

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));

                if (index === currentRating) {
                    currentRating = 0;
                } else {
                    currentRating = index;
                }

                ratingInput.value = currentRating;

                stars.forEach((s, i) => {
                    if (i < currentRating) {
                        s.classList.remove('far');
                        s.classList.add('fas', 'filled');
                    } else {
                        s.classList.remove('fas', 'filled');
                        s.classList.add('far');
                    }
                });
            });
        });
    });
</script>
@endpush