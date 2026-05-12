@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('astrologer'))

@section('content')

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('blocked_astrologer_&_pandit') }}
                {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $festivals->total() }}</span> --}}
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            {{-- <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('admin.astrologers.manage.add-new') }}" type="button" class="btn btn-outline--primary">
                                    {{ translate('add_astrologer') }}
                                </a>
                            </div> --}}
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
                                        <th>{{ translate('Contact Info') }}</th>
                                        <th>{{ translate('Type') }}</th>
                                        <th>{{ translate('Service Type') }}</th>
                                        <th>{{ translate('Total Services') }}</th>
                                        <th>{{ translate('Total Orders') }}</th>
                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Block', 'verify'))
                                        <th>{{ translate('Action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($blocked as $key => $value)
                                        @php
                                            $totalPooja = !empty($value['is_pandit_pooja'])
                                            ? count(json_decode($value['is_pandit_pooja'], true))
                                            : 0;
                                            $totalVipPooja = !empty($value['is_pandit_vippooja'])
                                                ? count(json_decode($value['is_pandit_vippooja'], true))
                                                : 0;
                                            $totalAnushthan = !empty($value['is_pandit_anushthan'])
                                                ? count(json_decode($value['is_pandit_anushthan'], true))
                                                : 0;
                                            $totalChadhava = !empty($value['is_pandit_chadhava'])
                                                ? count(json_decode($value['is_pandit_chadhava'], true))
                                                : 0;
                                            $totalConsultation = !empty($value['consultation_charge'])
                                                ? count(json_decode($value['consultation_charge'], true))
                                                : 0;
                                            $totalService = $totalPooja + $totalVipPooja + $totalAnushthan + $totalChadhava + $totalConsultation;
                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td> <img
                                                    src="{{$value['image']}}"
                                                    alt="" width="50"></td>
                                            <td>{{ @ucwords($value->name) }}</td>
                                            <td><b>{{ $value->email }}</b> <br> {{ $value->mobile_no }}</td>
                                            <td>{{ @ucwords($value->type) }}</td>
                                            <td>{{ $value['primarySkill']['name'] }}</td>
                                            <td>{{ $totalService }}</td>
                                            <td>{{ !empty($value['orders']) ? count($value['orders']) : 0 }}</td>
                                            @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Block', 'verify'))
                                            <td>
                                                <div class="d-flex justify-content-start gap-2">
                                                    <span
                                                        class="btn btn-outline-success btn-sm square-btn unblock-astrologer"
                                                        title="{{ translate('unblock_astrologer') }}"
                                                        data-id="unblock-{{ $value['id'] }}">
                                                        <i class="tio-checkmark-circle-outlined"></i>
                                                    </span>
                                                    <form action="{{ route('admin.astrologers.manage.status') }}"
                                                        method="post" id="unblock-{{ $value['id'] }}">
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
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $blocked->links() }}
                        </div>
                    </div>
                    @if (count($blocked) == 0)
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

    {{-- unblock astrologer --}}
    <script>
        $('.unblock-astrologer').on('click', function() {
            let astrologerId = $(this).attr("data-id");
            Swal.fire({
                title: 'Are You Sure To Unblock Astrologer',
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + astrologerId).submit();
                }
            });
        });
    </script>
    </script>
@endpush
