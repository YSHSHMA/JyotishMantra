@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('review_detail'))

@section('content')
    {{-- pooja review modal --}}
    <div class="modal fade" id="user-review-model" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Reviews</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Order Id</th>
                                <th>Rate</th>
                                <th>Review</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody id="userReviewTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- offline pooja review modal --}}
    <div class="modal fade" id="offlinepooja-user-review-model" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Offline Pooja User Reviews</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Order Id</th>
                                <th>Rate</th>
                                <th>Review</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody id="offlinepoojaUserReviewTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
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
                {{ translate('review_detail') }}
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
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.setting', $id) }}">Setting</a>
                        </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'transaction'))
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.transaction', $id) }}">Transaction</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.transaction.history', $id) }}">Transaction History</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'review'))
                        <li class="nav-item">
                            <a class="nav-link active"
                                href="{{ route('admin.astrologers.manage.detail.review', $id) }}">Review</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.history', $id) }}">History</a>
                        </li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item col-6" role="presentation">
                                <button class="nav-link w-100 active" id="pooja-tab"
                                    data-toggle="pill" data-target="#pooja" type="button"
                                    role="tab" aria-controls="pooja"
                                    aria-selected="true">Pooja
                                    </button>
                            </li>

                            <li class="nav-item col-6" role="presentation">
                                <button class="nav-link w-100" id="offlinepooja-tab"
                                    data-toggle="pill" data-target="#offlinepooja" type="button"
                                    role="tab" aria-controls="offlinepooja"
                                    aria-selected="true">Offline Pooja
                                    </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content mt-5">
                    <div class="tab-pane fade show active" id="pooja" role="tabpanel" aria-labelledby="pooja-tab">
                        <div class="row pt-2">
                            <div class="col-md-12">
                                <div class="card w-100">
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100 text-start">
                                                    <thead class="thead-light thead-50 text-capitalize">
                                                        <tr>
                                                            <th>{{ translate('#') }}</th>
                                                            <th>{{ translate('Service Name') }}</th>
                                                            <th>{{ translate('service_Type') }}</th>
                                                            <th>{{ translate('Rate') }}</th>
                                                            @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'review-detail'))
                                                            <th>{{ translate('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($reviews as $key => $review)
                                                            <tr>
                                                                <td>{{ $key + 1 }}</td>
                                                                <td>
                                                                    @if ($review['service_type'] == 'pooja')
                                                                        {{ $review['services']['name'] }}
                                                                    @elseif ($review['service_type'] == 'vip')
                                                                        {{ $review['vippoojas']['name'] }}
                                                                    @elseif ($review['service_type'] == 'anushthan')
                                                                        {{ $review['vippoojas']['name'] }}
                                                                    @elseif ($review['service_type'] == 'chadhava')
                                                                        {{ $review['chadhava']['name'] }}
                                                                    @endif    
                                                                </td>
                                                                <td>{{ $review['service_type'] }}</td>
                                                                <td>{{ $review['total_rate'] / $review['total_count'] }}
                                                                </td>
                                                                @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'seting-update'))
                                                                <td>
                                                                    <a href="javascript:0"
                                                                        data-serviceid="{{ $review['service_id'] }}"
                                                                        data-astroid="{{ $review['astro_id'] }}"
                                                                        onclick="reviewModal(this)">
                                                                        <i class="tio-invisible"></i>
                                                                    </a>
                                                                </td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            {{-- <div class="table-responsive mt-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    @if (!request()->has('search_type') && !request()->has('search_name') && !request()->has('search_service_type'))
                                                        {{ $reviews->links() }}
                                                    @endif
                                                </div>
                                            </div> --}}
                                            @if (count($reviews) == 0)
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
                        </div>
                    </div>
                    <div class="tab-pane fade show " id="offlinepooja" role="tabpanel" aria-labelledby="offlinepooja-tab">
                        <div class="row pt-2">
                            <div class="col-md-12">
                                <div class="card w-100">
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100 text-start">
                                                    <thead class="thead-light thead-50 text-capitalize">
                                                        <tr>
                                                            <th>{{ translate('#') }}</th>
                                                            <th>{{ translate('Service Name') }}</th>
                                                            <th>{{ translate('Rate') }}</th>
                                                            @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'review-detail'))
                                                            <th class="text-center">{{ translate('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($offlinepoojaReviews as $key => $offlinepoojaReview)
                                                            <tr>
                                                                <td>{{ $key + 1 }}</td>
                                                                <td>{{ $offlinepoojaReview['offlinePooja']['name'] }}</td>
                                                                <td>{{ $offlinepoojaReview['total_rate'] / $offlinepoojaReview['total_count'] }}
                                                                </td>
                                                                @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'seting-update'))
                                                                <td class="text-center">
                                                                    <a href="javascript:0"
                                                                        data-serviceid="{{ $offlinepoojaReview['service_id'] }}"
                                                                        data-astroid="{{ $offlinepoojaReview['astro_id'] }}"
                                                                        onclick="offlinepoojaReviewModal(this)">
                                                                        <i class="tio-invisible"></i>
                                                                    </a>
                                                                </td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            {{-- <div class="table-responsive mt-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    @if (!request()->has('search_type') && !request()->has('search_name') && !request()->has('search_service_type'))
                                                        {{ $offlinepoojaReviews->links() }}
                                                    @endif
                                                </div>
                                            </div> --}}
                                            @if (count($offlinepoojaReviews) == 0)
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

    {{-- review modal --}}
    <script>
        function reviewModal(that) {
            var serviceId = $(that).data('serviceid');
            var astroId = $(that).data('astroid');
            var list = "";
            
            $.ajax({
                type: "get",
                url: "{{ url('admin/astrologers/manage/user/review/list') }}" + '/pooja/' + serviceId + '/' + astroId,
                success: function(response) {
                    $('#userReviewTB').html('');
                    if (response.status == 200) {
                        $.each(response.data, function(key, value) {
                            list +=
                                `<tr><td>${value.users.f_name}</td><td>${value.order_id}</td><td>${value.rating}</td><td>${value.comment}</td><td><a href="{{url('admin/astrologers/manage/user/review/delete')}}/pooja/${value.id}"><i class="tio-delete text-danger"></i></a></td></tr>`;
                        });
                        $('#userReviewTB').append(list);
                        $('#user-review-model').modal('show');
                    } else {
                        alert('No Data Found');
                    }
                }
            });
        }
    </script>
    
    {{-- offline pooja review modal --}}
    <script>
        function offlinepoojaReviewModal(that) {
            var serviceId = $(that).data('serviceid');
            var astroId = $(that).data('astroid');
            var list = "";
            
            $.ajax({
                type: "get",
                url: "{{ url('admin/astrologers/manage/user/review/list') }}" + '/offlinepooja/' + serviceId + '/' + astroId,
                success: function(response) {
                    $('#offlinepoojaUserReviewTB').html('');
                    if (response.status == 200) {
                        $.each(response.data, function(key, value) {
                            list +=
                                `<tr><td>${value.users.f_name}</td><td>${value.order_id}</td><td>${value.rating}</td><td>${value.comment}</td><td><a href="{{url('admin/astrologers/manage/user/review/delete')}}/offlinepooja/${value.id}"><i class="tio-delete text-danger"></i></a></td></tr>`;
                        });
                        $('#offlinepoojaUserReviewTB').append(list);
                        $('#offlinepooja-user-review-model').modal('show');
                    } else {
                        alert('No Data Found');
                    }
                }
            });
        }
    </script>
@endpush