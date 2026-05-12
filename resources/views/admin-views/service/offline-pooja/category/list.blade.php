@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('offline_Pooja_Category_List'))

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
                        {{ translate('offline_Pooja_Category') }}

                        </span>
                    </h2>
                </div>
            </div>
        </div>

        @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Category', 'add'))
        <div class="row mt-20">
            <div class="col-md-12">
                <form class="product-form text-start"
                    action="{{ route('admin.service.offline.pooja.category.add-new') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="px-4 pt-3">
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach ($languages as $lang)
                                    <li class="nav-item">
                                        <span
                                            class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer"
                                            id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div>
                                        @foreach ($languages as $lang)
                                            <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                                                id="{{ $lang }}-form">
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label
                                                            class="title-color"for="{{ $lang }}_name">{{ translate('name') }}
                                                            ({{ strtoupper($lang) }})
                                                        </label>
                                                        <input type="text" name="name[]" id="{{ $lang }}_name" class="form-control" placeholder="{{translate('enter_category_name')}}" {{ $lang == $defaultLanguage ? 'required' : 'required' }}></input>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="from_part_2">
                                        <label class="title-color">{{ translate('category_Logo') }}</label>
                                        <span class="text-info"><span class="text-danger">*</span> {{ THEME_RATIO[theme_root_path()]['Category Image'] }}</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="category-image"
                                                   class="custom-file-input image-preview-before-upload"
                                                   data-preview="#viewer"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                   required>
                                            <label class="custom-file-label"
                                                   for="category-image">{{ translate('choose_File') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-4 mt-lg-0 from_part_2">
                                    <div class="form-group">
                                        <div class="text-center mx-auto">
                                            <img class="upload-img-view" id="viewer" alt=""
                                                 src="{{ dynamicAsset(path: 'public/assets/back-end/img/image-place-holder.png') }}">
                                        </div>
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
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                {{-- <form action="{{ url()->current() }}" method="GET">
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
                                </form> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="myTable"
                                class="display table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('image') }}</th>
                                        <th>{{ translate('name') }}</th>
                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Category', 'status'))
                                        <th class="text-center">{{ translate('status') }}</th>
                                        @endif
                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Category', 'edit'))
                                        <th class="text-center"> {{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($offlinePoojaCategory as $key => $item)
                                        <tr>
                                            <td>{{ $offlinePoojaCategory->firstItem() + $key }}</td>
                                            <td class="">
                                                    <img class="img-fluid" alt=""
                                                         src="{{ getValidImage(path: 'storage/app/public/offlinepooja/category/'.$item['image'], type: 'backend-category') }}" width="50">
                                            </td>
                                            <td>
                                                <span class="media-body title-color hover-c1">
                                                    {{ $item['name'] }}
                                                </span>
                                            </td>
                                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Category', 'status'))
                                            <td>
                                                <form
                                                    action="{{ route('admin.service.offline.pooja.category.status-update') }}"
                                                    method="post" id="service-status{{ $item['id'] }}-form">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                    <label class="switcher mx-auto">
                                                        <input type="checkbox"
                                                            class="switcher_input toggle-switch-message" name="status"
                                                            id="service-status{{ $item['id'] }}" value="1"
                                                            {{ $item['status'] == 1 ? 'checked' : '' }}
                                                            data-modal-id = "toggle-status-modal"
                                                            data-toggle-id = "service-status{{ $item['id'] }}"
                                                            data-on-image = "service-status-on.png"
                                                            data-off-image = "service-status-off.png"
                                                            data-on-title = "{{ translate('Want_to_Turn_ON') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                            data-off-title = "{{ translate('Want_to_Turn_OFF') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                            data-on-message = "<p>{{ translate('if_enabled_this_rashi_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                            data-off-message = "<p>{{ translate('if_disabled_this_rashi_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </form>
                                            </td>
                                            @endif

                                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Category', 'edit'))
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('edit') }}"
                                                        href="{{ route('admin.service.offline.pooja.category.update', [$item['id']]) }}">
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
                            {{ $offlinePoojaCategory->links() }}
                        </div>
                    </div>

                    @if (count($offlinePoojaCategory) == 0)
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
    <span id="route-admin-rashi-status-update"
        data-url="{{ route('admin.service.offline.pooja.category.status-update') }}"></span>
    <!-- Modal Structure -->
    <div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel"
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
    </div>
@endsection

@push('script')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>
@endpush
