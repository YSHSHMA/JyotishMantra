@extends('layouts.back-end.app')

@section('title', translate('edit_Type'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-10">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
            {{ translate('edit_Type') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-start">
                    <form action="{{ route('admin.support-ticket.type-edit')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <div class="form-group form-system-language-form">
                                        <div class="form-group">
                                            <label class="title-color">{{ translate('type_name') }}<span class="text-danger">*</span> </label>
                                            <input type="text" name="name" class="form-control" value="{{ ($support['name']??'')}}" placeholder="{{ translate('ticket_type_name') }}" required>
                                            <input type="hidden" name="id" value="{{ ($support['id']??'')}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" id="reset"
                                class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

@endpush