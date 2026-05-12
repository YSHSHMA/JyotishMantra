@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('astrologer_talk'))
@push('css_or_js')
    <style>
    .pandit-status-badge{
            display: inline-block;
            font-size: 11px;
            line-height: 1;
            padding: 3px 7px;
            border-radius: 12px;
            margin-left: 8px;
            vertical-align: middle;
            font-weight: 600;
            color: #fff;
    }
    .badge-online {
        background: #3498db;
    }

    .badge-online::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #fff;
        display: inline-block;
        margin-right: 6px;
        animation: livePulse 1.2s infinite;
    }

    /* LIVE → green + blink dot */
    .badge-live{
        background: #2ecc71;
        position: relative;
    }

    .badge-live::before{
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #fff;
        display: inline-block;
        margin-right: 6px;
        animation: livePulse 1.2s infinite;
    }

    /* OFFLINE → red */
    .badge-offline{
        background: #e74c3c;
    }

    /* blinking animation */
    @keyframes livePulse{
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.5; }
        100% { transform: scale(1); opacity: 1; }
    }

    </style>

@endpush
@section('content')

    {{-- modal --}}
    <div class="modal fade" id="detail-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <input type="hidden" id="modal-id">
                        <tbody id="detailTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="viewButton()">View</button>
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
                {{ translate('astrologer_talk') }}
                {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $festivals->total() }}</span> --}}
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="px-3 py-4">
                            <h4>Filter List</h4>
                            <div class="row g-2 flex-grow-1 mt-3">
                                <div class="col-4">
                                    <form action="{{ url()->current() }}" method="GET">
                                        <label for="">Name</label>
                                        <div class="input-group input-group-custom input-group-merge">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-search"></i>
                                                </div>
                                            </div>
                                            <input type="search" name="search_name" class="form-control"
                                                placeholder="{{ translate('search_by_name') }}"
                                                aria-label="{{ translate('search_by_name') }}"
                                                value="{{ request()->has('search_name') ? request()->get('search_name') : '' }}"
                                                required>
                                            <button type="submit"
                                                class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-4">
                                    <form id="type-form" action="{{ url()->current() }}" method="GET">
                                        <label for="">Type</label>
                                        <select name="search_type" id="" class="form-control"
                                            onchange="filter('type-form')">
                                            <option disabled selected>Select</option>
                                            <option value="in house"
                                                {{ request()->has('search_type') && request()->get('search_type') == 'in house' ? 'selected' : '' }}>
                                                In house</option>
                                            <option value="freelancer"
                                                {{ request()->has('search_type') && request()->get('search_type') == 'freelancer' ? 'selected' : '' }}>
                                                Freelancer</option>
                                        </select>
                                    </form>
                                </div>
                                <div class="col-4">
                                    <form id="service-type-form" action="{{ url()->current() }}" method="GET">
                                        <label for="">Service Type</label>
                                        <select name="search_service_type" id="search_service_type" class="form-control"
                                            onchange="filter('service-type-form')">
                                            <option disabled selected>Select</option>
                                            <option value="3"
                                                {{ request()->has('search_service_type') && request()->get('search_service_type') == '3' ? 'selected' : '' }}>
                                                Pandit</option>
                                            <option value="4"
                                                {{ request()->has('search_service_type') && request()->get('search_service_type') == '4' ? 'selected' : '' }}>
                                                Astrologer</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-0 mt-2">
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('#') }}</th>
                                        <th>{{ translate('Image') }}</th>
                                        <th>{{ translate('Contact Info') }}</th>
                                        <th>{{ translate('Total Earnings') }}</th>
                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'detail'))
                                        <th>{{ translate('Action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($astrolist as $key => $value)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><img src="{{ $value['image'] }}" alt="" width="50"></td>
                                            <td>
                                                <a href="javascript:0"
                                                    data-name="{{ $value['name'] }}"
                                                    data-id="{{ $value['id'] }}"
                                                    data-type="{{ $value['primarySkill']['name'] ?? '' }}"
                                                    data-mob="{{ $value['mobile_no'] }}"
                                                    data-minCharge="{{ $value['is_pandit_min_charge'] }}"
                                                    data-maxCharge="{{ $value['is_pandit_max_charge'] }}"
                                                    data-pooja="{{ $value['is_pandit_pooja_per_day'] }}"
                                                    data-city="{{ $value['city'] }}"
                                                    data-experience="{{ $value['experience'] }}"
                                                   data-status="{{ $value->is_online ? 'live' : 'offline' }}" onclick="detailModal(this)"
                                                    class="text-black">
                                                    {{ @ucwords($value->name) }}
                                                </a>

                                                <!-- badge: aage (after) link -->
                                                @if($value->is_live)
                                                    <span class="pandit-status-badge badge-live">Live</span>
                                                    <span class="pandit-status-badge badge-live">Online</span>
                                                @elseif($value->is_online)
                                                    <span class="pandit-status-badge badge-online">Online</span>
                                                @else
                                                    <span class="pandit-status-badge badge-offline">Offline</span>
                                                @endif
                                                <br><b>{{ $value->email }}</b> <br> {{ $value->mobile_no }}
                                            </td>
                                            <td>₹{{ number_format($value->total_earning, 2) }}</td>

                                            <td class="d-flex gap-2">
                                                <a class="btn btn-outline-info btn-sm square-btn"
                                                    id="view-anchor{{ $value['id'] }}" title="{{ translate('view') }}"
                                                    href="{{ route('admin.astrologers.manage.detail.overview', $value['id']) }}">
                                                    <i class="tio-invisible"></i>
                                                </a>
                                                <a class="btn btn-outline-primary btn-sm square-btn" title="{{ translate('add_package') }}"
                                                    href="{{ route('admin.astrologers.manage.astrologers', $value['id']) }}">
                                                    <i class="tio-add"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            @if (!request()->has('search_type') && !request()->has('search_name') && !request()->has('search_service_type'))
                                {{ $astrolist->links() }}
                            @endif
                        </div>
                    </div>
                    @if (count($astrolist) == 0)
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
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

    {{-- reject astrologer --}}
    {{-- <script>
        $('.reject-astrologer').on('click', function() {
            let astrologerId = $(this).attr("data-id");
            Swal.fire({
                title: 'Are You Sure To Reject Astrologer',
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + astrologerId).submit();
                }
            });
        });
    </script> --}}

    {{-- block astrologer --}}
    {{-- <script>
        $('.block-astrologer').on('click', function() {
            let astrologerId = $(this).attr("data-id");
            Swal.fire({
                title: 'Are You Sure To Block Astrologer',
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + astrologerId).submit();
                }
            });
        });
    </script> --}}

    {{-- delete astrologer --}}
    {{-- <script>
        $('.delete-astrologer').on('click', function() {
            let astrologerId = $(this).attr("data-id");
            Swal.fire({
                title: 'Are You Sure To Delete Astrologer',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + astrologerId).submit();
                }
            });
        });
    </script> --}}

    {{-- detail modal --}}
    <script>
        function detailModal(that) {
            $('#detailTB').html('');
            var id = $(that).data('id');
            $('#modal-id').val(id);
            var name = $(that).data('name');
            var type = $(that).data('type');
            var mob = $(that).data('mob');
            var minCharge = $(that).data('mincharge');
            var maxCharge = $(that).data('maxcharge');
            var pooja = $(that).data('pooja');
            var city = $(that).data('city');
            var experience = $(that).data('experience');
            var chargeRow = type === 'Pandit' ?
                `<tr><td>Charge (Min to Max)</td><td>₹${minCharge} to ₹${maxCharge}</td></tr><tr><td>Pooja Per Day</td><td>${pooja}</td></tr>` :
                '';

            var list =
                `<tr><td>Name</td><td>${name}</td></tr>
                <tr><td>Type</td><td>${type}</td></tr>
                <tr><td>Mobile No.</td><td>${mob}</td></tr>
                ${chargeRow}
                <tr><td>City</td><td>${city}</td></tr>
                <tr><td>Experience</td><td>${experience} years</td></tr>`;

            $('#detailTB').append(list);

            $('#detail-modal').modal('show');
        }
    </script>

    {{-- view button click --}}
    <script>
        function viewButton() {
            var id = $('#modal-id').val();
            var url = '{{ route('admin.astrologers.manage.detail.overview', ':id') }}';
            url = url.replace(':id', id);
            window.location.href = url;
        }
    </script>

    {{-- filter --}}
    <script>
        function filter(value) {

            $('#' + value).submit();
        }
    </script>
@endpush