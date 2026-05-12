@extends('layouts.back-end.app-trustees')
@php use App\Utils\Helpers; @endphp

@section('title', translate('purohit_list'))
@push('css_or_js')
<style>
    .btn-outline-warning:hover {
        color: #ffffffff;
        text-decoration: none;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ translate('purohit_list') }}
            <span class="badge badge-soft-dark radius-50 fz-14"></span>
        </h2>
    </div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="t">
                        <form action="{{ route('trustees-vendor.purohit-data.purohit-list') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="purohit_id" id="purohit_id">
                            <div class="">
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
                                    <div class="col-md-4">
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
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="panditName">{{ translate('Puroit Name') }} *</label>
                                                    <input type="text" class="form-control" id="panditName" name="name"
                                                        placeholder="Enter Pandit Name" pattern="[A-Za-z ]{2,50}" minlength="2" maxlength="50"
                                                        title="Use only letters and spaces (2-50 characters)" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mobile">{{ translate('Mobile Number') }} *</label>
                                                    <input type="text" class="form-control" id="mobile" name="mobile"
                                                        placeholder="Enter Mobile Number" inputmode="number" pattern="^(\+91[\-\s]?)?[6-9][0-9]{9}$"
                                                        title="Enter a valid Indian mobile number (with or without +91)">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="panditName">{{ translate('Holder  Name') }} *</label>
                                                    <input type="text" class="form-control" id="holder-name" name="holdername"
                                                        placeholder="Enter Pandit Name" required inputmode="text">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="panditName">{{ translate('Bank Name') }} *</label>
                                                    <input type="text" class="form-control" id="bank-name" name="bankname"
                                                        placeholder="Enter Pandit Name" pattern="[A-Za-z\s\.]{3,50}"
                                                        title="Enter a valid bank name (letters and spaces only)" required inputmode="text">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mobile">{{ translate('Account Number') }} *</label>
                                                    <input type="number" class="form-control" id="account-num" name="account_num"
                                                        placeholder="Enter Mobile Number" required inputmode="number">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mobile">{{ translate('IFSC code') }} *</label>
                                                    <input type="text" class="form-control" id="ifsc-code" name="ifsccode"
                                                        placeholder="Enter Mobile Number" required inputmode="text" pattern="^[A-Z]{4}0[A-Z0-9]{6}$"
                                                        title="Enter a valid IFSC code (e.g. SBIN0001234)"
                                                        maxlength="11"
                                                        style="text-transform:uppercase;">
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
        </div>
    </div>

    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table id="purohitTable" class="table table-striped table-bordered table-hover">
                                        <thead class="thead-light text-capitalize">
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('purohit_information') }}</th>
                                                <th>{{ translate('bank_information') }}</th>
                                                <th>{{ translate('temple_name') }}</th>
                                                <th>{{ translate('Amount') }}</th>
                                                <th>{{ translate('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purohitList as $key => $item)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="d-flex align-items-center justify-content-between" style="min-width: 200px;">
                                                    <div>
                                                        <strong>{{ $item->name }},</strong><br>
                                                        <small>{{ $item->mobile }},</small><br>
                                                        <small>{{ $item->address ?? '-' }}</small>
                                                    </div>
                                                    <div>
                                                        <img src="{{ $item->profile  ? asset('storage/app/public/' . $item->profile) 
                                                                        : asset('storage/app/public/purohit_images/assignPurohit.png') }}"
                                                            alt="{{ $item->name }}"
                                                            width="50" height="50"
                                                            class="rounded-circle border">
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $item->holdername ?? '-' }},<br>
                                                    {{ $item->bankname ?? '-' }},<br>
                                                    {{ $item->account_num ?? '-' }},<br>
                                                    {{ $item->ifsccode ?? '-' }}
                                                </td>
                                                <td>{{ $item->temple ? $item->temple->name : '-' }}</td>
                                                <td>
                                                    <?php
                                                    $getAmount = \App\Models\TrustPanditTransection::where('pandit_id', $item->id)->where('payment_status', 'complete');
                                                    ?>
                                                    <span>Online: {{ (clone $getAmount)->where('payment_method', 'online')->sum('package_price') }}</span><br>
                                                    <span>Cash: {{ (clone $getAmount)->where('payment_method', 'cash')->sum('package_price') }}</span><br>
                                                    <span>Total: {{ (clone $getAmount)->sum('package_price') }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="{{ route('trustees-vendor.purohit-data.purohit-balance-sheet',['id'=>$item->id]) }}" target="_blank" class="btn btn-outline-warning btn-sm square-btn" rel="noopener noreferrer">
                                                            <i class="tio tio-invisible"></i>
                                                        </a>
                                                        <a class="btn btn-outline-info btn-sm square-btn editPurohitBtn"
                                                            title="{{ translate('Edit') }}"
                                                            href="javascript:void(0);"
                                                            data-id="{{ $item->id }}"
                                                            data-temple_id="{{ $item->temple_id }}"
                                                            data-name="{{ $item->name }}"
                                                            data-mobile="{{ $item->mobile }}"
                                                            data-holdername="{{ $item->holdername }}"
                                                            data-bankname="{{ $item->bankname }}"
                                                            data-account_num="{{ $item->account_num }}"
                                                            data-ifsccode="{{ $item->ifsccode }}"
                                                            data-address="{{ $item->address }}"
                                                            data-description="{{ $item->description }}">
                                                            <i class="tio tio-edit nav-icon"></i>
                                                        </a>
                                                        <div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('purohit_information') }}</th>
                                                <th>{{ translate('bank_information') }}</th>
                                                <th>{{ translate('temple_name') }}</th>
                                                <th>{{ translate('Amount') }}</th>
                                                <th>{{ translate('Action') }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<span id="route-admin-rashi-status-update" data-url="{{ route('admin.temple.status-update') }}"></span>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#purohitTable').DataTable({
            responsive: true,
            pageLength: 20,
            autoWidth: false,
            scrollY: '450px',
            scrollX: true,
            scrollCollapse: true,
            fixedHeader: {
                header: true,
                footer: true
            },
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "{{ translate('search_here') }}",
                lengthMenu: "{{ translate('Show') }} _MENU_",
                info: "{{ translate('Showing') }} _START_ {{ translate('to') }} _END_ {{ translate('of') }} _TOTAL_ {{ translate('entries') }}",
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                }
            }
        });
    });
    // Edit the form
    $(document).on('click', '.editPurohitBtn', function() {
        const data = $(this).data();

        // Fill form fields
        $('#purohit_id').val(data.id);
        $('#temple').val(data.temple_id);
        $('#panditName').val(data.name);
        $('#mobile').val(data.mobile);
        $('#holder-name').val(data.holdername);
        $('#bank-name').val(data.bankname);
        $('#account-num').val(data.account_num);
        $('#ifsc-code').val(data.ifsccode);
        $('#address').val(data.address);
        $('#description').val(data.description);
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
        $('.btn-success').text('Update');
    });
</script>

@endpush