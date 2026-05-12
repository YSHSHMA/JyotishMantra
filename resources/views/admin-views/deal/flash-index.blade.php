@php use App\Utils\Helpers; @endphp
@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Session
@endphp
@extends('layouts.back-end.app')

@section('title', translate('flash_Deal'))

@section('content')
    @php($direction = Session::get('direction'))
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/flash_deal.png')}}" alt="">
                {{translate('flash_deals')}}
            </h2>
        </div>
        <div class="row">
            @if (Helpers::modules_permission_check('Offers & Setup', 'Flash Deal', 'add'))
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.deal.flash')}}" method="post"
                              class="text-start onsubmit-disable-action-button"
                              enctype="multipart/form-data" >
                            @csrf
                            @php($language = getWebConfig(name:'pnc_language'))
                            @php($defaultLanguage = 'en')
                            @php($defaultLanguage = $language[0])
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach($language as $lang)
                                    <li class="nav-item text-capitalize font-weight-medium">
                                        <a class="nav-link lang-link {{$lang == $defaultLanguage ? 'active':''}}"
                                           href="javascript:"
                                           id="{{$lang}}-link">{{getLanguageName($lang).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="row">
                                <div class="col-lg-6">
                                    @foreach($language as $lang)
                                        <div class="{{$lang != $defaultLanguage ? 'd-none':''}} lang-form"
                                             id="{{$lang}}-form">
                                            <input type="text" name="deal_type" value="flash_deal" class="d-none">
                                            <div class="form-group">
                                                <label for="name"
                                                       class="title-color font-weight-medium text-capitalize">{{ translate('title')}}
                                                    ({{strtoupper($lang)}})</label>
                                                <input type="text" name="title[]" class="form-control" id="title"
                                                       placeholder="{{translate('ex').':'.translate('LUX')}}"
                                                    {{$lang == $defaultLanguage ? 'required':''}}>
                                            </div>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                    @endforeach
                                    <div class="form-group">
                                        <label for="name"
                                               class="title-color font-weight-medium text-capitalize">{{ translate('start_date')}}</label>
                                        <input type="date" name="start_date"  id="start-date-time" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name"
                                               class="title-color font-weight-medium text-capitalize">{{ translate('end_date')}}</label>
                                        <input type="date" name="end_date" id="end-date-time" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="text-center">
                                            <img class="border radius-10 ratio-4:1 max-w-655px w-100" id="viewer"
                                                 src="{{dynamicAsset(path: 'public/assets/front-end/img/placeholder.png')}}"
                                                 alt="{{translate('banner_image')}}"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="name"
                                               class="title-color font-weight-medium text-capitalize">{{translate('upload_image')}}</label>
                                        <span class="text-info ml-1">( {{translate('ratio').' '.'5:1'}} )</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="custom-file-upload"
                                                   class="custom-file-input image-input" data-image-id="viewer"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label text-capitalize"
                                                   for="custom-file-upload">{{translate('choose_file')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" id="reset"
                                        class="btn btn-secondary px-4">{{ translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary px-4">{{ translate('submit')}}</button>
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
                                <h5 class="mb-0 text-capitalize d-flex gap-2">
                                    {{ translate('flash_deal_table')}}
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
                                               placeholder="{{translate('search_by_Title')}}" aria-label="Search orders"
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
                               class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('SL')}}</th>
                                <th>{{ translate('title')}}</th>
                                <th>{{ translate('duration')}}</th>
                                <th>{{ translate('status')}}</th>
                                <th  class="text-center">{{ translate('active_products')}}</th>
                                @if (Helpers::modules_permission_check('Offers & Setup', 'Flash Deal', 'publish'))
                                <th  class="text-center">{{ translate('publish')}}</th>
                                @endif
                                @if (Helpers::modules_permission_check('Offers & Setup', 'Flash Deal', 'edit') || Helpers::modules_permission_check('Offers & Setup', 'Flash Deal', 'add-product'))
                                <th class="text-center">{{ translate('action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($flashDeals as $key => $deal)
                                <tr>
                                    <td>{{$flashDeals->firstItem()+ $key }}</td>
                                    <td><span class="font-weight-semibold">{{$deal['title']}}</span></td>
                                    <td>{{date('d-M-y',strtotime($deal['start_date'])).'-'.' '}}
                                        {{date('d-M-y',strtotime($deal['end_date']))}}</td>
                                    <td>
                                        @if(Carbon::parse($deal['end_date'])->endOfDay()->isPast())
                                            <span class="badge badge-soft-danger">{{ translate('expired')}} </span>
                                        @else
                                            <span class="badge badge-soft-success"> {{ translate('active')}} </span>
                                        @endif
                                    </td>
                                    <td  class="text-center">{{ $deal->products_count }}</td>
                                    @if (Helpers::modules_permission_check('Offers & Setup', 'Flash Deal', 'publish'))
                                    <td>
                                        <form action="{{route('admin.deal.status-update')}}" method="post"
                                              id="flash-deal-status{{$deal['id']}}-form" data-from="deal">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$deal['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message"
                                                       id="flash-deal-status{{$deal['id']}}" name="status"
                                                       value="1"
                                                       {{ $deal['status'] == 1?'checked':'' }}
                                                       data-modal-id = "toggle-status-modal"
                                                       data-toggle-id = "flash-deal-status{{$deal['id']}}"
                                                       data-on-image = "flash-deal-status-on.png"
                                                       data-off-image = "flash-deal-status-off.png"
                                                       data-on-title = "{{translate('Want_to_Turn_ON_Flash_Deal_Status').'?'}}"
                                                       data-off-title = "{{translate('Want_to_Turn_OFF_Flash_Deal_Status').'?'}}"
                                                       data-on-message = "<p>{{translate('if_enabled_this_flash_sale_will_be_available_on_the_website_and_customer_app')}}</p>"
                                                       data-off-message = "<p>{{translate('if_disabled_this_flash_sale_will_be_hidden_from_the_user_website_and_customer_app')}}</p>">`)">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endif

                                    @if (Helpers::modules_permission_check('Offers & Setup', 'Flash Deal', 'edit') || Helpers::modules_permission_check('Offers & Setup', 'Flash Deal', 'add-product'))
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-10">
                                            @if (Helpers::modules_permission_check('Offers & Setup', 'Flash Deal', 'add-product'))
                                            <a class="h-30 d-flex gap-2 text-capitalize align-items-center btn btn-soft-info btn-sm border-info"
                                               href="{{route('admin.deal.add-product',[$deal['id']])}}">
                                                <img src="{{dynamicAsset(path: 'public/assets/back-end/img/plus.svg')}}" class="svg"
                                                     alt="">
                                                {{translate('add_product')}}
                                            </a>
                                            @endif
                                            @if (Helpers::modules_permission_check('Offers & Setup', 'Flash Deal', 'edit'))
                                            <a title="{{translate('edit')}}"
                                               href="{{route('admin.deal.update',[$deal['id']])}}"
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
