@extends('layouts.back-end.app-trustees')
@section('title', translate('dashboard'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@php
use App\Utils\Helpers;
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
$roleTabs = 1;
$logintype = 'trust';
$PurohitsId = 0;
$purohitsEmpId = 0;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
$roleTabs = 0;
$logintype = 'employee';
$PurohitsId = auth('trust_employee')->user()->purohit_id;
$purohitsEmpId = auth('trust_employee')->user()->id;
} elseif (auth('purohit')->check()) {
$roleTabs = 1;
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
$logintype = 'purohit';
$PurohitsId = auth('purohit')->user()->id;
$purohitsEmpId = 0;
}
@endphp
<div class="content container-fluid">
    <div class="page-header pb-0 border-0 mb-3">
        <div class="flex-between row align-items-center mx-1">
            <div>
                @if (auth('trust')->check())
                <h1 class="page-header-title text-capitalize">
                    {{ translate('welcome') . ' ' . auth('trust')->user()->f_name . ' ' . auth('trust')->user()->l_name }}
                </h1>
                @elseif(auth('trust_employee')->check())
                <h1 class="page-header-title text-capitalize">
                    {{ translate('welcome') . ' ' . auth('trust_employee')->user()->name }}
                </h1>
                @elseif(auth('purohit')->check())
                <h1 class="page-header-title text-capitalize">
                    {{ translate('welcome') . ' ' . auth('purohit')->user()->name }}
                </h1>
                @endif

            </div>
        </div>
    </div>
    @if (Helpers::Employee_modules_permission('Dashboard', 'Puja Dashboard', 'View'))
    <div class="card mb-3 remove-card-shadow">
        <div class="card-body">
            <div class="row justify-content-between align-items-center g-2 mb-3">
                <div class="col-sm-6">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/business_analytics.png') }}"
                            alt="">
                        {{ translate('order_analytics') }}
                    </h4>
                </div>
            </div>
            <div class="row g-2">
                <div class="col-md-12">
                    <h3>Services Order Count</h3>
                </div>
                <?php
                $temple_id = 0;
                if (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) {
                    $temple_id = auth('trust_employee')->user()->temple_id;
                } elseif (auth('purohit')->check()) {
                    $temple_id = auth('purohit')->user()->temple_id;
                }
                $getTemples = \App\Models\Temple::where('trust_id', $relationEmployees)
                    ->when(!empty($temple_id), function ($q) use ($temple_id) {
                        $q->where('id', $temple_id);
                    })
                    ->get();
                $vendorEmp = [];
                if ($roleTabs == 0) {
                    $vendorEmp = \App\Models\VendorEmployees::where('type', 'trust')->where('relation_id', $relationEmployees)->first();
                }
                $decoded = [];
                if ($vendorEmp && $vendorEmp->selected_services) {
                    $raw = $vendorEmp->selected_services ?? '[]';
                    $firstDecode = json_decode($raw, true);
                    if (is_string($firstDecode)) {
                        $decoded = json_decode($firstDecode, true);
                    } else {
                        $decoded = $firstDecode;
                    }
                }
                ?>
                @if ($getTemples)
                @foreach ($getTemples as $planT)
                <div class="col-sm-12">
                    <span class="d-flex align-items-center text-capitalize gap-10 mb-0"
                        style="text-decoration:underline;font-size: 18px;">
                        {{ $planT['name'] }}
                    </span>
                </div>

                <div class="col-md-6 mb-4">
                    <a href="{{ route('trustees-vendor.order-management.create-ticket',['temple_id'=>$planT['id']]) }}" class="btn btn-primary w-100" target="_blank" rel="noopener noreferrer">Create Ticket</a>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6 mb-4">
                    <div class="card card-custom card-today p-4">
                        <div class="card-body text-center">
                            <div class="card-icon">
                                <i class="tio-wallet text-success" style="font-size: 54px;"></i>
                            </div>

                            <?php
                            $getOrderHeader = \App\Models\TempleOrderMaster::where('trust_id', $relationEmployees)->whereHas('details', function ($q1) {
                                $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                                })->when((auth('purohit')->check()), function ($q) {
                                    $q->where('purohit_id', auth('purohit')->user()->id);
                                });
                            })
                                ->where('temple_id', $planT['id'])->where('booking_status', 'confirmed');
                            ?>
                            <hr class="my-1">
                            <h6 class="">Total Today</h6>
                            <hr class="my-1">
                            <h2 class="card-title">
                                @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                {{ ((clone $getOrderHeader)->whereDate('created_at', date('Y-m-d'))->withSum(['details as total_base_price' => function ($q) {
                                        $q->where('type', 'puja');
                                    }], 'base_price')->get() ->sum('total_base_price') )}}
                                @else
                                {{ (clone $getOrderHeader)->whereDate('created_at', date('Y-m-d'))->sum('total_amount') }}
                                @endif
                            </h2>
                            <div class="payment-breakdown">
                                <div class="row">
                                    <div class="col-6 text-start">
                                        <div class="payment-item">
                                            <span class="payment-label">ONLINE</span><br>
                                            <span class="payment-value text-primary font-weight-bolder">
                                                @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                                {{ ((clone $getOrderHeader)->where('payment_mode','online')->whereDate('created_at', date('Y-m-d'))->withSum(['details as total_base_price' => function ($q) {
                                                    $q->where('type', 'puja');
                                                }], 'base_price')->get() ->sum('total_base_price') ) }}
                                                @else
                                                {{ (clone $getOrderHeader)->where('payment_mode','online')->whereDate('created_at', date('Y-m-d'))->sum('total_amount') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <div class="payment-item">
                                            <span class="payment-label">CASH</span><br>
                                            <span class="payment-value text-success font-weight-bolder">
                                                @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                                {{ ((clone $getOrderHeader)->where('payment_mode','cash')->whereDate('created_at', date('Y-m-d'))->withSum(['details as total_base_price' => function ($q) {
                                                    $q->where('type', 'puja');
                                                }], 'base_price')->get() ->sum('total_base_price') )}}
                                                @else
                                                {{ (clone $getOrderHeader)->where('payment_mode','cash')->whereDate('created_at', date('Y-m-d'))->sum('total_amount') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card card-custom card-today p-4">
                        <div class="card-body text-center">
                            <div class="card-icon">
                                <i class="tio-star text-success" style="font-size: 54px;"></i>
                            </div>
                            <hr class="my-1">
                            <h6 class="">Month Total</h6>
                            <hr class="my-1">
                            <h2 class="card-title">
                                @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                {{ ((clone $getOrderHeader)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->withSum(['details as total_base_price' => function ($q) {
                                        $q->where('type', 'puja');
                                    }], 'base_price')->get() ->sum('total_base_price') )}}
                                @else
                                {{ (clone $getOrderHeader)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->sum('total_amount') }}
                                @endif
                            </h2>
                            <div class="payment-breakdown">
                                <div class="row">
                                    <div class="col-6 text-start">
                                        <div class="payment-item">
                                            <span class="payment-label">ONLINE</span><br>
                                            <span class="payment-value text-primary font-weight-bolder">
                                                @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                                {{ ((clone $getOrderHeader)->where('payment_mode','online')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->withSum(['details as total_base_price' => function ($q) {
                                                    $q->where('type', 'puja');
                                                }], 'base_price')->get() ->sum('total_base_price') )}}
                                                @else
                                                {{ (clone $getOrderHeader)->where('payment_mode','online')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->sum('total_amount') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <div class="payment-item">
                                            <span class="payment-label">CASH</span><br>
                                            <span class="payment-value text-success font-weight-bolder">
                                                @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                                {{ ((clone $getOrderHeader)->where('payment_mode','cash')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->withSum(['details as total_base_price' => function ($q) {
                                                    $q->where('type', 'puja');
                                                }], 'base_price')->get() ->sum('total_base_price') )}}
                                                @else
                                                {{ (clone $getOrderHeader)->where('payment_mode','cash')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])->sum('total_amount') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card card-custom card-today p-4">
                        <div class="card-body text-center">
                            <div class="card-icon">
                                <i class="tio-event text-success" style="font-size: 54px;"></i>
                            </div>
                            <hr class="my-1">
                            <h6 class="">Year Total</h6>
                            <hr class="my-1">
                            <h2 class="card-title">
                                @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                {{ ((clone $getOrderHeader)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->withSum(['details as total_base_price' => function ($q) {
                                        $q->where('type', 'puja');
                                    }], 'base_price')->get() ->sum('total_base_price') )}}
                                @else
                                {{ (clone $getOrderHeader)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->sum('total_amount') }}
                                @endif
                            </h2>
                            <div class="payment-breakdown">
                                <div class="row">
                                    <div class="col-6 text-start">
                                        <div class="payment-item">
                                            <span class="payment-label">ONLINE</span><br>
                                            <span class="payment-value text-primary font-weight-bolder">
                                                @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                                {{ ((clone $getOrderHeader)->where('payment_mode', 'online')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->withSum(['details as total_base_price' => function ($q) {
                                                        $q->where('type', 'puja');
                                                    }], 'base_price')->get() ->sum('total_base_price') )}}
                                                @else
                                                {{ (clone $getOrderHeader)->where('payment_mode','online')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->sum('total_amount') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <div class="payment-item">
                                            <span class="payment-label">CASH</span><br>
                                            <span class="payment-value text-success font-weight-bolder">
                                                @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                                {{ ((clone $getOrderHeader)->where('payment_mode', 'cash')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->withSum(['details as total_base_price' => function ($q) {
                                                        $q->where('type', 'puja');
                                                    }], 'base_price')->get() ->sum('total_base_price') )}}
                                                @else
                                                {{ (clone $getOrderHeader)->where('payment_mode','cash')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()])->sum('total_amount') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card card-custom card-today p-4">
                        <div class="card-body text-center">
                            <div class="card-icon">
                                <i class="tio-receipt text-success" style="font-size: 54px;"></i>
                            </div>
                            <hr>
                            <h6 class="">Total Receipt</h6>
                            <hr>
                            <h2 class="card-title">
                                @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                {{ ((clone $getOrderHeader)->whereHas('details', function ($q) {
                                        $q->where('type', 'puja');
                                        $q->where('print_status', '1');
                                    })->count()) }}
                                @else
                                {{ ((clone $getOrderHeader)->whereHas('details',function ($q) {
                                        $q->where('print_status', '1');
                                    })->count()) }}
                                @endif
                            </h2>
                        </div>
                    </div>
                </div>
                @if ($planT['package_service'] && json_decode($planT['package_service'] ?? '[]', true))
                @foreach (json_decode($planT['package_service'] ?? '[]', true) as $plan)
                <?php
                if (in_array($plan['name'], $decoded)) {
                    $roleTabs2 = 1;
                } else {
                    $roleTabs2 = 0;
                }
                ?>
                @if ($plan['status'] == 1 && ($roleTabs2 == 1 || $roleTabs == 1))
                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_pending">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/pending.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ $plan['name'] }}</h6>
                        </div>
                        <span
                            class="order-stats__title">{{ \App\Models\TempleOrderDetails::where('type', $plan['name'])->where('payment_status', 1)->where('trust_id', $relationEmployees)
                                ->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                                })->when((auth('purohit')->check()), function ($q) {
                                    $q->where('purohit_id', auth('purohit')->user()->id);
                                })->count() ?? 0 }}</span>
                    </a>
                </div>
                @endif
                @endforeach
                @else
                <div class="col-sm-12 text-center text-danger">
                    <span>
                        No Service Found
                    </span>
                </div>
                @endif
                @endforeach
                @endif

                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_pending">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/pending.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('Cash') }}
                                ({{ \App\Models\TempleOrderMaster::where('payment_mode', 'cash')->where('payment_status', 1)->where('trust_id', $relationEmployees)->whereHas('details', function ($q1) {
                                $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                                })->when((auth('purohit')->check()), function ($q) {
                                    $q->where('purohit_id', auth('purohit')->user()->id);
                                });
                            })->count() ?? 0 }})
                            </h6>
                        </div>
                        <span
                            class="order-stats__title">
                            @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                            {{ \App\Models\TempleOrderMaster::where('payment_mode', 'cash')->where('payment_status', 1)->where('trust_id', $relationEmployees)->whereHas('details', function ($q1) {
                                    $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                        $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                                    })->when((auth('purohit')->check()), function ($q) {
                                        $q->where('purohit_id', auth('purohit')->user()->id);
                                    });
                                })->withSum(['details as total_base_price' => function ($q) {
                                                        $q->where('type', 'puja');
                                                    }], 'base_price')->get() ->sum('total_base_price') }}
                            @else
                            {{ \App\Models\TempleOrderMaster::where('payment_mode', 'cash')->where('payment_status', 1)->where('trust_id', $relationEmployees)->sum('total_amount') ?? 0 }}
                            @endif
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_pending">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/pending.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('Online') }}
                                ({{ \App\Models\TempleOrderMaster::where('payment_mode', 'online')->where('payment_status', 1)->where('trust_id', $relationEmployees)->whereHas('details', function ($q1) {
                                $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                                })->when((auth('purohit')->check()), function ($q) {
                                    $q->where('purohit_id', auth('purohit')->user()->id);
                                });
                            })->count() ?? 0 }})
                            </h6>
                        </div>
                        <span
                            class="order-stats__title">
                            @if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                            {{ \App\Models\TempleOrderMaster::where('payment_mode', 'online')->where('payment_status', 1)->where('trust_id', $relationEmployees)->whereHas('details', function ($q1) {
                                    $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                        $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                                    })->when((auth('purohit')->check()), function ($q) {
                                        $q->where('purohit_id', auth('purohit')->user()->id);
                                    });
                                })->withSum(['details as total_base_price' => function ($q) {
                                                        $q->where('type', 'puja');
                                                    }], 'base_price')->get() ->sum('total_base_price') }}
                            @else

                            {{ \App\Models\TempleOrderMaster::where('payment_mode', 'online')->where('payment_status', 1)->where('trust_id', $relationEmployees)->sum('total_amount') ?? 0 }}
                            @endif
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if (Helpers::Employee_modules_permission('Dashboard', 'Puja Dashboard', 'Wallet'))
    <div class="card mb-3 remove-card-shadow">
        <div class="card-body">
            <div class="row justify-content-between align-items-center g-2 mb-3">
                <div class="col-sm-6">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                        <img width="20" class="mb-1"
                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png') }}"
                            alt="">
                        {{ translate('Wallet') }}
                    </h4>
                </div>
            </div>
            <div class="row g-2" id="order_stats">
                <div class="col-lg-4">
                    <!-- Card -->
                    <div class="card h-100 d-flex justify-content-center align-items-center">
                        <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                            <img width="48" class="mb-2"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw.png') }}"
                                alt="">
                            <h3 class="for-card-count mb-0 fz-24">
                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['totalEarning']??0), currencyCode: getCurrencyCode(type: 'default')) }}
                            </h3>
                            <div class="font-weight-bold text-capitalize mb-30">
                                {{ translate('balance') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row g-2">
                        <div class="col-md-{{ ((auth('trust')->check() || auth('purohit')->check())?'4':'6')}}">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:  $dashboardData['pendingWithdraw']??0), currencyCode: getCurrencyCode(type: 'default')) }}
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
                        <div class="col-md-{{ ((auth('trust')->check() || auth('purohit')->check())?'4':'6')}}">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['withdrawn']??0), currencyCode: getCurrencyCode(type: 'default')) }}
                                        </h3>
                                        <div class="text-capitalize mb-0">{{ translate('already_Withdrawn') }}
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
                        <div class="col-md-{{ ((auth('trust')->check() || auth('purohit')->check())?'4':'6')}}">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['purohit_collected_amount']??0), currencyCode: getCurrencyCode(type: 'default')) }}
                                        </h3>
                                        <div class="text-capitalize mb-0">
                                            @if(auth('trust')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") != 'Sub Pandit')))
                                            {{ translate('Purohit_Amount') }}
                                            @else
                                            {{ translate('Cash_Pending_Amount') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <img width="40" src="{{ dynamicAsset(path: 'public/assets/back-end/img/aw.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(($dashboardData['trust_fee']??0) > 0)
                        <div class="col-md-4">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['trust_fee']??0), currencyCode: getCurrencyCode(type: 'default')) }}
                                        </h3>
                                        <div class="text-capitalize mb-0">{{ translate('trust_Fee') }}
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
                        @endif
                        <div class="col-md-4">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['adminCommission']??0), currencyCode: getCurrencyCode(type: 'default')) }}
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

                        <div class="col-md-4">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['gst_total_amount']??0), currencyCode: getCurrencyCode(type: 'default')) }}
                                        </h3>
                                        <div class="text-capitalize mb-0">{{ translate('total_gst_amount') }}
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

                    </div>
                </div>


            </div>
        </div>
    </div>
    @endif

</div>

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/vendor/chart.js/dist/Chart.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/apexcharts.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/vendor/chart.js.extensions/chartjs-extensions.js') }}">
</script>
<script
    src="{{ dynamicAsset(path: 'public/assets/back-end/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js') }}">
</script>
@endpush

@push('script_2')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/vendor/dashboard.js') }}"></script>
@endpush