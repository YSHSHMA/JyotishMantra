@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', $seller?->shop->name ?? translate('shop_Name'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/add-new-seller.png') }}" alt="">
            {{ translate('vendor_details') }}
        </h2>
    </div>
    <div class="page-header border-0 mb-4">
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <ul class="nav nav-tabs flex-wrap page-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active"
                        href="{{ route('admin.sellers.view', $seller['id']) }}">{{ translate('shop') }}</a>
                </li>
                @if ($seller['status'] != 'pending')
                @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'order'))
                <li class="nav-item">
                    <a class="nav-link"
                        href="{{ route('admin.sellers.view', ['id' => $seller['id'], 'tab' => 'order']) }}">{{ translate('order') }}</a>
                </li>
                @endif
                @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'product'))
                <li class="nav-item">
                    <a class="nav-link"
                        href="{{ route('admin.sellers.view', ['id' => $seller['id'], 'tab' => 'product']) }}">{{ translate('product') }}</a>
                </li>
                @endif
                @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'setting'))
                <li class="nav-item">
                    <a class="nav-link"
                        href="{{ route('admin.sellers.view', ['id' => $seller['id'], 'tab' => 'setting']) }}">{{ translate('setting') }}</a>
                </li>
                @endif
                @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'transaction'))
                <li class="nav-item">
                    <a class="nav-link"
                        href="{{ route('admin.sellers.view', ['id' => $seller['id'], 'tab' => 'transaction']) }}">{{ translate('transaction') }}</a>
                </li>
                @endif
                @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'review'))
                <li class="nav-item">
                    <a class="nav-link"
                        href="{{ route('admin.sellers.view', ['id' => $seller['id'], 'tab' => 'review']) }}">{{ translate('review') }}</a>
                </li>
                @endif
                @endif
            </ul>
        </div>
    </div>
    <div class="card card-top-bg-element mb-5">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-3 justify-content-between">
                <div class="media flex-column flex-sm-row gap-3">
                    <img class="avatar avatar-170 rounded-0"
                        src="{{ getValidImage(path: 'storage/app/public/shop/' . $seller?->shop->image, type: 'backend-basic') }}"
                        alt="{{ translate('image') }}">
                    <div class="media-body">
                        @if (
                        $seller?->shop->temporary_close ||
                        ($seller?->shop->vacation_status &&
                        $current_date >= date('Y-m-d', strtotime($seller?->shop->vacation_start_date)) &&
                        $current_date <= date('Y-m-d', strtotime($seller?->shop->vacation_end_date))))
                            <div class="d-flex justify-content-between gap-2 mb-4">
                                @if ($seller->shop->temporary_close)
                                <div class="btn btn-soft-danger">{{ translate('this_shop_currently_close_now') }}
                                </div>
                                @elseif(
                                $seller->shop->vacation_status &&
                                $current_date >= date('Y-m-d', strtotime($seller->shop->vacation_start_date)) &&
                                $current_date <= date('Y-m-d', strtotime($seller->shop->vacation_end_date)))
                                    <div class="btn btn-soft-danger">{{ translate('this_shop_currently_on_vacation') }}
                                    </div>
                                    @endif
                            </div>
                            @endif
                            <div class="d-block">
                                <h2 class="mb-2 pb-1">
                                    {{ $seller->shop ? $seller->shop->name : translate('shop_Name') . ' : ' . translate('update_Please') }}
                                </h2>
                                <div class="d-flex gap-3 flex-wrap mb-3 lh-1">
                                    <div
                                        class="review-hover position-relative cursor-pointer d-flex gap-2 align-items-center">
                                        <i class="tio-star"></i>
                                        <span>{{ round($seller->average_rating, 1) }}</span>
                                        <div class="review-details-popup">
                                            <h6 class="mb-2">{{ translate('rating') }}</h6>
                                            <div class="">
                                                <ul class="list-unstyled list-unstyled-py-2 mb-0">
                                                    <li class="d-flex align-items-center font-size-sm">
                                                        <span class="mr-3">{{ '5' . ' ' . translate('star') }}</span>
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar width--100" role="progressbar"
                                                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <span class="ml-3">{{ $seller->single_rating_5 }}</span>
                                                    </li>
                                                    <li class="d-flex align-items-center font-size-sm">
                                                        <span class="mr-3">{{ '4' . ' ' . translate('star') }}</span>
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar width--80" role="progressbar"
                                                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <span class="ml-3">{{ $seller->single_rating_4 }}</span>
                                                    </li>
                                                    <li class="d-flex align-items-center font-size-sm">
                                                        <span class="mr-3">{{ '3' . ' ' . translate('star') }}</span>
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar width--60" role="progressbar"
                                                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <span class="ml-3">{{ $seller->single_rating_3 }}</span>
                                                    </li>
                                                    <li class="d-flex align-items-center font-size-sm">
                                                        <span class="mr-3">{{ '2' . ' ' . translate('star') }}</span>
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar width--40" role="progressbar"
                                                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <span class="ml-3">{{ $seller->single_rating_2 }}</span>
                                                    </li>
                                                    <li class="d-flex align-items-center font-size-sm">
                                                        <span class="mr-3">{{ '2' . ' ' . translate('star') }}</span>
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar width--20" role="progressbar"
                                                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <span class="ml-3">{{ $seller->single_rating_1 }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="border-left"></span>
                                    <a href="javascript:" class="text-dark">{{ $seller->total_rating }}
                                        {{ translate('ratings') }}</a>
                                    <span class="border-left"></span>
                                    <a href="{{ $seller['status'] != 'pending' ? route('admin.sellers.view', ['id' => $seller['id'], 'tab' => 'review']) : 'javascript:' }}"
                                        class="text-dark">{{ $seller->rating_count }} {{ translate('reviews') }}</a>
                                    <span class="border-left"></span>
                                    <a>{{ translate('profile_status') }} : &nbsp;&nbsp;{{ ucwords($seller->status) }}</a>

                                </div>
                                @if ($seller['status'] != 'pending' && $seller['status'] != 'suspended' && $seller['status'] != 'rejected')
                                @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'live'))
                                <a href="{{ route('shopView', ['id' => $seller->shop->id]) }}"
                                    class="btn btn-outline--primary px-4" target="_blank"><i
                                        class="tio-globe"></i> {{ translate('view_live') }}
                                    @endif
                                </a>
                                @endif
                            </div>

                            <div>
                                <div class="border p-3 w-170">
                                    <div class="d-flex mb-1">
                                        <h6 class="font-weight-normal">{{ translate('total_products') }} :</h6>
                                        <h4 class="text-primary fs-18">&nbsp;&nbsp;{{ $seller->product_count }}</h4>
                                    </div>

                                    <div class="d-flex">
                                        <h6 class="font-weight-normal">{{ translate('total_orders') }} :</h6>
                                        <h4 class="text-primary fs-18">&nbsp;&nbsp;{{ $seller->orders_count }}</h4>
                                    </div>

                                </div>
                            </div>
                    </div>
                </div>


                <div class="d-flex justify-content-sm-end flex-wrap gap-2 mb-3">
                    @if ($seller['verify_status'] == 1)
                    <form class="d-inline-block" action="{{ route('admin.sellers.updateStatus') }}"
                        id="onlyapprove-form" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $seller['id'] }}">
                        <input type="hidden" name="status" value="hold">
                        <button type="button" class="btn btn-warning text-white px-5 form-alert"
                            data-message="{{ translate('want_to_hold_this_vendor') . '?' }}"
                            data-id="onlyapprove-form">{{ translate('hold') }}</button>
                    </form>
                    @if ($seller['status'] != 'approved')
                    @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'approve'))
                    <form class="d-inline-block" action="{{ route('admin.sellers.updateStatus') }}"
                        id="approve-form" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $seller['id'] }}">
                        <input type="hidden" name="status" value="approved">
                        <button type="button" class="btn btn-success px-5 form-alert"
                            data-message="{{ translate('want_to_approve_this_vendor') . '?' }}"
                            data-id="approve-form">{{ translate('go_live') }}</button>
                    </form>
                    @endif
                    @endif

                    @endif
                    @if ($seller['status'] != 'approved')
                    @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'reject'))
                    <form class="d-inline-block" action="{{ route('admin.sellers.updateStatus') }}"
                        id="reject-form" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $seller['id'] }}">
                        <input type="hidden" name="status" value="rejected">
                        <button type="button" class="btn btn-danger px-5 form-alert"
                            data-message="{{ translate('want_to_reject_this_vendor') . '?' }}"
                            data-id="reject-form">{{ translate('reject') }}</button>
                    </form>
                    @endif
                    @endif
                    @if ($seller['status'] == 'approved')
                    @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'suspend'))
                    <form class="d-inline-block" action="{{ route('admin.sellers.updateStatus') }}"
                        id="suspend-form" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $seller['id'] }}">
                        <input type="hidden" name="status" value="suspended">
                        <button type="button" class="btn btn-danger px-5 form-alert"
                            data-message="{{ translate('want_to_suspend_this_vendor') . '?' }}"
                            data-id="suspend-form">{{ translate('suspend_this_vendor') }}</button>
                    </form>
                    @endif
                    @endif
                </div>
            </div>
            <hr>

            <div class="row">
                <div class="col-md-12">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="navbar-vertical navbar-expand-lg mb-3 mb-lg-5">
                                    <button type="button" class="navbar-toggler btn btn-block btn-white mb-3"
                                        aria-label="Toggle navigation" aria-expanded="false"
                                        aria-controls="navbarVerticalNavMenu" data-bs-toggle="collapse"
                                        data-bs-target="#navbarVerticalNavMenu">
                                        <span class="d-flex justify-content-between align-items-center">
                                            <span class="h5 mb-0">{{ translate('nav_menu') }}</span>
                                            <span class="navbar-toggle-default">
                                                <i class="tio-menu-hamburger"></i>
                                            </span>
                                            <span class="navbar-toggle-toggled">
                                                <i class="tio-clear"></i>
                                            </span>
                                        </span>
                                    </button>

                                    <div id="navbarVerticalNavMenu" class="collapse navbar-collapse">
                                        <ul id="navbarSettings"
                                            class="js-sticky-block js-scrollspy navbar-nav navbar-nav-lg nav-tabs card card-navbar-nav">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="javascript:void(0);"
                                                    data-target="general1">
                                                    <i
                                                        class="tio-user-outlined nav-icon"></i>{{ translate('basic_Information') }}
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="javascript:void(0);" data-target="shops1">
                                                    <i class="tio-shop nav-icon"></i>{{ translate('Shop') }}
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="javascript:void(0);"
                                                    data-target="document">
                                                    <i class="tio-documents nav-icon"></i> {{ translate('Document') }}
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="javascript:void(0);" data-target="banks">
                                                    <i class="tio-museum nav-icon"></i> {{ translate('Bank') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <button class="btn btn-danger btn-sm" onclick="resend_doc()">Send
                                        Re-Upload</button>&nbsp;&nbsp;
                                    @if ($seller['verify_status'] == 0)
                                    <button class="btn btn-success btn-sm"
                                        onclick="verified_doc()">verify</button>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="card mb-3 mb-lg-5" id="general1-div">
                                    <div class="row p-4 ">
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-f_name">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['f_name'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['f_name'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('first_Name') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2"> {{ $seller->f_name ?? '' }}</div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-f_name">
                                                {{ translate('last_Name') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->l_name ?? '' }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['f_name'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="f_name" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="f_name" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-phone">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['phone'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['phone'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('phone') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->phone ?? '' }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['phone'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="phone" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="phone" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-email">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['email'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['email'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('email') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->email ?? '' }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['email'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="email" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="email" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-user_image">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['user_image'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['user_image'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('profile_image') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="{{ getValidImage(path: 'storage/app/public/seller/' . ($seller->image ?? ''), type: 'backend-basic') }}"
                                                        data-lightbox="mygallery" class="d-flex">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/seller/' . ($seller->image ?? ''), type: 'backend-basic') }}"
                                                            alt="{{ translate('image') }}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['user_image'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="user_image" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="user_image" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>

                                    </div>
                                </div>
                                <div class="card mb-3 mb-lg-5" id="shops1-div" style="display: none;">
                                    <div class="row p-4 ">
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-name">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['name'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['name'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('shop_name') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller['shop']->name ?? '' }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['name'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="name" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="name" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-contact">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['contact'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['contact'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('contact') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller['shop']->contact ?? '' }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['contact'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="contact" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="contact" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-pincode">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['pincode'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['pincode'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('pincode') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller['shop']->pincode ?? '' }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['pincode'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="pincode" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="pincode" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-address">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['address'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['address'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('address') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller['shop']->address ?? '' }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['address'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="address" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="address" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-fassai_no">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['fassai_no'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['fassai_no'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('Fassai_number') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller['shop']->fassai_no ?? '- -' }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['fassai_no'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="fassai_no" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="fassai_no" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable
                                                class="col-form-label font-weight-bold send_wrong_data-fassai_image">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['fassai_image'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['fassai_image'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('fassai_image') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="{{ getValidImage(path: 'storage/app/public/shop/fassai/' . ($seller['shop']->fassai_image ?? ''), type: 'backend-basic') }}"
                                                        data-lightbox="mygallery" class="d-flex">
                                                        <img id="fassai-preview"
                                                            src="{{ getValidImage(path: 'storage/app/public/shop/fassai/' . ($seller['shop']->fassai_image ?? ''), type: 'backend-basic') }}"
                                                            alt="{{ translate('fassai_image') }}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['fassai_image'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="fassai_image" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="fassai_image" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-gumasta">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['gumasta'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['gumasta'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('gumasta_image') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="{{ getValidImage(path: 'storage/app/public/shop/gumasta/' . ($seller['shop']->gumasta ?? ''), type: 'backend-basic') }}"
                                                        data-lightbox="mygallery" class="d-flex">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/shop/gumasta/' . ($seller['shop']->gumasta ?? ''), type: 'backend-basic') }}"
                                                            alt="{{ translate('fassai_image') }}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['gumasta'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="gumasta" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="gumasta" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-image">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['image'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['image'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('shop_image') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="{{ getValidImage(path: 'storage/app/public/shop/' . ($seller['shop']->image ?? ''), type: 'backend-basic') }}"
                                                        alt="{{ translate('image') }}" data-lightbox="mygallery"
                                                        class="d-flex">
                                                        <img id="viewer"
                                                            src="{{ getValidImage(path: 'storage/app/public/shop/' . ($seller['shop']->image ?? ''), type: 'backend-basic') }}"
                                                            alt="{{ translate('image') }}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['image'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="image" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="image" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-banner">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['banner'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['banner'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('shop_banner') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="{{ getValidImage(path: 'storage/app/public/shop/banner/' . ($seller['shop']->banner ?? ''), type: 'backend-basic') }}"
                                                        alt="{{ translate('image') }}" data-lightbox="mygallery"
                                                        class="d-flex">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/shop/banner/' . ($seller['shop']->banner ?? ''), type: 'backend-basic') }}"
                                                            alt="{{ translate('image') }}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['banner'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="banner" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="banner" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-3 mb-lg-5" id="document-div" style="display: none;">
                                    <div class="row p-4 ">
                                        <div class="col-md-6 mt-2">
                                            <lable
                                                class="col-form-label font-weight-bold send_wrong_data-aadhar_number">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['aadhar_number'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['aadhar_number'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('aadhar_Number') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->aadhar_number }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['aadhar_number'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="aadhar_number" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="aadhar_number" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable
                                                class="col-form-label font-weight-bold send_wrong_data-aadhar_front_image">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['aadhar_front_image'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['aadhar_front_image'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('aadhar_image') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="{{ getValidImage(path: 'storage/app/public/seller/' . ($seller->aadhar_front_image ?? ''), type: 'backend-basic') }}"
                                                        data-lightbox="mygallery" class="d-flex">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/seller/' . ($seller->aadhar_front_image ?? ''), type: 'backend-basic') }}"
                                                            alt="">
                                                    </a>
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ getValidImage(path: 'storage/app/public/seller/' . ($seller->aadhar_back_image ?? ''), type: 'backend-basic') }}"
                                                        data-lightbox="mygallery" class="d-flex">
                                                        <img class="h-auto aspect-1 bg-white"
                                                            src="{{ getValidImage(path: 'storage/app/public/seller/' . ($seller->aadhar_back_image ?? ''), type: 'backend-basic') }}"
                                                            alt="">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['aadhar_front_image'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="aadhar_front_image" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="aadhar_front_image" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-pan_number">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['pan_number'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['pan_number'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('pancard_Number') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->pan_number ?? '' }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['pan_number'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="pan_number" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="pan_number" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable
                                                class="col-form-label font-weight-bold send_wrong_data-pancard_image">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['pancard_image'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['pancard_image'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('pancard_image') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="{{ getValidImage(path: 'storage/app/public/seller/' . ($seller->pancard_image ?? ''), type: 'backend-basic') }}"
                                                        data-lightbox="mygallery" class="d-flex">
                                                        <img class="h-auto aspect-1 bg-white"
                                                            src="{{ getValidImage(path: 'storage/app/public/seller/' . ($seller->pancard_image ?? ''), type: 'backend-basic') }}"
                                                            alt="">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['pancard_image'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="pancard_image" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="pancard_image" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-gst">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['gst'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif( $seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['gst'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('GST_Number') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->gst ?? '' }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['gst'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="gst" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="gst" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-3 mb-lg-5" id="banks-div" style="display: none;">
                                    <div class="row p-4 ">
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-bank_name">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['bank_name'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['bank_name'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('bank_Name') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->bank_name }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['bank_name'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="bank_name" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="bank_name" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-branch">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['branch'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['branch'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('branch_Name') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->branch }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['branch'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="branch" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="branch" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-holder_name">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['holder_name'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['holder_name'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('holder_Name') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->holder_name }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['holder_name'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="holder_name" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="holder_name" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-account_no">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['account_no'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['account_no'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('account_No') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->account_no }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['account_no'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="account_no" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="account_no" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable class="col-form-label font-weight-bold send_wrong_data-ifsc">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['ifsc'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['ifsc'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('IFSC_code') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">{{ $seller->ifsc }}</div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['ifsc'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="ifsc" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="ifsc" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <lable
                                                class="col-form-label font-weight-bold send_wrong_data-cancel_check">
                                                @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['cancel_check'] == 1)
                                                <i class="tio-all_done text-success font-weight-bold"
                                                    style="font-size: 22px;">all_done</i>
                                                @elseif($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['cancel_check'] == 2)
                                                <i class="tio-clear text-danger font-weight-bold"
                                                    style="font-size: 22px;"></i>
                                                @endif
                                                {{ translate('cancel_check') }}
                                            </lable>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <div class="text-center">
                                                        <a href="{{ getValidImage(path: 'storage/app/public/seller/' . ($seller->cancel_check ?? ''), type: 'backend-basic') }}"
                                                            data-lightbox="mygallery" class="d-flex">
                                                            <img class="upload-img-view upload-img-view__banner bg-white"
                                                                src="{{ getValidImage(path: 'storage/app/public/seller/' . ($seller->cancel_check ?? ''), type: 'backend-basic') }}"
                                                                alt="">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            @if ($seller && $seller['all_doc_info'] && json_decode($seller['all_doc_info'], true) && json_decode($seller['all_doc_info'], true)['cancel_check'] == 0)
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="cancel_check" data-value="1">
                                                <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                            </span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="cancel_check" data-value="2">
                                                <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                                            </span>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($seller['status'] != 'pending')
    <div class="card mt-3">
        <div class="card-body">
            <div class="row justify-content-between align-items-center g-2 mb-3">
                <div class="col-sm-6">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                        <img width="20" class="mb-1"
                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png') }}"
                            alt="">
                        {{ translate('vendor_Wallet') }}
                    </h4>
                </div>
            </div>

            <div class="row g-2" id="order_stats">
                <div class="col-lg-4">
                    <div class="card h-100 d-flex justify-content-center align-items-center">
                        <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                            <img width="48" class="mb-2"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw.png') }}"
                                alt="">
                            <h3 class="for-card-count mb-0 fz-24">
                                {{ $seller->wallet ? setCurrencySymbol(amount: usdToDefaultCurrency(amount: $seller->wallet->total_earning)) : 0 }}
                            </h3>
                            <div class="font-weight-bold text-capitalize mb-30">
                                {{ translate('withdrawable_balance') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ $seller->wallet ? setCurrencySymbol(amount: usdToDefaultCurrency(amount: $seller->wallet->pending_withdraw)) : 0 }}
                                        </h3>
                                        <div class="text-capitalize mb-0">{{ translate('pending_Withdraw') }}
                                        </div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/pw.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ $seller->wallet ? setCurrencySymbol(amount: usdToDefaultCurrency(amount: $seller->wallet->commission_given)) : 0 }}
                                        </h3>
                                        <div class="text-capitalize mb-0">
                                            {{ translate('total_Commission_given') }}
                                        </div>
                                    </div>
                                    <div>
                                        <img width="40"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/tcg.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ $seller->wallet ? setCurrencySymbol(amount: usdToDefaultCurrency(amount: $seller->wallet->withdrawn)) : 0 }}
                                        </h3>
                                        <div class="text-capitalize mb-0">{{ translate('aready_Withdrawn') }}
                                        </div>
                                    </div>
                                    <div>
                                        <img width="40"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/aw.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ $seller->wallet ? setCurrencySymbol(amount: usdToDefaultCurrency(amount: $seller->wallet->delivery_charge_earned)) : 0 }}
                                        </h3>
                                        <div class="text-capitalize mb-0">
                                            {{ translate('total_delivery_charge_earned') }}
                                        </div>
                                    </div>
                                    <div>
                                        <img width="40"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/tdce.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ $seller->wallet ? setCurrencySymbol(amount: usdToDefaultCurrency(amount: $seller->wallet->total_tax_collected)) : 0 }}
                                        </h3>
                                        <div class="text-capitalize mb-0">{{ translate('total_tax_given') }}
                                        </div>
                                    </div>
                                    <div>
                                        <img width="40"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/ttg.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ $seller->wallet ? setCurrencySymbol(amount: usdToDefaultCurrency(amount: $seller->wallet->collected_cash)) : 0 }}
                                        </h3>
                                        <div class="text-capitalize mb-0">{{ translate('collected_cash') }}</div>
                                    </div>
                                    <div>
                                        <img width="40"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/cc.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('script')
<script>
    function handleTabSwitch(target) {
        const sections = ['general1', 'shops1', 'document', 'banks'];

        // Hide all sections
        sections.forEach(section => {
            document.getElementById(section + '-div').style.display = 'none';
            document.querySelector('[data-target="' + section + '"]').classList.remove('active');
        });

        // Show the selected section
        document.getElementById(target + '-div').style.display = 'block';
        document.querySelector('[data-target="' + target + '"]').classList.add('active');
    }

    // Add event listeners to the tabs
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            handleTabSwitch(target);
        });
    });


    let all_doc_check = [];
    let messsage_use_doc = 0;
    $('.send_wrong_data').click(function() {
        var name = $(this).data('name');
        var value = $(this).data('value');
        if (value == 1) {
            $(`.send_wrong_data-${name}`).addClass('text-success');
            $(`.send_wrong_data-${name}`).removeClass('text-danger'); // Remove danger class if already added
        } else {
            $(`.send_wrong_data-${name}`).addClass('text-danger');
            $(`.send_wrong_data-${name}`).removeClass('text-success');
            if (messsage_use_doc == 0) {
                messsage_use_doc = 1;
            }
        }

        let existingEntry = all_doc_check.find(entry => entry.name === name);
        if (existingEntry) {
            existingEntry.value = value;
        } else {
            all_doc_check.push({
                name: name,
                value: value
            });
        }
        console.log(all_doc_check);
    });

    function verified_doc() {
        Swal.fire({
            title: 'Are You Sure Vendor Verified!',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.sellers.doc_verified_success') }}",
                    type: 'POST',
                    data: {
                        vendor_id: "{{ $seller->id }}",
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            Swal.fire({
                                title: 'Success!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            window.location.href = ``;
                        } else {
                            Swal.fire({
                                title: 'Same document upload in progress!',
                                icon: 'Error',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    }
                });
            }
        });
    }

    function resend_doc() {
        if (all_doc_check.length > 0) {
            if (messsage_use_doc == 1) {
                Swal.fire({
                    title: 'Enter a vendor Resend reason',
                    input: 'textarea',
                    inputValue: all_doc_check.map(item =>
                            `${item.name.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase())} : `)
                        .join('\n'),
                    inputPlaceholder: 'Type your reason here',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => {
                        if (!value.trim()) {
                            return 'You need to write something!';
                        }
                    }
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('admin.sellers.doc_verified_resend') }}", // Replace with your server endpoint
                            type: 'POST',
                            data: {
                                reason: result.value,
                                arrays: all_doc_check,
                                vendor_id: "{{ $seller->id }}",
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                window.location.href = ``;
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Are You Sure!',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('admin.sellers.doc_verified_resend') }}",
                            type: 'POST',
                            data: {
                                reason: result.value,
                                arrays: all_doc_check,
                                vendor_id: "{{ $seller->id }}",
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                window.location.href = ``;
                            }
                        });
                    }
                });
            }


        } else {
            toastr.error('Please Choose Invalid Information !', 'Error!', {
                closeButton: true,
                progressBar: true,
                timeOut: 5000,
                positionClass: 'toast-top-right',
            });
        }
    }
</script>
@endpush