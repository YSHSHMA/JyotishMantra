@extends('layouts.back-end.app')

@section('title', translate('pandit'))

@section('content')

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('verfied_pandit') }}
                {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $festivals->total() }}</span> --}}
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('admin.pandit.add') }}" type="button" class="btn btn-outline--primary">
                                    {{ translate('add_Pandit') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('#') }}</th>
                                        <th>{{ translate('Image') }}</th>
                                        <th>{{ translate('Name') }}</th>
                                        <th>{{ translate('Email') }}</th>
                                        <th>{{ translate('Mobile No') }}</th>
                                        <th>{{ translate('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($verfiedPandit as $key => $value)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td> <img src="{{ asset('storage/pandit/profile/' . $value['image']) }}"
                                                    alt="" width="50"></td>
                                            <td>{{ $value->name }}</td>
                                            <td>{{ $value->email }}</td>
                                            <td>{{ $value->mobile_no }}</td>
                                            <td>
                                                <div class="d-flex justify-content-start gap-2">
                                                    <span class="btn btn-outline-danger btn-sm square-btn verify-pandit"
                                                        title="{{ translate('reject_pandit') }}"
                                                        data-id="verify-{{ $value['id'] }}">
                                                        <i class="tio-blocked"></i>
                                                    </span>
                                                    <form action="{{ route('admin.pandit.verify') }}" method="post"
                                                        id="verify-{{ $value['id'] }}">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $value['id'] }}">
                                                        <input type="hidden" name="status" value="0">
                                                    </form>

                                                    {{-- <a class="btn btn-outline-info btn-sm square-btn"
                                                    title="{{ translate('edit') }}" href="javascript:0" data-id="{{$value->id}}" data-name="{{$value->name}}"  onclick="editModal(this)">
                                                    <i class="tio-edit"></i>
                                                </a> --}}
                                                    {{-- <a class="btn btn-outline-danger btn-sm delete delete-data"
                                                    href="javascript:" title="{{ translate('delete') }}">
                                                    <i class="tio-delete"></i>
                                                </a> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $verfiedPandit->links() }}
                        </div>
                    </div>
                    @if (count($verfiedPandit) == 0)
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
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

    <script>
        $('.verify-pandit').on('click', function() {
            let panditId = $(this).attr("data-id");
            Swal.fire({
                title: 'Are You Sure To Verify Pandit',
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + panditId).submit();
                }
            });
        });
    </script>
@endpush
