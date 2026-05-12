@php
    $package = \App\Models\Package::find($pac->package_id);
@endphp

@if (auth('customer')->check())
    <form method="GET" action="{{ route('poojastore', $epooja->slug) }}"
        class="package-form">
        @csrf
        <input type="hidden" name="package_id" value="{{ $package->id }}">
        <input type="hidden" name="package_price"
            value="{{ $pac->package_price }}">
        <input type="hidden" name="package_name"
            value="{{ $package->title }}">
        <input type="hidden" name="noperson"
            value="{{ $package->person }}">
        <input type="hidden" name="service_id"
            value="{{ $forecastServiceId }}">
        <input type="hidden" name="product_id"
            value="{{ $epooja->product_id }}">
        <input type="hidden" name="booking_date"
            value="{{ date('Y-m-d', strtotime($date)) }}">
        <input type="hidden" name="person_phone"
            value="{{ auth('customer')->user()->phone ?? '' }}">
        <input type="hidden" name="person_name"
            value="{{ auth('customer')->user()->f_name . ' ' . auth('customer')->user()->l_name ?? '' }}">

        <div class="package-card {{ $loop->first ? 'active-card' : '' }}"
            data-id="{{ $package->id }}"
            data-price="{{ $pac->package_price }}"
            data-title="{{ $package->title }}"
            data-person="{{ $package->person }}"
            data-color="{{ $package->color ?? '#888' }}">

            <div class="person-badge"
                style="background: {{ $package->color ?? '#888' }}">
                <i class="fas fa-user"></i> {{ $package->person }} Person
            </div>

            <div class="select-circle" style="background: {{ $loop->first ? $package->color : 'white' }}"></div>

            <div class="title">{{ $package->title }}</div>
            <div class="subtitle">All above included</div>

            <button type="submit" class="bottom-bar"
                style="background: linear-gradient(to right, {{ $package->color ?? '#888' }}, transparent); display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border: none; width: 100%; border-radius: 8px; cursor: pointer;">
                <div class="price"
                    style="font-size: 18px; font-weight: bold; color: white;">
                    ₹{{ $pac->package_price }}
                </div>
                <img src="{{ getValidImage(path: 'storage/app/logo/' . $package->image, type: 'product') ?? asset('default-image.png') }}"
                    alt="Package" style="height: 50px; object-fit: contain;">
            </button>
        </div>
    </form>
@else
    {{-- Not logged in --}}
    <a href="javascript:void(0);" onclick="participateModel(this)"
        data-id="{{ $package->id }}" data-name="{{ $package->title }}"
        data-price="{{ $pac->package_price }}"
        data-person="{{ $package->person }}">
        <div class="package-card {{ $loop->first ? 'active-card' : '' }}" data-id="{{ $package->id }}"
            data-price="{{ $pac->package_price }}"
            data-title="{{ $package->title }}"
            data-person="{{ $package->person }}"
            data-color="{{ $package->color ?? '#888' }}">
            <div class="person-badge"
                style="background: {{ $package->color ?? '#888' }}">
                <i class="fas fa-user"></i> {{ $package->person }} Person
            </div>
            <div class="select-circle" style="background: {{ $loop->first ? $package->color : 'white' }}">
            </div>
            <div class="title">{{ $package->title }}</div>
            <div class="subtitle">All above included</div>
            <div class="bottom-bar"
                style="background: linear-gradient(to right, {{ $package->color ?? '#888' }}, transparent); display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 8px;">
                <div class="price"
                    style="font-size: 18px; font-weight: bold; color: white;">
                    ₹{{ $pac->package_price }}
                </div>
                <img src="{{ getValidImage(path: 'storage/app/logo/' . $package->image, type: 'product') ?? asset('default-image.png') }}"
                    alt="Package" style="height: 50px; object-fit: contain;">
            </div>
        </div>
    </a>
@endif