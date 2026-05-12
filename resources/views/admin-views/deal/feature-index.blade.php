@php use App\Utils\Helpers; @endphp
@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Session
@endphp
@extends('layouts.back-end.app')
@section('title', translate('feature_Deal'))

@section('content')
    @php($direction = Session::get('direction'))
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/featured_deal.png')}}" alt="">
                {{translate('feature_deal')}}
            </h2>
        </div>
        <div class="row">
            @if (Helpers::modules_permission_check('Offers & Setup', 'Feature Deal', 'add'))
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.deal.flash')}}"
                              class="text-start onsubmit-disable-action-button"
                              method="post">
                            @csrf
                            @php($language = getWebConfig(name:'pnc_language'))
                            @php($defaultLanguage = 'en')

                            @php($defaultLanguage = $language[0])
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach($language as $lang)
                                    <li class="nav-item text-capitalize">
                                        <a class="nav-link lang-link {{$lang == $defaultLanguage? 'active':''}}"
                                           href="javascript:"
                                           id="{{$lang}}-link">{{getLanguageName($lang).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="form-group">
                                <div class="row">
                                    <input type="text" name="deal_type" value="feature_deal" class="d-none">
                                    @foreach($language as $lang)
                                        <div class="col-md-12 {{$lang != $defaultLanguage ? 'd-none':''}} lang-form"
                                             id="{{$lang}}-form">
                                            <label for="name"
                                                   class="title-color text-capitalize">{{ translate('title')}}
                                                ({{strtoupper($lang)}})</label>
                                            <input type="text" name="title[]" class="form-control" id="title"
                                                   placeholder="{{translate('ex').':'.translate('LUX')}}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                    @endforeach
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <label for="name"
                                               class="title-color text-capitalize">{{ translate('start_date')}}</label>
                                        <input type="date" name="start_date" id="start-date-time" required class="form-control">
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <label for="name"
                                               class="title-color text-capitalize">{{ translate('end_date')}}</label>
                                        <input type="date" name="end_date" id="end-date-time" required class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" id="reset"
                                        class="btn btn-secondary">{{ translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{ translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5 class="mb-0 text-capitalize align-items-center d-flex gap-2">
                                    {{ translate('feature_deal_table')}}
                                    <span
                                        class="badge badge-soft-dark radius-50 fz-12">{{ $flashDeals->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-custom">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="searchValue"
                                               class="form-control"
                                               placeholder="{{translate('search_by_title')}}" aria-label="Search orders"
                                               value="{{ request('searchValue') }}" required>
                                        <button type="submit" class="btn btn--primary">{{translate('search')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable"
                               style="text-align: {{$direction === "rtl" ? 'right' : 'left'}};"
                               class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('SL')}}</th>
                                <th>{{ translate('title')}}</th>
                                <th>{{ translate('start_Date')}}</th>
                                <th>{{ translate('end_Date')}}</th>
                                <th>{{ translate('active')}} / {{ translate('expired')}}</th>
                                @if (Helpers::modules_permission_check('Offers & Setup', 'Feature Deal', 'status'))
                                <th class="text-center">{{ translate('status')}}</th>
                                @endif
                                @if (Helpers::modules_permission_check('Offers & Setup', 'Feature Deal', 'edit') || Helpers::modules_permission_check('Offers & Setup', 'Feature Deal', 'add-product'))
                                <th class="text-center">{{ translate('action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($flashDeals as $key => $deal)
                                <tr>
                                    <th>{{$key+1}}</th>
                                    <td>{{$deal['title']}}</td>
                                    <td>{{date('d-M-y',strtotime($deal['start_date']))}}</td>
                                    <td>{{date('d-M-y',strtotime($deal['end_date']))}}</td>
                                    <td>
                                        @if(Carbon::parse($deal['end_date'])->endOfDay()->isPast())
                                            <span class="badge badge-soft-danger"> {{ translate('expired')}} </span>
                                        @else
                                            <span class="badge badge-soft-success"> {{ translate('active')}} </span>
                                        @endif
                                    </td>
                                    @if (Helpers::modules_permission_check('Offers & Setup', 'Feature Deal', 'status'))
                                    <td>
                                        <form action="{{route('admin.deal.feature-status')}}" method="post"
                                              id="feature-status{{$deal['id']}}-form" data-from="deal">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$deal['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message"
                                                       id="feature-status{{$deal['id']}}" name="status" value="1"
                                                       {{ $deal['status'] == 1 ? 'checked':'' }}
                                                       data-modal-id = "toggle-status-modal"
                                                       data-toggle-id = "feature-status{{$deal['id']}}"
                                                       data-on-image = "feature-status-on.png"
                                                       data-off-image = "feature-status-off.png"
                                                       data-on-title = "{{translate('Want_to_Turn_ON_Featured_Deal_Status').'?'}}"
                                                       data-off-title = "{{translate('Want_to_Turn_OFF_Featured_Deal_Status').'?'}}"
                                                       data-on-message = "<p>{{translate('if_enabled_this_featured_deal_will_be_available_on_the_website_and_customer_app')}}</p>"
                                                       data-off-message = "<p>{{translate('if_disabled_this_featured_deal_will_be_hidden_from_the_website_and_customer_app')}}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endif

                                    @if (Helpers::modules_permission_check('Offers & Setup', 'Feature Deal', 'edit') || Helpers::modules_permission_check('Offers & Setup', 'Feature Deal', 'add-product'))
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-10">
                                            @if (Helpers::modules_permission_check('Offers & Setup', 'Feature Deal', 'add-product'))
                                            <a class="h-30 d-flex gap-2 align-items-center btn btn-soft-info btn-sm border-info"
                                               href="{{route('admin.deal.add-product',[$deal['id']])}}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9"
                                                     viewBox="0 0 9 9" fill="none" class="svg replaced-svg">
                                                    <path
                                                        d="M9 3.9375H5.0625V0H3.9375V3.9375H0V5.0625H3.9375V9H5.0625V5.0625H9V3.9375Z"
                                                        fill="#00A3AD"></path>
                                                </svg>
                                                {{translate('add_product')}}
                                            </a>
                                            @endif
                                            @if (Helpers::modules_permission_check('Offers & Setup', 'Feature Deal', 'edit'))
                                            <a title="{{ trans ('edit')}}"
                                               href="{{route('admin.deal.edit',[$deal['id']])}}"
                                               class="btn btn-outline--primary btn-sm edit">
                                                <i class="tio-edit"></i>
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
                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            {{$flashDeals->links()}}
                        </div>
                    </div>
                    @if(count($flashDeals)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                 src="{{dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg')}}"
                                 alt="{{translate('image_description')}}">
                            <p class="mb-0">{{translate('no_data_to_show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/admin/deal.js')}}"></script>
@endpush
