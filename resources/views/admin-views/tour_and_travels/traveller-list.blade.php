@php
    use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('Travels_list'))

@section('content')
 <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
                {{ translate('Travels_list') }}
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
                                    <a href="{{route('admin.tour_and_travels.add-traveller')}}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ translate('Add_Traveller') }}</span>
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
                                    <th>{{ translate('Travels_ID') }}</th>
                                    <th class="max-width-100px">{{ translate('Owner_name') }}</th>
                                    <th class="max-width-100px">{{ translate('Company_Name') }}</th>
                                    <th class="text-center">{{ translate('Phone_number') }}</th>
                                    @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'status'))
                                    <th class="text-center">{{ translate('status') }}</th>
                                    @endif
                                    <th class="text-center">{{ translate('order_cancellation_count') }}</th>
                                    <th class="text-center">{{ translate('Approval_status') }}</th>
                                    @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'edit') || Helpers::modules_permission_check('Tour', 'Travel Agent', 'delete'))
                                    <th class="text-center"> {{ translate('action') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($getDatalist as $key => $item)
                                <tr>
                                    <td>{{ $getDatalist->firstItem()+$key }}</td>
                                    <td> <a class="font-weight-bold text-secondary" href="{{ route('admin.tour_and_travels.information',[$item['id']])}}">{{ $item['traveller_id']??"" }}</a> </td>
                                    <td>  {{ $item['owner_name']??"" }}  </td>
                                    <td>  {{ Str::limit(($item['company_name']??""), 20) }}  </td>
                                    <td>  {{ $item['phone_no']??"" }}  </td>
                                    @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'status'))
                                    <td>
                                        <form action="{{route('admin.tour_and_travels.status-update') }}" method="post" id="temple-status{{$item['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$item['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                       id="temple-status{{ $item['id'] }}" value="1" {{ $item['status'] == 1 ? 'checked' : '' }}
                                                       data-modal-id = "toggle-status-modal"
                                                       data-toggle-id = "temple-status{{ $item['id'] }}"
                                                       data-on-title = "{{ translate('Want_to_Turn_ON').' '.$item['company_name'].' '. translate('status') }}"
                                                       data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$item['company_name'].' '.translate('status') }}"
                                                       data-on-message = "<p>{{ translate('if_enabled_this_Travels_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                       data-off-message = "<p>{{ translate('if_disabled_this_Travels_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endif
                                    <td>  {{ $item['cancel_order']??"" }}  </td>
                                    <td> <span class="badge badge-soft-{{ (($item['is_approve'] == 1)?'success':(($item['is_approve'] == 2)?'danger':'warning') )}}">{{ (($item['is_approve'] == 1)?'Approve':(($item['is_approve'] == 2)?'suspended':(($item['is_approve'] == 3)?'Hold':'Pending')) )}}</span></td>
                                    @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'edit') || Helpers::modules_permission_check('Tour', 'Travel Agent', 'delete'))
                                    <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                    href="{{ route('admin.tour_and_travels.update', [$item['id']]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="tourtravellers-{{$item['id']}}"  title="{{ translate('delete')}}"><i class="tio-delete"></i>
                                                </a>
                                                <form action="{{ route('admin.tour_and_travels.traveller-delete',[$item['id']]) }}" method="post" id="tourtravellers-{{ $item['id']}}">
                                                    @csrf @method('delete')
                                                </form>
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
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush
