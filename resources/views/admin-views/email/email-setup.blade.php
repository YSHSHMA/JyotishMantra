@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('email'))
@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--<link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">-->
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }
    </style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <span class="tio-circle nav-indicator-icon"></span>
            {{ translate('Email') }}
        </h2>
    </div>
    <div class="row">
        @foreach ($setemail as $key => $email)
            <div class="col-md-6">
                <form action="{{ route('admin.email.update-email-template', $email['id']) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" placeholder="email type" value="{{$email['id']}}">
                    {{-- Category Selected Div --}}
                    <div class="card mt-3 rest-part">
                        <div class="card-header">
                            <div class="d-flex gap-2">
                                <i class="tio-user-big"></i>
                                <h4 class="mb-0">{{ translate('email_setup') }} {{$email['type']}}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="title-color">{{ translate('Mailer_name') }}</label>
                                        <input type="text" class="form-control" name="mailername" id="maiiler-name"
                                            value="{{$email['mailername']}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('Driver') }}</label>
                                        <input type="text" name="driver" id="drivers" class="form-control"
                                            value="{{$email['driver']}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="title-color">{{ translate('Username') }}</label>
                                        <input type="text" class="form-control" name="username" id="user-name"
                                            value="{{$email['username']}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('Encryption') }}</label>
                                        <input type="text" name="encryption" id="encryptions" class="form-control"
                                            value="{{$email['encryption']}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="title-color">{{ translate('Host') }}</label>
                                        <input type="text" class="form-control" name="host" id="hosts"
                                            value="{{$email['host']}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('Port') }}</label>
                                        <input type="text" name="port" id="ports" class="form-control"
                                            value="{{$email['port']}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="title-color">{{ translate('EmailId') }}</label>
                                        <input type="text" class="form-control" name="emailid" id="email-id"
                                            value="{{$email['emailid']}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('Password') }}</label>
                                        <input type="text" name="password" id="password" class="form-control"
                                            value="{{$email['password']}}">
                                    </div>
                                </div>
                            </div>
                            @if (Helpers::modules_permission_check('Email', 'Set Email', 'submit'))
                            <div class="row justify-content-end pt-3 ">
                                <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        @endforeach

    </div>
</div>
@endsection
@push('script')
@endpush