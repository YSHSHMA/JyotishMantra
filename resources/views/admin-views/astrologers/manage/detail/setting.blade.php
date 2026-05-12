@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('setting'))

@section('content')

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('commission_detail') }}
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
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.service', $id) }}">Service</a>
                        </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting'))
                        <li class="nav-item">
                            <a class="nav-link active"
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
                                    <div class="card-header">
                                        <h5 class="mb-0">Service Commission</h5>
                                    </div>

                                    <div class="my-5">
                                        <form action="{{ route('admin.astrologers.manage.commission.update') }}"
                                            method="post">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $setting['id'] }}">
                                            <input type="hidden" name="type" value="commission">
                                            <div class="row">

                                                @if (!empty($setting['is_pandit_live_stream_charge']))
                                                    <div class="col-md-6 my-2">
                                                        <div class="row">
                                                            <div class="col-4 text-center" style="align-content: center;">
                                                                <p style="font-size: 15px; margin: 0px"><b>Live Stream</b>
                                                                </p>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control" required
                                                                        name="pandit_live_stream_commission"
                                                                        value="{{ $setting['is_pandit_live_stream_commission'] }}">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($setting['is_astrologer_live_stream_charge']))
                                                    <div class="col-md-6 my-2">
                                                        <div class="row">
                                                            <div class="col-4 text-center" style="align-content: center;">
                                                                <p style="font-size: 15px; margin: 0px"><b>Live Stream</b>
                                                                </p>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control" required
                                                                        name="live_stream_commission"
                                                                        value="{{ $setting['is_astrologer_live_stream_commission'] }}">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($setting['is_astrologer_call_charge']))
                                                    <div class="col-md-6 my-2">
                                                        <div class="row">
                                                            <div class="col-4 text-center" style="align-content: center;">
                                                                <p style="font-size: 15px; margin: 0px"><b>Calling</b></p>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control" required
                                                                        name="call_commission"
                                                                        value="{{ $setting['is_astrologer_call_commission'] }}">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($setting['is_astrologer_chat_charge']))
                                                    <div class="col-md-6 my-2">
                                                        <div class="row">
                                                            <div class="col-4 text-center" style="align-content: center;">
                                                                <p style="font-size: 15px; margin: 0px"><b>Chatting</b></p>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control" required
                                                                        name="chat_commission"
                                                                        value="{{ $setting['is_astrologer_chat_commission'] }}">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($setting['is_astrologer_report_charge']))
                                                    <div class="col-md-6 my-2">
                                                        <div class="row">
                                                            <div class="col-4 text-center" style="align-content: center;">
                                                                <p style="font-size: 15px; margin: 0px"><b>Report</b></p>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control" required
                                                                        name="report_commission"
                                                                        value="{{ $setting['is_astrologer_report_commission'] }}">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting-update'))
                                                @if (
                                                    !empty($setting['is_pandit_live_stream_charge']) ||
                                                        !empty($setting['is_astrologer_live_stream_charge']) ||
                                                        !empty($setting['is_astrologer_call_charge']) ||
                                                        !empty($setting['is_astrologer_chat_charge']) ||
                                                        !empty($setting['is_astrologer_report_charge']))
                                                    <div class="col-12 text-end"><button style="submit"
                                                            class="btn btn-primary mr-5">Update</button></div>
                                                @endif
                                                @endif
                                            </div>
                                        </form>
                                    </div>

                                    @php
                                        $poojaService = json_decode($setting['is_pandit_pooja_commission'], true);
                                        $vipPoojaService = json_decode($setting['is_pandit_vippooja_commission'], true);
                                        $anushthanService = json_decode($setting['is_pandit_anushthan_commission'], true);
                                        $chadhavaService = json_decode($setting['is_pandit_chadhava_commission'], true);
                                        $consultationService = json_decode($setting['consultation_commission'], true);
                                        $offlinepoojaService = json_decode($setting['is_pandit_offlinepooja_commission'], true);
                                    @endphp
                                    <div class="row">
                                        <div class="col-12">
                                            <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                                                @if (!empty($poojaService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button class="nav-link w-100 active" id="pooja-tab"
                                                            data-toggle="pill" data-target="#pooja" type="button"
                                                            role="tab" aria-controls="pooja"
                                                            aria-selected="true">Pooja
                                                            </button>
                                                    </li>
                                                @endif

                                                @if (!empty($vipPoojaService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button class="nav-link w-100" id="vippooja-tab"
                                                            data-toggle="pill" data-target="#vippooja" type="button"
                                                            role="tab" aria-controls="vippooja"
                                                            aria-selected="true">Vip Pooja
                                                            </button>
                                                    </li>
                                                @endif

                                                @if (!empty($anushthanService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button class="nav-link w-100" id="anushthan-tab"
                                                            data-toggle="pill" data-target="#anushthan" type="button"
                                                            role="tab" aria-controls="anushthan"
                                                            aria-selected="true">Anushthan
                                                            </button>
                                                    </li>
                                                @endif

                                                @if (!empty($chadhavaService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button class="nav-link w-100" id="chadhava-tab"
                                                            data-toggle="pill" data-target="#chadhava" type="button"
                                                            role="tab" aria-controls="chadhava"
                                                            aria-selected="true">Chadhava
                                                            </button>
                                                    </li>
                                                @endif

                                                @if (!empty($consultationService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button
                                                            class="nav-link w-100 {{ $setting['primary_skills']==4?'active':'' }}"
                                                            id="consultation-tab" data-toggle="pill"
                                                            data-target="#consultation" type="button" role="tab"
                                                            aria-controls="consultation"
                                                            aria-selected="false">Consultation</button>
                                                    </li>
                                                @endif

                                                @if (!empty($offlinepoojaService))
                                                    <li class="nav-item col-2" role="presentation">
                                                        <button class="nav-link w-100" id="offlinepooja-tab"
                                                            data-toggle="pill" data-target="#offlinepooja" type="button"
                                                            role="tab" aria-controls="offlinepooja"
                                                            aria-selected="true">Offline Pooja
                                                            </button>
                                                    </li>
                                                @endif
                                                @if (!empty($setting['is_kundali_make']) && $setting['is_kundali_make'] == 1)
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
                                                            <form
                                                                action="{{ route('admin.astrologers.manage.commission.update') }}"
                                                                method="post">
                                                                @csrf
                                                                <input type="hidden" name="user_id"
                                                                    value="{{ $setting['id'] }}">
                                                                <input type="hidden" name="type" value="pooja">
                                                                <table id="datatable" style="text-align: left;"
                                                                    class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                    <thead class="thead-light thead-50 text-capitalize">
                                                                        <tr>
                                                                            <th>{{ translate('#') }}</th>
                                                                            <th style="width: 50%;">
                                                                                {{ translate('name') }}
                                                                            </th>
                                                                            <th>{{ translate('category') }}</th>
                                                                            <th>{{ translate('commission') }}</th>
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
                                                                                <td>{{ $poojaData['category']['name'] }}
                                                                                </td>
                                                                                <td><input type="hidden"
                                                                                        class="form-control"
                                                                                        name="pooja_commission_key[]"
                                                                                        required
                                                                                        value="{{ $poojaKey }}">
                                                                                    <input type="number"
                                                                                        name="pooja_commission_value[]"
                                                                                        class="form-control" required
                                                                                        value="{{ $poojaValue }}" {{$setting['type']!='freelancer'?'disabled':''}}
                                                                                        style="width: 150px !important">
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                @if ($setting['type']=='freelancer')
                                                                    @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting-update'))
                                                                    <div class="mr-3 my-3" style="text-align: end;">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Update</button>
                                                                    </div>
                                                                    @endif
                                                                @endif
                                                            </form>
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
                                                    <div class="tab-pane fade show " id="vippooja" role="tabpanel"
                                                        aria-labelledby="vippooja-tab">

                                                        <div class="table-responsive datatable-custom">
                                                            <form
                                                                action="{{ route('admin.astrologers.manage.commission.update') }}"
                                                                method="post">
                                                                @csrf
                                                                <input type="hidden" name="user_id"
                                                                    value="{{ $setting['id'] }}">
                                                                <input type="hidden" name="type" value="vippooja">
                                                                <table id="datatable" style="text-align: left;"
                                                                    class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                    <thead class="thead-light thead-50 text-capitalize">
                                                                        <tr>
                                                                            <th>{{ translate('#') }}</th>
                                                                            <th style="width: 60%;">
                                                                                {{ translate('name') }}
                                                                            </th>
                                                                            <th>{{ translate('commission') }}</th>
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
                                                                                )->first();
                                                                            @endphp

                                                                            <tr>
                                                                                <td>{{ $vipPoojaInc++ }}</td>
                                                                                <td>{{ $vipPoojaData['name'] }}</td>
                                                                                <td><input type="hidden"
                                                                                        class="form-control"
                                                                                        name="vipPooja_commission_key[]"
                                                                                        required
                                                                                        value="{{ $vipPoojaKey }}">
                                                                                    <input type="number"
                                                                                        name="vipPooja_commission_value[]"
                                                                                        class="form-control" required
                                                                                        value="{{ $vipPoojaValue }}" {{$setting['type']!='freelancer'?'disabled':''}}
                                                                                        style="width: 150px !important">
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                @if ($setting['type']=='freelancer')
                                                                    @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting-update'))
                                                                    <div class="mr-3 my-3" style="text-align: end;">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Update</button>
                                                                    </div>
                                                                    @endif
                                                                @endif
                                                            </form>
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
                                                    <div class="tab-pane fade show " id="anushthan" role="tabpanel"
                                                        aria-labelledby="anushthan-tab">

                                                        <div class="table-responsive datatable-custom">
                                                            <form
                                                                action="{{ route('admin.astrologers.manage.commission.update') }}"
                                                                method="post">
                                                                @csrf
                                                                <input type="hidden" name="user_id"
                                                                    value="{{ $setting['id'] }}">
                                                                <input type="hidden" name="type" value="anushthan">
                                                                <table id="datatable" style="text-align: left;"
                                                                    class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                    <thead class="thead-light thead-50 text-capitalize">
                                                                        <tr>
                                                                            <th>{{ translate('#') }}</th>
                                                                            <th style="width: 60%;">
                                                                                {{ translate('name') }}
                                                                            </th>
                                                                            <th>{{ translate('commission') }}</th>
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
                                                                                )->first();
                                                                            @endphp

                                                                            <tr>
                                                                                <td>{{ $anushthanInc++ }}</td>
                                                                                <td>{{ $anushthanData['name'] }}</td>
                                                                                <td><input type="hidden"
                                                                                        class="form-control"
                                                                                        name="anushthan_commission_key[]"
                                                                                        required
                                                                                        value="{{ $anushthanKey }}">
                                                                                    <input type="number"
                                                                                        name="anushthan_commission_value[]"
                                                                                        class="form-control" required
                                                                                        value="{{ $anushthanValue }}" {{$setting['type']!='freelancer'?'disabled':''}}
                                                                                        style="width: 150px !important">
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                @if ($setting['type']=='freelancer')
                                                                    @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting-update'))
                                                                    <div class="mr-3 my-3" style="text-align: end;">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Update</button>
                                                                    </div>
                                                                    @endif
                                                                @endif
                                                            </form>
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
                                                    <div class="tab-pane fade show " id="chadhava" role="tabpanel"
                                                        aria-labelledby="chadhava-tab">

                                                        <div class="table-responsive datatable-custom">
                                                            <form
                                                                action="{{ route('admin.astrologers.manage.commission.update') }}"
                                                                method="post">
                                                                @csrf
                                                                <input type="hidden" name="user_id"
                                                                    value="{{ $setting['id'] }}">
                                                                <input type="hidden" name="type" value="chadhava">
                                                                <table id="datatable" style="text-align: left;"
                                                                    class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                    <thead class="thead-light thead-50 text-capitalize">
                                                                        <tr>
                                                                            <th>{{ translate('#') }}</th>
                                                                            <th style="width: 60%;">
                                                                                {{ translate('name') }}
                                                                            </th>
                                                                            <th>{{ translate('commission') }}</th>
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
                                                                                )->first();
                                                                            @endphp

                                                                            <tr>
                                                                                <td>{{ $chadhavaInc++ }}</td>
                                                                                <td>{{ $chadhavaData['name'] }}</td>
                                                                                <td><input type="hidden"
                                                                                        class="form-control"
                                                                                        name="chadhava_commission_key[]"
                                                                                        required
                                                                                        value="{{ $chadhavaKey }}">
                                                                                    <input type="number"
                                                                                        name="chadhava_commission_value[]"
                                                                                        class="form-control" required
                                                                                        value="{{ $chadhavaValue }}" {{$setting['type']!='freelancer'?'disabled':''}}
                                                                                        style="width: 150px !important">
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                @if ($setting['type']=='freelancer')
                                                                    @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting-update'))
                                                                    <div class="mr-3 my-3" style="text-align: end;">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Update</button>
                                                                    </div>
                                                                    @endif
                                                                @endif
                                                            </form>
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
                                                            <form
                                                                action="{{ route('admin.astrologers.manage.commission.update') }}"
                                                                method="post">
                                                                @csrf
                                                                <input type="hidden" name="user_id"
                                                                    value="{{ $setting['id'] }}">
                                                                <input type="hidden" name="type"
                                                                    value="consultation">
                                                                <table id="datatable" style="text-align: left;"
                                                                    class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                    <thead class="thead-light thead-50 text-capitalize">
                                                                        <tr>
                                                                            <th>{{ translate('#') }}</th>
                                                                            <th>{{ translate('name') }}</th>
                                                                            <th>{{ translate('category') }}</th>
                                                                            <th>{{ translate('commission') }}</th>
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
                                                                                <td><input type="hidden"
                                                                                        class="form-control"
                                                                                        name="consultation_commission_key[]"
                                                                                        required
                                                                                        value="{{ $consultationKey }}">
                                                                                    <input type="number"
                                                                                        class="form-control" required
                                                                                        name="consultation_commission_value[]"
                                                                                        value="{{ $consultationValue }}" {{$setting['type']!='freelancer'?'disabled':''}}
                                                                                        style="width: 150px !important">
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>

                                                                </table>
                                                                @if ($setting['type']=='freelancer')
                                                                    @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting-update'))
                                                                    <div class="mr-3 my-3" style="text-align: end;">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Update</button>
                                                                    </div>
                                                                    @endif
                                                                @endif
                                                            </form>
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
                                                    <div class="tab-pane fade show " id="offlinepooja" role="tabpanel"
                                                        aria-labelledby="offlinepooja-tab">

                                                        <div class="table-responsive datatable-custom">
                                                            <form
                                                                action="{{ route('admin.astrologers.manage.commission.update') }}"
                                                                method="post">
                                                                @csrf
                                                                <input type="hidden" name="user_id"
                                                                    value="{{ $setting['id'] }}">
                                                                <input type="hidden" name="type" value="offlinepooja">
                                                                <table id="datatable" style="text-align: left;"
                                                                    class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                    <thead class="thead-light thead-50 text-capitalize">
                                                                        <tr>
                                                                            <th>{{ translate('#') }}</th>
                                                                            <th style="width: 60%;">
                                                                                {{ translate('name') }}
                                                                            </th>
                                                                            <th>{{ translate('commission') }}</th>
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
                                                                                )->first();
                                                                            @endphp

                                                                            <tr>
                                                                                <td>{{ $offlinepoojaInc++ }}</td>
                                                                                <td>{{ $offlinepoojaData['name'] }}</td>
                                                                                <td><input type="hidden"
                                                                                        class="form-control"
                                                                                        name="offlinepooja_commission_key[]"
                                                                                        required
                                                                                        value="{{ $offlinepoojaKey }}">
                                                                                    <input type="number"
                                                                                        name="offlinepooja_commission_value[]"
                                                                                        class="form-control" required
                                                                                        value="{{ $offlinepoojaValue }}" {{$setting['type']!='freelancer'?'disabled':''}}
                                                                                        style="width: 150px !important">
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                @if ($setting['type']=='freelancer')
                                                                    @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting-update'))
                                                                    <div class="mr-3 my-3" style="text-align: end;">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Update</button>
                                                                    </div>
                                                                    @endif
                                                                @endif
                                                            </form>
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
                                                @if (!empty($setting['is_kundali_make']) && $setting['is_kundali_make'] == 1)
                                            <div class="tab-pane fade show " id="kundalimilan" role="tabpanel" aria-labelledby="kundalimilan-tab">
                                                <div class="table-responsive datatable-custom">
                                                    <form action="{{ route('admin.astrologers.manage.commission.update') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="user_id" value="{{ $setting['id'] }}">
                                                        <input type="hidden" name="type" value="kundali">
                                                        <input type="hidden" name="is_kundali_make" value="1">
                                                        <table id="datatable" style="text-align: left;" class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                            <thead class="thead-light thead-50 text-capitalize">
                                                                <tr>
                                                                    <th>{{ translate('#') }}</th>
                                                                    <th>{{ translate('type') }}</th>
                                                                    <th>{{ translate('commission') }}</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody id="set-rows">
                                                                <tr>
                                                                    <td>1</td>
                                                                    <td>Basic</td>
                                                                    <td><input type="number" name="kundali_make_commission" class="form-control" required value="{{ $setting['kundali_make_commission'] }}" {{$setting['type']!='freelancer'?'disabled':''}} style="width: 150px !important">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>2</td>
                                                                    <td>Professional</td>
                                                                    <td><input type="number" name="kundali_make_commission_pro" class="form-control" required value="{{ $setting['kundali_make_commission_pro'] }}" {{$setting['type']!='freelancer'?'disabled':''}} style="width: 150px !important">
                                                               
                                                                </tr>                                                                
                                                            </tbody>
                                                        </table>
                                                        @if ($setting['type']=='freelancer')
                                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting-update'))
                                                        <div class="mr-3 my-3" style="text-align: end;">
                                                            <button type="submit"
                                                                class="btn btn-primary">Update</button>
                                                        </div>
                                                        @endif
                                                        @endif
                                                    </form>
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