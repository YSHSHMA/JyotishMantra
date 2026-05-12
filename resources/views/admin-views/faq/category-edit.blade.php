@extends('layouts.back-end.app')

@section('title', translate('faq_category_Edit'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('faq_category_Edit') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new faq_category -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.faq.category-edit') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <label class="title-color" for="name">{{ translate('category_name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{old('name',$getData['name'])}}" class="form-control" placeholder="{{ translate('enter_category_name') }}" required>
                                <input type="hidden" name="id" value="{{$getData['id']}}">
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
    </div>
</div>

@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endpush