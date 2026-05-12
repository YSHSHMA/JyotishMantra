@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('add_counselling'))

@section('content')
    <style>
        .switcher_control {
            pointer-events: none !important;
        }
    </style>
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
                    $counsellingService = json_decode($overview['consultation_charge'], true);
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
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-detail', $overview['id']) }}">Detail</a>
                                </li>
                                {{-- @endif --}}
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-additional-detail', $overview['id']) }}">Additional
                                        Detail</a>
                                </li>
                                {{-- @endif --}}
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-gallery', $overview['id']) }}">Gallery</a>
                                </li>
                                {{-- @endif --}}
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                @if (!empty($overview['consultation_charge']))
                                    <li class="nav-item">
                                        <a class="nav-link active"
                                            href="{{ route('admin.astrologers.manage.add-counselling', $overview['id']) }}">Counselling</a>
                                    </li>
                                @endif
                                {{-- @endif --}}
                            </ul>
                        </div>

                        <div class="tab-content mt-5">
                            <div class="tab-pane fade show active" id="order">
                                <div class="row pt-2">
                                    <div class="col-md-12">
                                        <div class="card w-100">
                                            <div class="card-header">
                                                <h5 class="mb-0">Counselling Service Price</h5>
                                            </div>
                                            <div class="col-12">
                                                <form action="{{ route('admin.astrologers.manage.store-counselling') }}"
                                                    method="post" enctype="multipart/form-data">
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
                                                                    <th style="width: 25%;">{{ translate('price') }}</th>
                                                                    <th style="width: 25%;">{{ translate('thumbnail') }}</th>
                                                                    {{-- <th style="width: 10%;">{{ translate('status') }}</th> --}}
                                                                    {{-- <th class="text-left">{{ translate('action') }}</th> --}}
                                                                </tr>
                                                            </thead>


                                                            <tbody id="set-rows">
                                                                @php
                                                                    $index = 1;
                                                                @endphp
                                                                @if (count($panditCounsellings) > 0)
                                                                    <input type="hidden" name="method" value="update">
                                                                    @foreach ($panditCounsellings as $pcIndex => $pcData)
                                                                        @php
                                                                            $service = App\Models\Service::find(
                                                                                $pcData->service_id,
                                                                            );
                                                                        @endphp

                                                                        <tr class="service-row">

                                                                            <input type="hidden" name="update_id[]"
                                                                                value="{{ $pcData->id }}">

                                                                            <td>
                                                                                {{ $index++ . ')' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $service->name }}
                                                                            </td>

                                                                            <td>
                                                                                <input type="number" name="price[]"
                                                                                    class="form-control"
                                                                                    value="{{ $pcData->price }}" required>
                                                                            </td>

                                                                            <td>
                                                                                <div class="pb-2 text-center">
                                                                                    <img src="{{url('/storage/app/public/astrologers/service-thumbnail/'.$pcData->thumbnail)}}" alt="" width="50">
                                                                                </div>
                                                                                <label for="" class="form-label">Update</label>
                                                                                <input type="hidden" name="thumbnail[]" value="">

                                                                                <input type="file" name="thumbnail[]" class="form-control" accept="image/*">
                                                                            </td>

                                                                            {{-- <td>
                                                                            <label class="switcher mx-auto">
                                                                                <input type="checkbox"
                                                                                    class="switcher_input"
                                                                                    name="status[]" value="1"
                                                                                    {{ $saved->status == 1 ? 'checked' : '' }}>
                                                                                <span class="switcher_control"></span>
                                                                            </label>

                                                                            <input type="hidden" name="status_hidden[]"
                                                                                value="{{ $saved->status }}">
                                                                        </td> --}}

                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <input type="hidden" name="method" value="save">
                                                                    @foreach ($counsellingService as $cIndex => $cData)
                                                                        @php
                                                                            $service = App\Models\Service::find(
                                                                                $cIndex,
                                                                            );
                                                                        @endphp

                                                                        <tr class="service-row">

                                                                            <input type="hidden" name="service_id[]"
                                                                                value="{{ $service->id }}">

                                                                            <td>
                                                                                {{ $index++ . ')' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $service->name }}
                                                                            </td>

                                                                            <td>
                                                                                <input type="number" name="price[]"
                                                                                    class="form-control"
                                                                                    value="{{ $cData }}"
                                                                                    required>
                                                                            </td>

                                                                            <td>
                                                                                <input type="file" name="thumbnail[]"
                                                                                    class="form-control" accept="image/*" value="" required>
                                                                            </td>

                                                                        </tr>
                                                                    @endforeach

                                                                @endif

                                                            </tbody>



                                                        </table>
                                                    </div>
                                                    <div class="text-center py-3">
                                                        <button type="submit" class="btn btn-primary">Update</button>
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
    {{-- <script>
        // service checkbox
        $(document).on("change", ".switcher_input", function() {

            let hidden = $(this)
                .closest("td")
                .find('input[name="status_hidden[]"]');

            if (!hidden.length) {
                hidden = $(this).parent().parent().find('input[name="status_hidden[]"]');
            }

            hidden.val($(this).is(":checked") ? 1 : 0);
        });
    </script> --}}
@endpush
