@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', 'Export Filter')

@section('content')
<div class="content container-fluid">
    <div class="card p-4">
        <h4>Export {{ $service }} Data</h4>

        <form action="{{ route('admin.pujarecords.export-download') }}" method="GET" target="_blank">
            <input type="hidden" name="service" value="{{ $service }}">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control" required>
                </div>
            </div>
            @if (Helpers::modules_permission_check('Pooja Records', 'Pooja Records', 'export'))
            <button type="submit" class="btn btn-primary">Export</button>
            @endif
        </form>
    </div>
</div>
@endsection
