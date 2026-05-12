@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('all_donated'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('all_donated') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-2">
            <div class='row'>
                <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                    <a class="order-stats order-stats_confirmed">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('All_donated')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\DonateAllTransaction::whereIn('type',['donate_trust','donate_ads'])->where('amount_status',1)->count();
                            @endphp
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                    <a class="order-stats order-stats_confirmed">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('trust')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\DonateAllTransaction::where('type','donate_trust')->where('amount_status',1)->count();
                            @endphp
                        </span>

                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
                    <a class="order-stats order-stats_confirmed">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('ads')}}</h6>
                        </div>
                        <span class="order-stats__title">

                            @php
                            echo \App\Models\DonateAllTransaction::where('type','donate_ads')->where('amount_status',1)->count();
                            @endphp

                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
                    <a class="order-stats order-stats_confirmed">
                        <div class="order-stats__content">
                            <i class="tio-dollar_outlined">dollar_outlined</i>
                            <h6 class="order-stats__subtitle">{{ translate('all_amount')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\DonateAllTransaction::whereIn('type',['donate_trust','donate_ads'])->where('amount_status',1)->sum('amount');
                            @endphp
                        </span>
                    </a>
                </div>

            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        <li class="nav-item text-capitalize">
                            <a class="nav-link form-system-language-tab cursor-pointer {{ ((empty(request('show')) || request('show') == 'all')?'active':'')}}" id="Allrecode-link">All Recodes {{request('show')}}</a>
                        </li>
                        <li class="nav-item text-capitalize">
                            <a class="nav-link form-system-language-tab cursor-pointer {{ ((!empty(request('show')) && request('show') == 'trust')?'active':'')}}" id="trust-link">Trust</a>
                        </li>
                        <li class="nav-item text-capitalize">
                            <a class="nav-link form-system-language-tab cursor-pointer {{ ((!empty(request('show')) && request('show') == 'ads')?'active':'')}}" id="ads-link">ads</a>
                        </li>
                    </ul>
                    <div class="row">
                        <div class="col-12 form-group form-system-language-form {{ ((empty(request('show')) || request('show') == 'all')?'':'d-none')}}" id="Allrecode-form">
                            <div class="px-3 py-4">
                                <!-- Search bar -->
                                <div class="row align-items-center">
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" class="form-control all_select_data start_date_end_date" data-point='8' value="{{ old('start_to_end_date') }}" name='start_to_end_date' placeholder="{{ translate('enter_start_to_end_date') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">{{ translate('Submit') }}</button>
                                                </div>
                                            </div>
                                            <input type="hidden" name='show' value="all">
                                        </form>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-lg-4"></div>
                                    <div class="col-sm-6 col-md-6 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-custom input-group-merge">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input type="hidden" name='show' value="all">
                                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="text-start">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                        <thead class="thead-light thead-50 text-capitalize">
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('ID') }}</th>
                                                <th>{{ translate('customer_info') }}</th>
                                                <th>{{ translate('Type') }}</th>
                                                <th>{{ translate('Trust') }} /{{ translate('ads') }} Name </th>
                                                <th>{{ translate('Purpose') }}</th>
                                                <th>{{ translate('amount') }}</th>
                                                <th>{{ translate('donated_date') }}</th>
                                                <th>{{ translate('option') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($getDonated as $key => $items)
                                            <tr>
                                                
                                                <td>{{$getDonated->firstItem()+$key}}</td>
                                                @if($items['type'] == 'donate_trust')
                                                <td><a href="{{route('admin.donate_management.trust.trust-detail',[$items['trust_id']??''])}}" class='font-weight-bold text-secondary'>{{ $items['getTrust']['trust_id']??'' }}</a></td>
                                                @else
                                                <td><a href="{{route('admin.donate_management.ad_trust.ads-details',[$items['ads_id']??''])}}" class='font-weight-bold text-secondary'>{{ $items['adsTrust']['ads_id']??'' }}</a></td>
                                                @endif
                                                <td>
                                                    <span>{{ ($items['users']['name']??"") }}</span><br>
                                                    <span>{{ ($items['users']['phone']??"") }}</span><br>
                                                    <span>{{($items['users']['email']??"")}}</span><br>
                                                </td>
                                                <td>{{ translate($items['type']) }}</td>
                                                @if($items['type'] == 'donate_trust')
                                                <td><span role='tooltip' data-toggle="tooltip" title="{{ ($items['getTrust']['trust_name']??'') }}">{{ Str::Limit(($items['getTrust']['trust_name']??''),20) }}</span></td>
                                                <td>-</td>
                                                @else
                                                <td><span role='tooltip' data-toggle="tooltip" title="{{ ($items['adsTrust']['name']??'') }}">{{ Str::Limit(($items['adsTrust']['name']??''),20) }}</span></td>
                                                <td><span role='tooltip' data-toggle="tooltip" title="{{ ($items['adsTrust']['Purpose']['name']??'') }}">{{ Str::Limit(($items['adsTrust']['Purpose']['name']??''),20)}}</span></td>
                                                @endif
                                                <td>{{ ($items['amount']??'') }}</td>
                                                <td>{{ date("d M,Y h:i A",strtotime($items['created_at']??"")) }}</td>

                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('share') }}" href="{{ route('admin.donate_management.donated.view',['id'=>$items['id']])}}">
                                                            <i class="tio-invisible"></i>
                                                        </a>
                                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('invoice') }}" target="_blank" href="{{ route('donate-create-pdf-invoice', [$items['id']]) }}">
                                                            <i class="tio-arrow_large_downward">arrow_large_downward</i>
                                                        </a>
                                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('80G') }}" target="_blank"  href="{{ url('api/v1/donate/twoal-a-certificate', [$items['id']]) }}">
                                                            <i class="tio-file_text">file_text</i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive mt-4">
                                <div class="d-flex justify-content-lg-end">
                                    {!! $getDonated->links() !!}
                                </div>
                            </div>
                            @if(count($getDonated) == 0)
                            <div class="text-center p-4">
                                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="col-12 form-group form-system-language-form {{ ((!empty(request('show')) && request('show') == 'trust')?'':'d-none')}}" id="trust-form">
                            <!-- trust-tab -->
                            <div class="px-3 py-4">
                                <div class="row align-items-center">
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" class="form-control all_select_data start_date_end_date" data-point='8' value="{{ old('start_to_end_date') }}" name='start_to_end_date' placeholder="{{ translate('enter_start_to_end_date') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">{{ translate('Submit') }}</button>
                                                </div>
                                            </div>
                                            <input type="hidden" name='show' value="trust">
                                        </form>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-lg-4"></div>
                                    <div class="col-sm-6 col-md-6 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-custom input-group-merge">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input type="hidden" name='show' value="trust">
                                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="text-start">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                        <thead class="thead-light thead-50 text-capitalize">
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('ID') }}</th>
                                                <th>{{ translate('Trust') }} Name </th>
                                                <th>{{ translate('category') }} </th>
                                                <th>{{ translate('Total_amount') }}</th>
                                                <th>{{ translate('option') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($gettrust as $key => $items)
                                            <tr>
                                                <td>{{$gettrust->firstItem()+$key}}</td>
                                                <td><a href="{{route('admin.donate_management.trust.trust-detail',[$items['trust_id']])}}" class='font-weight-bold text-secondary'>{{ $items['getTrust']['trust_id'] }}</a></td>
                                                <td><span role='tooltip' data-toggle="tooltip" title="{{ ($items['getTrust']['trust_name']??'') }}">{{ Str::Limit(($items['getTrust']['trust_name']??''),20) }}</span></td>
                                                <td>
                                                    @if(\App\Models\DonateCategory::where('id',($items['getTrust']['category_id']??''))->exists())
                                                    {{ (\App\Models\DonateCategory::where('id',($items['getTrust']['category_id']??''))->first()['name']??'') }}
                                                    @endif
                                                </td>
                                                <td>{{ ($items['amount']??'') }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('share') }}">
                                                            <i class="tio-share_vs">share_vs</i>
                                                        </a>
                                                    </div>
                                                </td>

                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive mt-4">
                                <div class="d-flex justify-content-lg-end">
                                    {!! $gettrust->links() !!}
                                </div>
                            </div>
                            @if(count($gettrust) == 0)
                            <div class="text-center p-4">
                                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="col-12 form-group form-system-language-form {{ ((!empty(request('show')) && request('show') == 'ads')?'':'d-none')}}" id="ads-form">
                            <!-- ads-tab -->
                            <div class="px-3 py-4">
                                <div class="row align-items-center">
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" class="form-control all_select_data start_date_end_date" data-point='8' value="{{ old('start_to_end_date') }}" name='start_to_end_date' placeholder="{{ translate('enter_start_to_end_date') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">{{ translate('Submit') }}</button>
                                                </div>
                                            </div>
                                            <input type="hidden" name='show' value="ads">
                                        </form>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-lg-4"></div>
                                    <div class="col-sm-6 col-md-6 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-custom input-group-merge">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input type="hidden" name='show' value="ads">
                                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="text-start">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                        <thead class="thead-light thead-50 text-capitalize">
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('ID') }}</th>
                                                <th>{{ translate('Type') }}</th>
                                                <th>{{ translate('Trust') }} Name </th>
                                                <th>{{ translate('ads') }} Name </th>
                                                <th>{{ translate('Purpose') }}</th>
                                                <th>{{ translate('amount') }}</th>
                                                <th>{{ translate('option') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($getads as $key => $items)
                                            <tr>
                                                <td>{{$getads->firstItem()+$key}}</td>
                                                <td><a href="{{route('admin.donate_management.ad_trust.ads-details',[$items['ads_id']??''])}}" class='font-weight-bold text-secondary'>{{ $items['adsTrust']['ads_id']??'' }}</a></td>
                                                <td>{{ ($items['adsTrust']['type']??'') }}</td>
                                                <td><span role='tooltip' data-toggle="tooltip" title="{{ ($items['getTrust']['name']??'Mahakal.com') }}">{{ Str::Limit(($items['getTrust']['name']??'Mahakal.com'),20) }}</span></td>
                                                <td><span role='tooltip' data-toggle="tooltip" title="{{ ($items['adsTrust']['name']??'') }}">{{ Str::Limit(($items['adsTrust']['name']??''),20) }}</span></td>
                                                <td><span role='tooltip' data-toggle="tooltip" title="{{ ($items['adsTrust']['Purpose']['name']??'') }}">{{ Str::Limit(($items['adsTrust']['Purpose']['name']??''),20)}}</span></td>
                                                <td>{{ ($items['amount']??'') }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('share') }}">
                                                            <i class="tio-share_vs">share_vs</i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive mt-4">
                                <div class="d-flex justify-content-lg-end">
                                    {!! $getads->links() !!}
                                </div>
                            </div>
                            @if(count($getads) == 0)
                            <div class="text-center p-4">
                                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
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

<script>
    function initializeDateRangePicker(isSingleDate) {
        $('.start_date_end_date').daterangepicker({
            singleDatePicker: isSingleDate,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function(start, end) {
            // When a date range is selected, set the min and max dates on the individual date pickers
            $('.datePickers').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                minDate: start.format('YYYY-MM-DD'),
                maxDate: end.format('YYYY-MM-DD')
            });
        });
    }

    // Initial setup for date range picker
    initializeDateRangePicker(false);
</script>
@endpush