@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('Whatsapp_template_create'))
@push('css_or_js')
    <style>
        .activities {
            display: flex;
            flex-wrap: wrap;
        }

        .activities .activity {
            width: 100%;
            display: flex;
            position: relative;
        }

        .activities .activity .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 3px;
            line-height: 50px;
            font-size: 20px;
            text-align: center;
            margin-right: 20px;
            border-radius: 50%;
            flex-shrink: 0;
            text-align: center;
            z-index: 1;
        }

        .activities .activity1 {
            width: 100%;
            display: flex;
            position: relative;
        }

        .activities .activity1 .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 3px;
            line-height: 50px;
            font-size: 20px;
            text-align: center;
            margin-right: 20px;
            border-radius: 50%;
            flex-shrink: 0;
            text-align: center;
            z-index: 1;
        }

        .shadow-primary {
            box-shadow: 0 2px 6px #acb5f6;
        }

        .activities .activity .activity-detail {
            box-shadow: 0 4px 8px rgb(0 0 0 / 3%);
            background-color: #fff;
            border-radius: 3px;
            border: none;
            position: relative;
            margin-bottom: 30px;
            position: relative;
            padding: 1px;
        }

        .text-job {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #34395e;
        }

        .bullet,
        .slash {
            display: inline;
            margin: 0 4px;
        }

        .activities .activity .activity-detail p {
            margin-bottom: 0;
        }

        .activities .activity:before {
            content: " ";
            position: absolute;
            left: 25px;
            top: 0;
            width: 2px;
            height: 100%;
            background-color: #6777ef;
        }

        .card .card-header h4 {
            font-size: 16px;
            line-height: 28px;
            padding-right: 10px;
            margin-bottom: 0;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 400;
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner-border {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: -0.125em;
            border: 0.25em solid currentcolor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: .75s linear infinite spinner-border;
            animation: .75s linear infinite spinner-border;
        }

        .spinner-border-sm {
            width: 50px;
            height: 50px;
            border-width: $spinner-border-width-sm;
        }


        @keyframes spinner-grow {
            0% {
                transform: scale(0);
            }

            50% {
                opacity: 1;
                transform: none;
            }
        }

        .spinner-grow {
            display: inline-block;
            width: 50px;
            height: 50px;
            vertical-align: center;
            background-color: currentcolor;

            border-radius: 50%;
            opacity: 0;
            animation: .75s linear infinite spinner-grow;
        }

        .spinner-grow-sm {
            width: 50px;
            height: 50px;
        }
        .qr-area img{
            width: 200px !important;
            height: auto;
        }
    </style>
@endpush


@section('content')
    <div class="content container-fluid">
        <div class="row justify-content-center">
            <div class="col-sm-8">
               
                <div class="card card-neutral none helper-box" style="display:none">
                    <div class="card-header">
                        <h4>Send Test message</h4>
                    </div>
                    <div class="card-body">
                        <form id="sendtest" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label>Mobile Number</label>
                                        <select class="form-control" name="reciver">
                                            <option value="all">All</option>
                                            @foreach($userPhone as $user)
                                                <option value="{{ str_replace('+91', '', $user->phone) }}">
                                                    {{ $user->name }} ({{ str_replace('+91', '', $user->phone) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Message</label>
                                        <textarea name="message" class="form-control"></textarea>
                                    </textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-block btn-primary">Send</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            $('select[name="reciver"]').select2({
                placeholder: "Select a user or 'All'",
                allowClear: true
            });
        });
    </script>
    <script>
        "use strict";
        var attampt = 0;
        var session_attampt = 0;

        var base_url='{{url("")}}';

        checkSession();
        function createSession() {
        
            attampt++;
            if (attampt == 6) {
                clearInterval(sessionMake);
                const image = `<img src="{{ dynamicAsset(path: 'public/assets/back-end/img/waiting.jpeg') }}" class="w-50">`;
                $('.qr-area').html(image);
                Swal.fire({
                    title: 'Opps!',
                    text: "Time Expired For Logged In Please Reload This Page",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Close',
                    confirmButtonText: 'Refresh This Page'
                }).then((result) => {
                    if (result.value == true) {
                        location.reload();
                    }
                });
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //sending ajax request
            $.ajax({
                type: 'POST',
                url: "{{ url('/admin/whatsapp/create-session') }}",
                data: '_token={{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    const image = `<img src="${response.qr}" class="w-90">`;
                    $('.qr-area').html(image);
                    $('.server_disconnect').hide();
                    $('.progress').show();

                },
                error: function(xhr, status, error) {

                    const image = `<img src="{{ dynamicAsset(path: 'public/assets/back-end/img/disconnect.webp') }}" class="w-50"><br>`;
                    $('.qr-area').html(image);
                    $('.server_disconnect').show();

                    if (xhr.status == 500) {
                        clearInterval(checkSessionRecurr);
                        clearInterval(sessionMake);
                    }
                }
            });
        }

        //check is device logged in
        function checkSession() {
            session_attampt++;
            if (session_attampt >= 10) {
                clearInterval(checkSessionRecurr);
                return false;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: "{{ url('/admin/whatsapp/check-session') }}",
                data: '_token={{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    if (response.connected === true) {
                        clearInterval(checkSessionRecurr);
                        clearInterval(sessionMake);

                        $('.loggout_area').show();


                        const image = `<img src="{{ dynamicAsset(path: 'public/assets/back-end/img/connected.png') }}" class="w-50"><br>`;
                        $('.qr-area').html(image);
                        $('.deviceConnected').html(`Device is successfully Connected  <div class="card-header-action none loggout_area float-end">
                            <a href="javascript:void(0)" class="btn btn-sm btn-neutral logout-btn" data-id="    ">
                                <i class="fas fa-sign-out-alt"></i>&nbsp{{ __('Logout') }}
                            </a>
                        </div>`);
                        $('.logged-alert').show();
                        $('.progress').hide();
                        $('.helper-box').show();

                        device_status == '0' ? congratulations() : '';
                    } else {
                        session_attampt == 1 ? createSession() : '';
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status == 500) {
                        clearInterval(checkSessionRecurr);
                        clearInterval(sessionMake);
                        const image = `<img src="{{ dynamicAsset(path: 'public/assets/back-end/img/disconnect.webp')}}" class="w-50"><br>`;
                        $('.qr-area').html(image);
                        $('.server_disconnect').show();
                    }

                }
            });
        }


        //if click logout button
        $(document).on('click', '.logout-btn', function() {

            Swal.fire({
                title: 'Are you sure want to logout?',
                text: "Once make logout you have to make login useing qr",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No Please',
                confirmButtonText: 'Yes make logout'
            }).then((result) => {
                if (result.value == true) {
                    let previous_btn = $('.logout-btn').html();

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('/admin/whatsapp/logout-session') }}",
                        data: '_token={{ csrf_token() }}',
                        dataType: 'json',

                        beforeSend: function() {
                            $('.logout-btn').html(
                                '<i class="fas fa-spinner"><i>&nbspPlease Wait...');
                            $('.logout-btn').attr('disabled', '');
                        },
                        success: function(response) {
                            $('.logout-btn').html(previous_btn);
                            $('.logout-btn').hide();
                            $('.logout-btn').removeAttr('disabled');

                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            $('.logout-btn').html(previous_btn);
                            $('.logout-btn').removeAttr('disabled');

                        }
                    });
                }

            });


        });

        const sessionMake = setInterval(function() {
            createSession();
        }, 12000);

        const checkSessionRecurr = setInterval(function() {
            checkSession();
        }, 5000);


        $('#sendtest').on('submit', function(e) {
            e.preventDefault();
            var formD = $(this).serialize();
            console.log(formD);
            $.ajax({
                url: "{{ url('/admin/whatsapp/all-send-message') }}",
                method: "POST",
                data: formD,
                success: function(res) {
                    console.log(res);
                    $('#sendtest')[0].reset();
                    Swal.fire({
                        position: "top-end",
                        title: 'Message sent Successfully',
                        showConfirmButton: !1,
                        timer: 1500,
                        buttonsStyling: !1
                    });
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
    </script>
@endpush
