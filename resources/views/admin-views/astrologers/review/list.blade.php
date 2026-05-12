@extends('layouts.back-end.app')

@section('title', translate('astrologer_Review'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('astrologer_Review') }}
                {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $festivals->total() }}</span> --}}
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
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('User Profile') }}</th>
                                    <th>{{ translate('User Name') }}</th>
                                    <th>{{ translate('Astrologer') }}</th>
                                    <th>{{ translate('Date') }}</th>
                                    <th>{{ translate('Review') }}</th>
                                    <th>{{ translate('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><img src="{{url('public/assets/back-end/img/admin.jpg')}}" alt="" width="50px"></td>
                                        <td>Safal</td>
                                        <td>Safal</td>
                                        <td>09/02/2002</td>
                                        <td>test</td>
                                        <td>
                                            <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                            title="{{ translate('delete')}}">
                                            <i class="tio-delete"></i>
                                        </td>
                                    </tr>
                                {{-- @foreach($festivals as $key => $festival)
                                    <tr>
                                        <td>{{ $festivals->firstItem()+$key }}</td>
                                        <td>{{isset($festival->month) ? $festival->month->month : translate('month_not_found') }}

                                        </td>
                                        <td>{{ $festival->festival_date }}</td>

                                        <td>
                                            <div class="avatar-60 d-flex align-items-center rounded">
                                                <img class="img-fluid" alt=""
                                                     src="{{ getValidImage(path: 'storage/app/public/festival-img/'.$festival['festival_image'], type: 'backend-festival') }}">
                                            </div>
                                        </td>
                                        <td>{{ $festival->title }}</td>
                                        <td>{{ $festival->tithi }}</td>
                                        <td class="overflow-hidden max-width-100px">
                                            <span data-toggle="tooltip" data-placement="right" title="{{$festival['detail']}}">
                                                 {!! Str::limit($festival['detail'],20) !!}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{route('admin.festival.status-update') }}" method="post" id="festival-status{{$festival['id']}}-form">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$festival['id']}}">
                                                <label class="switcher mx-auto">
                                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                           id="festival-status{{ $festival['id'] }}" value="1" {{ $festival['status'] == 1 ? 'checked' : '' }}
                                                           data-modal-id = "toggle-status-modal"
                                                           data-toggle-id = "festival-status{{ $festival['id'] }}"
                                                           data-on-image = "festival-status-on.png"
                                                           data-off-image = "festival-status-off.png"
                                                           data-on-title = "{{ translate('Want_to_Turn_ON').' '.$festival['defaultname'].' '. translate('status') }}"
                                                           data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$festival['defaultname'].' '.translate('status') }}"
                                                           data-on-message = "<p>{{ translate('if_enabled_this_festival_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                           data-off-message = "<p>{{ translate('if_disabled_this_festival_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                    href="{{ route('admin.festival.update', [$festival['id']]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="festival-{{$festival['id']}}"
                                                title="{{ translate('delete')}}">
                                                <i class="tio-delete"></i>
                                            </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{-- {{ $festivals->links() }} --}}
                        </div>
                    </div>
                    {{-- @if(count($festivals)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
    {{-- <span id="route-admin-festival-delete" data-url="{{ route('admin.festival.delete') }}"></span> --}}
    {{-- <span id="route-admin-festival-status-update" data-url="{{ route('admin.festival.status-update') }}"></span> --}}
    {{-- <span id="get-festivals" data-festivals="{{ json_encode($festivals) }}"></span> --}}
    {{-- <div class="modal fade" id="select-festival-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
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
                        <h5 class="modal-title mb-2 festival-title-message"></h5>
                    </div>
                    <form action="{{ route('admin.festival.delete') }}" method="post" class="product-festival-update-form-submit">
                        @csrf
                        <input name="id" hidden="">
                        <div class="gap-2 mb-3">
                            <label class="title-color"
                                   for="exampleFormControlSelect1">{{ translate('select_Category') }}
                                <span class="text-danger">*</span>
                            </label>
                            <select name="festival_id" class="form-control js-select2-custom festival-option" required>

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
    </div> --}}
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush
