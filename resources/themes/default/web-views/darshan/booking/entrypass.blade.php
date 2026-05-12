<html>

<head>
    <meta charset="UTF-8">
    <title>{{ ucwords('entry_pass') }}</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/invoice.css') }}">
</head>

<body>

    @php($companyName = getWebConfig(name: 'company_name'))
    <div class="first">
        <table class="content-position mb-30">
            <tr>
                <th class="p-0 text-left">
                    <span class="font-size-26px">{{ ucwords('Booking') }}</span> <br>
                    <span
                        style="font-size: 44px; color: orange;">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($getData['darshanOrder']['price'] ?? 0) / ($getData['darshanOrder']['people_qty'] ?? 1)), currencyCode: getCurrencyCode()) }}</span>
                    <span><img src="data:image/png;base64,{{ $barcodes }}"></span>
                </th>
                <th class="p-0 text-right">
                    <img height="40"
                        src="{{ dynamicStorage(path: 'storage/app/public/company/' . getWebConfig(name: 'company_web_logo')) }}"
                        alt="">
                </th>
            </tr>
        </table>

        <table class="bs-0 mb-30 px-10">
            <tr>
                <th class="content-position-y text-left">
                    <h4 class="text-uppercase mb-1 fz-14">
                        {{ ucwords('Order') }} #{{ $getData['darshanOrder']['order_id'] ?? '' }}
                    </h4>
                    <br>
                    <h4 class="text-uppercase mb-1 fz-14">
                        {{ ucwords('Purohit name') }} #{{ $getData['darshanOrder']['purohit']['name'] ?? '' }}
                    </h4>
                    <br>
                    <h4 class="text-uppercase mb-1 fz-14">
                        {{ ucwords('Temple Name') }} : {{ $getData['darshanOrder']['Temple']['name'] ?? '' }}
                    </h4><br>
                    <h4 class="text-uppercase mb-1 fz-14">
                        {{ ucwords('Service Name') }} : {{ $getData['darshanOrder']['title'] ?? '' }}
                        ({{ $getData['darshanOrder']['package_name'] ?? '' }})
                    </h4>
                    <h4 class="text-uppercase mb-1 fz-14">
                        {{ ucwords('Booking Date') }} : {{ $getData['darshanOrder']['date'] ?? '' }}
                    </h4>
                    <h4 class="text-uppercase mb-1 fz-14">
                        {{ ucwords('Time Slot') }} : {{ $getData['darshanOrder']['time'] ?? '' }}
                    </h4>
                </th>
                <th class="content-position-y text-right">
                    <h4 class="fz-14">
                        {{ ucwords('date') }} :
                        {{ date('d-m-Y h:i:s a', strtotime($getData['darshanOrder']['created_at'])) }}
                    </h4>
                </th>
            </tr>
        </table>
    </div>
    <div class="">
        <section>
            <table class="content-position-y fz-12">
                <tr>
                    <td class="font-weight-bold p-1">
                        <table>
                            <tr>
                                <td>
                                    <span class="h2 m-0">{{ ucwords('Member Info') }} </span>
                                    <div class="h3">{{ ucwords('Name') }} : {{ $getData['name'] }} </div>
                                    @if ($getData['phone'])
                                        <div class="h3">{{ ucwords('Phone No.') }} : {{ $getData['phone'] }}
                                        </div>
                                    @endif
                                    <div class="h3">{{ ucwords('Aadhar Number') }} : {{ $getData['aadhar'] }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td>
                        <table>
                            <tr>
                                <td class="text-right">
                                    <span class="h2">{{ ucwords('customer info') }} </span>
                                    <div class="h4">
                                        @if ($getData['darshanOrder']['userdata'])
                                            <p class="mt-6px mb-0">
                                                {{ $getData['darshanOrder']['userdata']['name'] ?? '' }} </p>
                                            <p class="mt-6px mb-0">
                                                {{ $getData['darshanOrder']['userdata']['phone'] ?? '' }} </p>
                                            @if ($getData['darshanOrder']['userdata']['phone'] != $getData['darshanOrder']['userdata']['email'])
                                                <p class="mt-6px mb-0">
                                                    {{ $getData['darshanOrder']['userdata']['email'] ?? '' }} </p>
                                            @endif
                                        @else
                                            <p class="mt-6px mb-0">Guest User</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </section>
    </div>
    <br>

    <div class="row">
        <section>
            <table>
                <tr>
                    <th class="content-position-y bg-light py-4">
                        <div class="d-flex justify-content-center gap-2">
                            <div class="mb-2">
                                <img height="10"
                                    src="{{ theme_asset(path: 'public/assets/front-end/img/icons/telephone.png') }}"
                                    alt="">
                                {{ ucwords('phone') }}
                                : {{ getWebConfig(name: 'company_phone') }}
                            </div>
                            <div class="mb-2">
                                <img height="10"
                                    src="{{ theme_asset(path: 'public/assets/front-end/img/icons/email.png') }}"
                                    alt="">
                                {{ ucwords('email') }}
                                : {{ getWebConfig(name: 'company_email') }}
                            </div>
                        </div>
                        <div class="mb-2">
                            <img height="10"
                                src="{{ theme_asset(path: 'public/assets/front-end/img/icons/web.png') }}"
                                alt="">
                            {{ ucwords('website') }}
                            : {{ url('/') }}
                        </div>
                        <div>
                            {{ ucwords('all copy right reserved © ' . date('Y') . ' ') . $companyName }}
                        </div>
                    </th>
                </tr>
            </table>
        </section>
    </div>

</body>

</html>
