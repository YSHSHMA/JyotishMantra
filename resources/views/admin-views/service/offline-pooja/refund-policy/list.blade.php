@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('pandit/pooja_Refund_Policy_List'))

@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="row g-2 flex-grow-1">
            <div class="col-sm-8 col-md-6 col-lg-4">
                <div class="mb-3">
                    <h2 class="h1 mb-0 d-flex gap-2">
                        <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}"
                            alt="">
                        {{ translate('pandit/pooja_Refund_Policy') }}

                        </span>
                    </h2>
                </div>
            </div>
        </div>
        
        @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Refund', 'add'))
        <div class="row mt-20">
            <div class="col-md-12">
                <form class="product-form text-start"
                    action="{{ route('admin.service.offline.pooja.refund.policy.add-new') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="px-4 pt-3">
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach ($languages as $lang)
                                    <li class="nav-item">
                                        <span
                                            class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer"
                                            id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('days') }}</label>
                                        <select name="days" class="form-control">
                                            <option value="1" selected>1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                            <option value="13">13</option>
                                            <option value="14">14</option>
                                            <option value="15">15</option>
                                            <option value="16">16</option>
                                            <option value="17">17</option>
                                            <option value="18">18</option>
                                            <option value="19">19</option>
                                            <option value="20">20</option>
                                            <option value="21">21</option>
                                            <option value="22">22</option>
                                            <option value="23">23</option>
                                            <option value="24">24</option>
                                            <option value="25">25</option>
                                            <option value="26">26</option>
                                            <option value="27">27</option>
                                            <option value="28">28</option>
                                            <option value="29">29</option>
                                            <option value="30">30</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('percent') }}</label>
                                        <select name="percent" class="form-control">
                                            <option value="0" selected>0</option>
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                            <option value="25">25</option>
                                            <option value="30">30</option>
                                            <option value="35">35</option>
                                            <option value="40">40</option>
                                            <option value="45">45</option>
                                            <option value="50">50</option>
                                            <option value="55">55</option>
                                            <option value="60">60</option>
                                            <option value="65">65</option>
                                            <option value="70">70</option>
                                            <option value="75">75</option>
                                            <option value="80">80</option>
                                            <option value="85">85</option>
                                            <option value="90">90</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @foreach ($languages as $lang)
                                <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                                    id="{{ $lang }}-form">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label
                                                class="title-color"for="{{ $lang }}_name">{{ translate('message') }}
                                                ({{ strtoupper($lang) }})
                                            </label>
                                            <textarea name="message[]" id="{{ $lang }}_message" class="form-control" cols="30" rows="10"
                                                {{ $lang == $defaultLanguage ? 'required' : 'required' }}></textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                </div>
                            @endforeach
                            <div class="text-end">
                                <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        @endif

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                {{-- <form action="{{ url()->current() }}" method="GET">
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
                                </form> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="myTable"
                                class="display table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('days') }}</th>
                                        <th>{{ translate('percent') }}</th>
                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Refund', 'status'))
                                        <th class="text-center">{{ translate('status') }}</th>
                                        @endif
                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Refund', 'edit') || Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Refund', 'delete'))
                                        <th class="text-center"> {{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($offlineRefundPolicyPooja as $key => $item)
                                        <tr>
                                            <td>{{ $offlineRefundPolicyPooja->firstItem() + $key }}</td>
                                            <td>
                                                <span class="media-body title-color hover-c1">
                                                    {{ $item['days'] }}
                                                </span>
                                            </td>
                                            <td class="">
                                                {{ $item['percent'] . '%' }}
                                            </td>
                                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Refund', 'status'))
                                            <td>
                                                <form
                                                    action="{{ route('admin.service.offline.pooja.refund.policy.status-update') }}"
                                                    method="post" id="service-status{{ $item['id'] }}-form">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                    <label class="switcher mx-auto">
                                                        <input type="checkbox"
                                                            class="switcher_input toggle-switch-message" name="status"
                                                            id="service-status{{ $item['id'] }}" value="1"
                                                            {{ $item['status'] == 1 ? 'checked' : '' }}
                                                            data-modal-id = "toggle-status-modal"
                                                            data-toggle-id = "service-status{{ $item['id'] }}"
                                                            data-on-image = "service-status-on.png"
                                                            data-off-image = "service-status-off.png"
                                                            data-on-title = "{{ translate('Want_to_Turn_ON') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                            data-off-title = "{{ translate('Want_to_Turn_OFF') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                            data-on-message = "<p>{{ translate('if_enabled_this_rashi_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                            data-off-message = "<p>{{ translate('if_disabled_this_rashi_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </form>
                                            </td>
                                            @endif

                                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Refund', 'edit') || Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Refund', 'delete'))
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Refund', 'edit'))
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('edit') }}"
                                                        href="{{ route('admin.service.offline.pooja.refund.policy.update', [$item['id']]) }}">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                    @endif

                                                    @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Refund', 'delete'))
                                                    <a class="btn btn-outline-danger btn-sm delete delete-data"
                                                        href="javascript:" data-id="service-{{ $item['id'] }}"
                                                        title="{{ translate('delete') }}"><i class="tio-delete"></i>
                                                    </a>
                                                    @endif
                                                    <form
                                                        action="{{ route('admin.service.offline.pooja.refund.policy.delete', [$item['id']]) }}"
                                                        method="post" id="service-{{ $item['id'] }}">
                                                        @csrf @method('delete')
                                                    </form>
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
                            {{ $offlineRefundPolicyPooja->links() }}
                        </div>
                    </div>

                    @if (count($offlineRefundPolicyPooja) == 0)
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
    <!-- <span id="route-admin-rashi-delete" data-url=""></span> -->
    <span id="route-admin-rashi-status-update"
        data-url="{{ route('admin.service.offline.pooja.refund.policy.status-update') }}"></span>
    <!-- Modal Structure -->
    <div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">Offile Pooja Refund Policy Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody id="service-details">
                            <!-- Details will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>
@endpush
