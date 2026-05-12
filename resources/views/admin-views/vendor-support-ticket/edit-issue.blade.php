@extends('layouts.back-end.app')

@section('title', translate('issue_update'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-10">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
            {{ translate('issue_update') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-start">
                    <form action="{{ route('admin.vendor-support-ticket.issue-edit')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="title-color">{{ translate('created_by') }}<span class="text-danger">*</span> </label>
                                    <select name="created_by" class="form-control" required>
                                        <option value="">{{ translate('created_by') }} </option>
                                        <option value="admin" {{ (($getissue['created_by'] == 'admin' )?'selected':'') }}>admin</option>
                                        <option value="vendor" {{ (($getissue['created_by'] == 'vendor' )?'selected':'') }}>vendor</option>
                                    </select>
                                    <input type="hidden" name="id" value="{{ $getissue['id'] }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="title-color">{{ translate('select_type') }}<span class="text-danger">*</span> </label>
                                    <select name="type" class="form-control" required>
                                        <option value="">{{ translate('select_type') }} </option>
                                        @if($TypeList)
                                        @foreach($TypeList as $v_data)
                                        <option value="{{ $v_data}}"  {{ (($getissue['type'] == $v_data )?'selected':'') }}>{{ $v_data}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div>
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('issue_name') }}<span class="text-danger">*</span> </label>
                                        <input type="text" name="message" value="{{ $getissue['message']}}" class="form-control" placeholder="{{ translate('ticket_issue_name') }}" required>
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
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>

@endpush