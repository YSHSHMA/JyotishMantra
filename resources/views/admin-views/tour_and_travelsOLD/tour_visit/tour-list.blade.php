@extends('layouts.back-end.app')

@section('title', translate('Tour_list'))

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
                            <a href="{{route('admin.tour_visits.add-tour')}}" class="btn btn--primary">
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
                                    <th class="max-width-100px">{{ translate('tour_name') }}</th>
                                    <th class="max-width-100px">{{ translate('description') }}</th>
                                    <th class="text-center">{{ translate('status') }}</th>
                                    <th class="text-center"> {{ translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getDatalist as $key => $item)
                                <tr>
                                    <td>{{ $getDatalist->firstItem()+$key }}</td>
                                    <td> <a class="font-weight-bold text-secondary" href="{{ route('admin.tour_visits.overview',[$item['id']])}}">#{{ $item['tour_id']??"" }}</a> </td>
                                    <td> {{ translate($item['tour_type']??"") }} </td>
                                    <td> {{ $item['tour_name']??"" }} </td>
                                    <td> {{ Str::limit(strip_tags($item['description']??""), 40) }} </td>
                                    <td>
                                        <form action="{{route('admin.tour_visits.status-update') }}" method="post" id="temple-status{{$item['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$item['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                    id="temple-status{{ $item['id'] }}" value="1" {{ $item['status'] == 1 ? 'checked' : '' }}
                                                    data-modal-id="toggle-status-modal"
                                                    data-toggle-id="temple-status{{ $item['id'] }}"
                                                    data-on-title="{{ translate('Want_to_Turn_ON').' '.$item['name'].' '. translate('status') }}"
                                                    data-off-title="{{ translate('Want_to_Turn_OFF').' '.$item['name'].' '.translate('status') }}"
                                                    data-on-message="<p>{{ translate('if_enabled_this_tour_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                    data-off-message="<p>{{ translate('if_disabled_this_tour_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline- btn-sm square-btn" title="{{ translate('edit') }}"
                                                href="{{ route('admin.tour_visits.add-visit', [$item['id']]) }}">
                                                <i class="tio-boot_open">boot_open</i>
                                            </a>
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                href="{{ route('admin.tour_visits.update', [$item['id']]) }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="tourtravellers-{{$item['id']}}" title="{{ translate('delete')}}"><i class="tio-delete"></i>
                                            </a>
                                            <form action="{{ route('admin.tour_visits.tour-delete',[$item['id']]) }}" method="post" id="tourtravellers-{{ $item['id']}}">
                                                @csrf @method('delete')
                                            </form>
@if($item['tour_type'] == 'special_tour')
                                            <a class="btn btn-sm btn-outline-primary" onclick="booking_cancel(`{{$item['id']}}`)"><i class="tio-autorenew"></i></a>
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

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script>
    function booking_cancel(id) {
        $.ajax({
            url: "{{ route('admin.tour_visits.company-booking-order-get')}}",
            data: {
                id,
                _token: '{{ csrf_token() }}'
            },
            dataType: "json",
            type: "post",
            success: function(data) {
                if (data.status == 1) {
                    $(".modal_order_view").modal('show');
                    var html = '';

                    html += `
                    <form action="{{ route('admin.tour_visits.company-booking-settlement')}}" method="post">
                    <div class="row">
                    @csrf
                        <div class="col-md-6 mt-3">
                        <input type="hidden" name="tour_id" value="${id}">
                            <label for="">Select type</label>
                        </div>
                        <div class="col-md-6 mt-3">
                            <select name="type" id="" class="form-control" onchange="
    if (this.value == '2') {
        $('.transfor_cab_data').removeClass('d-none');
    } else {
        $('.transfor_cab_data').addClass('d-none');
    }">
                                <option value="1">All Refund</option>
                                <option value="2">Cab Transfer</option>
                                <option value="3">Cab Refund</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3 d-none transfor_cab_data">
                            <label for="">company name</label>
                        </div>
                        <div class="col-md-6 mt-3 d-none transfor_cab_data">
                            <select name="transfor_cab" class="form-control">`;
                 if (data.data.company_all) {
                                $.each(data.data.company_all, function(index, value) {
                                    html +=`<option value="${value['id']}">${value['company_name']}</option>`;
                        })
                }
                           html +=` </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="">company name</label>
                        </div>
                        <div class="col-md-6 mt-3">
                            <select name="cab_id" class="form-control">`;
                 if (data.data.company) {
                                $.each(data.data.company, function(index, value) {
                                    html +=`<option value="${value['cab_assign']}">${(value?.company?.company_name ?? '')} || ${(value?.amount ?? '')} || ${(value?.qty ?? '')}</option>`;
                        })
                }
                           html +=` </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="">user name</label>
                        </div>
                        <div class="col-md-6 mt-3">
                            <select name="order_id[]" multiple class="form-control">
                            <option value=""></option>`;
                 if (data.data.order_list) {
                                $.each(data.data.order_list, function(index, value) {
                                    html +=`<option value="${value['id']}">${(value?.user_data?.name ?? '')} || ${(value?.company?.company_name ?? '')} || ${(value?.amount ?? '')} || ${(value?.qty ?? '')}</option>`;
                        })
                }
                           html +=`</select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <input type="submit" class="btn btn--primary">
                        </div>
                        </div>
                        </form>
                    `;

                    $(".view_orders_items").html(html);
                    $('select[name="order_id[]"]').select2();
                } else {
                    toastr.error('Order booking is not available');
                }
                console.log(data);
            }
        })

    }
</script>
@endpush