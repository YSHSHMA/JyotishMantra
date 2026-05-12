@extends('layouts.back-end.app-trustees')
@php use App\Utils\Helpers; @endphp

@section('title', translate('temple_list'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
                {{ translate('temple_list') }}
                <span class="badge badge-soft-dark radius-50 fz-14"></span>
            </h2>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#addPanditModal">
                Add Pandit
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addPanditModal" tabindex="-1" role="dialog" aria-labelledby="addPanditModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="addPanditModalLabel">Add Pandit Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="#" method="POST" enctype="multipart/form-data">
                     
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="text-bold">{{ translate('Select Temple') }} *</label>
                                <select class="form-control" id="temple" name="temple_id" required>
                                    <option value="">-- Select Temple --</option>
                                    @foreach ($temple as $key => $templeItem)
                                        <option value="{{ $templeItem->id }}">{{ $templeItem->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <!-- Left Side: Image Upload -->
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div>
                                            <label class="text-bold">{{ translate('profile image') }}</label>
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                title="{{ translate('add_profile_Image_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB, {{ translate('which_will_be_shown_in_search_engine_results') }}.">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                    alt="">
                                            </span>
                                        </div>

                                    </div>


                                    <div class="custom_upload_input position-relative">
                                        <input type="file" name="profile"
                                            class="custom-upload-input-file meta-img action-upload-color-image"
                                            data-imgpreview="pre_meta_image_viewer"
                                            accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                            <i class="tio-delete"></i>
                                        </span>

                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_meta_image_viewer" class="h-auto bg-white onerror-add-class-d-none"
                                                alt=""
                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}">
                                        </div>

                                        <div
                                            class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-75"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Side: Pandit Name & Mobile Number -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="panditName">{{ translate('Pandit Name') }} *</label>
                                                <input type="text" class="form-control" id="panditName" name="name"
                                                    placeholder="Enter Pandit Name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="mobile">{{ translate('Mobile Number') }} *</label>
                                                <input type="text" class="form-control" id="mobile" name="mobile"
                                                    placeholder="Enter Mobile Number" required inputmode="number">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                   
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter Address"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter Description"></textarea>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
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
                                        <input id="datatableSearch_" type="search" name="searchValue"
                                            class="form-control" placeholder="{{ translate('search_by_name') }}"
                                            aria-label="{{ translate('search_by_name') }}"
                                            value="{{ request('searchValue') }}" required>
                                        <button type="submit"
                                            class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">

                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('temple_name') }}</th>
                                        <th class="max-width-100px">{{ translate('state_name') }}</th>
                                        <th class="max-width-100px">{{ translate('cities_name') }}</th>
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'status'))
                                            <th class="text-center">{{ translate('status') }}</th>
                                        @endif
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'gallery') ||
                                                Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'edit'))
                                            <th class="text-center"> {{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($temple as $key => $item)
                                        <tr>
                                            <td>{{ $temple->firstItem() + $key }}</td>

                                            <td>
                                                <a class="media align-items-center gap-2">
                                                    <img src="{{ getValidImage(path: 'storage/app/public/temple/thumbnail/' . $item['thumbnail'], type: 'backend-product') }}"
                                                        class="avatar border" alt="">
                                                    <span class="media-body title-color hover-c1">
                                                        {{ Str::limit($item['name'], 20) }}
                                                    </span>
                                                </a>
                                            </td>
                                            <td>
                                                {{ $item['states']['name'] ?? '' }}
                                            </td>
                                            <td>
                                                {{ $item['cities']['city'] ?? '' }}
                                            </td>

                                            @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'status'))
                                                <td>
                                                    <form
                                                        action="{{ route('trustees-vendor.vip-darshan.status-update') }}"
                                                        method="post" id="temple-status{{ $item['id'] }}-form">
                                                        @csrf
                                                        <input type="hidden" name="id"
                                                            value="{{ $item['id'] }}">
                                                        <label class="switcher mx-auto">
                                                            <input type="checkbox"
                                                                class="switcher_input toggle-switch-message"
                                                                name="status" id="temple-status{{ $item['id'] }}"
                                                                value="1" {{ $item['status'] == 1 ? 'checked' : '' }}
                                                                data-modal-id="toggle-status-modal"
                                                                data-toggle-id="temple-status{{ $item['id'] }}"
                                                                data-on-image="temple-status-on.png"
                                                                data-off-image="temple-status-off.png"
                                                                data-on-title="{{ translate('Want_to_Turn_ON') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                                data-off-title="{{ translate('Want_to_Turn_OFF') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                                data-on-message="<p>{{ translate('if_enabled_this_temple_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                                data-off-message="<p>{{ translate('if_disabled_this_temple_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </form>
                                                </td>
                                            @endif

                                            @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'gallery') ||
                                                    Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'edit'))
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'gallery'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                title="{{ translate('gallery') }}"
                                                                href="{{ route('trustees-vendor.vip-darshan.add-gallery', [$item['id']]) }}">
                                                                <i class="tio-photo-square-outlined nav-icon"></i>
                                                            </a>
                                                        @endif

                                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'edit'))
                                                            <!-- <a class="btn btn-outline-info btn-sm square-btn"
                                                                title="{{ translate('edit') }}"
                                                                href="{{ route('trustees-vendor.vip-darshan.temple-edit', ['id' => $item['id']]) }}">
                                                                <i class="tio-edit"></i>
                                                            </a> -->
                                                        @endif
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                title="{{ translate('edit') }}"
                                                                href="{{ route('trustees-vendor.purohit-data.purohitview',['id' => $item['id']]) }}">
                                                               <i class="tio-visible nav-icon"></i>
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
                            {{ $temple->links() }}
                        </div>
                    </div>

                    @if (count($temple) == 0)
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
    <span id="route-admin-rashi-status-update" data-url="{{ route('admin.temple.status-update') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
@endpush