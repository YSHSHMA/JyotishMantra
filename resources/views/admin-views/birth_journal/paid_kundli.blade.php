@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Birth_Journal'))

@section('content')
<div class="content container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
            <a class="order-stats order-stats_confirmed" href="{{ route('admin.birth_journal.paid_kundli')}}">
                <div class="order-stats__content">
                    <i class="tio-all_done">all_done</i>
                    <h6 class="order-stats__subtitle">{{ translate('All')}}</h6>
                </div>
                <span class="order-stats__title">
                    @php
                    echo \App\Models\BirthJournalKundali::where('payment_status',1)->whereHas('birthJournal_kundali', function ($query) {
                    $query->where('name', 'kundali');
                    })->with('birthJournal_kundali')->count();
                    @endphp
                </span>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
            <a class="order-stats order-stats_confirmed" href="{{ route('admin.birth_journal.paid_kundli',['type'=>1])}}">
                <div class="order-stats__content">
                    <i class="tio-all_done">all_done</i>
                    <h6 class="order-stats__subtitle">{{ translate('pdf_generated')}}</h6>
                </div>
                <span class="order-stats__title">
                    @php
                    echo \App\Models\BirthJournalKundali::where('payment_status',1)->where('kundali_pdf','!=','')->whereHas('birthJournal_kundali', function ($query) {
                    $query->where('name', 'kundali');
                    })->with('birthJournal_kundali')->count();
                    @endphp
                </span>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
            <a class="order-stats order-stats_confirmed" href="{{ route('admin.birth_journal.paid_kundli',['type'=>0])}}">
                <div class="order-stats__content">
                    <i class="tio-all_done">all_done</i>
                    <h6 class="order-stats__subtitle">{{ translate('pdf_failed')}}</h6>
                </div>
                <span class="order-stats__title">
                    @php
                    echo \App\Models\BirthJournalKundali::where('payment_status',1)->where('kundali_pdf','=','')->whereHas('birthJournal_kundali', function ($query) {
                    $query->where('name', 'kundali');
                    })->with('birthJournal_kundali')->count();
                    @endphp
                </span>
            </a>
        </div>
    </div>
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
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" value="{{ request('searchValue') }}" required>
                                    @if(request('type') == 0 || request('type') == 1)
                                    <input type="hidden" name="type" value="{{ request('type') }}">
                                    @endif
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
                                    <th>{{ translate('user_info') }}</th>                                    
                                    <th class="text-center"> {{ translate('kundali_type') }}</th>
                                    <th class="text-center"> {{ translate('amount') }}</th>
                                    <th class="text-center"> {{ translate('type') }}</th>
                                    <th class="text-center"> {{ translate('booking_date') }}</th>
                                    @if (Helpers::modules_permission_check('Birth Journal', 'Paid Kundali', 'detail'))
                                    <th class="text-center"> {{ translate('option') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getData as $key => $brand)
                                <tr>
                                    <td>{{ $getData->firstItem()+$key }}</td>
                                    <td><span class="font-weight-bolder"> {{ ($brand['order_id']??"") }}</span></td>
                                    <td>
                                        <span>{{ ($brand['userData']['name']??"")}}</span><br>
                                        <span>{{($brand['userData']['phone']??"")}}</span><br>
                                        <span>{{($brand['userData']['email']??"")}}</span><br>
                                    </td>
                                    <td class="text-center">{{ ((($brand['birthJournal']['type']??"") == 'pro')?"Professional":"Basic") }}</td>
                                    
                                    <td class="text-center">{{ ($brand['amount']??"") }}</td>
                                    <td class="text-center">{{ translate($brand['chart_style']??"") }}</td>
                                    <td class="text-center">{{ date('d M,Y h:i A',strtotime($brand['created_at']??"")) }}</td>
                                    @if (Helpers::modules_permission_check('Birth Journal', 'Paid Kundali', 'detail'))
                                    <td class="text-center">
                                        <a href="{{ route('admin.birth_journal.view-kundali-milan', ['id'=>$brand['id']]) }}" target="_block" class='btn btn-sm btn-info' data-toggle="tooltip" title="details"><i class='tio-invisible'></i></a>
                                        <a href="{{ route('admin.birth_journal.order.generate-invoice', ['id'=>$brand['id']]) }}" target="_block" class='btn btn-sm btn-info' data-toggle="tooltip" title="Invoice"><i class='tio-document'></i></a>
                                        @if(($brand['payment_status']??"") == 1)
                                        @if(($brand["kundali_pdf"]??"") == "")
                                        <a href="{{ route('admin.birth_journal.reupload-birth-pdf',['id'=>$brand['id']])}}" target="_block" class='btn btn-sm btn-danger' data-toggle="tooltip" title="Upload pdf"><i class='tio-publish'></i></a>
                                        @else
                                        <a href="{{  dynamicStorage(path: 'storage/app/public/birthjournal/kundali/'.($brand['kundali_pdf']??'')) }}" target="_block" class='btn btn-sm btn-success' data-toggle="tooltip" title="download pdf"><i class='tio-arrow_large_downward'>arrow_large_downward</i></a>
                                        @endif
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
                        @if(request('type') == 0 || request('type') == 1)
                        {{ $getData->appends(['type'=>request('type'),'page' => request('page')])->links() }}
                        @else
                        {{ $getData->links() }}
                        @endif
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