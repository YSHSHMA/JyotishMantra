@extends('layouts.back-end.app-trustees')
@php use App\Utils\Helpers; @endphp

@section('title', translate('temple_lead_list'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ translate('temple_lead_list') }}
        </h2>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#addPanditModal">
            Add Pandit
        </button>
    </div>

    <!-- Add Pandit Modal -->
    <div class="modal fade" id="addPanditModal" tabindex="-1" role="dialog" aria-labelledby="addPanditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPanditModalLabel">Add Pandit Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal body can go here -->
            </div>
        </div>
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
                                    <input id="datatableSearch_" type="search" name="searchValue"
                                        class="form-control" placeholder="{{ translate('search_by_name') }}"
                                        aria-label="{{ translate('search_by_name') }}"
                                        value="{{ request('searchValue') }}" required>
                                    <button type="submit" class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                </div>
                            </form>
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
                                    <th>{{ translate('temple_name') }}</th>
                                    <th>{{ translate('yajman Name') }}</th>
                                    <th>{{ translate('amount') }}</th>
                                    <th>{{ translate('payment_mode') }}</th>
                                    <th>{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($LeadList as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->order_id }}<br>
                                        @if($item->payment_status == 0)
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($item->payment_status == 1)
                                                <span class="badge badge-success">Confirmed</span>
                                            @elseif($item->payment_status == 2)
                                                <span class="badge badge-danger">Cancelled</span>
                                            @else
                                                <span class="badge badge-secondary">Unknown</span>
                                            @endif</td>
                                        <td>{{ $item->temple->name ?? '-' }}</td>
                                        <td>{{ $item->user->name ?? '-' }} ({{ $item->customer_qty ?? 1 }})</td>
                                        <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item->amount ?? 0), currencyCode: getCurrencyCode()) }}</td>
                                        <td>
                                            @if(strtolower($item->payment_mode) == 'cash')
                                                <span class="badge badge-danger">{{ $item->payment_mode }}</span>
                                            @elseif(strtolower($item->payment_mode) == 'online')
                                                <span class="badge badge-success">{{ $item->payment_mode }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $item->payment_mode }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#leadDetailsModal{{ $item->id }}">
                                                <i class="tio tio-info"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @foreach ($LeadList as $item)
                    <div class="modal fade" id="leadDetailsModal{{ $item->id }}" tabindex="-1" aria-labelledby="leadDetailsModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="leadDetailsModalLabel{{ $item->id }}">Lead Details - Order #{{ $item->order_id }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <strong>Payment Mode:</strong> {{ $item->payment_mode }}
                                        </div>
                                        <div>
                                            <strong>Payment Status:</strong>
                                            @if($item->payment_status == 0)
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($item->payment_status == 1)
                                                <span class="badge badge-success">Confirmed</span>
                                            @elseif($item->payment_status == 2)
                                                <span class="badge badge-danger">Cancelled</span>
                                            @else
                                                <span class="badge badge-secondary">Unknown</span>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>Amount:</strong> 
                                            <span style="font-size: 1.3rem; font-weight: bold;"> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item->amount ?? 0), currencyCode: getCurrencyCode()) }}</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <h6>Services Booked:</h6>
                                    <div class="row">
                                        @foreach ($item->details as $detail)
                                            <div class="col-md-4 mb-3">
                                                <div class="ticket-card h-100 border p-3 rounded shadow-sm">
                                                    <h6 class="mb-1">{{ ucfirst($detail->type ?? '-') }} - {{ $detail->package->varient_name ?? '-' }}</h6>
                                                    <p class="mb-1">{{ !empty($detail->booking_date) ? \Carbon\Carbon::parse($detail->booking_date)->format('d M Y') : '-' }}</p>
                                                    <p class="mb-1">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $detail->amount ?? 0), currencyCode: getCurrencyCode()) }}</p>
                                                    <p class="mb-1">{{ $detail->type_order_id }}</p>
                                                    <p class="mb-1">
                                                    {{ !empty($detail->timeslot) ? \Carbon\Carbon::parse($detail->timeslot->start_time)->format('h:i A') : '-' }} 
                                                        - 
                                                    {{ !empty($detail->timeslot) ? \Carbon\Carbon::parse($detail->timeslot->end_time)->format('h:i A') : '-' }}
                                                    </p>



                                                    @php
                                                        $members = json_decode($detail->customers ?? '[]', true);
                                                    @endphp

                                                    @if(!empty($members))
                                                        <p class="mb-1">Yajman Information:</p>
                                                        <ul class="list-group">
                                                            @foreach($members as $member)
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    {{ $member['name'] ?? $member['mobile'] ?? 'N/A' }}
                                                                    @if(!empty($member['aadhar']))
                                                                        <span class="badge badge-secondary">Aadhaar: {{ $member['aadhar'] }}</span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="mb-0">No members added.</p>
                                                    @endif

                                                    <p class="mt-2"><strong>Purohit / Pandit:</strong> {{ $detail->purohit->name ?? '-' }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <hr>
                                    <p><strong>Temple Location:</strong>
                                        {{ $item->temple->name ?? '-' }},
                                        {{ $item->temple->cities->city ?? '' }},
                                        {{ ucwords(strtolower($item->temple->states->name ?? '')) }},
                                        {{ $item->temple->country->name ?? '' }}
                                    </p>
                                    <p><strong>Yajman Name:</strong> {{ $item->user->name ?? '-' }} ({{ $item->customer_qty ?? 1 }} persons)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach


            </div>
        </div>
    </div>
</div>

<span id="route-admin-rashi-status-update" data-url="{{ route('admin.temple.status-update') }}"></span>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
@endpush
