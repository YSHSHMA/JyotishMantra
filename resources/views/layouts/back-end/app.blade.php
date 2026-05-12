@php
use App\Utils\Helpers;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ Session::get('direction') }}"
    style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    <meta name="_token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="shortcut icon"
        href="{{ dynamicStorage(path: 'storage/app/public/company/' . getWebConfig(name: 'company_fav_icon')) }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/google-fonts.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/custom.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/vendor/icon-set/style.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/theme.minc619.css?v=1.0') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/style.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/toastr.css') }}">
    @if (Session::get('direction') === 'rtl')
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/menurtl.css') }}">
    @endif
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/css/lightbox.css') }}">
    @stack('css_or_js')
    <script
        src="{{ dynamicAsset(path: 'public/assets/back-end/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js') }}">
    </script>
    <style>
        select {
            background-image: url('{{ dynamicAsset(path: ' public/assets/back-end/img/arrow-down.png') }}');
            background-size: 7px;
            background-position: 96% center;
        }
    </style>
    @if (Request::is('admin/payment/configuration/addon-payment-get'))
    <style>
        .form-floating>label {
            position: relative;
            display: block;
            margin-bottom: 12px;
            padding: 0;
            inset-inline: 0 !important;
        }
    </style>
    @endif
</head>

<body class="footer-offset">

    <div id="recaptcha-container"></div>

    {{-- permission modal --}}
    <div class="modal fade" id="permission-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Permission Access</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id="permission-mobile-div" class="col-12">
                            <div class="form-group">
                                <label for="mobile_no"></label>
                                <select name="mobile_no" id="mobile-no-value" class="form-control">
                                    <option value="+919713794786">Admin</option>
                                    <option value="+918770540672">Varshaa mam</option>
                                    <option value="+918871604650">Safal</option>
                                </select>
                                <input type="hidden" id="role-access-type-value" class="form-control">
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-primary" onclick="sendOtp()">Send</button>
                            </div>
                        </div>
                        <div id="permission-otp-div" class="col-12" style="display: none;">
                            <div class="form-group">
                                <label for="otp">Enter OTP</label>
                                <input type="number" name="otp" id="otp-value" class="form-control"
                                    placeholder="Enter OTP">
                                <p id="permission-otp-validate" class="text-danger" style="display: none"></p>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-danger" onclick="backToMobile()">Back</button>
                                <button type="button" class="btn btn-primary ml-2"
                                    onclick="otpVerify()">Verify</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- remote access modal --}}
    <div class="modal fade" id="remote-access-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remote Access</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id="remote-access-mobile-div" class="col-12">
                            <div class="form-group">
                                <label for="mobile_no"></label>
                                <select name="mobile_no" id="remote-access-mobile-no-value" class="form-control">
                                    <option value="+919713794786">Admin</option>
                                    <option value="+918770540672">Varshaa mam</option>
                                    <option value="+918871604650">Safal</option>
                                </select>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-primary"
                                    onclick="sendOtpRemoteAccess()">Send</button>
                            </div>
                        </div>
                        <div id="remote-access-otp-div" class="col-12" style="display: none;">
                            <div class="form-group">
                                <label for="otp">Enter OTP</label>
                                <input type="number" name="otp" id="remote-access-otp-value"
                                    class="form-control" placeholder="Enter OTP">
                                <p id="remote-access-otp-validate" class="text-danger" style="display: none"></p>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-danger"
                                    onclick="backToMobileRemoteAccess()">Back</button>
                                <button type="button" class="btn btn-primary ml-2"
                                    onclick="otpVerifyRemoteAccess()">Verify</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- setting otp modal --}}
    <div class="modal fade" id="setting-access-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Setting Access</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id="setting-access-mobile-div" class="col-12">
                            <div class="form-group">
                                <label for="mobile_no"></label>
                                <select name="mobile_no" id="setting-access-mobile-no-value" class="form-control">
                                    <option value="+919713794786">Admin</option>
                                    <option value="+918770540672">Varshaa mam</option>
                                    <option value="+918871604650">Safal</option>
                                </select>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-primary"
                                    onclick="sendOtpSettingAccess()">Send</button>
                            </div>
                        </div>
                        <div id="setting-access-otp-div" class="col-12" style="display: none;">
                            <div class="form-group">
                                <label for="otp">Enter OTP</label>
                                <input type="number" name="otp" id="setting-access-otp-value"
                                    class="form-control" placeholder="Enter OTP">
                                <p id="setting-access-otp-validate" class="text-danger" style="display: none"></p>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-danger"
                                    onclick="backToMobileSettingAccess()">Back</button>
                                <button type="button" class="btn btn-primary ml-2"
                                    onclick="otpVerifySettingAccess()">Verify</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="permission-access-module" tabindex="-1" role="dialog"
        aria-labelledby="modelTitleId" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Permission Access</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id="permission-access-mobile-div" class="col-12">
                            <div class="form-group">
                                <label for="mobile_no"></label>
                                <?php $vendor_phone_permissions = \App\Models\VendorPhonePermissions::all(); ?>
                                <select name="mobile_no" id="permission-access-mobile-value" class="form-control">
                                    @if ($vendor_phone_permissions)
                                    @foreach ($vendor_phone_permissions as $vpp)
                                    <option value="{{ $vpp['phone'] }}">{{ $vpp['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <input type="hidden" id="role-permission-access-type-value" class="form-control">
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-primary" onclick="phoneSendOtp()">Send</button>
                            </div>
                        </div>
                        <div id="permission-access-otp-div" class="col-12" style="display: none;">
                            <div class="form-group">
                                <label for="otp">Enter OTP</label>
                                <input type="number" name="otp" id="otp-primission-access-value"
                                    class="form-control" placeholder="Enter OTP">
                                <p id="permission-module-otp-validate" class="text-danger" style="display: none"></p>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-danger"
                                    onclick="backTopermissionmoduleMobile()">Back</button>
                                <button type="button" class="btn btn-primary ml-2"
                                    onclick="otpVerifypermissionModule()">Verify</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.back-end.partials._front-settings')
    <span class="d-none" id="placeholderImg"
        data-img="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img3.png') }}"></span>
    <div class="row">
        <div class="col-12 position-fixed z-9999 mt-10rem">
            <div id="loading" class="d--none">
                <div id="loader"></div>
            </div>
        </div>
    </div>
    @include('layouts.back-end.partials._header')
    @include('layouts.back-end.partials._side-bar')
    @include('layouts.back-end._translator-for-js')
    <span id="get-root-path-for-toggle-modal-image"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/modal') }}"></span>

    <main id="content" role="main" class="main pointer-event">
        @yield('content')
        @include('layouts.back-end.partials._footer')
        @include('layouts.back-end.partials._modals')
        @include('layouts.back-end.partials._toggle-modal')
        @include('layouts.back-end.partials._sign-out-modal')
    </main>

    <span class="please_fill_out_this_field" data-text="{{ translate('please_fill_out_this_field') }}"></span>
    <span class="get-application-environment-mode"
        data-value="{{ env('APP_MODE') == 'demo' ? 'demo' : 'live' }}"></span>
    <span id="get-currency-symbol"
        data-currency-symbol="{{ getCurrencySymbol(currencyCode: getCurrencyCode(type: 'default')) }}"></span>

    <span id="message-select-word" data-text="{{ translate('select') }}"></span>
    <span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
    <span id="message-no-word" data-text="{{ translate('no') }}"></span>
    <span id="message-cancel-word" data-text="{{ translate('cancel') }}"></span>
    <span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }} ?"></span>
    <span id="message-invalid-date-range" data-text="{{ translate('invalid_date_range') }}"></span>
    <span id="message-status-change-successfully" data-text="{{ translate('status_change_successfully') }}"></span>
    <span id="message-are-you-sure-delete-this" data-text="{{ translate('are_you_sure_to_delete_this') }} ?"></span>
    <span id="message-you-will-not-be-able-to-revert-this"
        data-text="{{ translate('you_will_not_be_able_to_revert_this') }}"></span>

    <span id="get-customer-list-route" data-action="{{ route('admin.customer.customer-list-search') }}"></span>

    <span id="get-search-product-route" data-action="{{ route('admin.products.search-product') }}"></span>
    <span id="get-orders-list-route" data-action="{{ route('admin.orders.list', ['status' => 'all']) }}"></span>
    <span class="system-default-country-code" data-value="{{ getWebConfig(name: 'country_code') ?? 'us' }}"></span>

    <audio id="myAudio">
        <source src="{{ dynamicAsset(path: 'public/assets/back-end/sound/notification.mp3') }}" type="audio/mpeg">
    </audio>
    <!--- Data Table --->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/vendor.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/theme.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/bootstrap.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/sweet_alert.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/toastr.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/lightbox.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/custom.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/app-script.js') }}"></script>
    <!-- Firbase CDN -->
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>

    {!! Toastr::message() !!}

    @if ($errors->any())
    <script>
        'use strict';
        @foreach($errors->all() as $error)
        toastr.error('{{ $error }}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
    @endif

    <script>
        function initDataTable({
            tableId,
            ajaxUrl,
            columns,
            exportTitle = 'Exported Data',
            pageLength = 10,
            rowCallback = null,
            extraOptions = {}
        }) {
            if (!$(tableId).length) return;

            const mergedAjax = {
                url: ajaxUrl,
                type: "GET",
                error: function(xhr, status, error) {
                    console.error("DataTable Error:", error);
                    console.log("Response:", xhr.responseText);
                    alert('Something went wrong while loading data.');
                },
                ...(extraOptions.ajax || {})
            };

            let table = {
                processing: true,
                serverSide: true,                               
                ajax: mergedAjax,
                columns: columns,
                dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' +
                    '<"row mb-3"<"col-sm-12"B>>' +
                    'rt' +
                    '<"row mt-3"<"col-sm-5"i><"col-sm-7"p>>',
                buttons: [{
                        extend: 'csvHtml5',
                        title: exportTitle
                    },
                    {
                        extend: 'excelHtml5',
                        title: exportTitle
                    },
                    {
                        extend: 'pdfHtml5',
                        title: exportTitle,
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        extend: 'print',
                        title: exportTitle
                    }
                ],
                pageLength: pageLength,
                lengthMenu: [10, 25, 50, 100], 
                responsive: true,
                order: [
                    [0, "desc"]
                ],
                ...extraOptions,
                ajax: mergedAjax
            };
            if (typeof rowCallback === 'function') {
                table.rowCallback = rowCallback;
            }
            return $(tableId).DataTable(table);
        }
    </script>

    <script>
        setInterval(function() {
            $.get({
                url: "{{ route('admin.new-order-message.message') }}",
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                },
                dataType: 'json',
                success: function(response) {
                    let data = response.data;
                    console.log(data.event);
                    if (data['event'] > 0) {
                        var audio = document.getElementById("myAudio");
                        audio.play().catch(function(error) {});
                        Swal.fire({
                            title: 'You have a New Event Booking, Please Check!',
                            html: `
                            <div class="row">
                    <div class="col-12">
                        <h2 style="color: #4caf50;">Success!</h2>
                    </div>
                    <div class="col-6 text-center"><a class="btn btn-info" onclick="AllMessageView('event','view')">Ok,Let me Check</a></div>
                    <div class="col-6 text-center"><a class="btn btn-danger" onclick="AllMessageView('event','close');Swal.close();">Close</a></div>
                    </div>
                `,
                            icon: 'success',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                        });
                    } else if (data.tour > 0) {
                        var audio = document.getElementById("myAudio");
                        audio.play().catch(function(error) {});
                        Swal.fire({
                            title: 'You have a New Tour Booking, Please Check!',
                            html: `
                            <div class="row">
                    <div class="col-12">
                        <h2 style="color: #4caf50;">Success!</h2>
                    </div>
                    <div class="col-6 text-center"><a class="btn btn-info" onclick="AllMessageView('tour','view')">Ok,Let me Check</a></div>
                    <div class="col-6 text-center"><a class="btn btn-danger" onclick="AllMessageView('tour','close');Swal.close();">Close</a></div>
                    </div>
                `,
                            icon: 'success',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                        });
                    } else if (data.kundli > 0) {
                        var audio = document.getElementById("myAudio");
                        audio.play().catch(function(error) {});
                        Swal.fire({
                            title: 'You have a New Kundali Order, Please Check!',
                            html: `
                            <div class="row">
                    <div class="col-12">
                        <h2 style="color: #4caf50;">Success!</h2>
                    </div>
                    <div class="col-6 text-center"><a class="btn btn-info" onclick="AllMessageView('kundli','view')">Ok,Let me Check</a></div>
                    <div class="col-6 text-center"><a class="btn btn-danger" onclick="AllMessageView('kundli','close');Swal.close();">Close</a></div>
                    </div>
                `,
                            icon: 'success',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                        });
                    }
                },
            });
        }, 100000);

        function AllMessageView(name, type) {
            $.get({
                url: "{{ route('admin.new-order-message.message') }}",
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                },
                dataType: 'json',
                success: function(response) {
                    var audio = document.getElementById("myAudio");
                    audio.pause();
                    if (name == 'event' && type == 'view') {
                        window.location.href = "{{ route('admin.event-managment.event.list') }}";
                    } else if (name == 'tour' && type == 'view') {
                        window.location.href = "{{ route('admin.tour-visits-booking.all-list') }}";
                    } else if (name == 'kundli' && type == 'view') {
                        window.location.href = "{{ route('admin.birth_journal.orders.all_list') }}";
                    }
                }
            })

        }
    </script>


    @stack('script')

    @if (Helpers::modules_check('Dashboard'))
    <script>
        'use strict'
        setInterval(function() {
            $.get({
                url: "{{ route('admin.orders.get-order-data') }}",
                dataType: 'json',
                success: function(response) {
                    let data = response.data;
                    if (data.new_order > 0) {
                        playAudio();
                        $('#popup-modal').appendTo("body").modal('show');
                    }
                },
            });
        }, 100000);
    </script>
    @endif

    @stack('script_2')

    @if (Helpers::modules_check('Pooja Order'))
    <script>
        'use strict'
        setInterval(function() {
            $.get({
                url: "{{ route('admin.orders.get-order-pooja') }}",
                dataType: 'json',
                success: function(response) {
                    let data = response.data;
                    // console.log(response.data);
                    if (data.new_pooja > 0) {
                        playAudio();
                        $('#popup-modal-pooja').appendTo("body").modal('show');
                    }
                },
            });
        }, 100000);

        function poojaModal(type) {
            $.ajax({
                type: "get",
                url: "{{ url('admin/pooja/checked-status') }}",
                success: function(response) {
                    if (response.status == 200) {
                        if (type == 'yes') {
                            window.location.href = "{{ url('admin/pooja/orders/list/all') }}";
                        } else {
                            $('#popup-modal-pooja').modal('hide');
                        }
                    } else {
                        $('#popup-modal-pooja').modal('hide');
                    }
                }
            });
        }
    </script>
    @endif

    @stack('script_3')

    @if (Helpers::modules_check('Consultation Order'))
    <script>
        'use strict'
        setInterval(function() {
            $.get({
                url: "{{ route('admin.orders.get-order-counselling') }}",
                dataType: 'json',
                success: function(response) {
                    let data = response.data;
                    // console.log(response.data);
                    if (data.new_counselling > 0) {
                        playAudio();
                        $('#popup-modal-counselling').appendTo("body").modal('show');
                    }
                },
            });
        }, 100000);

        function counsellingModal(type) {
            $.ajax({
                type: "get",
                url: "{{ url('admin/counselling/order/checked-status') }}",
                success: function(response) {
                    if (response.status == 200) {
                        if (type == 'yes') {
                            window.location.href = "{{ url('admin/counselling/order/list/all') }}";
                        } else {
                            $('#popup-modal-counselling').modal('hide');
                        }
                    } else {
                        $('#popup-modal-counselling').modal('hide');
                    }
                }
            });
        }
    </script>
    @endif
    @stack('script_4')
    @if (Helpers::modules_check('Vip Order'))
    <script>
        'use strict'
        setInterval(function() {
            $.get({
                url: "{{ route('admin.orders.get-order-vip') }}",
                dataType: 'json',
                success: function(response) {
                    let data = response.data;
                    // console.log(response.data);
                    if (data.new_vip > 0) {
                        playAudio();
                        $('#popup-modal-vip').appendTo("body").modal('show');
                    }
                },
            });
        }, 100000);

        function vipModal(type) {
            $.ajax({
                type: "get",
                url: "{{ url('admin/vippooja/order/checked-status') }}",
                success: function(response) {
                    if (response.status == 200) {
                        if (type == 'yes') {
                            window.location.href = "{{ url('admin/vippooja/order/list/all') }}";
                        } else {
                            $('#popup-modal-vip').modal('hide');
                        }
                    } else {
                        $('#popup-modal-vip').modal('hide');
                    }
                }
            });
        }
    </script>
    @endif

    @stack('script_5')
    @if (Helpers::modules_check('Chadhava Order'))
    <script>
        'use strict'
        setInterval(function() {
            $.get({
                url: "{{ route('admin.orders.get-order-chadhava') }}",
                dataType: 'json',
                success: function(response) {
                    let data = response.data;
                    // console.log(response.data);
                    if (data.new_chadhava > 0) {
                        playAudio();
                        $('#popup-modal-chadhava').appendTo("body").modal('show');
                    }
                },
            });
        }, 100000);

        function chadhavaModal(type) {
            $.ajax({
                type: "get",
                url: "{{ url('admin/chadhava/order/checked-status') }}",
                success: function(response) {
                    if (response.status == 200) {
                        if (type == 'yes') {
                            window.location.href = "{{ url('admin/chadhava/order/list/all') }}";
                        } else {
                            $('#popup-modal-chadhava').modal('hide');
                        }
                    } else {
                        $('#popup-modal-chadhava').modal('hide');
                    }
                }
            });
        }
    </script>
    @endif

    @stack('script_6')
    @if (Helpers::modules_check('Anushthan Order'))
    <script>
        'use strict'
        setInterval(function() {
            $.get({
                url: "{{ route('admin.orders.get-order-anushthan') }}",
                dataType: 'json',
                success: function(response) {
                    let data = response.data;
                    // console.log(response.data);
                    if (data.new_anushthan > 0) {
                        playAudio();
                        $('#popup-modal-anushthan').appendTo("body").modal('show');
                    }
                },
            });
        }, 100000);

        function anushthanModal(type) {
            $.ajax({
                type: "get",
                url: "{{ url('admin/anushthan/order/checked-status') }}",
                success: function(response) {
                    if (response.status == 200) {
                        if (type == 'yes') {
                            window.location.href = "{{ url('admin/anushthan/order/list/all') }}";
                        } else {
                            $('#popup-modal-anushthan').modal('hide');
                        }
                    } else {
                        $('#popup-modal-anushthan').modal('hide');
                    }
                }
            });
        }
    </script>
    @endif


    @stack('script_7')
    {{-- firebase config --}}
    <script>
        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_APIKEY') }}",
            authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
            projectId: "{{ env('FIREBASE_PRODJECTID') }}",
            storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
            appId: "{{ env('FIREBASE_APPID') }}",
            measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
        };
        firebase.initializeApp(firebaseConfig);
    </script>

    {{-- permission check to open view --}}
    <script>
        function permissionModal(type) {
            $('#role-access-type-value').val(type);
            $('#permission-modal').modal('show');
        }

        var confirmationResultt = "";
        var appVerifierr;

        function sendOtp() {
            var mobile = $('#mobile-no-value').val();

            // Check if the Firebase app is already initialized
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }

            // Check if appVerifierr has already been created
            if (!appVerifierr) {
                appVerifierr = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    size: 'invisible'
                });
            }

            firebase.auth().signInWithPhoneNumber(mobile, appVerifierr).then(function(confirmation) {
                confirmationResultt = confirmation;
                toastr.success('OTP sent successfully');
                $('#permission-mobile-div').hide();
                $('#permission-otp-div').show();
            }).catch(function(error) {
                toastr.error('Failed to send OTP. Please try again');
                console.error('OTP sending error:', error);
            });
        }

        function backToMobile() {
            $('#otp-value').val('');
            $('#permission-mobile-div').show();
            $('#permission-otp-div').hide();
        }

        function otpVerify() {
            var otp = $('#otp-value').val();
            if (otp.length > 0) {
                toastr.success('Please wait...');
                $('#permission-otp-validate').hide();
                if (confirmationResultt) {
                    confirmationResultt.confirm(otp).then(function(result) {
                        var remoteAccessType = $('#role-access-type-value').val();
                        if (remoteAccessType == 'store') {
                            window.location.href = "{{ route('admin.custom-role.create') }}";
                        } else if (remoteAccessType == 'list') {
                            window.location.href = "{{ route('admin.custom-role.list') }}";
                        }
                    }).catch(function(error) {
                        $('#permission-otp-validate').text('Incorrect OTP');
                        $('#permission-otp-validate').show();
                        console.error('OTP verification error:', error);
                    });
                }
            } else {
                $('#permission-otp-validate').text('Please Enter OTP');
                $('#permission-otp-validate').show();
            }
        }
    </script>

    {{-- remote access check to open view --}}
    <script>
        function RemoteAccessModal() {
            $('#remote-access-modal').modal('show');
        }

        var confirmationRemoteAccessResult = "";
        var appVerifierrRemoteAccess;

        function sendOtpRemoteAccess() {
            var mobileRemoteAccess = $('#remote-access-mobile-no-value').val();

            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }

            if (!appVerifierrRemoteAccess) {
                appVerifierrRemoteAccess = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    size: 'invisible'
                });
            }

            firebase.auth().signInWithPhoneNumber(mobileRemoteAccess, appVerifierrRemoteAccess).then(function(
                confirmation) {
                confirmationRemoteAccessResult = confirmation;
                toastr.success('OTP sent successfully');
                $('#remote-access-mobile-div').hide();
                $('#remote-access-otp-div').show();
            }).catch(function(error) {
                toastr.error('Failed to send OTP. Please try again');
                console.error('OTP sending error:', error);
            });
        }

        function backToMobileRemoteAccess() {
            $('#remote-access-otp-value').val('');
            $('#remote-access-mobile-div').show();
            $('#remote-access-otp-div').hide();
        }

        function otpVerifyRemoteAccess() {
            var otp = $('#remote-access-otp-value').val();
            if (otp.length > 0) {
                toastr.success('Please wait...');
                $('#remote-access-otp-validate').hide();
                if (confirmationRemoteAccessResult) {
                    confirmationRemoteAccessResult.confirm(otp).then(function(result) {
                        window.location.href = "{{ route('admin.remote.access.list') }}";
                    }).catch(function(error) {
                        $('#remote-access-otp-validate').text('Incorrect OTP');
                        $('#remote-access-otp-validate').show();
                        console.error('OTP verification error:', error);
                    });
                }
            } else {
                $('#remote-access-otp-validate').text('Please Enter OTP');
                $('#remote-access-otp-validate').show();
            }
        }
    </script>

    {{-- setting access check to open view --}}
    <script>
        function SettingAccessModal() {
            $('#setting-access-modal').modal('show');
        }

        var confirmationSettingAccessResult = "";
        var appVerifierrSettingAccess;

        function sendOtpSettingAccess() {
            var mobileSettingAccess = $('#setting-access-mobile-no-value').val();

            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }

            if (!appVerifierrSettingAccess) {
                appVerifierrSettingAccess = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    size: 'invisible'
                });
            }

            firebase.auth().signInWithPhoneNumber(mobileSettingAccess, appVerifierrSettingAccess).then(function(
                confirmation) {
                confirmationSettingAccessResult = confirmation;
                toastr.success('OTP sent successfully');
                $('#setting-access-mobile-div').hide();
                $('#setting-access-otp-div').show();
            }).catch(function(error) {
                toastr.error('Failed to send OTP. Please try again');
                console.error('OTP sending error:', error);
            });
        }

        function backToMobileSettingAccess() {
            $('#setting-access-otp-value').val('');
            $('#setting-access-mobile-div').show();
            $('#setting-access-otp-div').hide();
        }

        function otpVerifySettingAccess() {
            var otp = $('#setting-access-otp-value').val();
            if (otp.length > 0) {
                toastr.success('Please wait...');
                $('#setting-access-otp-validate').hide();
                if (confirmationSettingAccessResult) {
                    confirmationSettingAccessResult.confirm(otp).then(function(result) {
                        window.location.href = "{{ route('admin.profile.update', auth('admin')->user()->id) }}";
                    }).catch(function(error) {
                        $('#setting-access-otp-validate').text('Incorrect OTP');
                        $('#setting-access-otp-validate').show();
                        console.error('OTP verification error:', error);
                    });
                }
            } else {
                $('#setting-access-otp-validate').text('Please Enter OTP');
                $('#setting-access-otp-validate').show();
            }
        }
    </script>
    <script>
        function permissionModule(type) {
            $('#role-permission-access-type-value').val(type);
            $('#permission-access-module').modal('show');
        }

        function phoneSendOtp() {
            var mobile = $('#permission-access-mobile-value').val();
            $.ajax({
                url: "{{ route('admin.permission-module.phone-check') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    mobile: mobile,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.exists) {
                        if (!firebase.apps.length) {
                            firebase.initializeApp(firebaseConfig);
                        }
                        if (!appVerifierr) {
                            appVerifierr = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                                size: 'invisible'
                            });
                        }
                        firebase.auth().signInWithPhoneNumber(mobile, appVerifierr).then(function(
                            confirmation) {
                            confirmationResultt = confirmation;
                            toastr.success('OTP sent successfully');
                            $('#permission-access-mobile-div').hide();
                            $('#permission-access-otp-div').show();
                        }).catch(function(error) {
                            toastr.error('Failed to send OTP. Please try again');
                            console.error('OTP sending error:', error);
                        });
                    } else {
                        toastr.error('Mobile number not registered');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong while checking the mobile number.');
                }
            });
        }

        function backTopermissionmoduleMobile() {
            $('#otp-primission-access-value').val('');
            $('#permission-access-mobile-div').show();
            $('#permission-access-otp-div').hide();
        }

        function otpVerifypermissionModule() {
            var otp = $('#otp-primission-access-value').val();
            if (otp.length > 0) {
                toastr.success('Please wait...');
                $('#permission-module-otp-validate').hide();
                if (confirmationResultt) {
                    confirmationResultt.confirm(otp).then(function(result) {
                        var remoteAccessType = $('#role-permission-access-type-value').val();
                        if (remoteAccessType == 'add') {
                            window.location.href = "{{ route('admin.permission-module.role') }}";
                        } else if (remoteAccessType == 'list') {
                            window.location.href = "{{ route('admin.permission-module.list') }}";
                        } else if (remoteAccessType == 'module') {
                            window.location.href = "{{ route('admin.permission-module.module') }}";
                        }
                    }).catch(function(error) {
                        $('#permission-module-otp-validate').text('Incorrect OTP');
                        $('#permission-module-otp-validate').show();
                        console.error('OTP verification error:', error);
                    });
                }
            } else {
                $('#permission-module-otp-validate').text('Please Enter OTP');
                $('#permission-module-otp-validate').show();
            }
        }
    </script>
    <script>
        function qr_model(el) {
            $('#qrForm')[0].reset(); // Reset form
            $('#qrPreview').empty(); // Clear previous QR
            $('#qrModal').modal('show'); // Show modal
        }

        $('#qrForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = form.serialize();

            $.ajax({
                url: "{{ route('admin.dashboard.grcode') }}",
                type: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#qrPreview').html(response);
                },
                error: function() {
                    $('#qrPreview').html('<div class="text-danger">Failed to generate QR code.</div>');
                }
            });
        });
    </script>
</body>

</html>