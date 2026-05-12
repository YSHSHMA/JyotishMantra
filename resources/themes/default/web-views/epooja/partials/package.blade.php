@push('css_or_js')
    <style>
        .text-GRAY-90 {
            --tw-text-opacity: 1;
            color: rgb(246 141 28/var(--tw-text-opacity)) !important;
        }

        .text-PURPLE-60 {
            --tw-text-opacity: 1;
            color: rgb(67 10 189/var(--tw-text-opacity)) !important;
        }

        .text-BLUE-60 {
            --tw-text-opacity: 1;
            color: rgb(53 100 226/var(--tw-text-opacity));
        }

        .text-RED-61 {
            --tw-text-opacity: 1;
            color: rgb(255 50 1/var(--tw-text-opacity));
        }

        .package-Information div span {
            overflow: auto;
            height: 293px;
            /* display: -webkit-box;
        -webkit-line-clamp: 11;
        -webkit-box-orient: vertical; */
        }
    </style>
@endpush
@php
    $package = \App\Models\Package::where('id', $pac->package_id)->first();

@endphp
<div class="col-lg-3 packageCard">
    <div class="card mb-lg-0 rounded-lg shadow">
        <div
            class="card-header "style="background: linear-gradient(to bottom, {{ $package ? $package->color : 'primary' }}, #ffffff);height:180px;">
            <h5 class="card-title text-uppercase text-center font-bold" style="line-height: 1.5em;min-height: 3em;">
                {{ $package->title }}</h5>
            <h6 class="h3 text-center font-bold">&#8360;.{{ $pac->package_price }}</h6>
            <span class="h6 text-center" style="line-height: 1.5em;min-height: 3em;">
                @if ($package->person == 1)
                    {{ translate('Pooja_for') }} {{ $package->person }} {{ translate('person') }}
                @else
                    {{ translate('Pooja_for') }} {{ $package->person }} {{ translate('People') }}
                @endif
            </span>
        </div>

        <div class="card-body rounded-bottom">
            <div class="mb-5" style="margin-bottom:6rem!important;height: 250px;">
                <div class="flex flex-col package-Information">
                    <div style="display: flex; flex-direction: column">
                        <span style="flex-direction: row; align-items: start; width: 100%;overflow: auto;height: 290px;"
                            class="">
                            <div class="ck-rendered-content">
                                {!! $package->description !!}
                            </div>
                        </span>
                    </div>
                </div>
            </div>
            @php
                if (auth('customer')->check()) {
                    $customer = App\Models\User::where('id', auth('customer')->id())->first();
                }
            @endphp
            @if (auth('customer')->check())
                <form class="needs-validation_" id="QueryForm" name="QueryForm"
                    action="{{ route('poojastore', $epooja->slug) }}" method="GET">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $forecastServiceId }}">
                    <input type="hidden" name="product_id" value="{{ $epooja->product_id }}">
                    <input type="hidden" name="package_id" id="packagesId" value="{{ $package->id }}">
                    <input type="hidden" name="package_name" id="packagesName" value="{{ $package->title }}">
                    <input type="hidden" name="package_price" id="packagesPrice" value="{{ $pac->package_price }}">
                    <input type="hidden" name="noperson" id="packagesPerson" value="{{ $package->person }}">
                    <input type="hidden" name="noperson" id="packagesPerson" value="{{ $customer['id'] }}">
                    <input class="form-control text-align-direction" type="hidden" value="{{ $customer['phone'] }}"
                        name="person_phone" id="person-number" placeholder="{{ translate('enter_phone_number') }}"
                        inputmode="number" maxlength="10" minlength="10"
                        {{ isset($customer['phone']) ? 'readonly' : '' }} input-mode="number">
                    <input class="form-control text-align-direction"
                        value="{{ $customer['f_name'] }} {{ $customer['l_name'] }}" type="hidden" name="person_name"
                        id="person-name" placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                        inputmode="name" {{ isset($customer['f_name']) ? 'readonly' : '' }} input-mode="text">
                    <!-- @if ($epooja->pooja_type == '0') -->
                        <input type="hidden" name="booking_date" id="poojaBook"
                            value="{{ date('Y-m-d', strtotime($date)) }}">
                    <!-- @else
                        <input type="hidden" name="booking_date" id="poojaBook"
                            value="{{ date('Y-m-d', strtotime($date)) }}" placeholder="Events">
                    @endif -->
                    <button type="submit" name="QueryForm"
                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('GO_PARTICIPATE') }}</a>
                </form>
            @else
                <a href="javascript:void(0);" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold"
                    data-id="{{ $package->id }}" data-name="{{ $package->title }}"
                    data-price="{{ $pac->package_price }}" data-person="{{ $package->person }}"
                    onclick="participateModel(this)">{{ translate('GO_PARTICIPATE') }} &nbsp; ({{ $pac->package_price }}/-)</a>
            @endif
        </div>
    </div>
</div>
