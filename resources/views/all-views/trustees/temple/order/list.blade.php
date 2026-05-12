@extends('layouts.back-end.app-trustees')
@section('title', translate('temple_order_list'))
@section('content')
@php
use App\Utils\Helpers;
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
} elseif (auth('purohit')->check()) {
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
}
@endphp
<div class="content container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ translate('temple_order_list') }}
        </h2>
    </div>
    <?php
    if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check()) {
        $totalPayment = \App\Models\TempleOrderMaster::where('status', 1)->where('payment_status', 1)
            ->whereHas('details', function ($q1) {
                $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                })->when((auth('purohit')->check()), function ($q) {
                    $q->where('purohit_id', auth('purohit')->user()->id);
                });
            })->withSum(['details as total_base_price' => function ($q) {
                $q->where('type', 'puja');
            }], 'base_price')->get()->sum('total_base_price');
        $totalPaymentCash = \App\Models\TempleOrderMaster::where('payment_mode', 'cash')->where('status', 1)->where('payment_status', 1)->whereHas('details', function ($q1) {
            $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
            })->when((auth('purohit')->check()), function ($q) {
                $q->where('purohit_id', auth('purohit')->user()->id);
            });
        })->withSum(['details as total_base_price' => function ($q) {
            $q->where('type', 'puja');
        }], 'base_price')->get()->sum('total_base_price');
        $totalPaymentOnline = \App\Models\TempleOrderMaster::where('payment_mode', 'online')->where('status', 1)->where('payment_status', 1)->whereHas('details', function ($q1) {
            $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
            })->when((auth('purohit')->check()), function ($q) {
                $q->where('purohit_id', auth('purohit')->user()->id);
            });
        })->withSum(['details as total_base_price' => function ($q) {
            $q->where('type', 'puja');
        }], 'base_price')->get()->sum('total_base_price');
    } else {
        $totalPayment = \App\Models\TempleOrderMaster::where('status', 1)->where('payment_status', 1)->sum('total_amount');
        $totalPaymentCash = \App\Models\TempleOrderMaster::where('payment_mode', 'cash')->where('status', 1)->where('payment_status', 1)->sum('total_amount');
        $totalPaymentOnline = \App\Models\TempleOrderMaster::where('payment_mode', 'online')->where('status', 1)->where('payment_status', 1)->sum('total_amount');
    }
    ?>
    <div>
        <div class="row g-3" id="order_stats">
            <div class="col-lg-12">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-success">
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPayment), currencyCode: getCurrencyCode()) }}
                                    </h3>
                                    <div class="text-capitalize mb-0 text-success">Total Earning</div>
                                </div>
                                <div>
                                    <img width="40" class="mb-2 rotate-icon"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.vippooja.order.list', 'all') }}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-warning">
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentCash), currencyCode: getCurrencyCode()) }}
                                        </h3>
                                        <div class="text-capitalize mb-0 text-warning">Cash Earning</div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2 rotate-icon"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.vippooja.order.list', 'all') }}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-warning">
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentOnline), currencyCode: getCurrencyCode()) }}
                                        </h3>
                                        <div class="text-capitalize mb-0 text-warning">Online Earning</div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2 rotate-icon"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.vippooja.order.list', 'all') }}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-info">
                                            {{ \App\Models\TempleOrderMaster::where('status', 1)
                                                ->whereHas('details', function ($q1) {
                                                $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                                                })->when((auth('purohit')->check()), function ($q) {
                                                    $q->where('purohit_id', auth('purohit')->user()->id);
                                                });
                                            })->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">TOTAL ORDER</div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/order.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.vippooja.order.list', 1) }}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-success">
                                            {{ \App\Models\TempleOrderMaster::where('booking_status','confirmed')->where('status', 1)->where('payment_status', 1)
                                                ->whereHas('details', function ($q1) {
                                                    $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                                        $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                                                    })->when((auth('purohit')->check()), function ($q) {
                                                        $q->where('purohit_id', auth('purohit')->user()->id);
                                                    });
                                                })->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">COMPLETED ORDER</div>
                                    </div>
                                    <div>
                                        <img width="40"
                                            class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/ordercom.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.vippooja.order.list', 0) }}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-primary">
                                            {{ \App\Models\TempleOrderMaster::where('booking_status','pending')->where('status', 1)->where('payment_status', 0)
                                                ->whereHas('details', function ($q1) {
                                                $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                                                })->when((auth('purohit')->check()), function ($q) {
                                                    $q->where('purohit_id', auth('purohit')->user()->id);
                                                });
                                            })->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">PENDING ORDER</div>
                                    </div>
                                    <div>
                                        <img width="40"
                                            class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/panding.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.vippooja.order.list', 6) }}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-danger">
                                            {{ \App\Models\TempleOrderMaster::where('booking_status','cancel')->where('status', 1)->where('payment_status', 2)
                                                ->whereHas('details', function ($q1) {
                                                    $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                                        $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                                                    })->when((auth('purohit')->check()), function ($q) {
                                                        $q->where('purohit_id', auth('purohit')->user()->id);
                                                    });
                                                })->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">REJECTED ORDER</div>
                                    </div>
                                    <div>
                                        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828665.png"
                                            alt="Rejected Icon" width="40">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <!-- Heading + Button Row -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Filter the Order List</h4>
                        </div>
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <form method="GET" action="{{ route('trustees-vendor.order-management.order-list') }}">
                                    <div class="row">
                                        {{-- Payment Status Filter --}}
                                        <div class="col-md-3">
                                            <label for="payment_status" class="font-weight-bold">Payment Status</label>
                                            <select name="payment_status" class="form-control payment_mode">
                                                <option value="">All</option>
                                                <option value="cash" {{ request('payment_status') == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="online" {{ request('payment_status') == 'online' ? 'selected' : '' }}>Online Pay</option>
                                                <option value="free" {{ request('payment_status') == 'free' ? 'selected' : '' }}>Free</option>
                                            </select>
                                        </div>
                                        {{-- Booking Status Filter --}}
                                        <div class="col-md-3">
                                            <label for="booking_status" class="font-weight-bold">Bookink Status</label>
                                            <select name="booking_status" class="form-control booking_status">
                                                <option value="">All</option>
                                                <option value="confirmed" {{ request('booking_status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                <option value="pending" {{ request('booking_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="cancel" {{ request('booking_status') == 'cancel' ? 'selected' : '' }}>Cancel</option>
                                            </select>
                                        </div>
                                        @if((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                        @else
                                        {{-- Purohit Name Filter --}}
                                        <div class="col-md-3">
                                            <label for="purohit_id" class="font-weight-bold">Purohit</label>
                                            <select name="purohit_id" class="form-control purohit_id">
                                                <option value="">All</option>
                                                @foreach($purohits as $purohit)
                                                <option value="{{ $purohit->id }}" {{ request('purohit_id') == $purohit->id ? 'selected' : '' }}>
                                                    {{ $purohit->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="purohitpayment" class="table table-striped table-bordered table-hover">
                                <thead class="thead-light  text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('order_id') }}</th>
                                        <th>{{ translate('temple_name') }}</th>
                                        <th>{{ translate('service_name') }}</th>
                                        <th>{{ translate('yajman Name') }}</th>
                                        <th>{{ translate('amount') }}</th>
                                        <th>{{ translate('platform') }}</th>
                                        <th>{{ translate('payment_mode') }}</th>
                                        <th>{{ translate('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($OrderList as $key => $item)
                                    @php
                                    $mode = strtolower($item->payment_mode );
                                    @endphp
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->order_id }}<br>
                                        @if($item->booking_status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                        @elseif($item->booking_status == 'confirmed')
                                        <span class="badge badge-success">Confirmed</span>
                                        @elseif($item->booking_status == 'cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                        @endif
                                        <br>
                                        @if($item->upgradeHistory->isNotEmpty())
                                        @foreach($item->upgradeHistory as $uh)
                                        <div class="p-2 mb-2 border rounded bg-light">
                                            <strong>Puja Upgrade:</strong>
                                            {{ optional($item->details->where('type', 'puja')->first()->package)->varient_name }}

                                            <br>

                                            <strong>Old Amount:</strong> ₹{{ $uh->old_amount }}
                                            <br>

                                            <strong>New Amount:</strong> ₹{{ $uh->new_amount }}
                                            <br>

                                            <strong>Difference:</strong> ₹{{ $uh->new_amount - $uh->old_amount }}

                                            <br>
                                            <small class="text-muted">Upgraded on: {{ $uh->upgraded_at }}</small>
                                        </div>
                                        @endforeach
                                        @else
                                        <span class="badge badge-secondary"></span>
                                        @endif
                                    </td>
                                    <td>{{ $item->temple->name }}<br>
                                        @if($item->details && $item->details->count() > 0)
                                        @php
                                        $purohitName = $item->details->first()->purohit->name ?? '-';
                                        @endphp
                                        {{ $purohitName }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['details'] && count($item['details'])>0)
                                        @foreach($item['details'] as $va)
                                        <span>{{ $va['type'] }}</span><br>
                                        @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $item->user->name ?? '-' }} ({{ $item->total_people_count }})</td>
                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item->total_amount ?? 0), currencyCode: getCurrencyCode()) }}</td>
                                    <td>
                                        <?php
                                        if (($item->platform ?? "") == 'qr') {
                                            $messageText = 'info';
                                        } elseif (($item->platform ?? "") == 'counter' || ($item->platform ?? "") == 'purohit') {
                                            $messageText = 'warning';
                                        } elseif (($item->platform ?? "") == 'web') {
                                            $messageText = 'primary';
                                        } elseif (($item->platform ?? "") == 'app') {
                                            $messageText = 'success';
                                        } else {
                                            $messageText = 'secondary';
                                        } ?>
                                        <span class="badge badge-{{$messageText}} text-white"> {{ ucwords($item->platform??"") }}</span>
                                    </td>
                                    <td>
                                        @if($mode == 'cash' || $mode == 'online')
                                        <span class="badge badge-success">{{ ucfirst($item->payment_mode) }}</span>
                                        @else
                                        <span class="badge badge-secondary">{{ ucfirst($item->payment_mode) }}</span>
                                        @endif
                                    </td>


                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#leadDetailsModal{{ $item->id }}" title="{{ translate('Order Check') }}" data-toggle="tooltip" data-placement="left">
                                                <i class="tio tio-info"></i>
                                            </button>

                                            @if((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check())
                                            @else
                                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                                data-target="#puhrohitModal" data-id="{{ $item->order_id }}" title="{{ translate('Change the purohit Ji only for Puja Ticket') }}" data-toggle="tooltip" data-placement="top">
                                                <i class="tio tio-user"></i>
                                            </button>
                                            @endif

                                            @if($mode == 'cash' && $item->booking_status == 'pending')
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#cashConfirmModal" data-id="{{ $item->order_id }}" title="{{ translate('Payment Confirm By Cash') }}" data-toggle="tooltip" data-placement="left">
                                                <i class="tio tio-checkmark-circle"></i>
                                            </button>
                                            @endif
                                            @if($item->is_upgraded == 0)
                                            <button type="button" class="btn btn-info btn-sm"
                                                data-toggle="modal"
                                                data-target="#upgradePackage"
                                                data-id="{{ $item->order_id }}"
                                                title="{{ translate('Change the purohit Ji only for Puja Ticket') }}"
                                                data-toggle="tooltip"
                                                data-placement="top">
                                                <i class="tio tio-new-message"></i>
                                            </button>
                                            @endif

                                            <div>
                                    </td>
                                    </tr>
                                    @endforeach
                                    --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal fade" id="leadDetailsModal" tabindex="-1" aria-labelledby="leadDetailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="leadDetailsModalLabel"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body add-new-order-details">

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Payment Confirm -->
                    <div class="modal fade" id="cashConfirmModal" tabindex="-1" role="dialog" aria-labelledby="cashConfirmModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title" id="cashConfirmModalLabel">{{ translate('Cash Payment Confirmation') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <p class="mb-3">{{ translate('Have you received this payment in cash?') }}</p>
                                    <input type="hidden" id="confirmOrderId">
                                    <button type="button" class="btn btn-success" id="confirmCashBtn">{{ translate('Yes, payment received') }}</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('No') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Payment Confirm -->
                    <!-- Purohit Confirm -->
                    <div class="modal fade" id="puhrohitModal" tabindex="-1" role="dialog" aria-labelledby="puhrohitModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title" id="puhrohitModalLabel">{{ translate('Purohit Confirmation') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <p class="mb-3">{{ translate('Select the Purohit who received the payment') }}</p>

                                    <input type="hidden" id="confirmPurohitOrderId">

                                    <!-- Dropdown for Purohit list -->
                                    <div class="form-group">
                                        <select id="purohitSelect" class="form-control">
                                            <option value="">{{ translate('Select Purohit') }}</option>
                                            @foreach($purohits as $purohit)
                                            <option value="{{ $purohit->id }}">{{ $purohit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="button" class="btn btn-success" id="confirmPurohitBtn">{{ translate('Confirm Payment') }}</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Purohit Confirm -->
                    <!-- Upgrade Package Confirm -->
                    <div class="modal fade" id="upgradePackage" tabindex="-1" role="dialog" aria-labelledby="upgradePackageLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title" id="upgradePackageLabel">{{ translate('Package Upgrade') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <p class="mb-3">{{ translate('Upgrade the Puja Package') }}</p><br>
                                    <span>{{ translate('Please Select the Puja Package Only') }}</span>
                                    <input type="hidden" id="upgradePackageOrderId">

                                    <!-- Dropdown for Purohit list -->
                                    <div class="form-group">
                                        <select id="upgradeSelectedPackage" class="form-control">
                                            <option value="">{{ translate('Select Package') }}</option>
                                            @foreach($packagesdata as $pkg)
                                            @php
                                            $allAmount = (($pkg->base_price ?? 0) +
                                            ($pkg->platform_fee_percentage ?? 0) +
                                            ($pkg->receipt_fee_percentage ?? 0));
                                            $pkgAmount = (($allAmount * ($pkg->gst_rate ?? 1))/100);
                                            @endphp
                                            {{-- Show only higher priced packages --}}
                                            <option value="{{ $pkg->id }}">
                                                {{ $pkg->varient_name }} - ₹{{ $pkgAmount }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="button" class="btn btn-success" id="upgradePackageBtn">{{ translate('Upgrade Package') }}</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Upgrade Package Confirm -->
                </div>
            </div>
        </div>
    </div>

    <span id="route-admin-rashi-status-update" data-url="{{ route('admin.temple.status-update') }}"></span>

    <?php $newtotalOrders = \App\Models\TempleOrderMaster::with(['temple', 'user', 'details.package'])
        ->where('status', 1)
        ->where('trust_id', $relationEmployees)->whereHas('details', function ($q1) {
            $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? '') == 'Sub Pandit')), function ($q) {
                $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                $q->where('type', 'puja');
            })->when((auth('purohit')->check()), function ($q) {
                $q->where('purohit_id', auth('purohit')->user()->id);
                $q->where('type', 'puja');
            });
        })->count() ?>
    <div id="newcountGet">
        <input type="hidden" class="order-count-show" value="{{ \App\Models\TempleOrderMaster::with(['temple', 'user', 'details.package'])
    ->where('status', 1)
    ->where('trust_id', $relationEmployees)->whereHas('details', function ($q1) {
        $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? '') == 'Sub Pandit')), function ($q) {
            $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
            $q->where('type', 'puja');
        })->when((auth('purohit')->check()), function ($q) {
            $q->where('purohit_id', auth('purohit')->user()->id);
            $q->where('type', 'puja');
        });
    })->count() }}">
    </div>
    <input type="hidden" class="order-count-show-old" value="{{ $newtotalOrders }}">
    @endsection

    @push('script')
    <script>
        $(document).on('click', '[data-target="#cashConfirmModal"]', function() {
            let orderId = $(this).data('id');
            $('#confirmOrderId').val(orderId);
        });

        $('#confirmCashBtn').click(function() {
            let orderId = $('#confirmOrderId').val();
            $.ajax({
                url: "{{ route('trustees-vendor.recepit-management.cash.confirm') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order_id: orderId
                },
                success: function(res) {
                    if (res.success) {
                        console.log(res);
                        toastr.success('Cash payment confirmed successfully!');
                        $('#cashConfirmModal').modal('hide');
                        location.reload();
                    } else {
                        toastr.error('Failed to confirm payment.');
                    }
                }
            });
        });
    </script>
    <script>
        $(document).on('click', '[data-target="#puhrohitModal"]', function() {
            let orderId = $(this).data('id');
            $('#confirmPurohitOrderId').val(orderId);
        });

        $('#confirmPurohitBtn').click(function() {
            let orderId = $('#confirmPurohitOrderId').val();
            let purohitId = $('#purohitSelect').val();

            if (!purohitId) {
                toastr.warning('Please select a Purohit.');
                return;
            }
            if (!confirm('Are you sure you want to change the Purohit?')) {
                // User clicked Cancel, do nothing
                return;
            }

            $.ajax({
                url: "{{ route('trustees-vendor.recepit-management.purohit.confirm') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order_id: orderId,
                    purohit_id: purohitId
                },
                success: function(res) {
                    if (res.success) {
                        toastr.success('Purohit assigned and payment confirmed successfully!');
                        $('#puhrohitModal').modal('hide');
                        location.reload();
                    } else {
                        toastr.error('Failed to confirm payment.');
                    }
                }
            });
        });
    </script>
    <script>
        $(document).on('click', '[data-target="#upgradePackage"]', function() {
            let orderId = $(this).data('id');
            $('#upgradePackageOrderId').val(orderId);
        });

        $('#upgradePackageBtn').click(function() {
            let orderId = $('#upgradePackageOrderId').val();
            var packageId = $('#upgradeSelectedPackage').val();

            if (!packageId) {
                toastr.warning('Please select a package.');
                return;
            }
            if (!confirm('Are you sure you want to upgrade this package?')) return;

            $.ajax({
                url: "{{ route('trustees-vendor.recepit-management.package.confirm') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order_id: orderId,
                    package_id: packageId
                },
                success: function(res) {
                    if (res.success) {
                        toastr.success('Package upgraded!');
                        $('#upgradePackage').modal('hide');
                        location.reload();
                    } else {
                        toastr.error(res.message || 'Upgrade failed');
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            initDataTable({
                tableId: '#purohitpayment',
                ajaxUrl: "{{ route('trustees-vendor.order-management.order-list-filter') }}",
                exportTitle: "Trust Puja Orders",
                pageLength: 25,
                notshowfooter: 1,
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'order_id',
                        name: 'order_id'
                    },
                    {
                        data: 'temple_name',
                        name: 'temple_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'service_name',
                        name: 'service_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'yajman_name',
                        name: 'yajman_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'platform',
                        name: 'platform',
                    },
                    {
                        data: 'payment_mode',
                        name: 'payment_mode',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                extraOptions: {
                    serverSide: true,
                    createdRow: function(row, data, dataIndex) {
                        $(row).addClass('row-order-id-' + data.order_ids);
                        $(row).addClass('get-order-recodes');
                        $(row).attr('row-order-id', data.order_ids);
                        $(row).attr('row-order-status', data.order_status);
                        // if (data.order_status === 'pending') {
                        //     updateOrderArray(data.order_ids);
                        // }
                    },
                    initComplete: function() {
                        console.log('DataTable initialized successfully');
                    },
                    ajax: {
                        data: function(d) {
                            d.searchValue = $('#datatableSearch_').val();
                            d.start_date = $('.start_date').val();
                            d.end_date = $('.end_date').val();
                            d.payment_mode = $('.payment_mode').val();
                            d.payment_status = $('.booking_status').val();
                            d.temple_name = $('.temple_name').val();
                            d.puja_name = $('.puja_name').val();
                            d.purohit_id = $('.purohit_id').val();
                        }
                    }
                }
            });
        });

        $('.payment_mode, .start_date, .end_date, .payment_status, .temple_name,.booking_status,.purohit_id').on('change', function() {
            $('#purohitpayment').DataTable().draw();
        });
        $('#purohitpayment').on('draw.dt', function() {
            updateOrderArray();
        });
    </script>
    <script>
        $(document).on('click', '.show-order-details-now', function() {
            let orders = $(this).data('orderid');
            $.ajax({
                url: "{{ route('trustees-vendor.recepit-management.order-details-modal-data') }}",
                type: "POST",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    order_id: $(this).data('orderid'),
                },
                success: function(res) {
                    $('#leadDetailsModal').modal('show');
                    $("#leadDetailsModalLabel").text(`Lead Details - Order #${orders}`);
                    $('.add-new-order-details').html(res.html);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    toastr.error('An error occurred while processing your request.');
                }
            });
        });
    </script>
    <script>
        const OrderArrays = [];

        function updateOrderArray() {
            OrderArrays.length = 0;
            $('#purohitpayment tbody tr').each(function() {
                if ($(this).attr('row-order-status') === 'pending') {
                    OrderArrays.push($(this).attr('row-order-id'));
                }
            });
        }

        function checkOrderStatus() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: "{{ route('trustees-vendor.order-management.multi-order-status-check') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        order_id: JSON.stringify(OrderArrays),
                    },
                    success: function(res) {
                        resolve(res);
                    },
                    error: function(xhr) {
                        reject(xhr);
                    }
                });
            });
        }

        setInterval(async () => {
            try {
                const res = await checkOrderStatus();
                if (res.status == 1) {
                    const index = OrderArrays.indexOf(res.data);
                    if (index !== -1) {
                        OrderArrays.splice(index, 1);
                    }
                    const $row = $('#purohitpayment tbody').find('tr[row-order-id="' + res.data + '"]');
                    if ($row.length) {
                        $row.attr('row-order-status', 'confirmed');
                        $row.find('.order-status-text').removeClass('badge-danger').addClass('badge-success').text('Confirmed');
                    }
                }

            } catch (error) {
                console.error('Order status check failed:', error);
            }
        }, 4000);

        setInterval(() => {
            $.get(location.href, function(response) {
                const html = $('<div>').html(response);
                $('#newcountGet').html(html.find('#newcountGet').html());
                $('#order_stats').html(html.find('#order_stats').html());
                tablerset();
            });
        }, 7000);

        function tablerset() {
            let newnum = parseInt($('.order-count-show').val()) || 0;
            let oldnum = parseInt($('.order-count-show-old').val()) || 0;
            if (newnum > oldnum) {
                $('#purohitpayment').DataTable().draw();
                $('.order-count-show-old').val(newnum);
            }
        }
    </script>
    @endpush