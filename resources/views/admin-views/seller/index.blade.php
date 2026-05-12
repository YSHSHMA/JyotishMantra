@php use App\Utils\Helpers; @endphp
@php use Illuminate\Support\Str; @endphp
@extends('layouts.back-end.app')

@section('title', translate('vendor_List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/add-new-seller.png') }}" alt="">
                {{ translate('vendor_List') }}
                <span class="badge badge-soft-dark radius-50 fz-12">{{ $sellers->total() }}</span>
            </h2>
        </div>
        <div class="row my-2">
            <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                <a class="order-stats order-stats_confirmed" href="{{ route('admin.sellers.seller-list') }}">
                    <div class="order-stats__content">
                        <i class="tio-all_done">all_done</i>
                        <h6 class="order-stats__subtitle">{{ translate('All_vendor') }}</h6>
                    </div>
                    <span class="order-stats__title">
                        @php
                            echo \App\Models\Seller::where('type', 'seller')->count();
                        @endphp
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                <a class="order-stats order-stats_confirmed"
                    href="{{ route('admin.sellers.seller-list', ['status' => 'approved']) }}">
                    <div class="order-stats__content">
                        <i class="tio-all_done">all_done</i>
                        <h6 class="order-stats__subtitle">{{ translate('live') }}</h6>
                    </div>
                    <span class="order-stats__title">
                        @php
                            echo \App\Models\Seller::where('type', 'seller')->where('status', 'approved')->count();
                        @endphp
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                <a class="order-stats order-stats_confirmed"
                    href="{{ route('admin.sellers.seller-list', ['status' => 'hold']) }}">
                    <div class="order-stats__content">
                        <i class="tio-all_done">all_done</i>
                        <h6 class="order-stats__subtitle">{{ translate('hold') }}</h6>
                    </div>
                    <span class="order-stats__title">
                        @php
                            echo \App\Models\Seller::where('type', 'seller')->where('status', 'hold')->count();
                        @endphp
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                <a class="order-stats order-stats_confirmed"
                    href="{{ route('admin.sellers.seller-list', ['status' => 'pending']) }}">
                    <div class="order-stats__content">
                        <i class="tio-all_done">all_done</i>
                        <h6 class="order-stats__subtitle">{{ translate('pending') }}</h6>
                    </div>
                    <span class="order-stats__title">
                        @php
                            echo \App\Models\Seller::where('type', 'seller')->where('status', 'pending')->count();
                        @endphp
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                <a class="order-stats order-stats_confirmed"
                    href="{{ route('admin.sellers.seller-list', ['status' => 'suspended']) }}">
                    <div class="order-stats__content">
                        <i class="tio-all_done">all_done</i>
                        <h6 class="order-stats__subtitle">{{ translate('suspended') }}</h6>
                    </div>
                    <span class="order-stats__title">
                        @php
                            echo \App\Models\Seller::where('type', 'seller')->where('status', 'suspended')->count();
                        @endphp
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                <a class="order-stats order-stats_confirmed"
                    href="{{ route('admin.sellers.seller-list', ['status' => 'rejected']) }}">
                    <div class="order-stats__content">
                        <i class="tio-all_done">all_done</i>
                        <h6 class="order-stats__subtitle">{{ translate('rejected') }}</h6>
                    </div>
                    <span class="order-stats__title">
                        @php
                            echo \App\Models\Seller::where('type', 'seller')->where('status', 'rejected')->count();
                        @endphp
                    </span>
                </a>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="d-flex justify-content-between gap-10 flex-wrap align-items-center">
                            <div class="">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-custom width-500px">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                            placeholder="{{ translate('search_by_shop_name_or_vendor_name_or_phone_or_email') }}"
                                            aria-label="Search orders" value="{{ request('searchValue') }}">
                                        <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <div class="dropdown text-nowrap">
                                    <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                        <i class="tio-download-to"></i>
                                        {{ translate('export') }}
                                        <i class="tio-chevron-down"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a type="submit" class="dropdown-item d-flex align-items-center gap-2 "
                                                href="{{ route('admin.sellers.export', ['searchValue' => request('searchValue')]) }}">
                                                <img width="14"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/excel.png') }}"
                                                    alt="">
                                                {{ translate('excel') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'add'))
                                    <a href="{{ route('admin.sellers.add') }}" type="button"
                                        class="btn btn--primary text-nowrap">
                                        <i class="tio-add"></i>
                                        {{ translate('add_New_Vendor') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('shop_name') }}</th>
                                    <th>{{ translate('vendor_name') }}</th>
                                    <th>{{ translate('contact_info') }}</th>
                                    <th>{{ translate('status') }}</th>
                                    <th class="text-center">{{ translate('total_products') }}</th>
                                    <th class="text-center">{{ translate('total_orders') }}</th>
                                    @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'detail'))
                                        <th class="text-center">{{ translate('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sellers as $key => $seller)
                                    <tr>
                                        <td>{{ $sellers->firstItem() + $key }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-10 w-max-content">
                                                <img width="50" class="avatar rounded-circle"
                                                    src="{{ getValidImage(path: 'storage/app/public/shop/' . ($seller->shop ? $seller->shop->image : ''), type: 'backend-basic') }}"
                                                    alt="">
                                                <div>
                                                    <a class="title-color"
                                                        href="{{ route('admin.sellers.view', ['id' => $seller->id]) }}">{{ $seller->shop ? Str::limit($seller->shop->name, 20) : translate('shop_not_found') }}</a>
                                                    <br>
                                                    <span class="text-danger">
                                                        @if ($seller->shop && $seller->shop->temporary_close)
                                                            {{ translate('temporary_closed') }}
                                                        @elseif(
                                                            $seller->shop &&
                                                                $seller->shop->vacation_status &&
                                                                $current_date >= date('Y-m-d', strtotime($seller->shop->vacation_start_date)) &&
                                                                $current_date <= date('Y-m-d', strtotime($seller->shop->vacation_end_date)))
                                                            {{ translate('on_vacation') }}
                                                        @endif
                                                    </span>
                                                    {{ $seller->created_at->format('d-m-Y h:i A') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a title="{{ translate('view') }}" class="title-color"
                                                href="{{ route('admin.sellers.view', $seller->id) }}">
                                                {{ $seller->f_name }} {{ $seller->l_name }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <strong><a class="title-color hover-c1"
                                                        href="mailto:{{ $seller->email }}">{{ $seller->email }}</a></strong>
                                            </div>
                                            <a class="title-color hover-c1"
                                                href="tel:{{ $seller->phone }}">{{ $seller->phone }}</a><br>
                                            @if ($seller->reupload_doc_status == 2 || $seller->reupload_doc_status == 3)
                                                @if ($seller->reupload_doc_status == 2)
                                                    <span class="badge badge-danger">ReUpload</span>
                                                @else
                                                    <span class="badge badge-success">Uploaded</span>
                                                @endif
                                            @else
                                                @if (!empty($seller->pan_number) && !empty($seller->aadhar_number))
                                                    <span class="badge badge-success">Doc</span>
                                                @else
                                                    <span class="badge badge-danger">Not Upload</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {!! $seller->status == 'approved'
                                                ? '<label class="badge badge-success">' . translate('active') . '</label>'
                                                : '<label class="badge badge-danger">' . translate($seller->status) . '</label>' !!}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.sellers.product-list', [$seller['id']]) }}"
                                                class="btn text--primary bg-soft--primary font-weight-bold px-3 py-1 mb-0 fz-12">
                                                {{ $seller->product->count() }}
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.sellers.order-list', [$seller['id']]) }}"
                                                class="btn text-info bg-soft-info font-weight-bold px-3 py-1 fz-12 mb-0">
                                                {{ $seller->orders->where('seller_is', 'seller')->where('order_type', 'default_type')->count() }}
                                            </a>
                                        </td>
                                        @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'detail'))
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a title="{{ translate('view') }}"
                                                        class="btn btn-outline-info btn-sm square-btn"
                                                        href="{{ route('admin.sellers.view', $seller->id) }}">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-center justify-content-md-end">
                            {!! $sellers->links() !!}
                        </div>
                    </div>
                    @if (count($sellers) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="{{ 'image_description' }}">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
