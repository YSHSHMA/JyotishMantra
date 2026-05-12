@extends('layouts.back-end.app')

@section('title', translate('fastfestival_List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/fastfestival.png') }}" alt="">
                {{ translate('fastfestival_List') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ $fastfestivals->total() }}</span>
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
                            <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                                <!-- <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                    <i class="tio-download-to"></i>
                                    {{ translate('export') }}
                                    <i class="tio-chevron-down"></i>
                                </button> -->
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.fastfestival.export', ['searchValue'=>request('searchValue')]) }}">
                                            <img width="14" src="{{ dynamicAsset(path: 'public/assets/back-end/img/excel.png') }}" alt="">
                                            {{ translate('excel') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('event_name') }}</th>
                                    <th>{{ translate('event_type') }}</th>
                                    <th>{{ translate('en_description') }}</th>
                                    <th>{{ translate('hi_description') }}</th>
                                    <th>{{ translate('image') }}</th>
                                    <th class="text-center">{{ translate('status') }}</th>
                                    <th class="text-center"> {{ translate('action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($fastfestivals as $key => $fastfestival)
                                    <tr>
                                         <td>{{ $fastfestivals->firstItem() + $key }}</td>
                                        <td>{{ $fastfestival->event_name }}</td>
                                        <td>{{ $fastfestival->event_type }}</td>
                                        <td>{{ $fastfestival->en_description }}</td>
                                        <td>{{ $fastfestival->hi_description }}</td>
                                      

                                        <td>
                                            <div class="avatar-60 d-flex align-items-center rounded">
                                                <img class="img-fluid" alt=""
                                                     src="{{ getValidImage(path: 'storage/app/public/fastfestival-img/'.$fastfestival['image'], type: 'backend-fastfestival') }}">
                                            </div>
                                        </td>
                                        <td>
                                            <form action="{{route('admin.fastfestival.status-update') }}" method="post" id="fastfestival-status{{$fastfestival['id']}}-form">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$fastfestival['id']}}">
                                                <label class="switcher mx-auto">
                                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                           id="fastfestival-status{{ $fastfestival['id'] }}" value="1" {{ $fastfestival['status'] == 1 ? 'checked' : '' }}
                                                           data-modal-id = "toggle-status-modal"
                                                           data-toggle-id = "fastfestival-status{{ $fastfestival['id'] }}"
                                                           data-on-image = "fastfestival-status-on.png"
                                                           data-off-image = "fastfestival-status-off.png"
                                                           data-on-title = "{{ translate('Want_to_Turn_ON').' '.$fastfestival['defaultname'].' '. translate('status') }}"
                                                           data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$fastfestival['defaultname'].' '.translate('status') }}"
                                                           data-on-message = "<p>{{ translate('if_enabled_this_fastfestival_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                           data-off-message = "<p>{{ translate('if_disabled_this_fastfestival_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                    href="{{ route('admin.fastfestival.update', [$fastfestival['id']]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="fastfestival-{{$fastfestival['id']}}"
                                                title="{{ translate('delete')}}">
                                                <i class="tio-delete"></i>
                                            </a>
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
                            {{ $fastfestivals->links() }}
                        </div>
                    </div>
                    @if(count($fastfestivals)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <span id="route-admin-fastfestival-delete" data-url="{{ route('admin.fastfestival.delete') }}"></span>
    <span id="route-admin-fastfestival-status-update" data-url="{{ route('admin.fastfestival.status-update') }}"></span>
    <span id="get-fastfestivals" data-fastfestivals="{{ json_encode($fastfestivals) }}"></span>
    <div class="modal fade" id="select-fastfestival-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i
                            class="tio-clear"></i></button>
                </div>
                <div class="modal-body px-4 px-sm-5 pt-0 pb-sm-5">
                    <div class="d-flex flex-column align-items-center text-center gap-2 mb-2">
                        <div
                            class="toggle-modal-img-box d-flex flex-column justify-content-center align-items-center mb-3 position-relative">
                            <img src="{{dynamicAsset('public/assets/back-end/img/icons/info.svg')}}" alt="" width="90"/>
                        </div>
                        <h5 class="modal-title mb-2 fastfestival-title-message"></h5>
                    </div>
                    <form action="{{ route('admin.fastfestival.delete') }}" method="post" class="product-fastfestival-update-form-submit">
                        @csrf
                        <input name="id" hidden="">
                        <div class="gap-2 mb-3">
                            <label class="title-color"
                                   for="exampleFormControlSelect1">{{ translate('select_Category') }}
                                <span class="text-danger">*</span>
                            </label>
                            <select name="fastfestival_id" class="form-control js-select2-custom fastfestival-option" required>

                            </select>
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="submit" class="btn btn--primary min-w-120">{{translate('update')}}</button>
                            <button type="button" class="btn btn-danger-light min-w-120"
                                    data-dismiss="modal">{{ translate('cancel') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush
