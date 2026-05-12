@extends('layouts.back-end.app')

@section('title', translate('collector_Detail'))

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
                            {{ translate('collector_Detail') }}
                            {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $collectors->total() }}</span> --}}
                        </h2>
                    </div>
                    <div class="row m-3">
                        <div class="col-md-6 d-flex gap-3">
                            <p>Name: </p>
                            <h5>{{ $details->name }}</h5>
                        </div>
                        <div class="col-md-6 d-flex gap-3">
                            <p>District: </p>
                            <h5>{{ $details->district_name }}</h5>
                        </div>
                        <div class="col-md-6 d-flex gap-3">
                            <p>Email: </p>
                            <h5>{{ $details->email }}</h5>
                        </div>
                        <div class="col-md-6 d-flex gap-3">
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

        @forelse ($details->sdms as $sdm)
            <div class="row my-3">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="mb-1">
                            <h2 class="h1 mb-0 d-flex gap-2">
                                {{-- <img width="20"
                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/collector.jpg') }}"
                                    alt=""> --}}
                                {{ translate('SDM_Detail') }}
                                {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $collectors->total() }}</span> --}}
                            </h2>
                        </div>

                        <div class="row m-3">
                            <div class="col-md-4 d-flex gap-3">
                                <p>Name: </p>
                                <h5>{{ $sdm->name }}</h5>
                            </div>
                            <div class="col-md-4 d-flex gap-3">
                                <p>Email: </p>
                                <h5>{{ $sdm->email }}</h5>
                            </div>
                            <div class="col-md-4 d-flex gap-3">
                                <p>Mobile: </p>
                                <h5>{{ $sdm->mobile }}</h5>
                            </div>

                            <div class="col-md-12 py-2">
                                <h5>Temples</h5>
                                <div class="row">
                                    @forelse (json_decode($sdm->temples,true) as $key=>$templeId)
                                        @php
                                            $sdmTempleData = App\Models\Temple::select('name')
                                                ->where('id', $templeId)
                                                ->first();
                                        @endphp
                                        <div class="col-md-4">{{ $key + 1 . ') ' . $sdmTempleData->name }}</div>
                                    @empty
                                        <p class="text-center">No temple found</p>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                        
                        <hr>

                        @forelse ($sdm->employees as $employee)                            
                            <h4>Employee Detail</h4>
                            <div class="row m-3">
                                <div class="col-md-4 d-flex gap-3">
                                    <p>Name: </p>
                                    <h5>{{ $employee->name }}</h5>
                                </div>
                                <div class="col-md-4 d-flex gap-3">
                                    <p>Email: </p>
                                    <h5>{{ $employee->email }}</h5>
                                </div>
                                <div class="col-md-4 d-flex gap-3">
                                    <p>Mobile: </p>
                                    <h5>{{ $employee->mobile }}</h5>
                                </div>

                                <div class="col-md-12 py-2">
                                    <h5>Temples</h5>
                                    <div class="row">
                                        @forelse (json_decode($employee->temples,true) as $key=>$templeId)
                                            @php
                                                $employeeTempleData = App\Models\Temple::select('name')
                                                    ->where('id', $templeId)
                                                    ->first();
                                            @endphp
                                            <div class="col-md-4">{{ $key + 1 . ') ' . $employeeTempleData->name }}</div>
                                        @empty
                                            <p class="text-center">No temple found</p>
                                        @endforelse
                                    </div>
                                </div>

                            </div>
                        @empty
                        <div class="my-2 py-2 text-center">
                            <p>No Employee Found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="row my-3">
                <div class="card w-100">
                    <div class="card-body py-2 text-center">
                <p>No SDM Found</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

@endsection

@push('script')
@endpush
