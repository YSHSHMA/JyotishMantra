@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('temple_list'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ translate('temple_list') }}
            <span class="badge badge-soft-dark radius-50 fz-14"></span>
        </h2>
    </div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">

                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="pandit-withdrawal-list" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('temple_name') }}</th>
                                    <th class="max-width-100px">{{ translate('pandit_info') }}</th>
                                    <th class="max-width-100px">{{ translate('request_amount') }}</th>
                                    <th class="text-center">{{ translate('status') }}</th>
                                    <th class="text-center"> {{ translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script>
$(document).ready(function() {
        initDataTable({
            tableId: '#pandit-withdrawal-list',
            ajaxUrl: "{{ route('admin.temple.temple-pandit-withdrawal-history-filter') }}",
            exportTitle: "withdrawal list",
            pageLength: 25,
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'temple_name',
                    name: 'temple_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'pandit_name',
                    name: 'pandit_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'request_amount',
                    name: 'request_amount',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'create_by',
                    name: 'create_by',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'option',
                    name: 'option',
                    orderable: false,
                    searchable: false
                },
            ],
            extraOptions: {
                serverSide: true,
                ajax: {
                    data: function(d) {
                        d.search_by_name = $('.search_by_name').val();
                        d.search_by_type = $('.search_by_type').val();
                        d.search_by_cabid = $('.search_by_cab_name').val();
                    }
                }
            }
        });
    });

    $('.search_by_type, .search_by_cab_name').on('change', function() {
        $('#pandit-withdrawal-list').DataTable().draw();
    });
    let searchDelay;
    $('.search_by_name').on('keyup', function() {
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function() {
            $('#pandit-withdrawal-list').DataTable().draw();
        }, 500);
    });
    </script>
@endpush