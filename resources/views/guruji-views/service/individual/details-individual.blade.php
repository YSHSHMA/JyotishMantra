@extends('layouts.back-end.app-guruji')
@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Utils\Helpers;
if (auth('guruji')->check()) {
$vendorId = auth('guruji')->user()->id;
} 
@endphp
@section('title', translate('add_counselling'))
@push('css_or_js')


<style>
    .switcher_control {
        pointer-events: none !important;
    }
</style>
@endpush
@section('content')
    {{-- main page --}}
<div class="content container-fluid">
    <div class="row mt-20">
            @include('guruji-views.partials.vendor-info', ['vendor' => $vendor])
    </div>

    <div class="row pt-2">
        <div class="col-md-12">
            <div class="row">
                <div class="col-12">
                    <div class="sticky-tabs-wrapper">
                        <div class="js-nav-scroller hs-nav-scroller-horizontal mb-5">
                            @include('guruji-views.partials.vendor-service-tabs', ['vendor' => $vendor])
                        </div>
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
                                                <form action="{{ route('guruji.services.details.store-detail', $vendor->id) }}"
                                                    method="post">
                                                    @csrf
                                                    <input type="hidden" name="pandit_id" value="{{ $vendor['id'] }}">
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

                                                                @foreach ($panditDetail as $serviceIndex => $service)
                                                                    @php
                                                                        $service = App\Models\Service::find($service->service_id);
                                                                        $savedRows = $groupedPackages[$service->service_id] ?? collect();
                                                                        $rowIndex = 1;
                                                                    @endphp
                                                                    @php
                                                                       
                                                                    @endphp


                                                                    <input type="hidden" name="method" value="{{count($savedRows)==0?'save':'update'}}">

                                                                    {{-- ===================== SAVED ROWS ===================== --}}
                                                                    @foreach ($savedRows as $index => $saved)
                                                                        <tr class="service-row">

                                                                            <input type="hidden" name="service_id[]"
                                                                                value="{{ $service->id }}">

                                                                            <td>
                                                                                @if ($index == 0)
                                                                                    {{ $loop->parent->iteration }}
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
                                                                                {{ $loop->iteration }}
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
