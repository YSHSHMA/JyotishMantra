@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('kundali_milan'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('kundali_milan') }}
            <span class="badge badge-soft-dark radius-50 fz-14">{{ $getData->total() }}</span>
        </h2>
    </div>
    <div class="row mt-20">
        @if(isset($types) && !empty($types) && $types=='pendings')
        <div class="col-md-12 mb-2">
            <div class='row'>
                <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.birth_journal.orders.pending')}}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('All_Kundali_milan')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\BirthJournalKundali::where('payment_status',1)->where('milan_verify',0)->whereHas('birthJournal_kundalimilan', function ($query) {
                            $query->where('name', 'kundali_milan');
                            })->count();
                            @endphp
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.birth_journal.orders.pending',['kundali_pdf'=>'1'])}}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('Verify_Pending')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\BirthJournalKundali::where('payment_status',1)->where('kundali_pdf','!=','')->where('milan_verify',0)->whereHas('birthJournal_kundalimilan', function ($query) {
                            $query->where('name', 'kundali_milan');
                            })->count();
                            @endphp
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.birth_journal.orders.pending',['kundali_pdf'=>'0'])}}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('No_activity')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\BirthJournalKundali::where('payment_status',1)->where('kundali_pdf','=','')->where('milan_verify',0)->whereHas('birthJournal_kundalimilan', function ($query) {
                            $query->where('name', 'kundali_milan');
                            })->count();
                            @endphp
                        </span>
                    </a>
                </div>
            </div>
        </div>
        @endif
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
                        <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">



                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('order_id') }}</th>
                                    <th>{{ translate('user_Info') }}</th>
                                    <th class="text-center"> {{ translate('kundali_Type') }}</th>
                                    <th class="text-center"> {{ translate('type') }}</th>
                                    <th class="text-center"> {{ translate('amount') }}</th>
                                    <th class="text-center"> {{ translate('assign') }}</th>
                                    <th class="text-center"> {{ translate('booking_Date') }}</th>
                                    <th class="text-center"> {{ translate('status') }}</th>
                                    @if (Helpers::modules_permission_check('Birth Journal', 'Kundali Milan All', 'download') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan All', 'detail') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Pending', 'download') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Pending', 'detail') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Completed', 'download') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Completed', 'detail'))
                                    <th class="text-center"> {{ translate('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getData as $key => $brand)
                                <tr>
                                    <td>{{ $getData->firstItem()+$key }}</td>
                                    <td><span class="font-weight-bolder">{{ ($brand['order_id']) }}</span></td>
                                    <td>
                                       <span>{{ ($brand['userData']['name']??'')}}</span><br> 
                                       <span>{{ ($brand['userData']['phone']??'')}}</span><br> 
                                       <span>{{ ($brand['userData']['email']??'')}}</span><br> 
                                    </td>                                    
                                    <td class="text-center">{{ ((($brand['birthJournal']['type']??"") == 'pro')?"Professional":"Basic") }} </td>
                                    <td class="text-center">{{ translate($brand['chart_style']??"") }} </td>
                                    <td class="text-center">{{ ($brand['amount']??"") }}</td>
                                    <td class="text-center">@if($brand['assign_pandit']) {{ ($brand['astrologer']['name']??"")}} @else <span class='badge badge-soft-danger'>Not Assigned</span> @endif</td>
                                    <td class="text-center">{{ date('d M,Y h:i A',strtotime($brand['created_at']??"")) }}</td>
                                    <td class="text-center">@if($brand['milan_verify'] == 1) <span class='badge badge-soft-success'>Success</span> @else <span class='badge badge-soft-danger'>Pending</span> @endif</td>
                                    @if (Helpers::modules_permission_check('Birth Journal', 'Kundali Milan All', 'download') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan All', 'detail') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Pending', 'download') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Pending', 'detail') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Completed', 'download') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Completed', 'detail'))
                                    <td class="text-center">
                                        @if(isset($brand['kundali_pdf']) && !empty($brand['kundali_pdf']))
                                        @if (Helpers::modules_permission_check('Birth Journal', 'Kundali Milan All', 'download') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Pending', 'download') ||Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Completed', 'download'))
                                        <a class='btn btn-secondary btn-sm' href="{{  dynamicStorage(path: 'storage/app/public/birthjournal/kundali_milan/'.($brand['kundali_pdf']??''))}}" target="_blank"><i class="tio-download_to">download_to</i></a>
                                        @endif
                                        @endif

                                        @if (Helpers::modules_permission_check('Birth Journal', 'Kundali Milan All', 'detail') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Pending', 'detail') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Completed', 'detail'))
                                        <a href="{{  route('admin.birth_journal.view-kundali-milan',[$brand['id']]) }}" class='btn btn-success btn-sm'><i class='tio-invisible'></i></a>
                                        @endif
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

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush