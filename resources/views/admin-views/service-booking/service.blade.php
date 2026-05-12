@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('booking'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 row">
            <div class="col-md-4 align-content-center">
                <h2 class="h1 mb-0 d-flex gap-2">
                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" alt="">
                    {{ translate('booking') }}
                </h2>
            </div>
            <div class="col-md-8">
                <input type="text" name="" id="search" class="form-control" placeholder="Search your service">
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    @forelse ($services as $service)
                        <div class="card col-md-3 my-2 py-2 text-center align-content-middle service-card">
                            <a href="{{ route('admin.book.package', ['type' => $type, 'id' => $service->id]) }}"
                                class="service-name">
                                {{ $service->name }}
                            </a>
                            @php
                                $weekDay = json_decode($service->week_days);
                                $time = date('H:i:s', strtotime($service->pooja_time));
                                $nextPoojaDay = App\Utils\getNextPoojaDay($weekDay, $time);
                                $bookingDate = $nextPoojaDay->format('Y-m-d');
                            @endphp
                            <p class="mt-3">Pooja Date - {{ $bookingDate ?? 'Not Available' }}</p>
                        </div>
                    @empty
                        <h5 class="text-center">No Data Found</h5>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        $('#search').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.service-card').filter(function() {
                $(this).toggle($(this).find('.service-name').text().toLowerCase().indexOf(value) > -1)
            });
        });
    </script>
@endpush
