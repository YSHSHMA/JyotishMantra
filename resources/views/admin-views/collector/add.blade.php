@extends('layouts.back-end.app')

@section('title', translate('Collector'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/Bhagwan.png') }}" alt="">
                {{ translate('Images') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.collector.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="title-color">
                                            {{ translate('name') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="{{ translate('enter_name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="district" class="title-color">
                                            {{ translate('district') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="district" name="district" class="form-control" required>
                                            <option value="ujjain">Ujjain</option>
                                            <option value="indore">Indore</option>
                                            <option value="bhopal">Bhopal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email" class="title-color">
                                            {{ translate('email') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="{{ translate('enter_email') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mobile" class="title-color">
                                            {{ translate('mobile') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="mobile" class="form-control"
                                            placeholder="{{ translate('enter_mobile') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password" class="title-color">
                                            {{ translate('password') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" name="password" class="form-control"
                                            placeholder="{{ translate('enter_password') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 justify-content-end">
                                <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
@endpush
