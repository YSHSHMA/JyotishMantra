@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('consultancy_template_create'))
@push('css_or_js')
@endpush
@section('content')
    <div class="content container-fluid">
        {{-- Model --}}
        {{-- Model --}}
        <div class="row g-2 flex-grow-1">
            <div class="col-sm-8 col-md-6 col-lg-4">
                <div class="mb-3">
                    <h2 class="h1 mb-0 d-flex gap-2">
                        <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/whatsapp.png') }}"
                            alt="">
                        {{ translate('Consultancy_template_create') }}
                    </h2>
                </div>
            </div>
        </div>


        <div class="row mt-20">
            <div class="col-lg-4 col-md-4 mb-md-0 mb-3">
                <div class="card" style="min-height: 350px">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title mb-0">
                            <h5 class="mb-0">{{ translate('Consultancy_Order_Management') }}</h5>
                        </div>
                    </div>
                    <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical"
                        style="margin: 8px 23px 0px 23px;">
                        @foreach ($whatsapp as $key => $whats)
                            <a class="pt-2 nav-link @if ($key == 0) {{ 'active' }} @endif"
                                id="OrderPlaced-tab" data-toggle="pill" href="#OrderPlaced{{ $key }}"
                                role="tab" aria-controls="OrderPlaced{{ $key }}"
                                aria-selected="true">{{ $whats['template_name'] }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="col-lg-8 col-md-8 mb-md-0 mb-3">

                <div class="nav-align-top mb-4">

                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="d-flex mb-3 gap-3">
                            <div>
                                <span class="badge bg-label-primary rounded-2 p-2">
                                    <i class="ti ti-credit-card ti-lg"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-0">
                                    <span class="align-middle" id="menuContent{{ $key }}">Order
                                        placed</span>
                                </h4>
                            </div>
                        </div>
                        @foreach ($whatsapp as $key => $whats)
                            <div class="tab-pane fade show @if ($key == 0) {{ 'active' }} @endif"
                                id="OrderPlaced{{ $key }}" role="tabpanel"
                                aria-labelledby="OrderPlaced{{ $key }}-tab">
                                <form method="post" action="{{ route('admin.whatsapp.counsltancy-template-update', $whats['id']) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="" name="id" placeholder="Template Name"
                                        value="{{ $whats['id'] }}">
                                    <input type="hidden" id="exampleInput{{ $key }}" name="template_name"
                                        placeholder="Template Name" value="{{ $whats['template_name'] }}">
                                    <input type="hidden" name="token" value="{{ Session::get('user_token') }}">
                                    <div class="row g-3">
                                        <div class="col-12">

                                            <div class="row mt-2">
                                                <div class="col">
                                                    <div class="d-flex justify-content-between">
                                                        <label class="card-title mb-2">Message</label>
                                                        <a class="badge bg-label-primary text-muted mb-1"
                                                            data-toggle="modal" data-target="#Shortcodes"><i
                                                                class="ti ti-code"></i> Shortcode</a>
                                                    </div>
                                                    <textarea name="body" id="text_message" class="form-control" rows="8" placeholder="Enter Message">{{ $whats['body'] }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pt-4 text-end">

                                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Save
                                            Template</button>

                                    </div>
                                </form>
                            </div>
                            <script>
                                $(document).ready(function(){
                                    $(".nav-link").click(function(){
                                        var menuName = $(this).find('span').text();
                                        $("#menuContent{{$key}}").text(menuName);
                                        $("#exampleInput{{$key}}").val(menuName);
                                        });
                                    });
                             </script>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="Shortcodes" tabindex="-1" role="dialog" aria-labelledby="Shortcodes"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ShortcodesLabel">Shortcode</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">{name} = recipient can see his/her name</li>
                            <li class="list-group-item">{phone_number} = recipient can see his/her phone number</li>
                            <li class="list-group-item">{my_name} = recipient can see your name</li>
                            <li class="list-group-item">{my_email} = recipient can see your email</li>
                            <li class="list-group-item">{my_contact_number} = recipient can see your contact number</li>

                            <li class="list-group-item">
                                <p><b>Note: </b>If you want custom parameter for API. Use <strong>{1}</strong> maximum
                                    parameter limit is 20. Like <strong>{1},{2},{3},.....,{20}</strong> <br> Example: You
                                    made a purchase for <b>{1}</b> using a credit card ending in <b>{2}</b></p>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('script')
        <script>
            $(document).ready(function() {
                var i = 1;
                $("#add").click(function() {
                    i++;
                    $('#dynamic_field').append('<tr id="row' + i +
                        '"><td class="col-md-11 px-0"><input type="text" id="button_buttontext" name="button_buttontext[]" class="form-control" placeholder="Enter Button Text" autocomplete="off" required/></td><td class="col-md-1"><button type="button" name="remove" id="' +
                        i +
                        '" class="btn btn-sm btn-danger btn_remove"><i class="ti ti-circle-x"></i></button></td></tr>'
                    );
                });

                $(document).on('click', '.btn_remove', function() {
                    var button_id = $(this).attr("id");
                    $('#row' + button_id + '').remove();
                });
            });
        </script>
        {{-- add more list --}}
        <script>
            $(document).ready(function() {
                var j = 1;
                $("#add_list").click(function() {
                    j++;
                    $('#list_dynamic_field').append('<tr id="row' + j +
                        '"><td class="col-md-12 px-0"><div class="row"><div class="col-12 my-2"><label for="">List Section Title</label><input type="text" id="list_title" name="list_title[]" class="form-control" placeholder="Enter Title" autocomplete="off" required/></div><div class="col-md-6 my-2"><label for="">Enter List Value Name</label><input type="text" id="list_value_name" name="list_value_name[]" class="form-control" placeholder="Enter List Value Name" autocomplete="off" required/></div><div class="col-md-6 my-2"><label for="">Enter List Value Description</label><input type="text" id="list_value_description" name="list_value_description[]" class="form-control" placeholder="Enter List Value Description" autocomplete="off" required/></div></div></td></tr>'
                    );
                });

                // $(document).on('click', '.btn_remove', function() {
                //     var button_id = $(this).attr("id");
                //     $('#row' + button_id + '').remove();
                // });
            });
        </script>
    @endpush
