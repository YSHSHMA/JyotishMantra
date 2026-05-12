@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Birth_Journal'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('Birth_Journal') }}
            <span class="badge badge-soft-dark radius-50 fz-14">{{ $getData->total() }}</span>
        </h2>
    </div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                        placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" value="{{ request('searchValue') }}" required>
                                    <button type="submit" class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                        @if (Helpers::modules_permission_check('Birth Journal', 'Kundali', 'add'))
                        <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">

                            <a href="{{ route('admin.birth_journal.add_kundali')}}" class="btn btn-outline--primary">
                                <i class="tio-add"></i>{{ translate('Add') }}
                            </a>

                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('image') }}</th>
                                    <th>{{ translate('name') }}</th>
                                    <th>{{ translate('type') }}</th>
                                    <th class="text-center">{{ translate('selling_price') }}</th>
                                    <th class="text-center">{{ translate('pages') }}</th>
                                    @if (Helpers::modules_permission_check('Birth Journal', 'Kundali', 'status'))
                                    <th class="text-center">{{ translate('status') }}</th>
                                    @endif
                                    @if (Helpers::modules_permission_check('Birth Journal', 'Kundali', 'edit') || Helpers::modules_permission_check('Birth Journal', 'Kundali', 'delete'))
                                    <th class="text-center"> {{ translate('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getData as $key => $brand)
                                <tr>
                                    <td>{{ $getData->firstItem()+$key }}</td>
                                    <td>
                                        <div class="avatar-60 d-flex align-items-center rounded">
                                            <img class="img-fluid" alt=""
                                                src="{{ getValidImage(path: 'storage/app/public/birthjournal/image/'.$brand['image'], type: 'backend-brand') }}">
                                        </div>
                                    </td>
                                    <td> {{ translate($brand['name']??"") }}  </td>
                                    <td> {{ ((($brand['type']??"") == 'pro')?"Professional":"Basic") }}  </td>
                                    <td class="text-center">{{ $brand['selling_price'] }}</td>
                                    <td class="text-center">{{ $brand['pages'] }}</td>
                                    @if (Helpers::modules_permission_check('Birth Journal', 'Kundali', 'status'))
                                    <td>
                                        <form action="{{route('admin.birth_journal.status-update') }}" method="post" id="brand-status{{$brand['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$brand['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                    id="brand-status{{ $brand['id'] }}" value="1" {{ $brand['status'] == 1 ? 'checked' : '' }}
                                                    data-modal-id="toggle-status-modal"
                                                    data-toggle-id="brand-status{{ $brand['id'] }}"
                                                   
                                                    data-on-title="{{ translate('Want_to_Turn_ON').' '.translate('birth_journal').' '. translate('status') }}"
                                                    data-off-title="{{ translate('Want_to_Turn_OFF').' '.translate('birth_journal').' '.translate('status') }}"
                                                    data-on-message="<p>{{ translate('if_enabled_this_birth_journal_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                    data-off-message="<p>{{ translate('if_disabled_this_brand_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endif

                                    @if (Helpers::modules_permission_check('Birth Journal', 'Kundali', 'edit') || Helpers::modules_permission_check('Birth Journal', 'Kundali', 'delete'))
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            @if (Helpers::modules_permission_check('Birth Journal', 'Kundali', 'edit'))
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                href="{{ route('admin.birth_journal.update', [$brand['id']]) }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            @endif
                                            @if (Helpers::modules_permission_check('Birth Journal', 'Kundali', 'delete'))
                                            <a class="btn btn-outline-danger btn-sm attribute-delete-button square-btn " title="{{ translate('delete') }}" id="{{ $brand['id'] }}">
                                                <i class="tio-delete"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {{ $getData->links() }}
                    </div>
                </div>
                @if(count($getData)==0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<span id="route-admin-attribute-delete" data-url="{{ route('admin.birth_journal.remove') }}"></span>

<div class="modal fade" id="select-brand-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i
                        class="tio-clear"></i></button>
            </div>
            <div class="modal-body px-4 px-sm-5 pt-0 pb-sm-5">
                <div class="d-flex flex-column align-items-center text-center gap-2 mb-2">
                    <div
                        class="toggle-modal-img-box d-flex flex-column justify-content-center align-items-center mb-3 position-relative">
                        <img src="{{dynamicAsset('public/assets/back-end/img/icons/info.svg')}}" alt="" width="90" />
                    </div>
                    <h5 class="modal-title mb-2 brand-title-message"></h5>
                </div>
                <form action="{{ route('admin.brand.delete') }}" method="post" class="product-brand-update-form-submit">
                    @csrf
                    <input name="id" hidden="">
                    <div class="gap-2 mb-3">
                        <label class="title-color"
                            for="exampleFormControlSelect1">{{ translate('select_Category') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="brand_id" class="form-control js-select2-custom brand-option" required>

                        </select>
                    </div>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="submit" class="btn btn--primary min-w-120">{{translate('update')}}</button>
                        <button type="button" class="btn btn-danger-light min-w-120"
                            data-dismiss="modal">{{ translate('cancel') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush