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
@push('css_or_js')
<link rel="stylesheet"  href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
@endpush
@section('title', translate('counselling'))
@section('content')
<div id="pageLoader" style=" display:none; position:fixed;   top:0; left:0;  width:100%; height:100%;
    background:rgba(255,255,255,0.8);   z-index:9999; align-items:center;justify-content:center;">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 fw-bold">Please wait...</div>
    </div>
</div>
<div class="content container-fluid">
    <div class="mb-3">
      <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
         <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/my-bank-info.png')}}" alt="">
         {{translate('counselling')}}
      </h2>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card card-top-bg-element mb-4">
                {{-- Card Header --}}
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ translate('service_charges') }}</h5>
                    <a href="{{ route('guruji.services.create', $vendorId) }}" class="btn btn-md btn-primary">
                        <i class="fa fa-plus"></i> {{ translate('add_new_service') }}
                    </a>




                </div>

                {{-- Card Body --}}
                <div class="px-3 py-4">
                    <div class="table-responsive">
                    <form action="{{ route('guruji.services.counselling.update') }}" method="POST">
                        @csrf

                        <table id="serviceList" class="table table-striped table-bordered table-hover">
                            <thead class="thead-light text-capitalize">
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th style="width:40%">{{ translate('counselling_name') }} </th>
                                    <th>{{ translate('commission') }}</th>
                                    <th class="text-end">{{ translate('price') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($services as $key => $service)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>

                                        <td>{{ $service->name }}</td>
                                        <td>
                                            <input type="number"
                                                name="commission[{{ $service->id }}]"
                                                class="form-control form-control-sm"
                                                value="{{ old('times.'.$service->id, $panditTimes[$service->id] ?? 0) }}"
                                                min="0">
                                            <small class="text-muted">commission</small>
                                        </td>

                                        {{-- Price --}}
                                        <td>
                                            <input type="number"
                                                name="prices[{{ $service->id }}]"
                                                class="form-control form-control-sm text-end"
                                                value="{{ old('prices.'.$service->id, $panditPrices[$service->id] ?? 0) }}"
                                                min="0">
                                                <small class="text-muted">Rate/Price</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No services available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th style="width:40%">{{ translate('counselling_name') }}</th>
                                    <th>{{ translate('commission') }}</th>
                                    <th class="text-end">{{ translate('price') }}</th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary" id="updateBtn">
                            {{ translate('update_services') }}
                            </button>
                        </div>
                    </form>

                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- Add Service Modal -->
    <!-- <div class="modal fade" id="addServiceModal" tabindex="-1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <form action="#" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Add New Service</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">

                        {{-- Category --}}
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                           

                        </div>
                        {{-- Service Name --}}
                        <div class="mb-3">
                            <label class="form-label">Service Name</label>
                            <input type="text" name="name"class="form-control" required>
                        </div>


                        {{-- Time --}}
                        <div class="mb-3">
                            <label class="form-label">Time (Minutes)</label>
                            <input type="number" name="time" class="form-control" min="0" required>
                        </div>

                        {{-- Price --}}
                        <div class="mb-3">
                            <label class="form-label" name="price"  class="form-control" min="0"required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"class="btn btn-secondary" data-dismiss="modal"> Cancel</button>
                        <button type="submit" class="btn btn-primary"> Save Service</button>
                    </div>

                </form>

            </div>
        </div>
    </div> -->

</div>
@endsection
@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/country-picker-init.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#serviceList').DataTable({
            pageLength: 10,
            scrollY: '500px',
            scrollCollapse: true,
            paging: true,
            fixedHeader: true,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
        });

        
    });
</script>
<script>
    $(document).ready(function () {

        $('form').on('submit', function () {
            $('#updateBtn').prop('disabled', true);
            $('#pageLoader').css('display', 'flex');
        });

    });
</script>

@endpush