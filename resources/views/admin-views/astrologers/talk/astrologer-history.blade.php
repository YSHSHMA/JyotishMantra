@extends('layouts.back-end.app')

@section('title', 'Astrologer Wallet Transactions')
@push('css_or_js')
<style>
    .rotate-icon {
        animation: spin 2s linear infinite, moveX 3s ease-in-out infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @keyframes moveX {
        0% {
            transform: translateX(0);
        }

        50% {
            transform: translateX(-20px);
        }

        100% {
            transform: translateX(0);
        }
    }
</style>
@endpush

@section('content')
<div class="container">

<h1 class="mb-4">
        Astrologer Wallet Transactions
        ({{ $transactions->first()->astrologer->name ?? 'Unknown' }})
    </h1>

    {{-- ===================== TOP STATS CARDS ===================== --}}
    @php
    $total_paid = $transactions->sum('total_amount_paid');
    $total_earning = $transactions->sum('astrologer_earning');
    $total_commission = $transactions->sum('commission_amount');

    // New Stats Based on payment_type
    $total_voice_calls = $transactions->where('payment_type', 'voice')->count();
    $total_video_calls = $transactions->where('payment_type', 'video')->count();
    $total_chat = $transactions->where('payment_type', 'chat')->count();
    @endphp

    {{-- ROW 1 (Top 3 Cards) --}}
    <div class="row g-3 mb-4">

        {{-- Total Paid --}}
        <div class="col-md-4">
            <div class="card card-body h-100 justify-content-center">
                <div class="d-flex gap-2 justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fz-24 text-success">
                            ₹{{ number_format($total_paid, 2) }}
                        </h3>
                        <div class="mb-0 text-success">Total Paid</div>
                    </div>
                    <img width="40" class="rotate-icon"
                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}" alt="">
                </div>
            </div>
        </div>

        {{-- Astrologer Earnings --}}
        <div class="col-md-4">
            <div class="card card-body h-100 justify-content-center">
                <div class="d-flex gap-2 justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fz-24 text-primary">
                            ₹{{ number_format($total_earning, 2) }}
                        </h3>
                        <div class="mb-0 text-primary">Astrologer Earnings</div>
                    </div>
                    <img width="40" class="rotate-icon"
                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}" alt="">
                </div>
            </div>
        </div>

        {{-- Commission --}}
        <div class="col-md-4">
            <div class="card card-body h-100 justify-content-center">
                <div class="d-flex gap-2 justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fz-24 text-warning">
                            ₹{{ number_format($total_commission, 2) }}
                        </h3>
                        <div class="mb-0 text-warning">Total Commission</div>
                    </div>
                    <img width="40" class="rotate-icon"
                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}" alt="">
                </div>
            </div>
        </div>

    </div>

    {{-- ROW 2 (Voice + Video + Chat Cards) --}}
    <div class="row g-3 mb-4">

        {{-- Voice Calls --}}
        <div class="col-md-4">
            <div class="card card-body h-100 justify-content-center">
                <div class="d-flex gap-2 justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fz-24 text-info">
                            {{ $total_voice_calls }}
                        </h3>
                        <div class="mb-0 text-info">Voice Calls</div>
                    </div>
                    <img width="40" src="{{ dynamicAsset(path: 'public/assets/back-end/img/talk/call.png') }}" alt="">
                </div>
            </div>
        </div>

        {{-- Video Calls --}}
        <div class="col-md-4">
            <div class="card card-body h-100 justify-content-center">
                <div class="d-flex gap-2 justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fz-24 text-primary">
                            {{ $total_video_calls }}
                        </h3>
                        <div class="mb-0 text-primary">Video Calls</div>
                    </div>
                    <img width="40" src="{{ dynamicAsset(path: 'public/assets/back-end/img/talk/videocall.png') }}" alt="">
                </div>
            </div>
        </div>

        {{-- Chat --}}
        <div class="col-md-4">
            <div class="card card-body h-100 justify-content-center">
                <div class="d-flex gap-2 justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fz-24 text-success">
                            {{ $total_chat }}
                        </h3>
                        <div class="mb-0 text-success">Chat</div>
                    </div>
                    <img width="40" src="{{ dynamicAsset(path: 'public/assets/back-end/img/talk/chat.png') }}" alt="">
                </div>
            </div>
        </div>

    </div>

    {{-- ===================== FILTERS ===================== --}}
    <div class="card mb-3">
        <div class="px-3 py-4">
            <h4 class="mb-3">Filter Transactions</h4>
            <form method="GET" action="{{ url()->current() }}" class="row g-3">

                {{-- Payment Type --}}
                <div class="col-md-3">
                    <label for="payment_type" class="font-weight-bold">Payment Type</label>
                    @php
                    $paymentTypes = ['video' => 'Video', 'audio' => 'Audio', 'chat' => 'Chat'];
                    @endphp
                    <select name="payment_type" id="payment_type" class="form-control" onchange="this.form.submit()">
                        <option value="">All Payment Types</option>
                        @foreach($paymentTypes as $key => $type)
                        <option value="{{ $key }}" {{ request('payment_type') == $key ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Call Start Time --}}
                <div class="col-md-3">
                    <label for="call_start" class="font-weight-bold">Call Start Time</label>
                    <input type="datetime-local" name="call_start" id="call_start" class="form-control"
                        value="{{ request('call_start') }}"
                        onchange="this.form.submit()">
                </div>

                {{-- Call End Time --}}
                <div class="col-md-3">
                    <label for="call_end" class="font-weight-bold">Call End Time</label>
                    <input type="datetime-local" name="call_end" id="call_end" class="form-control"
                        value="{{ request('call_end') }}"
                        onchange="this.form.submit()">
                </div>

                {{-- Clear Filters --}}
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ url()->current() }}" class="btn btn-secondary w-100">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TRANSACTION TABLE --}}
    <div class="table-responsive">
        <table id="table" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Astrologer</th>
                    <th>User</th>
                    <th>Call Start</th>
                    <th>Call End</th>
                    <th>Duration (min)</th>
                    <th>Payment Type</th>
                    <th>Total Paid</th>
                    <th>Astrologer Earning</th>
                    <th>Commission %</th>
                    <th>Commission Amount</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($transactions as $key => $transaction)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $transaction->astrologer->name ?? '-' }}</td>
                    <td>{{ $transaction->user->name && trim($transaction->user->name) !== '' ? $transaction->user->name : 'Unknown' }}</td>
                    <td>{{ $transaction->start_time }}</td>
                    <td>{{ $transaction->end_time }}</td>
                    <td>{{ $transaction->duration_minutes }}</td>
                    <td>
                        <span class="badge 
                            @if($transaction->payment_type == 'audio')
                                bg-success
                            @elseif($transaction->payment_type == 'video')
                                bg-primary
                            @elseif($transaction->payment_type == 'chat')
                                bg-warning
                            @else
                                bg-secondary
                            @endif
                        ">
                            {{ ucfirst(str_replace('_', ' ', $transaction->payment_type)) }}
                        </span>
                    </td>
                    <td>₹{{ number_format($transaction->total_amount_paid, 2) }}</td>
                    <td>₹{{ number_format($transaction->astrologer_earning, 2) }}</td>
                    <td>{{ $transaction->commission_rate }}%</td>
                    <td>₹{{ number_format($transaction->commission_amount, 2) }}</td>
                    <td>
                        <span class="badge 
                            @if($transaction->transaction_status == 'completed')
                                bg-success
                            @elseif($transaction->transaction_status == 'pending')
                                bg-warning
                            @else
                                bg-danger
                            @endif
                        ">
                            {{ ucfirst($transaction->transaction_status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center text-danger">No Records Found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        // Trigger filter on change or typing
        $('#payment_type, #user_name').on('change keyup', function() {
            fetchFilteredTransactions();
        });

        // Clear filters
        $('#clearFilters').click(function() {
            $('#payment_type').val('');
            $('#user_name').val('');
            fetchFilteredTransactions();
        });

        function fetchFilteredTransactions() {
            $.ajax({
                url: "{{ url()->current() }}",
                type: "GET",
                data: $('#filterForm').serialize(),
                success: function(response) {
                    // Replace table content
                    $('#transactionsTable').html(response);
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                }
            });
        }

    });
</script>

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            pageLength: 20,
            scrollY: '500px',
            scrollCollapse: true,
            paging: true,
            fixedHeader: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
        });
    });
</script>
@endpush