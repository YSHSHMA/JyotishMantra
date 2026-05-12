@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('detail'))

@section('content')
    @push('css_or_js')
        <style>
            body {
                margin: 0;
                padding: 0;
            }

            .box {
                width: 200px;
                height: 200px;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                overflow: hidden;
            }

            .box .b {
                border-radius: 50%;
                border-left: 4px solid;
                border-right: 4px solid;
                border-top: 4px solid transparent !important;
                border-bottom: 4px solid transparent !important;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                animation: ro 2s infinite;
            }

            .box .b1 {
                border-color: #4A69BD;
                width: 120px;
                height: 120px;
            }

            .box .b2 {
                border-color: #F6B93B;
                width: 100px;
                height: 100px;
                animation-delay: 0.2s;
            }

            .box .b3 {
                border-color: #2ECC71;
                width: 80px;
                height: 80px;
                animation-delay: 0.4s;
            }

            .box .b4 {
                border-color: #34495E;
                width: 60px;
                height: 60px;
                animation-delay: 0.6s;
            }

            @keyframes ro {
                0% {
                    transform: translate(-50%, -50%) rotate(0deg);
                }

                50% {
                    transform: translate(-50%, -50%) rotate(-180deg);
                }

                100% {
                    transform: translate(-50%, -50%) rotate(0deg);
                }
            }
        </style>
    @endpush
    {{-- pooja member modal --}}
    <div class="modal fade" id="pooja-member-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pooja Members</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody id="memberTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- pooja Order modal --}}
    <div class="modal fade" id="pooja-order-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pooja Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">

                        <thead>
                            <th>Order Id</th>
                            <th>Customer Name</th>
                            <th>Prashad</th>
                            <th>Order Date</th>
                        </thead>

                        <tbody id="orderTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- chadhava member modal --}}
    <div class="modal fade" id="chadhava-member-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chadhava Members</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody id="chadhavaMemberTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- chadhava Order modal --}}
    <div class="modal fade" id="chadhava-order-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chadhava Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">

                        <thead>
                            <th>Order Id</th>
                            <th>Customer Name</th>
                            <th>Prashad</th>
                            <th>Order Date</th>
                        </thead>

                        <tbody id="chadhavaOrderTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- anushthan member modal --}}
    <div class="modal fade" id="anushthan-member-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Anushthan Members</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody id="anushthanMemberTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- anushthan Order modal --}}
    <div class="modal fade" id="anushthan-order-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pooja Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">

                        <thead>
                            <th>Order Id</th>
                            <th>Customer Name</th>
                            <th>Prashad</th>
                            <th>Order Date</th>
                        </thead>

                        <tbody id="anushthanOrderTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- vip member modal --}}
    <div class="modal fade" id="vip-member-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vip Members</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody id="vipMemberTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- vip Order modal --}}
    <div class="modal fade" id="vip-order-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vip Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">

                        <thead>
                            <th>Order Id</th>
                            <th>Customer Name</th>
                            <th>Prashad</th>
                            <th>Order Date</th>
                        </thead>

                        <tbody id="vipOrderTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- consultation user modal --}}
    <div class="modal fade" id="consultation-user-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Consultation User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody id="consultationTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- offline pooja Order modal --}}
    <div class="modal fade" id="offlinepooja-order-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Offline Pooja Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 border align-content-center py-2"><strong>Booking Date</strong></div>
                        <div class="col-md-9 border align-content-center py-2">
                            <p class="m-0" id="booking-date"></p>
                        </div>
                        <div class="col-md-3 border align-content-center py-2"><strong>Venue Address</strong></div>
                        <div class="col-md-9 border align-content-center py-2">
                            <p class="m-0" id="venue-address"></p>
                        </div>
                        <div class="col-md-3 border align-content-center py-2"><strong>Landmark</strong></div>
                        <div class="col-md-9 border align-content-center py-2">
                            <p class="m-0" id="landmark"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="kundali-milan-order-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kundali Milan Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td><span class="malename"></span></td>
                                    <td><span class="femalename"></span></td>
                                </tr>
                                <tr>
                                    <td>DoB</td>
                                    <td><span class="maledob"></span></td>
                                    <td><span class="femaledob"></span></td>
                                </tr>
                                <tr>
                                    <td>Time</td>
                                    <td><span class="maletime"></span></td>
                                    <td><span class="femaletime"></span></td>
                                </tr>
                                <tr>
                                    <td>Country</td>
                                    <td><span class="malecountry"></span></td>
                                    <td><span class="femalecountry"></span></td>
                                </tr>
                                <tr>
                                    <td>Location</td>
                                    <td><span class="malelocation"></span></td>
                                    <td><span class="femalelocation"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}"
                    alt="">
                {{ translate('order_detail') }}
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="js-nav-scroller hs-nav-scroller-horizontal mb-5">
                    <ul class="nav nav-tabs flex-wrap page-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link "
                                href="{{ route('admin.astrologers.manage.detail.overview', $id) }}">Overview</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order'))
                            <li class="nav-item">
                                <a class="nav-link active"
                                    href="{{ route('admin.astrologers.manage.detail.order', $id) }}">Order</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'service'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.service', $id) }}">Service</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.setting', $id) }}">Setting</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'transaction'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.transaction', $id) }}">Transaction</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.transaction.history', $id) }}">Transaction History</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'review'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.review', $id) }}">Review</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.history', $id) }}">History</a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content mt-5">
                    <div class="tab-pane fade show active" id="order">
                        <div class="row pt-2">
                            <div class="col-md-12">
                                <div class="card w-100">
                                    <div class="card-header">
                                        <h5 class="mb-0">Order info</h5>
                                    </div>
                                    {{-- <div class="card-body"> --}}
                                    {{-- <div class="row"> --}}
                                    {{-- <div class="col-md-6">
                                                <div class="order-stats order-stats_all">
                                                    <div class="order-stats__content" style="text-align: left;">
                                                        <i class="tio-table"></i>
                                                        <h6 class="order-stats__subtitle">All</h6>
                                                    </div>
                                                    <div class="order-stats__title">
                                                        @php
                                                            $poojaCount = !empty($poojaOrders)
                                                                ? count($poojaOrders)
                                                                : 0;
                                                            $chadhavaCount = !empty($ChadhavaOrder)
                                                                ? count($ChadhavaOrder)
                                                                : 0;
                                                            $consultationCount = !empty($consultationOrders)
                                                                ? count($consultationOrders)
                                                                : 0;
                                                            $offlinepoojaCount = !empty($offlinepoojaOrders)
                                                                ? count($offlinepoojaOrders)
                                                                : 0;
                                                        @endphp
                                                        {{ $poojaCount + $chadhavaCount + $consultationCount + $offlinepoojaCount}}
                                                    </div>
                                                </div>
                                            </div> --}}
                                    {{-- <div class="col-md-6 mb-3 mb-md-0">
                                                <div class="order-stats order-stats_pending">
                                                    <div class="order-stats__content" style="text-align: left;">
                                                        <i class="tio-airdrop"></i>
                                                        <h6 class="order-stats__subtitle">Pending</h6>
                                                    </div>
                                                    <div class="order-stats__title">
                                                        {{ $totalPending }}
                                                    </div>
                                                </div>
                                            </div> --}}
                                    {{-- <div class="col-md-4 mb-3 mb-md-0">
                                                <div class="order-stats order-stats_delivered">
                                                    <div class="order-stats__content" style="text-align: left;">
                                                        <i class="tio-checkmark-circle"></i>
                                                        <h6 class="order-stats__subtitle">Completed</h6>
                                                    </div>
                                                    <div class="order-stats__title">
                                                        {{ $totalcomplete }}
                                                    </div>
                                                </div>
                                            </div> --}}

                                    {{-- </div> --}}
                                    {{-- </div> --}}

                                    <div class="row">
                                        <div class="col-12">
                                            <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                                                @if (!empty($poojaOrders))
                                                    <li class="nav-item col-4" role="presentation">
                                                        <button class="nav-link w-100 active" id="pooja-tab"
                                                            data-toggle="pill" data-target="#pooja" type="button"
                                                            role="tab" aria-controls="pooja"
                                                            aria-selected="true">Pooja
                                                            Orders</button>
                                                    </li>
                                                @endif
                                                @if ($ChadhavaOrder->count() > 0)
                                                    <li class="nav-item col-4" role="presentation">
                                                        <button class="nav-link w-100" id="chadhava-tab"
                                                            data-toggle="pill" data-target="#chadhava" type="button"
                                                            role="tab" aria-controls="chadhava"
                                                            aria-selected="true">Chadhava
                                                            Orders</button>
                                                    </li>
                                                @endif
                                                @if ($vipOrders->count() > 0)
                                                    <li class="nav-item col-4" role="presentation">
                                                        <button class="nav-link w-100" id="vip-tab" data-toggle="pill"
                                                            data-target="#vip" type="button" role="tab"
                                                            aria-controls="vip" aria-selected="true">VIP
                                                            Orders</button>
                                                    </li>
                                                @endif
                                                @if ($anushthanOrders->count() > 0)
                                                    <li class="nav-item col-4" role="presentation">
                                                        <button class="nav-link w-100" id="anushthan-tab"
                                                            data-toggle="pill" data-target="#anushthan" type="button"
                                                            role="tab" aria-controls="anushthan"
                                                            aria-selected="true">Anushthan
                                                            Orders</button>
                                                    </li>
                                                @endif

                                                @if (!empty($consultationOrders))
                                                    <li class="nav-item col-4" role="presentation">
                                                        <button
                                                            class="nav-link w-100 {{ empty($poojaOrders) ? 'active' : '' }}"
                                                            id="consultation-tab" data-toggle="pill"
                                                            data-target="#consultation" type="button" role="tab"
                                                            aria-controls="consultation"
                                                            aria-selected="false">Consultation
                                                            Orders</button>
                                                    </li>
                                                @endif

                                                @if (!empty($offlinepoojaOrders))
                                                    <li class="nav-item col-4" role="presentation">
                                                        <button
                                                            class="nav-link w-100 {{ empty($offlinepoojaOrders) ? 'active' : '' }}"
                                                            id="offlinepooja-tab" data-toggle="pill"
                                                            data-target="#offlinepooja" type="button" role="tab"
                                                            aria-controls="offlinepooja" aria-selected="false">Offline
                                                            Pooja
                                                            Orders</button>
                                                    </li>
                                                @endif
                                                @if (!empty($KundaliOrders))
                                                    <li class="nav-item col-4" role="presentation">
                                                        <button
                                                            class="nav-link w-100 {{ empty($KundaliOrders) ? 'active' : '' }}"
                                                            id="kundalimilan-tab" data-toggle="pill"
                                                            data-target="#kundaliMilan" type="button" role="tab"
                                                            aria-controls="kundaliMilan" aria-selected="false">Kundali
                                                            Milan Orders</button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <div class="col-12">
                                            <div class="tab-content" id="pills-tabContent">
                                                @if (!empty($poojaOrders))
                                                    <div class="tab-pane fade show active" id="pooja" role="tabpanel"
                                                        aria-labelledby="pooja-tab">
                                                        @include(
                                                            'admin-views.astrologers.manage.detail.partial.poojaTab',
                                                            ['poojaOrders' => $poojaOrders]
                                                        )
                                                    </div>
                                                @endif

                                                {{-- Chadhava Order  --}}
                                                @if (!empty($ChadhavaOrder))
                                                    <div class="tab-pane fade show" id="chadhava" role="tabpanel"
                                                        aria-labelledby="chadhava-tab">
                                                        @include(
                                                            'admin-views.astrologers.manage.detail.partial.chadhavaTab',
                                                            ['ChadhavaOrder' => $ChadhavaOrder]
                                                        )

                                                    </div>
                                                @endif
                                                {{-- Chadhava Order  --}}
                                                {{-- VIP Order  --}}
                                                @if (!empty($vipOrders))
                                                    <div class="tab-pane fade show" id="vip" role="tabpanel"
                                                        aria-labelledby="vip-tab">
                                                        @include(
                                                            'admin-views.astrologers.manage.detail.partial.vipTab',
                                                            ['vipOrders' => $vipOrders]
                                                        )

                                                    </div>
                                                @endif
                                                {{-- vip Order  --}}
                                                {{-- Anushthan Order  --}}
                                                @if (!empty($anushthanOrders))
                                                    <div class="tab-pane fade show" id="anushthan" role="tabpanel"
                                                        aria-labelledby="anushthan-tab">
                                                        @include(
                                                            'admin-views.astrologers.manage.detail.partial.anushthanTab',
                                                            ['anushthanOrders' => $anushthanOrders]
                                                        )

                                                    </div>
                                                @endif
                                                {{-- vip Order  --}}
                                                @if (!empty($consultationOrders))
                                                    @include(
                                                        'admin-views.astrologers.manage.detail.partial.counsellingTab',
                                                        ['consultationOrders' => $consultationOrders]
                                                    )
                                                @endif
                                                {{-- offline pooja Order  --}}
                                                @if (!empty($offlinepoojaOrders))
                                                    @include(
                                                        'admin-views.astrologers.manage.detail.partial.offlinepoojaTab',
                                                        ['offlinepoojaOrders' => $offlinepoojaOrders]
                                                    )
                                                @endif
                                                @if (!empty($KundaliOrders))
                                                    @include(
                                                        'admin-views.astrologers.manage.detail.partial.kundaliMilanTab',
                                                        ['KundaliOrders' => $KundaliOrders]
                                                    )
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box" style="display: none;">
                <div class="b b1"></div>
                <div class="b b2"></div>
                <div class="b b3"></div>
                <div class="b b4"></div>
            </div>
        </div>
    @endsection

    @push('script')
        <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

        {{-- pooja member modal --}}
        <script>
            function poojaMemberModal(that) {
                $('#memberTB').html('');
                var list = "";
                const membersData = $(that).data('members');
                const gotra = $(that).data('gotra');
                const memberParts = membersData.split('|');
                const gotraParts = gotra.split('|');
                list += `<tr><th><b>Order</b></th><th><b>Member Name</b></th><th><b>Gotra</b></th></tr>`;
                $.each(memberParts, function(key, member) {
                    const gotraValue = gotraParts[key] || '';
                    member = member.replace(/[\[\]"]/g, '');
                    list += `<tr><td><b>Order- ${key + 1}<b></td><td>${member}</td><td>${gotraValue}</td></tr>`;
                });

                $('#memberTB').append(list);
                $('#pooja-member-modal').modal('show');
            }

            function poojaOrderModal(that) {
                $('.box').css('display', 'block');
                var serviceId = $(that).data('serviceid');
                var bookingDate = $(that).data('bookingdate');

                $.ajax({
                    url: "{{ url('admin/astrologers/manage/order-data') }}",
                    type: 'GET',
                    data: {
                        serviceId: serviceId,
                        bookingDate: bookingDate
                    },
                    success: function(data) {
                        console.log(data);

                        function formatDate(dateString) {
                            const date = new Date(dateString);
                            const options = {
                                year: 'numeric',
                                month: 'short',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            };
                            return date.toLocaleString('en-US', options).replace(',', '');
                        }
                        $('#orderTB').html('');
                        $.each(data.data, function(key, value) {
                            $('#orderTB').append(`
                                <tr>
                                    <td>${value.order_id}</td>
                                    <td>${value.customers.f_name} ${value.customers.l_name}</td>
                                    <td>${value.is_prashad == 1 ? 'Yes' : 'No'}</td>
                                    <td>${formatDate(value.created_at)}</td>
                                </tr>
                            `);
                        });
                        $('.box').css('display', 'none');
                        $('#pooja-order-modal').modal('show');
                    },
                    error: function() {
                        alert('Failed to fetch order details.');
                    }
                });

            }
        </script>

        {{-- chadhava member modal --}}
        <script>
            function chadhavaMemberModal(that) {
                $('#chadhavaMemberTB').html('');
                var list = "";
                const membersData = $(that).data('members');
                const gotra = $(that).data('gotra');
                const memberParts = membersData.split('|');
                const gotraParts = gotra.split('|');
                list += `<tr><th><b>Order</b></th><th><b>Member Name</b></th><th><b>Gotra</b></th></tr>`;
                $.each(memberParts, function(key, member) {
                    const gotraValue = gotraParts[key] || '';
                    member = member.replace(/[\[\]"]/g, '');
                    list += `<tr><td><b>Order- ${key + 1}<b></td><td>${member}</td><td>${gotraValue}</td></tr>`;
                });

                $('#chadhavaMemberTB').append(list);
                $('#chadhava-member-modal').modal('show');
            }

            function chadhavaOrderModal(that) {
                $('.box').css('display', 'block');
                var serviceId = $(that).data('serviceid');
                var bookingDate = $(that).data('bookingdate');

                $.ajax({
                    url: "{{ url('admin/astrologers/manage/order-data') }}",
                    type: 'GET',
                    data: {
                        serviceId: serviceId,
                        bookingDate: bookingDate
                    },
                    success: function(data) {
                        console.log(data);

                        function formatDate(dateString) {
                            const date = new Date(dateString);
                            const options = {
                                year: 'numeric',
                                month: 'short',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            };
                            return date.toLocaleString('en-US', options).replace(',', '');
                        }
                        $('#chadhavaOrderTB').html('');
                        $.each(data.data, function(key, value) {
                            $('#chadhavaOrderTB').append(`
                                <tr>
                                    <td>${value.order_id}</td>
                                    <td>${value.customers.f_name} ${value.customers.l_name}</td>
                                    <td>${value.is_prashad == 1 ? 'Yes' : 'No'}</td>
                                    <td>${formatDate(value.created_at)}</td>
                                </tr>
                            `);
                        });
                        $('.box').css('display', 'none');
                        $('#chadhava-order-modal').modal('show');
                    },
                    error: function() {
                        alert('Failed to fetch order details.');
                    }
                });

            }
        </script>

        {{-- anushthan member modal --}}
        <script>
            function anushthanMemberModal(that) {
                $('#anushthanMemberTB').html('');
                var list = "";
                const membersData = $(that).data('members');
                const gotra = $(that).data('gotra');
                const memberParts = membersData.split('|');
                const gotraParts = gotra.split('|');
                list += `<tr><th><b>Order</b></th><th><b>Member Name</b></th><th><b>Gotra</b></th></tr>`;
                $.each(memberParts, function(key, member) {
                    const gotraValue = gotraParts[key] || '';
                    member = member.replace(/[\[\]"]/g, '');
                    list += `<tr><td><b>Order- ${key + 1}<b></td><td>${member}</td><td>${gotraValue}</td></tr>`;
                });

                $('#anushthanMemberTB').append(list);
                $('#anushthan-member-modal').modal('show');
            }

            function anushthanOrderModal(that) {
                $('.box').css('display', 'block');
                var serviceId = $(that).data('serviceid');
                var bookingDate = $(that).data('bookingdate');

                $.ajax({
                    url: "{{ url('admin/astrologers/manage/order-data') }}",
                    type: 'GET',
                    data: {
                        serviceId: serviceId,
                        bookingDate: bookingDate
                    },
                    success: function(data) {
                        console.log(data);

                        function formatDate(dateString) {
                            const date = new Date(dateString);
                            const options = {
                                year: 'numeric',
                                month: 'short',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            };
                            return date.toLocaleString('en-US', options).replace(',', '');
                        }
                        $('#anushthanOrderTB').html('');
                        $.each(data.data, function(key, value) {
                            $('#anushthanOrderTB').append(`
                                <tr>
                                    <td>${value.order_id}</td>
                                    <td>${value.customers.f_name} ${value.customers.l_name}</td>
                                    <td>${value.is_prashad == 1 ? 'Yes' : 'No'}</td>
                                    <td>${formatDate(value.created_at)}</td>
                                </tr>
                            `);
                        });
                        $('.box').css('display', 'none');
                        $('#anushthan-order-modal').modal('show');
                    },
                    error: function() {
                        alert('Failed to fetch order details.');
                    }
                });

            }
        </script>

        {{-- vip member modal --}}
        <script>
            function vipMemberModal(that) {
                $('#vipMemberTB').html('');
                var list = "";
                const membersData = $(that).data('members');
                const gotra = $(that).data('gotra');
                const memberParts = membersData.split('|');
                const gotraParts = gotra.split('|');
                list += `<tr><th><b>Order</b></th><th><b>Member Name</b></th><th><b>Gotra</b></th></tr>`;
                $.each(memberParts, function(key, member) {
                    const gotraValue = gotraParts[key] || '';
                    member = member.replace(/[\[\]"]/g, '');
                    list += `<tr><td><b>Order- ${key + 1}<b></td><td>${member}</td><td>${gotraValue}</td></tr>`;
                });

                $('#vipMemberTB').append(list);
                $('#vip-member-modal').modal('show');
            }

            function vipOrderModal(that) {
                $('.box').css('display', 'block');
                var serviceId = $(that).data('serviceid');
                var bookingDate = $(that).data('bookingdate');

                $.ajax({
                    url: "{{ url('admin/astrologers/manage/order-data') }}",
                    type: 'GET',
                    data: {
                        serviceId: serviceId,
                        bookingDate: bookingDate
                    },
                    success: function(data) {
                        console.log(data);

                        function formatDate(dateString) {
                            const date = new Date(dateString);
                            const options = {
                                year: 'numeric',
                                month: 'short',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            };
                            return date.toLocaleString('en-US', options).replace(',', '');
                        }
                        $('#vipOrderTB').html('');
                        $.each(data.data, function(key, value) {
                            $('#vipOrderTB').append(`
                                <tr>
                                    <td>${value.order_id}</td>
                                    <td>${value.customers.f_name} ${value.customers.l_name}</td>
                                    <td>${value.is_prashad == 1 ? 'Yes' : 'No'}</td>
                                    <td>${formatDate(value.created_at)}</td>
                                </tr>
                            `);
                        });
                        $('.box').css('display', 'none');
                        $('#vip-order-modal').modal('show');
                    },
                    error: function() {
                        alert('Failed to fetch order details.');
                    }
                });

            }
        </script>

        {{-- pooja member modal --}}
        <script>
            function consultationUser(that) {
                $('#consultationTB').html('');
                var consultationList = "";
                var name = $(that).data('name');
                var gender = $(that).data('gender');
                var mob = $(that).data('mob');
                var dob = $(that).data('dob');
                var time = $(that).data('time');
                var country = $(that).data('country');
                var city = $(that).data('city');

                consultationList +=
                    `<tr><td>Name</td><td>${name}</td></tr><tr><td>Gender</td><td>${gender}</td></tr><tr><td>DOB</td><td>${dob}</td></tr><tr><td>Birth Time</td><td>${time}</td></tr><tr><td>Country</td><td>${country}</td></tr><tr><td>City</td><td>${city}</td></tr>`;

                $('#consultationTB').append(consultationList);
                $('#consultation-user-modal').modal('show');
            }
        </script>

        {{-- offline pooja order modal --}}
        <script>
            function offlinepoojaOrderModal(that) {
                var bookingDate = $(that).data('bookingdate');
                var venueAddress = $(that).data('venueaddress');
                var landmark = $(that).data('landmark');
                $('#booking-date').text(bookingDate);
                $('#venue-address').text(venueAddress);
                $('#landmark').text(landmark);
                $('#offlinepooja-order-modal').modal('show');
            }
        </script>
        {{-- Kundali --}}

        <script>
            function kundalimilanOrderModal(that) {
                var malename = $(that).data('malename');
                var femalename = $(that).data('femalename');
                var maledob = $(that).data('maledob');
                var femaledob = $(that).data('femaledob');
                var maletime = $(that).data('maletime');
                var femaletime = $(that).data('femaletime');
                var malecountry = $(that).data('malecountry');
                var femalecountry = $(that).data('femalecountry');
                var malelocation = $(that).data('malelocation');
                var femalelocation = $(that).data('femalelocation');
                $('.malename').text(malename);
                $('.femalename').text(femalename);
                $('.maledob').text(maledob);
                $('.femaledob').text(femaledob);
                $('.maletime').text(maletime);
                $('.femaletime').text(femaletime);
                $('.malecountry').text(malecountry);
                $('.femalecountry').text(femalecountry);
                $('.malelocation').text(malelocation);
                $('.femalelocation').text(femalelocation);
                $('#kundali-milan-order-modal').modal('show');
            }
        </script>
    @endpush