@extends('layouts.back-end.app-tour')
@section('title', translate('tour-list'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .btn-tour-visit-empty {
        animation: pulse-danger 1s infinite;
        border-color: red;
        color: red;
    }

    @keyframes pulse-danger {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(255, 0, 0, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
        }
    }
</style>
@endpush

@section('content')

<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ translate('Tour_list') }}
            <span class="badge badge-soft-dark radius-50 fz-14"></span>
        </h2>
    </div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
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
                        <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                            <a href="{{route('tour-vendor.tour_visits.add-tour')}}" class="btn btn--primary">
                                <i class="tio-add"></i>
                                <span class="text">{{ translate('Add_Tour_visit') }}</span>
                            </a>

                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>#{{ translate('ID') }}</th>
                                    <th>{{ translate('type') }}</th>
                                    <th>{{ translate('tour_variant') }}</th>
                                    <th class="max-width-100px">{{ translate('tour_name') }}</th>
                                    <th class="text-center">{{ translate('status') }}</th>
                                    <th class="text-center">{{ translate('live_status') }}</th>
                                    <th class="text-center">{{ translate('create_by') }}</th>
                                    <th class="text-center"> {{ translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getDatalist as $key => $item)
                                <tr>
                                    <td>
                                        {{--<input type="checkbox" class="accept_tour" data-id="{{ (($item['id']??'')) }}" value="{{ (($item['id']??'')) }}" {{ (($item['accept_type'] == 1)?'checked':'') }}>&nbsp; --}}
                                        {{ $getDatalist->firstItem()+$key }}
                                    </td>
                                    <td> <a class="font-weight-bold text-secondary" href="{{ route('tour-vendor.tour_visits.overview',[$item['id']])}}">#{{ $item['tour_id']??"" }}</a> </td>
                                    <td> {{ translate($item['tour_type']??"") }} </td>
                                    <td>
                                        @if($item['use_date'] == 1)
                                        Special Tour(With Date)
                                        @elseif($item['use_date'] == 2)
                                        Daily Tour(With Address)
                                        @elseif($item['use_date'] == 3)
                                        Daily Tour(WithOut Address)
                                        @elseif($item['use_date'] == 4)
                                        Special Tour(Without Date)
                                        @else
                                        Cities Tour
                                        @endif </td>
                                    <td> <span data-toggle="tooltip" data-title="{{ (($item['tour_name']??'')) }}">{{ Str::limit(($item['tour_name']??""),20) }}</span></td>
                                    <td>
                                        @if(auth('tour')->user()->status == 'approved')
                                        <form action="{{route('tour-vendor.tour_visits.accept-tour') }}" method="post" id="temple-status{{$item['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="tour_id" value="{{$item['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message1 accept_tour" name="status" data-id="{{ (($item['id']??'')) }}"
                                                    id="temple-status{{ $item['id'] }}" value="1" {{ $item['accept_type'] == 1 ? 'checked' : '' }}
                                                    data-modal-id="toggle-status-modal-tour-order"
                                                    data-toggle-id="temple-status{{ $item['id'] }}"
                                                    data-on-title="{{ translate('Want_to_Turn_ON').' '. translate('status') }}"
                                                    data-off-title="{{ translate('Want_to_Turn_OFF').' '.translate('status') }}"
                                                    data-on-message="<p>{{ translate('if_enabled_this_Booking_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                    data-off-message="<p>{{ translate('if_disabled_this_Booking_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                        @else
                                        <span class="badge badge-soft-{{ auth('tour')->user()->status == 'suspended' ?'danger':'warning' }}">{{ ucwords(auth('tour')->user()->status??'') }}</span>
                                        @endif

                                    </td>
                                    <td><span class="badge badge-soft-{{ ($item['status'] == 1) ?'success':'danger' }}">{{ ($item['status'] == 1) ? 'live' : 'off' }}</span></td>
                                    <td>
                                      <span class="font-weight-bolder">  {{ (($item['created_id'] == 0)?"Admin":"Vendor") }}</span><br>
                                       <span>Commission: {{ ($item['tour_commission'] ?? 0)}}%</span><br>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn {{ ((!\App\Models\TourVisitPlace::where('tour_visit_id', $item['id'])->where('status', 1)->exists()) ? 'btn-tour-visit-empty' : '') }}" title="{{ translate('edit') }}"
                                                href="{{ route('tour-vendor.tour_visits.add-visit', [$item['id']]) }}">
                                                <i class="tio-boot_open">boot_open</i>
                                            </a>
                                            @if($item['created_id'] == auth('tour')->user()->relation_id)
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                href="{{ route('tour-vendor.tour_visits.update', [$item['id']]) }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="tourtravellers-{{$item['id']}}" title="{{ translate('delete')}}"><i class="tio-delete"></i>
                                            </a>
                                            <form action="{{ route('tour-vendor.tour_visits.tour-delete',[$item['id']]) }}" method="post" id="tourtravellers-{{ $item['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                            @else
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('view') }}"
                                                href="{{ route('tour-vendor.tour_visits.view', [$item['id']]) }}">
                                                <i class="tio-visible"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {{ $getDatalist->links() }}
                    </div>
                </div>

                @if(count($getDatalist)==0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-center modal_order_view" role="dialog" aria-label="modal order">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><i class="tio-clear" aria-hidden="true"></i></button>
                <h4 class="modal-title">Booking cancel</h4>
                <div class="form-group view_orders_items">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="toggle-status-modal-tour-order" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                <button type="button" class="btn-close border-0" data-dismiss="modal" aria-label="Close"><i class="tio-clear"></i></button>
            </div>
            <div class="modal-body px-4 px-sm-5 pt-0">
                <div class="d-flex flex-column align-items-center text-center gap-2 mb-2">
                    <div class="toggle-modal-img-box d-flex flex-column justify-content-center align-items-center mb-3 position-relative">
                        <img src="" class="status-icon" alt="" width="30" />
                        <img src="" id="toggle-status-modal-tour-order-image" alt="" />
                    </div>
                    <h5 class="modal-title" id="toggle-status-modal-tour-order-title"></h5>

                    <div class="text-center" id="toggle-status-modal-tour-order-message"></div>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn--primary min-w-120" id="toggle-status-modal-tour-order-ok-button-tour-order" data-dismiss="modal">Ok</button>
                    <button type="button" class="btn btn-danger-light min-w-120" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script>
    $('.toggle-switch-message1').on('click', function(event) {
        event.preventDefault();
        let rootPath = $('#get-root-path-for-toggle-modal-image').data('path');
        const modalId = $(this).data('modal-id')
        const toggleId = $(this).data('toggle-id');
        const onImage = rootPath + '/' + $(this).data('on-image');
        const offImage = rootPath + '/' + $(this).data('off-image');
        const onTitle = $(this).data('on-title');
        const offTitle = $(this).data('off-title');
        const onMessage = $(this).data('on-message');
        const offMessage = $(this).data('off-message');
        toggleModal1(modalId, toggleId, onImage, offImage, onTitle, offTitle, onMessage, offMessage)
    });

    function toggleModal1(modalId, toggleId, onImage = null, offImage = null, onTitle, offTitle, onMessage, offMessage) {
        if ($('#' + toggleId).is(':checked')) {
            $('#' + modalId + '-title').empty().append(onTitle);
            $('#' + modalId + '-message').empty().append(onMessage);
            $('#' + modalId + '-image').attr('src', onImage);
            $('#' + modalId + '-ok-button-tour-order').attr('toggle-ok-button', toggleId);
        } else {
            $('#' + modalId + '-title').empty().append(offTitle);
            $('#' + modalId + '-message').empty().append(offMessage);
            $('#' + modalId + '-image').attr('src', offImage);
            $('#' + modalId + '-ok-button-tour-order').attr('toggle-ok-button', toggleId);
        }
        $('#' + modalId).modal('show');
    }

    $('#toggle-status-modal-tour-order-ok-button-tour-order').on('click', function() {
        const toggleId = $('#' + $(this).attr('toggle-ok-button'));
        console.log($(this));
        if (toggleId.is(':checked')) {
            toggleId.prop('checked', false);
        } else {
            toggleId.prop('checked', true);
        }
        let toggleOkButton = $(this).attr('toggle-ok-button') + '-form';
        submitStatusUpdateForm1(toggleOkButton, this);
    });

    function submitStatusUpdateForm1(formId, that) {
        const form = $('#' + formId);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            success: function(data) {
                if (data.success == 1) {
                    toastr.success(data.message);
                    location.reload();
                } else {
                    const toggleId = $('#' + $(that).attr('toggle-ok-button'));
                    if (toggleId.is(':checked')) {
                        toggleId.prop('checked', false);
                    } else {
                        toggleId.prop('checked', true);
                    }
                    toastr.error(data.message);
                    location.reload();
                }
            }
        })
    }
    // $(".accept_tour").click(function() {
    //     let status = 0;
    //     if ($(this).is(":checked")) {
    //         status = 1;
    //     }
    //     let tour_id = $(this).val();
    //     let confirmationMessage = status 
    //     ? "Are you sure you want to accept this tour?" 
    //     : "Are you sure you want to unaccept this tour?";

    // if (confirm(confirmationMessage)) {
    //     $.ajax({
    //         url:"{{ route('tour-vendor.tour_visits.accept-tour')}}",
    //         data:{status,tour_id,_token: '{{ csrf_token() }}'},

    //         dataType:"json",
    //         type:"post",
    //         success:function(data){
    //             var status = data.status;
    //             if(status == 0){
    //                 toastr.error(data.message);
    //                 window.location.href=``;
    //             }else{
    //                 toastr.success(data.message);
    //             }
    //         }
    //     })
    // }else{
    //     $(this).prop("checked", !status);
    // }
    // })
</script>
@endpush