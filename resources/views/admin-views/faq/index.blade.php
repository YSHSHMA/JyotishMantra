@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('faq_list'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('faq_list') }}
                <span class="badge badge-soft-dark radius-50 fz-14"></span>
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
                                            placeholder="{{ translate('search_by_name') }}"
                                            aria-label="{{ translate('search_by_name') }}"
                                            value="{{ request('searchValue') }}" required>
                                        <button type="submit"
                                            class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                                @if (Helpers::modules_permission_check('FAQ', 'FAQ', 'add'))
                                    <a href="{{ route('admin.faq.add-new') }}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ translate('add_new_faq') }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th class="max-width-100px">{{ translate('category') }}</th>
                                        <th class="max-width-100px">{{ translate('question') }}</th>
                                        <th class="max-width-100px">{{ translate('detail') }}</th>
                                        @if (Helpers::modules_permission_check('FAQ', 'FAQ', 'status'))
                                            <th class="text-center">{{ translate('status') }}</th>
                                        @endif
                                        @if (Helpers::modules_permission_check('FAQ', 'FAQ', 'edit') ||
                                                Helpers::modules_permission_check('FAQ', 'FAQ', 'delete'))
                                            <th class="text-center"> {{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($faqs as $key => $faq)
                                        <tr>
                                            <td>{{ $faqs->firstItem() + $key }}</td>
                                            <td>{{ $faq->Category['name'] ?? '' }}</td>
                                            <td>{{ $faq->question }}</td>
                                            <td class="overflow-hidden max-width-100px">
                                                <span>
                                                    {!! Str::limit(strip_tags($faq['detail']), 20) !!}
                                                </span>
                                            </td>
                                            @if (Helpers::modules_permission_check('FAQ', 'FAQ', 'status'))
                                                <td>
                                                    <form action="{{ route('admin.faq.status-update') }}" method="post"
                                                        id="festival-status{{ $faq['id'] }}-form">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $faq['id'] }}">
                                                        <label class="switcher mx-auto">
                                                            <input type="checkbox"
                                                                class="switcher_input toggle-switch-message" name="status"
                                                                id="festival-status{{ $faq['id'] }}" value="1"
                                                                {{ $faq['status'] == 1 ? 'checked' : '' }}
                                                                data-modal-id = "toggle-status-modal"
                                                                data-toggle-id = "festival-status{{ $faq['id'] }}"
                                                                data-on-image = "festival-status-on.png"
                                                                data-off-image = "festival-status-off.png"
                                                                data-on-title = "{{ translate('Want_to_Turn_ON') . ' ' . $faq['defaultname'] . ' ' . translate('status') }}"
                                                                data-off-title = "{{ translate('Want_to_Turn_OFF') . ' ' . $faq['defaultname'] . ' ' . translate('status') }}"
                                                                data-on-message = "<p>{{ translate('if_enabled_this_festival_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                                data-off-message = "<p>{{ translate('if_disabled_this_festival_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </form>
                                                </td>
                                            @endif

                                            @if (Helpers::modules_permission_check('FAQ', 'FAQ', 'edit') ||
                                                    Helpers::modules_permission_check('FAQ', 'FAQ', 'delete'))
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        @if (Helpers::modules_permission_check('FAQ', 'FAQ', 'edit'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                title="{{ translate('edit') }}"
                                                                href="{{ route('admin.faq.update', [$faq['id']]) }}">
                                                                <i class="tio-edit"></i>
                                                            </a>
                                                        @endif
                                                        @if (Helpers::modules_permission_check('FAQ', 'FAQ', 'delete'))
                                                            <a class="btn btn-outline-danger btn-sm delete delete-data"
                                                                href="javascript:" data-id="faq-{{ $faq['id'] }}"
                                                                title="{{ translate('delete') }}"><i
                                                                    class="tio-delete"></i>
                                                            </a>
                                                        @endif
                                                        <form action="{{ route('admin.faq.delete', [$faq['id']]) }}"
                                                            method="post" id="faq-{{ $faq['id'] }}">
                                                            @csrf @method('delete')
                                                        </form>
                                                        </a>
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
                            {{ $faqs->links() }}
                        </div>
                    </div>
                    @if (count($faqs) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <span id="route-admin-festival-delete" data-url=""></span>

@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush
