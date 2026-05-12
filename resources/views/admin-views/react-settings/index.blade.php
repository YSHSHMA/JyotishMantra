@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('react_site_setup'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4 pb-2">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{dynamicAsset(path: 'public/assets/back-end/svg/brands/react.svg')}}" width="25" alt="react">
                {{translate('react_site_setup')}}
            </h2>
        </div>
        <form action="{{ route('admin.react.activation') }}" method="post"
              enctype="multipart/form-data" id="update-settings">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="maximum_otp_hit">{{translate('react_license_code')}}</label>
                                <input type="text" value="{{ $reactData ? $reactData['react_license_code'] : '' }}" name="react_license_code" class="form-control"  placeholder="{{translate('react_license_code')}}" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="otp_resend_time">{{translate('react_domain')}}</label>
                                <input type="text" min="0" value="{{ $reactData ? $reactData['react_domain'] : '' }}" name="react_domain" class="form-control"  placeholder="{{translate('react_domain')}}" required>
                            </div>
                        </div>
                    </div>

                    @if (Helpers::modules_permission_check('React Website Configuration', 'React Website Setup', 'add'))
                    <div class="d-flex gap-3 justify-content-end">
                        <button type="reset" class="btn btn-secondary px-5">{{translate('reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" class="btn btn--primary px-5 {{env('APP_MODE') != 'demo'?'':'call-demo'}}">
                            {{translate('save')}}
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection
