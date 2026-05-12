@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('detail'))

@section('content')

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ $overview['primarySkill']['name'] ?? '' }} {{ translate('detail') }}
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="js-nav-scroller hs-nav-scroller-horizontal mb-5">
                    <ul class="nav nav-tabs flex-wrap page-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active"
                                href="{{ route('admin.astrologers.manage.detail.overview', $overview['id']) }}">Overview</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.order', $overview['id']) }}">Order</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'service'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.service', $overview['id']) }}">Service</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.setting', $overview['id']) }}">Setting</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'transaction'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.transaction', $overview['id']) }}">Transaction</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.transaction.history', $overview['id']) }}">Transaction History</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'review'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.review', $overview['id']) }}">Review</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.history', $overview['id']) }}">History</a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content mt-5">
                    <div class="tab-pane fade show active" id="order">
                        <div class="row pt-2">
                            <div class="col-md-12">
                                {{-- information --}}
                                <div class="card card-top-bg-element mb-5">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap gap-3 justify-content-between">
                                            <div class="media flex-column flex-sm-row gap-3">
                                                <img class="avatar avatar-170 rounded-0" src="{{ $overview['image'] }}"
                                                    alt="{{ translate('image') }}">
                                                <div class="media-body">
                                                    <div class="d-block">
                                                        <h2 class="mb-2 pb-1">
                                                            {{ @ucwords($overview['name']) . ' - (' . @ucwords($overview['type']) . ')' }}
                                                        </h2>
                                                        <div class="d-flex gap-3 flex-wrap mb-3 lh-1">
                                                            <div
                                                                class="review-hover position-relative cursor-pointer d-flex gap-2 align-items-center">
                                                                <i class="tio-star"></i>
                                                                <span>
                                                                    @php
                                                                        $onlineRate = !empty(
                                                                            $onlinePoojaReviews['total_rate']
                                                                        )
                                                                            ? $onlinePoojaReviews['total_rate']
                                                                            : 0;
                                                                        $offlineRate = !empty(
                                                                            $offlinePoojaReviews['total_rate']
                                                                        )
                                                                            ? $offlinePoojaReviews['total_rate']
                                                                            : 0;
                                                                        $rateCustomerCount =
                                                                            $onlinePoojaReviews->total_count +
                                                                            $offlinePoojaReviews->total_count;
                                                                        $rating = 0;
                                                                        if ($rateCustomerCount > 0) {
                                                                            $rating =
                                                                                ($onlineRate + $onlineRate) /
                                                                                ($onlinePoojaReviews->total_count +
                                                                                    $offlinePoojaReviews->total_count);
                                                                        }
                                                                    @endphp
                                                                    {{ $rating }}</span>
                                                            </div>
                                                            <span class="border-left"></span>
                                                            <a href="javascript:"
                                                                class="text-dark">{{ $rateCustomerCount }}
                                                                {{ translate('customers_rated') }}</a>
                                                            {{-- <span class="border-left"></span>
                                                            <a href="javascript:" class="text-dark">0
                                                                {{ translate('reviews') }}</a> --}}
                                                        </div>

                                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'live'))
                                                            <a href="#" class="btn btn-outline--primary px-4"
                                                                target="_blank"><i class="tio-globe"></i>
                                                                {{ translate('view_live') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="">
                                                {{-- <button type="button"
                                                    class="btn btn-danger px-5 form-alert">{{ translate('suspend_this_vendor') }}</button> --}}
                                                <div class="d-flex justify-content-start gap-2">
                                                    @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'reject'))
                                                        <span
                                                            class="btn btn-outline-warning btn-sm square-btn reject-astrologer"
                                                            title="{{ translate('reject_astrologer') }}"
                                                            data-id="reject-{{ $overview['id'] }}">
                                                            <i class="tio-blocked"></i>
                                                        </span>
                                                    @endif
                                                    <form action="{{ route('admin.astrologers.manage.status') }}"
                                                        method="post" id="reject-{{ $overview['id'] }}">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $overview['id'] }}">
                                                        <input type="hidden" name="status" value="0">
                                                    </form>

                                                    @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'block'))
                                                        <span
                                                            class="btn btn-outline-secondary btn-sm square-btn block-astrologer"
                                                            title="{{ translate('block_astrologer') }}"
                                                            data-id="block-{{ $overview['id'] }}">
                                                            <i class="tio-crossfit"></i>
                                                        </span>
                                                    @endif
                                                    <form action="{{ route('admin.astrologers.manage.status') }}"
                                                        method="post" id="block-{{ $overview['id'] }}">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $overview['id'] }}">
                                                        <input type="hidden" name="status" value="2">
                                                    </form>

                                                    @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'edit'))
                                                        <a class="btn btn-outline-info btn-sm square-btn"
                                                            title="{{ translate('edit') }}"
                                                            href="{{ route('admin.astrologers.manage.update', $overview['id']) }}">
                                                            <i class="tio-edit"></i>
                                                        </a>
                                                    @endif

                                                    {{-- <span class="btn btn-outline-danger btn-sm square-btn delete-astrologer ml-2"
                                                        title="{{ translate('delete_astrologer') }}"
                                                        data-id="delete-{{ $overview['id'] }}">
                                                        <i class="tio-delete"></i>
                                                    </span>
                                                    <form action="{{ route('admin.astrologers.manage.delete') }}"
                                                        method="post" id="delete-{{ $overview['id'] }}">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $overview['id'] }}">
                                                    </form> --}}
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="d-flex gap-3 flex-wrap flex-lg-nowrap">
                                            <div class="border p-3 w-170">
                                                <div class="d-flex flex-column mb-1">
                                                    @php
                                                        $totalPooja = !empty($overview['is_pandit_pooja'])
                                                            ? count(json_decode($overview['is_pandit_pooja'], true))
                                                            : 0;
                                                        $totalVipPooja = !empty($overview['is_pandit_vippooja'])
                                                            ? count(json_decode($overview['is_pandit_vippooja'], true))
                                                            : 0;
                                                        $totalAnushthan = !empty($overview['is_pandit_anushthan'])
                                                            ? count(json_decode($overview['is_pandit_anushthan'], true))
                                                            : 0;
                                                        $totalChadhava = !empty($overview['is_pandit_chadhava'])
                                                            ? count(json_decode($overview['is_pandit_chadhava'], true))
                                                            : 0;
                                                        $totalOfflinepooja = !empty($overview['is_pandit_offlinepooja'])
                                                            ? count(
                                                                json_decode($overview['is_pandit_offlinepooja'], true),
                                                            )
                                                            : 0;
                                                        $totalConsultation = !empty($overview['consultation_charge'])
                                                            ? count(json_decode($overview['consultation_charge'], true))
                                                            : 0;
                                                        $totalService =
                                                            $totalPooja +
                                                            $totalVipPooja +
                                                            $totalAnushthan +
                                                            $totalChadhava +
                                                            $totalOfflinepooja +
                                                            $totalConsultation;
                                                    @endphp
                                                    <h6 class="font-weight-normal">{{ translate('total_services') }} :</h6>
                                                    <h3 class="text-primary fs-18">{{ $totalService }}</h3>
                                                </div>

                                                <div class="d-flex flex-column">
                                                    <h6 class="font-weight-normal">{{ translate('total_orders') }} :</h6>
                                                    <h3 class="text-primary fs-18">
                                                        @if ($overview['primary_skills'] == 4)
                                                            {{ !empty($overview['orders']) ? count($overview['orders']->whereIn('status', [0, 1])) : 0 }}
                                                        @else
                                                            <?php
                                                            $chadhava = \App\Models\Chadhava_orders::where('pandit_assign', $overview['id'])
                                                                ->whereIn('status', [0, 1])
                                                                ->count();
                                                            $offlinepoojaOrder = \App\Models\OfflinePoojaOrder::where('pandit_assign', $overview['id'])
                                                                ->whereIn('status', [0, 1])
                                                                ->count();
                                                            ?>
                                                            {{ count($overview['orders']->whereIn('status', [0, 1])) + $chadhava + $offlinepoojaOrder }}
                                                        @endif
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="row gy-3 flex-grow-1 w-100">
                                                <div class="col-12">
                                                    <h4 class="mb-3 text-capitalize">
                                                        {{ $overview['primarySkill']['name'] }}
                                                        {{ translate(' Information') }}</h4>

                                                    <div class="pair-list">
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <span
                                                                    class="key font-weight-bold">{{ translate('name') }}</span>
                                                                <span>:</span>
                                                                <span
                                                                    class="value text-capitalize">{{ @ucwords($overview['name']) }}</span>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <span
                                                                    class="key font-weight-bold">{{ translate('DOB') }}</span>
                                                                <span>:</span>
                                                                <span
                                                                    class="value text-capitalize">{{ @ucwords($overview['dob']) }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <span
                                                                    class="key font-weight-bold">{{ translate('email') }}</span>
                                                                <span>:</span>
                                                                <span class="value">{{ $overview['email'] }}</span>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <span
                                                                    class="key font-weight-bold">{{ translate('phone') }}</span>
                                                                <span>:</span>
                                                                <span class="value">{{ $overview['mobile_no'] }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <span
                                                                    class="key font-weight-bold">{{ translate('address') }}</span>
                                                                <span>:</span>
                                                                <span class="value">{{ $overview['address'] }}</span>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <span
                                                                    class="key font-weight-bold">{{ translate('experience') }}</span>
                                                                <span>:</span>
                                                                <span class="value">{{ $overview['experience'] }}</span>
                                                            </div>
                                                        </div>

                                                        @if (!empty($overview['is_pandit_pooja_per_day']))
                                                            <div class="row">
                                                                <div class="col-md-5">
                                                                    <span
                                                                        class="key font-weight-bold">{{ translate('pooja_per_day') }}</span>
                                                                    <span>:</span>
                                                                    <span
                                                                        class="value">{{ $overview['is_pandit_pooja_per_day'] }}</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-xxl-6">
                                                    <div class="bg-light p-3 border border-primary-light rounded">
                                                        <h4 class="mb-3 text-capitalize">
                                                            {{ translate('bank_information') }}</h4>

                                                        <div class="d-flex gap-5">
                                                            <div class="pair-list">
                                                                <div>
                                                                    <span
                                                                        class="key text-nowrap">{{ translate('bank_name') }}</span>
                                                                    <span class="px-2">:</span>
                                                                    <span
                                                                        class="value ">{{ $overview['bank_name'] }}</span>
                                                                </div>

                                                                <div>
                                                                    <span
                                                                        class="key text-nowrap">{{ translate('IFSC') }}</span>
                                                                    <span class="px-2">:</span>
                                                                    <span
                                                                        class="value">{{ $overview['bank_ifsc'] }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="pair-list">
                                                                <div>
                                                                    <span
                                                                        class="key text-nowrap">{{ translate('holder_name') }}</span>
                                                                    <span class="px-2">:</span>
                                                                    <span
                                                                        class="value">{{ $overview['holder_name'] }}</span>
                                                                </div>

                                                                <div>
                                                                    <span
                                                                        class="key text-nowrap">{{ translate('A/C_No') }}</span>
                                                                    <span class="px-2">:</span>
                                                                    <span
                                                                        class="value">{{ $overview['account_no'] }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="pair-list">
                                                                <div>
                                                                    <span
                                                                        class="key text-nowrap">{{ translate('branch') }}</span>
                                                                    <span class="px-2">:</span>
                                                                    <span
                                                                        class="value">{{ $overview['branch_name'] }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- wallet --}}
                                <div class="card mt-3">
                                    <div class="card-body">

                                        @php
                                            $totalOrderAmt = 0;
                                            $totalCommissionAmt = 0;
                                            $totalTaxAmt = 0;
                                            $totalFinalAmt = 0;
                                            $totalServiceAmt = 0;
                                            if ($overview['primary_skills'] == 4) {
                                                $astrologerServices = json_decode(
                                                    $overview['consultation_charge'],
                                                    true,
                                                );
                                            } else {
                                                $panditpoojaServices = !empty($overview['is_pandit_pooja'])
                                                    ? json_decode($overview['is_pandit_pooja'], true)
                                                    : '';
                                                $panditVipPoojaServices = !empty($overview['is_pandit_vippooja'])
                                                    ? json_decode($overview['is_pandit_vippooja'], true)
                                                    : '';

                                                $panditAnushthanPoojaServices = !empty($overview['is_pandit_anushthan'])
                                                    ? json_decode($overview['is_pandit_anushthan'], true)
                                                    : '';

                                                $panditChadhavaPoojaServices = !empty($overview['is_pandit_chadhava'])
                                                    ? json_decode($overview['is_pandit_chadhava'], true)
                                                    : '';

                                                $panditOfflinePoojaServices = !empty($overview['is_pandit_offlinepooja'])
                                                    ? json_decode($overview['is_pandit_offlinepooja'], true)
                                                    : '';
                                            }

                                            foreach ($transaction as $key => $trans) {
                                                $totalOrderAmt += $trans['amount'];
                                                $totalCommissionAmt += ceil(
                                                    ($trans['amount'] * $trans['commission']) / 100,
                                                );
                                                $totalTaxAmt += ceil(($trans['amount'] * $trans['tax']) / 100);

                                                $totalFinalAmt +=
                                                    $trans['amount'] -
                                                    ceil(($trans['amount'] * $trans['commission']) / 100) -
                                                    ceil(($trans['amount'] * $trans['tax']) / 100);
                                                if ($overview['primary_skills'] == 4) {
                                                    $totalServiceAmt +=
                                                         $astrologerServices[$trans['serviceOrder']['service_id'] ?? null] ?? '';
                                                } else {
                                                    if ($trans['type'] == 'pooja') {
                                                        $totalServiceAmt +=
                                                            $panditpoojaServices[$trans['serviceOrder']['service_id']?? null] ?? '';
                                                    } elseif ($trans['type'] == 'vip') {
                                                        $totalServiceAmt +=
                                                            $panditVipPoojaServices[
                                                                $trans['serviceOrder']['service_id']
                                                                ?? null] ?? '';
                                                    } elseif ($trans['type'] == 'anushthan') {
                                                        $totalServiceAmt +=
                                                            $panditAnushthanPoojaServices[
                                                                $trans['serviceOrder']['service_id']
                                                                ?? null] ?? '';
                                                    } elseif ($trans['type'] == 'chadhava') {
                                                        $totalServiceAmt +=
                                                            $panditChadhavaPoojaServices[
                                                                $trans['chadhavaOrder']['service_id']
                                                                ?? null] ?? '';
                                                    } elseif ($trans['type'] == 'offlinepooja') {
                                                        $totalServiceAmt +=
                                                            $panditOfflinePoojaServices[
                                                                $trans['offlinepoojaOrder']['service_id']
                                                                ?? null] ?? '';
                                                    }
                                                }
                                            }
                                        @endphp

                                        <div class="row justify-content-between align-items-center g-2 mb-3">
                                            <div class="col-sm-6">
                                                <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                                                    <img width="20" class="mb-1"
                                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png') }}"
                                                        alt="">
                                                    {{ $overview['primarySkill']['name'] }} {{ translate(' Wallet') }}
                                                </h4>
                                            </div>
                                        </div>

                                        <div class="row g-2" id="order_stats">
                                            <div class="col-lg-4">
                                                <div class="card h-100 d-flex justify-content-center align-items-center">
                                                    <div
                                                        class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                                                        <img width="48" class="mb-2"
                                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw.png') }}"
                                                            alt="">
                                                        <h3 class="for-card-count mb-0 fz-24">
                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $overview->type == 'freelancer' ? $totalServiceAmt : $overview->salary)) }}
                                                        </h3>
                                                        <div class="font-weight-bold text-capitalize mb-30">
                                                            {{ translate($overview->type == 'freelancer' ? 'withdrawable_balance' : 'salary') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="row g-2">
                                                    @if ($overview->type == 'freelancer')
                                                        <div class="col-md-6">
                                                            <div class="card card-body h-100 justify-content-center">
                                                                <div
                                                                    class="d-flex gap-2 justify-content-between align-items-center">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <h3 class="mb-1 fz-24">
                                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: 0)) }}
                                                                        </h3>
                                                                        <div class="text-capitalize mb-0">
                                                                            {{ translate('pending_Withdraw') }}</div>
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
                                                                <div
                                                                    class="d-flex gap-2 justify-content-between align-items-center">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <h3 class="mb-1 fz-24">
                                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: 0)) }}
                                                                        </h3>
                                                                        <div class="text-capitalize mb-0">
                                                                            {{ translate('aready_Withdrawn') }}</div>
                                                                    </div>
                                                                    <div>
                                                                        <img width="40"
                                                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/aw.png') }}"
                                                                            alt="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-6">
                                                        <div class="card card-body h-100 justify-content-center">
                                                            <div
                                                                class="d-flex gap-2 justify-content-between align-items-center">
                                                                <div class="d-flex flex-column align-items-start">
                                                                    <h3 class="mb-1 fz-24">
                                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalOrderAmt)) }}
                                                                    </h3>
                                                                    <div class="text-capitalize mb-0">
                                                                        {{ translate('total_amount') }}
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
                                                            <div
                                                                class="d-flex gap-2 justify-content-between align-items-center">
                                                                <div class="d-flex flex-column align-items-start">
                                                                    <h3 class="mb-1 fz-24">
                                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalCommissionAmt)) }}
                                                                    </h3>
                                                                    <div class="text-capitalize mb-0">
                                                                        {{ translate('total_Commission_given') }}</div>
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
                                                            <div
                                                                class="d-flex gap-2 justify-content-between align-items-center">
                                                                <div class="d-flex flex-column align-items-start">
                                                                    <h3 class="mb-1 fz-24">
                                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalTaxAmt)) }}
                                                                    </h3>
                                                                    <div class="text-capitalize mb-0">
                                                                        {{ translate('total_tax_given') }}</div>
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
                                                            <div
                                                                class="d-flex gap-2 justify-content-between align-items-center">
                                                                <div class="d-flex flex-column align-items-start">
                                                                    <h3 class="mb-1 fz-24">
                                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalFinalAmt)) }}
                                                                    </h3>
                                                                    <div class="text-capitalize mb-0">
                                                                        {{ translate('final_amount') }}</div>
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

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

    {{-- reject astrologer --}}
    <script>
        $('.reject-astrologer').on('click', function() {
            let astrologerId = $(this).attr("data-id");
            Swal.fire({
                title: 'Are You Sure To Reject Astrologer',
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + astrologerId).submit();
                }
            });
        });
    </script>

    {{-- block astrologer --}}
    <script>
        $('.block-astrologer').on('click', function() {
            let astrologerId = $(this).attr("data-id");
            Swal.fire({
                title: 'Are You Sure To Block Astrologer',
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + astrologerId).submit();
                }
            });
        });
    </script>

    {{-- delete astrologer --}}
    <script>
        $('.delete-astrologer').on('click', function() {
            let astrologerId = $(this).attr("data-id");
            Swal.fire({
                title: 'Are You Sure To Delete Astrologer',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + astrologerId).submit();
                }
            });
        });
    </script>
@endpush