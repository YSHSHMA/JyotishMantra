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
                                            <h5 class="mb-0">Counselling Service Price</h5>
                                        </div>
                                        <div class="col-12">
                                            <form id="counsellingForm" action="{{ route('guruji.services.counselling.individual.save', $vendor->id) }}"
                                                method="post" enctype="multipart/form-data">
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
                                                                <th style="width: 25%;">{{ translate('price') }}</th>
                                                                <th style="width: 25%;">{{ translate('thumbnail') }}</th>
                                                                {{-- <th style="width: 10%;">{{ translate('status') }}</th> --}}
                                                                {{-- <th class="text-left">{{ translate('action') }}</th> --}}
                                                            </tr>
                                                        </thead>


                                                        <tbody id="set-rows">
                                                            @php $index = 1; @endphp

                                                            @if($panditCounsellings->count() > 0)
                                                                <input type="hidden" name="method" value="update">

                                                                @foreach ($panditCounsellings as $pcData)
                                                                    @php
                                                                        $service = App\Models\Service::find($pcData->service_id);
                                                                    @endphp

                                                                    @if($service) {{-- important null check --}}
                                                                    <tr class="service-row">
                                                                        <input type="hidden" name="update_id[]" value="{{ $pcData->id }}">

                                                                        <td>{{ $index++ }}</td>

                                                                        <td>{{ $service->name }}</td>

                                                                        <td>
                                                                            <div class="input-group shadow-sm">
                                                                                <input type="number"
                                                                                name="price[]"
                                                                                class="form-control"
                                                                                value="{{ $pcData->price }}"
                                                                                min="0"
                                                                                step="1"
                                                                                required>
                                                                                <span class="input-group-text fw-bold text-success">₹</span>
                                                                            </div>
                                                                        </td>



                                                                        <td class="text-center">
                                                                            <div class="pb-2">
                                                                                <img src="{{ asset('storage/app/public/astrologers/service-thumbnail/'.$pcData->thumbnail) }}"
                                                                                    width="50">
                                                                            </div>

                                                                            <label class="form-label">Update</label>
                                                                            <input type="file" name="thumbnail[]" class="form-control" accept="image/*">
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                @endforeach

                                                            @else
                                                                <input type="hidden" name="method" value="save">

                                                                @foreach ($services as $service)
                                                                    <tr class="service-row">
                                                                        <input type="hidden" name="service_id[]" value="{{ $service->id }}">

                                                                        <td>{{ $index++ }}</td>

                                                                        <td>{{ $service->name }}</td>

                                                                        <td>
                                                                        <div class="input-group shadow-sm">
                                                                                
                                                                            <input type="number" name="price[]" class="form-control" required value="0">
                                                                                <span class="input-group-text fw-bold text-success">₹</span>
                                                                            </div>
                                                                        </td>

                                                                        <td>
                                                                            <input type="file" name="thumbnail[]" class="form-control" accept="image/*">
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
