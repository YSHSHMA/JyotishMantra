@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('add_detail'))

@section('content')
    {{-- main page --}}
    <div class="content container-fluid">
        <div class="row mt-20">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row pt-2">
            <div class="col-md-12">
                @php
                    $poojaService = json_decode($overview['is_pandit_pooja'], true);
                @endphp
                <div class="row">

                    <div class="col-12">
                        <div class="js-nav-scroller hs-nav-scroller-horizontal mb-5">
                            <ul class="nav nav-tabs flex-wrap page-header-tabs">
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-package', $overview['id']) }}">Package</a>
                                </li>
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                <li class="nav-item">
                                    <a class="nav-link active"
                                        href="{{ route('admin.astrologers.manage.add-detail', $overview['id']) }}">Detail</a>
                                </li>
                                {{-- @endif --}}
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-additional-detail', $overview['id']) }}">Additional Detail</a>
                                </li>
                                {{-- @endif --}}
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-gallery', $overview['id']) }}">Gallery</a>
                                </li>
                                {{-- @endif --}}
                                @if (!empty($overview['consultation_charge']))
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-counselling', $overview['id']) }}">Counselling</a>
                                </li>
                                @endif
                            </ul>
                        </div>

                        <div class="tab-content mt-5">
                            <div class="tab-pane fade show active" id="order">
                                <div class="row pt-2">
                                    <div class="col-md-12">
                                        <div class="card w-100">
                                            <div class="card-header">
                                                <h5 class="mb-0">Service Address</h5>
                                            </div>
                                            <div class="col-12">
                                                <form action="{{ route('admin.astrologers.manage.store-detail') }}"
                                                    method="post">
                                                    @csrf
                                                    <input type="hidden" name="pandit_id" value="{{ $overview['id'] }}">
                                                    <div class="table-responsive datatable-custom">
                                                        <table id="datatable" style="text-align: left;"
                                                            class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                            <thead class="thead-light thead-50 text-capitalize">
                                                                <tr>
                                                                    <th style="width: 5%;">{{ translate('#') }}</th>
                                                                    <th style="width: 35%;">{{ translate('name') }}
                                                                    </th>
                                                                    <th style="width: 30%;">{{ translate('english') }}</th>
                                                                    <th style="width: 25%;">{{ translate('hindi') }}</th>
                                                                </tr>
                                                            </thead>


                                                            <tbody id="set-rows">

                                                                @foreach ($poojaService as $poojaKey => $poojaValue)
                                                                    @php
                                                                        $service = App\Models\Service::find($poojaKey);
                                                                        $savedRows = $groupedDetails[$poojaKey] ?? [];
                                                                        $rowIndex = 1;
                                                                    @endphp

                                                                    <input type="hidden" name="method" value="{{count($savedRows)==0?'save':'update'}}">

                                                                    {{-- ===================== SAVED ROWS ===================== --}}
                                                                    @foreach ($savedRows as $index => $saved)
                                                                        <tr class="service-row">

                                                                            <input type="hidden" name="service_id[]"
                                                                                value="{{ $service->id }}">

                                                                            <td>
                                                                                @if ($index == 0)
                                                                                    {{ $loop->parent->iteration }})
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($index == 0)
                                                                                    {{ $service->name }}
                                                                                @endif
                                                                            </td>

                                                                            @foreach ($languages as $lang)
                                                                                <?php
                                                                                // dd($saved['translations']);
                                                                                if (count($saved['translations'])) {
                                                                                    $translate = [];
                                                                                    foreach ($saved['translations'] as $translation) {
                                                                                        if ($translation->locale == $lang && $translation->key == 'address') {
                                                                                            $translate[$lang]['address'] = $translation->value;
                                                                                        }
                                                                                    }
                                                                                }
                                                                                // dd($translate,$lang);
                                                                                ?>
                                                                                <td>
                                                                                    <div class="form-group">
                                                                                        <input type="text"
                                                                                            name="address[{{ $service->id }}][]"
                                                                                            class="form-control"
                                                                                            placeholder="Enter address"
                                                                                            value="{{ $translate[$lang]['address'] ?? $saved['address'] }}"
                                                                                            required>

                                                                                    </div>
                                                                                </td>
                                                                            @endforeach

                                                                        </tr>
                                                                    @endforeach

                                                                    {{-- ===================== EMPTY ROW FOR NEW PACKAGE ===================== --}}
                                                                    @if (count($savedRows) == 0)
                                                                        <tr class="service-row"
                                                                            data-service="{{ $service->id }}">
                                                                            <input type="hidden" name="service_id[]"
                                                                                value="{{ $service->id }}">

                                                                            <td>
                                                                                {{ $loop->iteration }})
                                                                            </td>

                                                                            <td>
                                                                                {{ $service->name }}
                                                                            </td>

                                                                            @foreach ($languages as $lang)
                                                                                <?php
                                                                                if (count($service['translations'])) {
                                                                                    $translate = [];
                                                                                    foreach ($service['translations'] as $translation) {
                                                                                        if ($translation->locale == $lang && $translation->key == 'name') {
                                                                                            $translate[$lang]['name'] = $translation->value;
                                                                                        }
                                                                                    }
                                                                                }
                                                                                ?>
                                                                                <td>
                                                                                    <div class="form-group">
                                                                                        <input type="text"
                                                                                            name="address[{{ $service->id }}][]"
                                                                                            class="form-control"
                                                                                            placeholder="Enter address"
                                                                                            value="{{ $translate[$lang]['name'] ?? $service['name'] }}"
                                                                                            required>

                                                                                    </div>
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    @endif
                                                                @endforeach

                                                            </tbody>

                                                            @foreach ($languages as $lang)
                                                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                                                            @endforeach

                                                        </table>
                                                    </div>
                                                    <div class="text-center py-3">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </form>
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
@endpush
