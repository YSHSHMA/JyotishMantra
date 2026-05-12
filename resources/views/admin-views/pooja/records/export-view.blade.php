@extends('layouts.back-end.app')

@section('title', 'Export Pooja Records')
@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="card p-4">
        <h4>{{ ucfirst($range) }} Pooja Record Export</h4>
        <table id="exportTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Service</th>
                    <th>Devotee</th>
                    <th>Amount</th>
                    <th>Booking Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record->service->name ?? 'N/A' }}</td>
                        <td>{{ $record->devotee->name ?? 'N/A' }}</td>
                        <td>₹{{ number_format($record->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->booking_date)->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $('#exportTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print']
    });
</script>
@endpush
