@extends('layouts.back-end.app')

@section('title', translate('SDM_Employee_Detail'))

@section('content')
    <div class="content container-fluid">

        <div class="row my-3">
            <div class="card w-100">
                <div class="card-body">
                    <div class="mb-1">
                        <h2 class="h1 mb-0 d-flex gap-2">
                            {{-- <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/collector.jpg') }}"
                                alt=""> --}}
                            {{ translate('SDM_Employee_Detail') }}
                            {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $collectors->total() }}</span> --}}
                        </h2>
                    </div>
                    <div class="row m-3">
                        <div class="col-md-4 d-flex gap-3">
                            <p>Name: </p>
                            <h5>{{ $details->name }}</h5>
                        </div>
                        <div class="col-md-4 d-flex gap-3">
                            <p>Email: </p>
                            <h5>{{ $details->email }}</h5>
                        </div>
                        <div class="col-md-4 d-flex gap-3">
                            <p>Mobile: </p>
                            <h5>{{ $details->mobile }}</h5>
                        </div>

                        <div class="col-md-12 py-2">
                            <h5>Temples</h5>
                            <div class="row">
                                @forelse (json_decode($details->temples,true) as $key=>$templeId)
                                    @php
                                        $templeData = App\Models\Temple::select('name')
                                            ->where('id', $templeId)
                                            ->first();
                                    @endphp
                                    <div class="col-md-4">{{ $key + 1 . ') ' . $templeData->name }}</div>
                                @empty
                                    <p class="text-center">No temple found</p>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
@endpush
