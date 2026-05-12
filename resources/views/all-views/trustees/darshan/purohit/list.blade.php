@extends('layouts.back-end.app-trustees')
@php use App\Utils\Helpers; @endphp

@section('title', translate('purohit_list'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
                {{ translate('purohit_list') }}
                <span class="badge badge-soft-dark radius-50 fz-14"></span>
            </h2>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#temple-list">
                Add Pandit
            </button>
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
                                        <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                            placeholder="{{ translate('search_by_name') }}"
                                            aria-label="{{ translate('search_by_name') }}"
                                            value="{{ request('searchValue') }}" required>
                                        <button type="submit"
                                            class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <div class="table-responsive">
                                        <table id="purohitTable" class="table table-striped table-bordered table-hover">
                                            <thead class="thead-light text-capitalize">
                                                <tr>
                                                    <th>{{ translate('SL') }}</th>
                                                    <th>{{ translate('purohit_name') }}</th>
                                                    <th>{{ translate('purohit_mobile') }}</th>
                                                    <th>{{ translate('purohit_address') }}</th>
                                                    <th>{{ translate('temple_name') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($purohitList as $key => $item)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>{{ $item->mobile }}</td>
                                                        <td>{{ $item->address ?? '-' }}</td>
                                                        <td>{{ $item->temple ? $item->temple->name : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>{{ translate('SL') }}</th>
                                                    <th>{{ translate('purohit_name') }}</th>
                                                    <th>{{ translate('purohit_mobile') }}</th>
                                                    <th>{{ translate('purohit_address') }}</th>
                                                    <th>{{ translate('temple_name') }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="route-admin-rashi-status-update" data-url="{{ route('admin.temple.status-update') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#purohitTable').DataTable({
                responsive: true,
                pageLength: 20,
                autoWidth: false,
                scrollY: '450px',
                scrollX: true,
                scrollCollapse: true,
                fixedHeader: {
                    header: true,
                    footer: true
                },
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "{{ translate('search_here') }}",
                    lengthMenu: "{{ translate('Show') }} _MENU_",
                    info: "{{ translate('Showing') }} _START_ {{ translate('to') }} _END_ {{ translate('of') }} _TOTAL_ {{ translate('entries') }}",
                    paginate: {
                        previous: "&laquo;",
                        next: "&raquo;"
                    }
                }
            });
        });
    </script>
@endpush