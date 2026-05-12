@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('temple_Category_list'))

@section('content')
 <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
                {{ translate('temple_Category_list') }}
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
                            @if (Helpers::modules_permission_check('Temple', 'Category', 'add'))
                              <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                             
                                    <a href="{{route('admin.temple.category.add')}}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ translate('add_new_Category') }}</span>
                                    </a>
                               
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('name') }}</th>
                                    @if (Helpers::modules_permission_check('Temple', 'Category', 'status'))
                                    <th class="text-center">{{ translate('status') }}</th>
                                    @endif
                                    @if (Helpers::modules_permission_check('Temple', 'Category', 'edit') || Helpers::modules_permission_check('Temple', 'Category', 'delete'))
                                    <th class="text-center"> {{ translate('action') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($list as $key => $item)
                                <tr>
                                    <td>{{ $list->firstItem()+$key }}</td>
                                    <td>
                                        <a class="media align-items-center gap-2">
                                            <img src="{{ getValidImage(path: 'storage/app/public/temple/category/'.$item['image'], type: 'backend-product') }}"
                                                 class="avatar border" alt="">
                                            <span class="media-body title-color hover-c1">
                                            {{ Str::limit($item['name'], 20) }}
                                        </span>
                                        </a>
                                    </td>
                                    @if (Helpers::modules_permission_check('Temple', 'Category', 'status'))
                                    <td>
                                        <form action="{{route('admin.temple.category.status-update') }}" method="post" id="temple-status{{$item['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$item['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                       id="temple-status{{ $item['id'] }}" value="1" {{ $item['status'] == 1 ? 'checked' : '' }}
                                                       data-modal-id = "toggle-status-modal"
                                                       data-toggle-id = "temple-status{{ $item['id'] }}"
                                                       data-on-image = "temple-status-on.png"
                                                       data-off-image = "temple-status-off.png"
                                                       data-on-title = "{{ translate('Want_to_Turn_ON').' '.$item['defaultname'].' '. translate('status') }}"
                                                       data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$item['defaultname'].' '.translate('status') }}"
                                                       data-on-message = "<p>{{ translate('if_enabled_this_Temple_category_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                       data-off-message = "<p>{{ translate('if_disabled_this_Temple_category_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endif

                                    @if (Helpers::modules_permission_check('Temple', 'Category', 'edit') || Helpers::modules_permission_check('Temple', 'Category', 'delete'))
                                    <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                
                                                @if (Helpers::modules_permission_check('Temple', 'Category', 'edit'))

                                                <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                    href="{{ route('admin.temple.category.update', [$item['id']]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                @endif
                                                @if (Helpers::modules_permission_check('Temple', 'Category', 'delete'))
                                                <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="temple-{{$item['id']}}"  title="{{ translate('delete')}}"><i class="tio-delete"></i>
                                                </a>
                                                <form action="{{ route('admin.temple.category.delete',[$item['id']]) }}"
                                                    method="post" id="temple-{{ $item['id']}}">
                                                    @csrf @method('delete')
                                                </form>
                                                @endif
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
                            {{ $list->links() }}
                        </div>
                    </div>
                   
                    @if(count($list)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                 
                </div>
            </div>
        </div>
    </div>
    <!-- <span id="route-admin-rashi-delete" data-url=""></span> -->
    <span id="route-admin-rashi-status-update" data-url="{{ route('admin.temple.status-update') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush
