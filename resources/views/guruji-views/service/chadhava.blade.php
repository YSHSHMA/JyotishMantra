@extends('layouts.back-end.app-guruji')
@push('css_or_js')
<link rel="stylesheet"  href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
@endpush
@section('title', translate('chadhava'))
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
      <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
         <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/my-bank-info.png')}}" alt="">
         {{translate('chadhava')}}
      </h2>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card card-top-bg-element mb-4">
                {{-- Card Header --}}
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ translate('chadhava_charges') }}</h5>
                </div>
                {{-- Card Body --}}
                <div class="px-3 py-4">
                    <div class="table-responsive">
                    <form action="{{ route('guruji.services.chadhava.update') }}" method="POST">
                        @csrf

                        <table id="chadhavaList" class="table table-striped table-bordered table-hover">
                            <thead class="thead-light text-capitalize">
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th style="width:40%">{{ translate('chadhava_name') }}</th>
                                    <th>{{ translate('time') }}</th>
                                    <th>{{ translate('commission') }}</th>
                                    <th class="text-end">{{ translate('price') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($services as $key => $service)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $service->name }} <br>
                                        {{ $service->chadhava_venue }} 
                                    </td>
                                        {{-- Time --}}
                                        <td>
                                            <input type="text"
                                                name="times[{{ $service->id }}]"
                                                class="form-control form-control-sm"
                                                value="{{ old('times.'.$service->id, $panditTimes[$service->id] ?? 0) }}"
                                                min="0">
                                            <small class="text-muted">Minutes</small>
                                        </td>
                                         <!-- Commission -->
                                         <td>
                                            <input type="number"
                                                name="commission[{{ $service->id }}]"
                                                class="form-control form-control-sm"
                                                value="{{ old('commission.'.$service->id, $panditCommission[$service->id] ?? 0) }}"
                                                min="0">
                                            <small class="text-muted">Commission</small>
                                        </td>

                                        {{-- Price --}}
                                        <td class="text-end">
                                            <input type="number"
                                                name="prices[{{ $service->id }}]"
                                                class="form-control form-control-sm text-end"
                                                value="{{ old('prices.'.$service->id, $panditPrices[$service->id] ?? 0) }}"
                                                min="0">
                                                <small class="text-muted">Price / Rate</small>
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
                                    <th style="width:40%">{{ translate('chadhava_name') }}</th>
                                    <th>{{ translate('time') }}</th>
                                    <th>{{ translate('commission') }}</th>
                                    <th class="text-end">{{ translate('price') }}</th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                            {{ translate('update_chadhava') }}
                            </button>
                        </div>
                    </form>

                    </div>

                </div>
            </div>

        </div>
    </div>

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
        $('#chadhavaList').DataTable({
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

        // Filter auto-submit (no need for ajax reload)
        $('#payment-status, #purohit-id, #booking-status').on('change', function() {
            $(this).closest('form').submit();
        });
    });
</script>
@endpush