@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('City_Detail_List'))

@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="row g-2 flex-grow-1">
            <div class="col-sm-8 col-md-6 col-lg-4">
                <div class="mb-3">
                    <h2 class="h1 mb-0 d-flex gap-2">
                        <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}"
                            alt="">
                        {{ translate('City_Detail') }}

                        </span>
                    </h2>
                </div>
            </div>
        </div>

        @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja City', 'add'))
        <div class="row mt-20">
            <div class="col-md-12">
                <form class="product-form text-start" action="{{ route('admin.service.offline.pooja.city.add-new') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('city') }}</label>
                                        <select name="city_id" class="form-select js-select2-custom" required>
                                            @forelse ($cities as $item)
                                                <option value="{{ $item->id }}">{{ $item->city }}</option>
                                            @empty
                                                <option value="">City Not Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('pincode') }}</label>
                                        <input type="number" name="pincode" id="" class="form-control"
                                            placeholder="enter pincode" value="{{ old('pincode') }}" autocomplete="off"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('latitude') }}</label>
                                        <input type="text" name="latitude" id="" class="form-control"
                                            placeholder="enter latitude" value="{{ old('latitude') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('longitude') }}</label>
                                        <input type="text" name="longitude" id="" class="form-control"
                                            placeholder="enter longitude" value="{{ old('longitude') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <div class="row mt-20">
            <div class="col-md-12">

                <div class="py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-md-4">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-primary">
                                            {{ $offlinePoojaCity->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">Total</div>
                                    </div>
                                    <div>
                                        <img width="40"
                                            class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/ordercom.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-success">
                                            {{ $offlinePoojaCity->where('status', 1)->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">Active</div>
                                    </div>
                                    <div>
                                        <img width="40"
                                            class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/ordercom.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-primary">
                                            {{ $offlinePoojaCity->where('status', 0)->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">Inactive</div>
                                    </div>
                                    <div>
                                        <img width="40"
                                            class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/ordercom.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    {{-- <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                            placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" value="{{ request('searchValue') }}" required>
                                        <button type="submit" class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> --}}

                    <div class="card-body px-3">
                        <div class="table-responsive">
                            <table id="myTable"
                                class="display table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('city') }}</th>
                                        <th>{{ translate('pincode') }}</th>
                                        <th>{{ translate('latitude') }}</th>
                                        <th>{{ translate('longitude') }}</th>
                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja City', 'status'))
                                        <th class="text-center">{{ translate('status') }}</th>
                                        @endif
                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja City', 'edit'))
                                        <th class="text-center"> {{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($offlinePoojaCity as $key => $item)
                                        <tr>
                                            <td>{{ $offlinePoojaCity->firstItem() + $key }}</td>
                                            <td>
                                                <span class="media-body title-color hover-c1">
                                                    {{ $item->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="media-body title-color hover-c1">
                                                    {{ $item->pincode }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="media-body title-color hover-c1">
                                                    {{ $item->latitude }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="media-body title-color hover-c1">
                                                    {{ $item->longitude }}
                                                </span>
                                            </td>
                                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja City', 'status'))
                                            <td>
                                                <form
                                                    action="{{ route('admin.service.offline.pooja.city.status-update') }}"
                                                    method="post" id="service-status{{ $item['id'] }}-form">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                    <label class="switcher mx-auto">
                                                        <input type="checkbox" class="switcher_input toggle-switch-message"
                                                            name="status" id="service-status{{ $item['id'] }}"
                                                            value="1" {{ $item['status'] == 1 ? 'checked' : '' }}
                                                            data-modal-id = "toggle-status-modal"
                                                            data-toggle-id = "service-status{{ $item['id'] }}"
                                                            data-on-image = "service-status-on.png"
                                                            data-off-image = "service-status-off.png"
                                                            data-on-title = "{{ translate('Want_to_Turn_ON') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                            data-off-title = "{{ translate('Want_to_Turn_OFF') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                            data-on-message = "<p>{{ translate('if_enabled_this_city_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                            data-off-message = "<p>{{ translate('if_disabled_this_city_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </form>
                                            </td>
                                            @endif

                                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja City', 'edit'))
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('edit') }}"
                                                        href="{{ route('admin.service.offline.pooja.city.update', [$item['id']]) }}">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $offlinePoojaCity->links() }}
                        </div>
                    </div>

                    @if (count($offlinePoojaCity) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- <span id="route-admin-rashi-delete" data-url=""></span> -->
    {{-- <span id="route-admin-rashi-status-update"
        data-url="{{ route('admin.service.offline.pooja.category.status-update') }}"></span> --}}
    <!-- Modal Structure -->
    {{-- <div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">Offile Pooja Category Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody id="service-details">
                            <!-- Details will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@push('script')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>
@endpush
