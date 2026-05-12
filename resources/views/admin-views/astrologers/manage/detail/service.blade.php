@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('service_detail'))

@section('content')

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('service_detail') }}
                {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $festivals->total() }}</span> --}}
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="js-nav-scroller hs-nav-scroller-horizontal mb-5">
                    <ul class="nav nav-tabs flex-wrap page-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.overview', $id) }}">Overview</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.astrologers.manage.detail.order', $id) }}">Order</a>
                        </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'service'))
                        <li class="nav-item">
                            <a class="nav-link active"
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
                            <a class="nav-link" href="{{ route('admin.astrologers.manage.detail.transaction', $id) }}">Transaction</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.transaction.history', $id) }}">Transaction History</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'review'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.astrologers.manage.detail.review', $id) }}">Review</a>
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
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Service Charges</h5>

                                        <a href="{{ route('admin.astrologers.manage.update.services', $service['id']) }}" 
                                        class="btn btn-sm btn-primary">
                                            <i class="tio-edit"></i> Edit Charges
                                        </a>
                                </div>
                                    <div class="m-3">

                                        <div class="row">
                                            @if (!empty($service['is_astrologer_live_stream_charge']))
                                                <div class="col-3">
                                                    <div class="row bg-primary my-2"
                                                        style="border-radius: 15px; margin:0px; padding:12px 0;">
                                                        <div class="col-4 mt-2">
                                                            <i class="tio-invisible"
                                                                style="font-size: 35px; color: white;"></i>
                                                        </div>
                                                        <div class="col-8 text-right">
                                                            <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                                                                <b>Live Stream</b>
                                                            </p>
                                                            <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                                                                <b>₹ {{ $service['is_astrologer_live_stream_charge'] .' / minute' }}</b>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (!empty($service['is_astrologer_call_charge']))
                                                <div class="col-3">
                                                    <div class="row bg-danger my-2"
                                                        style="border-radius: 15px; margin:0px; padding:12px 0;">
                                                        <div class="col-4 mt-2">
                                                            <i class="tio-call" style="font-size: 30px; color: white;"></i>
                                                        </div>
                                                        <div class="col-8 text-right">
                                                            <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                                                                <b>Calling</b>
                                                            </p>
                                                            <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                                                                <b>₹ {{ $service['is_astrologer_call_charge'] .' / minute' }}</b>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (!empty($service['is_astrologer_chat_charge']))
                                                <div class="col-3">
                                                    <div class="row bg-success my-2"
                                                        style="border-radius: 15px; margin:0px; padding:12px 0;">
                                                        <div class="col-4 mt-2">
                                                            <i class="tio-chat" style="font-size: 30px; color: white;"></i>
                                                        </div>
                                                        <div class="col-8 text-right">
                                                            <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                                                                <b>Chatting</b>
                                                            </p>
                                                            <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                                                                <b>₹ {{ $service['is_astrologer_chat_charge'] .' / minute' }}</b>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (!empty($service['is_astrologer_report_charge']))
                                                <div class="col-3">
                                                    <div class="row bg-dark my-2"
                                                        style="border-radius: 15px; margin:0px; padding:12px 0;">
                                                        <div class="col-4 mt-2">
                                                            <i class="tio-report"
                                                                style="font-size: 30px; color: white;"></i>
                                                        </div>
                                                        <div class="col-8 text-right">
                                                            <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                                                                <b>Report</b>
                                                            </p>
                                                            <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                                                                <b>₹ {{ $service['is_astrologer_report_charge'] .' / minute' }}</b>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (!empty($service['is_pandit_live_stream_charge']))
                                                <div class="col-3">
                                                    <div class="row bg-primary my-2"
                                                        style="border-radius: 15px; margin:0px; padding:12px 0;">
                                                        <div class="col-4 mt-2">
                                                            <i class="tio-invisible"
                                                                style="font-size: 35px; color: white;"></i>
                                                        </div>
                                                        <div class="col-8 text-right">
                                                            <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                                                                <b>Live Stream</b>
                                                            </p>
                                                            <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                                                                <b>₹
                                                                    {{ $service['is_pandit_live_stream_charge'] .' / minute' }}</b>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @php
                                        $poojaService = json_decode($service['is_pandit_pooja'], true);
                                        $poojaServiceTime = json_decode($service['is_pandit_pooja_time'], true);
                                        $vipPoojaService = json_decode($service['is_pandit_vippooja'], true);
                                        $vipPoojaServiceTime = json_decode($service['is_pandit_vippooja_time'], true);
                                        $anushthanService = json_decode($service['is_pandit_anushthan'], true);
                                        $anushthanServiceTime = json_decode($service['is_pandit_anushthan_time'], true);
                                        $chadhavaService = json_decode($service['is_pandit_chadhava'], true);
                                        $chadhavaServiceTime = json_decode($service['is_pandit_chadhava_time'], true);
                                        $consultationService = json_decode($service['consultation_charge'], true);
                                        $offlinepoojaService = json_decode($service['is_pandit_offlinepooja'], true);
                                        $offlinepoojaServiceTime = json_decode($service['is_pandit_offlinepooja_time'], true);
                                    @endphp
                                    <div class="row">
                                        <div class="col-12">
                                            <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                                                @if (!empty($poojaService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button class="nav-link w-100 active" id="pooja-tab"
                                                            data-toggle="pill" data-target="#pooja" type="button"
                                                            role="tab" aria-controls="pooja" aria-selected="true">Pooja
                                                            </button>
                                                    </li>
                                                @endif

                                                @if (!empty($vipPoojaService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button class="nav-link w-100 {{ empty($vipPoojaService) ? 'active' : '' }}" id="vippooja-tab"
                                                            data-toggle="pill" data-target="#vippooja" type="button"
                                                            role="tab" aria-controls="vippooja" aria-selected="true">Vip Pooja
                                                            </button>
                                                    </li>
                                                @endif

                                                @if (!empty($anushthanService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button class="nav-link w-100 {{ empty($anushthanService) ? 'active' : '' }}" id="anushthan-tab"
                                                            data-toggle="pill" data-target="#anushthan" type="button"
                                                            role="tab" aria-controls="anushthan" aria-selected="true">Anushthan
                                                            </button>
                                                    </li>
                                                @endif

                                                @if (!empty($chadhavaService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button class="nav-link w-100 {{ empty($chadhavaService) ? 'active' : '' }}" id="chadhava-tab"
                                                            data-toggle="pill" data-target="#chadhava" type="button"
                                                            role="tab" aria-controls="chadhava" aria-selected="true">Chadhava
                                                            </button>
                                                    </li>
                                                @endif

                                                @if (!empty($consultationService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button
                                                            class="nav-link w-100 {{ $service['primary_skills']==4?'active':'' }}"
                                                            id="consultation-tab" data-toggle="pill"
                                                            data-target="#consultation" type="button" role="tab"
                                                            aria-controls="consultation"
                                                            aria-selected="false">Consultation</button>
                                                    </li>
                                                @endif

                                                @if (!empty($offlinepoojaService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button
                                                            class="nav-link w-100 {{ empty($offlinepoojaService)?'active':'' }}"
                                                            id="offlinepooja-tab" data-toggle="pill"
                                                            data-target="#offlinepooja" type="button" role="tab"
                                                            aria-controls="offlinepooja"
                                                            aria-selected="true">Offline Pooja</button>
                                                    </li>
                                                @endif
                                                @if (!empty($service['is_kundali_make']) && $service['is_kundali_make'] == 1)
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button class="nav-link w-100" id="kundalimilan-tab" data-toggle="pill" data-target="#kundalimilan" type="button" role="tab" aria-controls="kundalimilan" aria-selected="true">kundali Milan</button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <div class="col-12">
                                            <div class="tab-content" id="pills-tabContent">
                                                @if (!empty($poojaService))
                                                    <div class="tab-pane fade show active" id="pooja" role="tabpanel"
                                                        aria-labelledby="pooja-tab">

                                                        <div class="table-responsive datatable-custom">
                                                            <table id="datatable" style="text-align: left;"
                                                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>
                                                                        <th style="width: 40%;">{{ translate('name') }}
                                                                        </th>
                                                                        <th>{{ translate('category') }}</th>
                                                                        <th>{{ translate('time') }}</th>
                                                                        <th>{{ translate('price') }}</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="set-rows">
                                                                    @php
                                                                        $poojaInc = 1;
                                                                    @endphp
                                                                    @foreach ($poojaService as $poojaKey => $poojaValue)
                                                                        @php
                                                                            $poojaData = App\Models\Service::where(
                                                                                'id',
                                                                                $poojaKey,
                                                                            )
                                                                                ->with('category')
                                                                                ->first();
                                                                        @endphp

                                                                        <tr>
                                                                            <td>{{ $poojaInc++ }}</td>
                                                                            <td>{{ $poojaData['name'] }}</td>
                                                                            <td>{{ $poojaData['category']['name'] }}</td>
                                                                            <td>{{ $poojaServiceTime[$poojaKey] }}</td>
                                                                            <td>{{ '₹' . $poojaValue }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>

                                                            </table>
                                                        </div>
                                                        @if (empty($poojaService))
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                @if (!empty($vipPoojaService))
                                                    <div class="tab-pane fade show" id="vippooja" role="tabpanel"
                                                        aria-labelledby="vippooja-tab">

                                                        <div class="table-responsive datatable-custom">
                                                            <table id="datatable" style="text-align: left;"
                                                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>
                                                                        <th style="width: 40%;">{{ translate('name') }}
                                                                        </th>
                                                                        <th>{{ translate('time') }}</th>
                                                                        <th>{{ translate('price') }}</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="set-rows">
                                                                    @php
                                                                        $vipPoojaInc = 1;
                                                                    @endphp
                                                                    @foreach ($vipPoojaService as $vipPoojaKey => $vipPoojaValue)
                                                                        @php
                                                                            $vipPoojaData = App\Models\Vippooja::where(
                                                                                'id',
                                                                                $vipPoojaKey,
                                                                            )
                                                                                ->first();
                                                                        @endphp

                                                                        <tr>
                                                                            <td>{{ $vipPoojaInc++ }}</td>
                                                                            <td>{{ $vipPoojaData['name'] }}</td>
                                                                            <td>{{ $vipPoojaServiceTime[$vipPoojaKey] }}</td>
                                                                            <td>{{ '₹' . $vipPoojaValue }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>

                                                            </table>
                                                        </div>
                                                        @if (empty($vipPoojaService))
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                @if (!empty($anushthanService))
                                                    <div class="tab-pane fade show" id="anushthan" role="tabpanel"
                                                        aria-labelledby="anushthan-tab">

                                                        <div class="table-responsive datatable-custom">
                                                            <table id="datatable" style="text-align: left;"
                                                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>
                                                                        <th style="width: 40%;">{{ translate('name') }}
                                                                        </th>
                                                                        <th>{{ translate('time') }}</th>
                                                                        <th>{{ translate('price') }}</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="set-rows">
                                                                    @php
                                                                        $anushthanInc = 1;
                                                                    @endphp
                                                                    @foreach ($anushthanService as $anushthanKey => $anushthanValue)
                                                                        @php
                                                                            $anushthanData = App\Models\Vippooja::where(
                                                                                'id',
                                                                                $anushthanKey,
                                                                            )
                                                                                ->first();
                                                                        @endphp

                                                                        <tr>
                                                                            <td>{{ $anushthanInc++ }}</td>
                                                                            <td>{{ $anushthanData['name'] }}</td>
                                                                            <td>{{ $anushthanServiceTime[$anushthanKey] }}</td>
                                                                            <td>{{ '₹' . $anushthanValue }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>

                                                            </table>
                                                        </div>
                                                        @if (empty($anushthanService))
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                @if (!empty($chadhavaService))
                                                    <div class="tab-pane fade show" id="chadhava" role="tabpanel"
                                                        aria-labelledby="chadhava-tab">

                                                        <div class="table-responsive datatable-custom">
                                                            <table id="datatable" style="text-align: left;"
                                                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>
                                                                        <th style="width: 40%;">{{ translate('name') }}
                                                                        </th>
                                                                        <th>{{ translate('time') }}</th>
                                                                        <th>{{ translate('price') }}</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="set-rows">
                                                                    @php
                                                                        $chadhavaInc = 1;
                                                                    @endphp
                                                                    @foreach ($chadhavaService as $chadhavaKey => $chadhavaValue)
                                                                        @php
                                                                            $chadhavaData = App\Models\Chadhava::where(
                                                                                'id',
                                                                                $chadhavaKey,
                                                                            )
                                                                                ->first();
                                                                        @endphp

                                                                        <tr>
                                                                            <td>{{ $chadhavaInc++ }}</td>
                                                                            <td>{{ $chadhavaData['name'] }}</td>
                                                                            <td>{{ $chadhavaServiceTime[$chadhavaKey] }}</td>
                                                                            <td>{{ '₹' . $chadhavaValue }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>

                                                            </table>
                                                        </div>
                                                        @if (empty($chadhavaService))
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if (!empty($consultationService))
                                                    <div class="tab-pane fade show {{ empty($poojaService) ? 'active' : '' }}"
                                                        id="consultation" role="tabpanel"
                                                        aria-labelledby="consultation-tab">
                                                        <div class="table-responsive datatable-custom">
                                                            <table id="datatable" style="text-align: left;"
                                                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>
                                                                        <th>{{ translate('name') }}</th>
                                                                        <th>{{ translate('category') }}</th>
                                                                        <th>{{ translate('price') }}</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="set-rows">
                                                                    @php
                                                                        $consultationInc = 1;
                                                                    @endphp
                                                                    @foreach ($consultationService as $consultationKey => $consultationValue)
                                                                        @php
                                                                            $consultationData = App\Models\Service::where(
                                                                                'id',
                                                                                $consultationKey,
                                                                            )
                                                                                ->with('category')
                                                                                ->first();
                                                                        @endphp
                                                                        <tr>
                                                                            <td>{{ $consultationInc++ }}</td>
                                                                            <td>{{ $consultationData['name'] }}</td>
                                                                            <td>{{ $consultationData['category']['name'] }}
                                                                            </td>
                                                                            <td>{{ '₹ ' . $consultationValue }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>

                                                            </table>
                                                        </div>
                                                        @if (empty($consultationService))
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if (!empty($offlinepoojaService))
                                                    <div class="tab-pane fade show" id="offlinepooja" role="tabpanel"
                                                        aria-labelledby="offlinepooja-tab">

                                                        <div class="table-responsive datatable-custom">
                                                            <table id="datatable" style="text-align: left;"
                                                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>
                                                                        <th style="width: 40%;">{{ translate('name') }}
                                                                        </th>
                                                                        <th>{{ translate('time') }}</th>
                                                                        <th>{{ translate('price') }}</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="set-rows">
                                                                    @php
                                                                        $offlinepoojaInc = 1;
                                                                    @endphp
                                                                    @foreach ($offlinepoojaService as $offlinepoojaKey => $offlinepoojaValue)
                                                                        @php
                                                                            $offlinepoojaData = App\Models\PoojaOffline::where(
                                                                                'id',
                                                                                $offlinepoojaKey,
                                                                            )
                                                                                ->first();
                                                                        @endphp

                                                                        <tr>
                                                                            <td>{{ $offlinepoojaInc++ }}</td>
                                                                            <td>{{ $offlinepoojaData['name'] }}</td>
                                                                            <td>{{ $offlinepoojaServiceTime[$offlinepoojaKey] }}</td>
                                                                            <td>{{ '₹' . $offlinepoojaValue }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>

                                                            </table>
                                                        </div>
                                                        @if (empty($offlinepoojaService))
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if (!empty($service['is_kundali_make']) && $service['is_kundali_make'] == 1)
                                                    <div class="tab-pane fade show" id="kundalimilan" role="tabpanel" aria-labelledby="kundalimilan-tab">
                                                        <div class="table-responsive datatable-custom">
                                                            <table id="datatable" style="text-align: left;"
                                                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>
                                                                        <th>{{ translate('type') }}</th>
                                                                        <th>{{ translate('commission') }}</th>
                                                                        <th>{{ translate('price') }}</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="set-rows">
                                                                        <tr>
                                                                            <td>1</td>
                                                                            <td>Basic</td>
                                                                            <td>{{ $service['kundali_make_commission'] }}</td>
                                                                            <td>{{ '₹' . $service['kundali_make_charge'] }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>2</td>
                                                                            <td>Professional</td>
                                                                            <td>{{ $service['kundali_make_commission_pro'] }}</td>
                                                                            <td>{{ '₹' . $service['kundali_make_charge_pro'] }}</td>
                                                                        </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
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
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush