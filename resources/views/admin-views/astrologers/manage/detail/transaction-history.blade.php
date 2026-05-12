@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('transaction_history'))

@section('content')

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('transaction_history') }}
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="js-nav-scroller hs-nav-scroller-horizontal mb-5">
                    <ul class="nav nav-tabs flex-wrap page-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.overview', $id) }}">Overview</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.order', $id) }}">Order</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'service'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.service', $id) }}">Service</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.setting', $id) }}">Setting</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'transaction'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.transaction', $id) }}">Transaction</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link active"
                                href="{{ route('admin.astrologers.manage.detail.transaction.history', $id) }}">Transaction
                                History</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'review'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.review', $id) }}">Review</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.history', $id) }}">History</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content mt-5">
                    <div class="tab-pane fade show active" id="order">
                        <div class="row pt-2">
                            <div class="col-md-12">
                                <div class="card w-100">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100 text-start">
                                                    <thead class="thead-light thead-50 text-capitalize">
                                                        <tr>
                                                            <th>{{ translate('#') }}</th>
                                                            <th>{{ translate('Service Name') }}</th>
                                                            <th>{{ translate('Total Order') }}</th>
                                                            <th>{{ translate('Booking Date') }}</th>
                                                            <th>{{ translate('Type') }}</th>
                                                            <th>{{ translate('Order Amount') }}</th>
                                                            <th>{{ translate('Admin Commission') }}</th>
                                                            <th>{{ translate('Govt. Tax') }}</th>
                                                            <th>{{ translate('Pandit Price') }}</th>
                                                            <th>{{ translate('Company Received') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $totalOrderAmt = 0;
                                                            $totalCommissionAmt = 0;
                                                            $totalTaxAmt = 0;
                                                            $totalPanditAmt = 0;
                                                            $totalCompanyAmt = 0;
                                                            $transData = '';
                                                        @endphp
                                                        @foreach ($transactionHistory as $key => $trans)
                                                            @php
                                                            $adminCommission = ($trans['total_amount']*$trans['admin_commission'])/100;
                                                            $govtTax = ($trans['total_amount']*$trans['govt_tax'])/100;
                                                            $companyReceived = $trans['total_amount'] - $adminCommission - $govtTax - $trans['pandit_amount'];

                                                                $totalOrderAmt += (float) $trans['total_amount'];
                                                                $totalCommissionAmt +=
                                                                    (float) $adminCommission;
                                                                $totalTaxAmt += (float) $govtTax;
                                                                $totalPanditAmt += (float) $trans['pandit_amount'];
                                                                $totalCompanyAmt += (float) $companyReceived;
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $key + 1 }}</td>
                                                                @if ($trans->type == 'counselling' || $trans->type == 'pooja')
                                                                <td>{{ $trans->serviceOrder->services->name }}</td>
                                                                @elseif ($trans->type == 'anushthan' || $trans->type == 'vip')
                                                                <td>{{ $trans->serviceOrder->vippoojas->name }}</td>
                                                                @elseif ($trans->type == 'offlinepooja')
                                                                <td>{{ $trans->offlinepoojaOrder->offlinePooja->name }}</td>
                                                                @elseif ($trans->type == 'chadhava')
                                                                <td>{{ $trans->chadhavaOrder->chadhava->name }}</td>
                                                                @endif
                                                                
                                                                <td>{{ $trans['total_orders'] }}</td>
                                                                <td>{{ $trans['booking_date']??'NA' }}</td>
                                                                <td>{{ $trans['type'] }}</td>
                                                                <td>{{ $trans['total_amount'] }}</td>
                                                                <td>{{ $adminCommission }}</td>
                                                                <td> {{ $govtTax }}</td>
                                                                <td>{{ $trans['pandit_amount'] }}</td>
                                                                <td>{{ $companyReceived }}</td>
                                                            </tr>
                                                        @endforeach

                                                        {{-- @foreach ($kundaliOrder as $key1 => $kund)
                                                            @php
                                                                $totalOrderAmt += (float) $kund['total_amount'];
                                                                $totalCommissionAmt +=
                                                                    (float) $kund['total_commission'];
                                                                $totalTaxAmt += (float) $kund['total_tax'];
                                                                $totalPanditAmt += (float) $kund['pandit_price'];
                                                                $totalCompanyAmt += (float) $kund['company_received'];
                                                            @endphp
                                                            <tr>
                                                                <td>{{ ($key ?? 0) + $key1 + 2 }} </td>
                                                                <td>{{ ucwords(str_replace('_', ' ', $kund['birthJournal_kundalimilan']['name'])) }}
                                                                </td>
                                                                <td>{{ $kund['total_orders'] }}</td>
                                                                <td>{{ ($kund['birthJournal_kundalimilan']['type'] ?? '') == 'pro' ? 'Professional' : 'basic' }}
                                                                </td>
                                                                <td>{{ '₹ ' . $kund['total_amount'] }}</td>
                                                                <td>{{ '₹ ' . $kund['total_commission'] }}
                                                                </td>
                                                                <td>{{ '₹ ' . $kund['total_tax'] }}
                                                                </td>
                                                                \<td>{{ '₹ ' . $kund['pandit_price'] }}
                                                                </td>
                                                                <td>{{ '₹ ' . $kund['company_received'] }}
                                                                </td>
                                                            </tr>
                                                        @endforeach --}}
                                                    </tbody>
                                                    @if (count($transactionHistory) > 0)
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="5" class="text-end">Total</td>
                                                                <td>{{ '₹ ' . $totalOrderAmt }}</td>
                                                                <td>{{ '₹ ' . $totalCommissionAmt }}</td>
                                                                <td>{{ '₹ ' . $totalTaxAmt }}</td>
                                                                <td>{{ '₹ ' . $totalPanditAmt }}</td>
                                                                <td>{{ '₹ ' . $totalCompanyAmt }}</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                            {{-- <div class="table-responsive mt-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    @if (!request()->has('search_type') && !request()->has('search_name') && !request()->has('search_service_type'))
                                                        {{ $transaction->links() }}
                                                    @endif
                                                </div>
                                            </div> --}}
                                            @if (count($transactionHistory) == 0)
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush