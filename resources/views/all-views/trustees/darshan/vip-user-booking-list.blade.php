@extends('layouts.back-end.app-trustees')
@section('title', translate('VIP_darshan_User_List'))
@php
use App\Utils\Helpers;
@endphp
@push('css_or_js')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
            {{ translate('VIP_darshan_User_List') }}
        </h2>
    </div>

    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group input-group-custom input-group-merge">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group input-group-custom input-group-merge">
                                <input type="datetime-local" class="form-control start_date">
                                <input type="datetime-local" class="form-control end_date">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group input-group-custom input-group-merge">
                                <select class="temple_id form-control">
                                    <option value="">Select Temple</option>
                                    @if($templeList)
                                    @foreach($templeList as $val)
                                    <option value="{{ $val['id']}}">{{ $val['name']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-md-12">
                            <table id="table" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Order Id</th>
                                        <th>Type</th>
                                        <th>package</th>
                                        <th>Temple Name</th>
                                        <th>Date Time</th>
                                        <th>Use info</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    $(document).ready(function() {
        let table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            buttons: [
                'csv', 'excel', 'pdf', 'print'
            ],
            dom: 'Blfrtip',
            ajax: {
                url: "{{ route('trustees-vendor.vip-darshan.darshan-booking-filters') }}",
                type: "GET",
                data: function(d) {
                    d.start_date = $('.start_date').val();
                    d.end_date = $('.end_date').val();
                    d.temple_id = $('.temple_id').val();
                    d.searchValue = $('#datatableSearch_').val();
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'order_id'
                },
                {
                    data: 'title'
                },
                {
                    data: 'package_name'
                },
                {
                    data: 'temple_name'
                },
                {
                    data: "date"
                },
                {
                    data: 'useinfo'
                },
            ]
        });
        $('.start_date, .end_date, .temple_id').on('change', function() {
            table.ajax.reload();
        });
        let searchDelay;
        $('#datatableSearch_').on('keyup', function() {
            clearTimeout(searchDelay);
            searchDelay = setTimeout(function() {
                table.ajax.reload();
            }, 500);
        });
    });
</script>
@endpush