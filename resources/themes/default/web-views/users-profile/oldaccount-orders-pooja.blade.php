@extends('layouts.front-end.app')

@section('title', translate('Puja_order_list'))

@section('content')

    <div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
        <div class="row">
            @include('web-views.partials._profile-aside')

            <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">
                <div class="card __card d-none d-lg-flex web-direction customer-profile-orders">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-0 mb-md-3">
                            <h5 class="font-bold mb-0 fs-16">{{ translate('Puja_order_list') }}</h5>
                        </div>

                        @if ($serviceOrder->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_list') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-center text-capitalize">
                                                        {{ translate('user_detail') }}
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
                                                        {{ translate('total') }}
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
                                        @foreach ($serviceOrder as $serviceorder)
                                            <tr>
                                                <td class="bodytr">
                                                    <div class="media-order">
                                                        <a href="{{ route('account-service-order-details', ['order_id' => $serviceorder->order_id]) }}"
                                                            class="d-block position-relative"><img
                                                                alt="{{ translate('shop') }}"
                                                                src="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/' . $serviceorder->thumbnail, type: 'pooja') }}"></a>
                                                        <div class="cont text-start">
                                                            <h6 class="font-weight-bold m-0 mb-1">
                                                                <a href="{{ route('account-service-order-details', ['order_id' => $serviceorder->order_id]) }}"
                                                                    class="fs-14 font-semibold">
                                                                    {{ translate('order') }}
                                                                    #{{ $serviceorder['order_id'] }}
                                                                </a>
                                                            </h6>
                                                            <span class="fs-12 font-weight-medium">
                                                                {{ $serviceorder['services']['name'] }}
                                                                {{ translate('items') }}
                                                            </span>
                                                            <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                                {{ date('d M, Y h:i A', strtotime($serviceorder['created_at'])) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="bodytr">
                                                    @if ($serviceorder->is_edited == 1)
                                                        <a href="{{ route('account-service-sankalp', ['order_id' => $serviceorder->order_id]) }}"
                                                            class="badge badge-primary"
                                                            title="{{ translate('view_order_details') }}">
                                                            View Details
                                                        </a>
                                                    @else
                                                        <a href="{{ route('account-service-sankalp', ['order_id' => $serviceorder->order_id]) }}"
                                                            class="badge badge-primary"
                                                            title="{{ translate('view_order_details') }}">
                                                            Edit Details
                                                        </a>
                                                    @endif
                                                </td>

                                                <td class="bodytr">
                                                    <span
                                                        class="status-badge rounded-pill __badge badge-soft-{{ $serviceorder->order_status == 0
                                                            ? 'primary'
                                                            : ($serviceorder->order_status == 1
                                                                ? 'success'
                                                                : ($serviceorder->order_status == 2
                                                                   ? 'warning'
                                                                : ($serviceorder->order_status == 3
                                                                    ? 'danger'
                                                                    : ($serviceorder->order_status == 6
                                                                        ? 'warning'
                                                                        : ($serviceorder->order_status == 4
                                                                            ? 'info'
                                                                            : ($serviceorder->order_status == 5
                                                                                ? 'secondary'
                                                                                : 'dark')))))) }} fs-12 font-semibold text-capitalize">
                                                        {{ $serviceorder->order_status == 0
                                                            ? 'Pending'
                                                            : ($serviceorder->order_status == 1
                                                                ? 'Completed'
                                                                : ($serviceorder->order_status == 2
                                                                    ? 'Canceled'
                                                                    : ($serviceorder->order_status == 3
                                                                        ? 'Scheduled'
                                                                        : ($serviceorder->order_status == 4
                                                                            ? 'Live Stream'
                                                                            : ($serviceorder->order_status == 5
                                                                                ? 'Video Share'
                                                                                : ($serviceorder->order_status == 6
                                                                                    ? 'Rejected'
                                                                                    : 'Unknown')))))) }}
                                                    </span>
                                                </td>
                                                <td class="bodytr">
                                                    <div class="text-dark fs-13 font-bold">
                                                        {{ webCurrencyConverter(amount: $serviceorder['pay_amount']) }}
                                                    </div>
                                                </td>
                                                <td class="bodytr">
                                                    <div class="__btn-grp-sm flex-nowrap">
                                                        @if ($serviceorder->is_edited == 0)
                                                            <a href="{{ route('account-service-sankalp', ['order_id' => $serviceorder->order_id]) }}"
                                                                class="btn btn-outline--danger text-danger __action-btn btn-shadow rounded-full"
                                                                title="{{ translate('view_order_details') }}">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('account-service-sankalp', ['order_id' => $serviceorder->order_id]) }}"
                                                                class="btn btn-outline--success text-success __action-btn btn-shadow rounded-full"
                                                                title="{{ translate('view_order_details') }}">
                                                                <i class="fa fa-list"></i>
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('account-service-order-details', ['order_id' => $serviceorder->order_id]) }}"
                                                            class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                            title="{{ translate('view_order_details') }}">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('generate-invoice-service', ['id' =>$serviceorder->id]) }}" title="{{ translate('download_invoice') }}"
                                                            class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                                <i class="tio-download-to"></i>
                                                        </a>  

                                                       
                                                    </div>

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
                                <h5 class="mt-1 fs-14">{{ translate('no_puja_order_found') }}!</h5>
                            </div>
                        @endif
                        <div class="card-footer border-0">
                            {{ $serviceOrder->links() }}
                        </div>
                    </div>
                </div>


            </section>
        </div>

    </div>

@endsection
