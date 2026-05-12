@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('guruji_transaction'))

@section('content')

    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}"
                    alt="">
                {{ translate('guruji_transaction') }}
            </h2>
        </div>

        <div class="card mb-3 remove-card-shadow">
            <div class="card-body">
                <div class="row g-2" id="order_stats">
                    <div class="col-lg-12">
                        @php
                            $totalOrders = 0;
                            $totalPujaAmount  = 0;
                            $totalCounsellingAmount  = 0;
                            $totalAdminCommission  = 0;

                            foreach ($gurujies as $key => $guruji){
                                if ($guruji->guruji_order_count > 0){
                                    $totalOrders += $guruji->guruji_order_count;
                                    if (count($guruji->guruji_transaction) > 0) {
                                        foreach ($guruji->guruji_transaction as $transKey => $transaction) {
                                            if($transaction->type == 'panditpooja'){
                                                $totalPujaAmount += $transaction->amount;
                                                if($transaction->individual_commission > 0){
                                                    $totalAdminCommission += ($transaction->amount * $transaction->individual_commission) / 100;
                                                }
                                            } elseif($transaction->type == 'panditcounselling'){
                                                $totalCounsellingAmount += $transaction->amount;
                                                if($transaction->individual_commission > 0){
                                                    $totalAdminCommission += ($transaction->amount * $transaction->individual_commission) / 100;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp

                        

                        <div class="row g-2">
                            <div class="col-md-3">
                                <a href="javascript:0" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-info">
                                                    {{$totalOrders}}
                                                </h3>
                                                <div class="text-capitalize mb-0">TOTAL ORDER</div>
                                            </div>
                                            {{-- <div>
                                                <img width="40" class="mb-2"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/order.png') }}"
                                                    alt="">
                                            </div> --}}
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="javascript:0" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-primary">
                                                    {{$totalPujaAmount}}
                                                </h3>
                                                <div class="text-capitalize mb-0">Puja Amount</div>
                                            </div>
                                            {{-- <div>
                                                <img width="40"
                                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/pending.png') }}"
                                                    alt="">
                                            </div> --}}
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="javascript:0" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-success">
                                                    {{$totalCounsellingAmount}}
                                                </h3>
                                                <div class="text-capitalize mb-0">Counselling Amount</div>
                                            </div>
                                            {{-- <div>
                                                <img width="40"
                                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/ordercom.png') }}"
                                                    alt="">
                                            </div> --}}
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="javascript:0" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-primary">
                                                    {{$totalAdminCommission}}
                                                </h3>
                                                <div class="text-capitalize mb-0">Admin Commission</div>
                                            </div>
                                            {{-- <div>
                                                <img width="40"
                                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/reject.png') }}"
                                                    alt="">
                                            </div> --}}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        

        <div class="row mt-20">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-0 mt-2">
                        <div style="overflow: auto;">
                            <table id="table" class="table table-bordered">
                                <thead >
                                    <tr>
                                        <th>{{ translate('#') }}</th>
                                        <th>{{ translate('Name') }}</th>
                                        <th>{{ translate('total_orders') }}</th>
                                        <th>{{ translate('puja_amount') }}</th>
                                        <th>{{ translate('counselling_amount') }}</th>
                                        <th>{{ translate('admin_commission') }}</th>
                                        {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'detail'))
                                        <th>{{ translate('Action') }}</th>
                                        @endif --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $inc = 0;
                                    @endphp
                                    @foreach ($gurujies as $key => $guruji)
                                        @if ($guruji->guruji_order_count > 0)
                                            @php
                                                $inc++;
                                                $pujaAmount  = 0;
                                                $counsellingAmount  = 0;
                                                $adminCommission  = 0;
                                                if (count($guruji->guruji_transaction) > 0) {
                                                    foreach ($guruji->guruji_transaction as $transKey => $transaction) {
                                                        if($transaction->type == 'panditpooja'){
                                                            $pujaAmount += $transaction->amount;
                                                            if($transaction->individual_commission > 0){
                                                                $adminCommission += ($transaction->amount * $transaction->individual_commission) / 100;
                                                            }
                                                        } elseif($transaction->type == 'panditcounselling'){
                                                            $counsellingAmount += $transaction->amount;
                                                            if($transaction->individual_commission > 0){
                                                                $adminCommission += ($transaction->amount * $transaction->individual_commission) / 100;
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $inc }}</td>
                                                <td>{{ @ucwords($guruji->name) }}</td>
                                                <td>{{$guruji->guruji_order_count}}</td>
                                                <td>{{$pujaAmount}}</td>
                                                <td>{{$counsellingAmount}}</td>
                                                <td>{{$adminCommission}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot >
                                    <tr>
                                        <th>{{ translate('#') }}</th>
                                        <th>{{ translate('Name') }}</th>
                                        <th>{{ translate('total_orders') }}</th>
                                        <th>{{ translate('puja_amount') }}</th>
                                        <th>{{ translate('counselling_amount') }}</th>
                                        <th>{{ translate('admin_commission') }}</th>
                                        {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'detail'))
                                        <th>{{ translate('Action') }}</th>
                                        @endif --}}
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            @if (!request()->has('search_name'))
                                {{ $gurujies->links() }}
                            @endif
                        </div>
                    </div> --}}
                    {{-- @if (count($gurujies) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script>
    let table = $('#table').DataTable({
            pageLength: 20,
            scrollY: '500px',
            scrollCollapse: true,
            paging: true,
            fixedHeader: true,
            fixedFooter: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
        });
</script>
@endpush
