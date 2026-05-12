@extends('layouts.back-end.app')
@section('title', 'Puja Details')

@push('css_or_js')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="card p-4">
            <h4>Details for {{ $serviceName }} on {{ \Carbon\Carbon::parse($date)->format('d M Y (l)') }}</h4>
            <p>Total Orders: <strong>{{ $totalOrders }}</strong></p>
            <p>Total Amount: <strong>₹{{ number_format($totalAmount, 2) }}</strong></p>

            {{-- Optional: Date range filter (future expansion) --}}
            <form method="GET" action="{{ route('admin.pujarecords.puja-details', ['service' => $serviceName, 'date' => $date]) }}">
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

            <table class="table table-bordered" id="recordsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Devotee</th>
                        <th>Amount</th>
                        <th>Booking Date</th>
                        <th>Purohit Ji Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>

                                @if (!empty($record->member_names))
                                    <ul class="mb-0 ps-3 text-muted small">
                                        @foreach ($record->member_names as $name)
                                            <li>{{ $name }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>

                            <td>₹{{ number_format($record->amount, 2) }}</td>
                            <td>{{ $record->booking_date }}</td>
                            <td>
                                Pandit: {{ $record->pandit_name ?? 'N/A' }} <br>
                                Phone: {{ $record->pandit_phone ?? 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $('#recordsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'csv',
                    title: 'Puja Details - {{ $serviceName }} - {{ \Carbon\Carbon::parse($date)->format('d M Y') }}',
                    messageTop: 'Service: {{ $serviceName }} | Date: {{ \Carbon\Carbon::parse($date)->format('d M Y (l)') }} | Total Orders: {{ $totalOrders }} | Total Amount: ₹{{ number_format($totalAmount, 2) }}'
                },
                {
                    extend: 'excel',
                    title: 'Puja Details - {{ $serviceName }} - {{ \Carbon\Carbon::parse($date)->format('d M Y') }}',
                    messageTop: 'Service: {{ $serviceName }} | Date: {{ \Carbon\Carbon::parse($date)->format('d M Y (l)') }} | Total Orders: {{ $totalOrders }} | Total Amount: ₹{{ number_format($totalAmount, 2) }}'
                },
                {
                    extend: 'pdf',
                    title: 'Puja Details - {{ $serviceName }}',
                    messageTop: 'Date: {{ \Carbon\Carbon::parse($date)->format('d M Y (l)') }}\\nTotal Orders: {{ $totalOrders }}\\nTotal Amount: ₹{{ number_format($totalAmount, 2) }}',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    title: 'Puja Details - {{ $serviceName }}',
                    messageTop: 'Date: {{ \Carbon\Carbon::parse($date)->format('d M Y (l)') }}\\nTotal Orders: {{ $totalOrders }}\\nTotal Amount: ₹{{ number_format($totalAmount, 2) }}'
                }
            ],
            responsive: true,
            pageLength: 10
        });
    </script>
@endpush
