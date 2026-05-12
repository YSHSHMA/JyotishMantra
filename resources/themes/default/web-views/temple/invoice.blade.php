<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ 'Invoice' }}</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/invoice.css') }}">
</head>

<body>
    <div class="invoice-box">
        <table class="table-no-border" style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="text-align: left; vertical-align: middle;">
                    <img height="40" src="{{ dynamicStorage(path: "storage/app/public/company/".getWebConfig(name: 'company_web_logo')) }}" alt="">
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <h4 style="margin: 0;"><strong>INVOICE</strong></h4>
                </td>
            </tr>
        </table>

        <table class="table-no-border" style="margin-bottom: 20px;">
            <tr>
                <td style="width: 50%; text-align: left;">
                    <strong>From:</strong><br>
                    <strong>Mahakal AstroTech Pvt Ltd</strong><br>
                    {{ getWebConfig(name: 'shop_address') ?? 'N/A' }}<br>
                    GSTIN: {{ getWebConfig(name: 'company_gst') ?? 'N/A' }}<br>
                    PAN: {{ getWebConfig(name: 'company_pan') ?? 'N/A' }}
                </td>
                <td style="width: 50%; text-align: left;">
                    Order No: {{ $lead['order_id'] }}<br>
                    Date: {{ date('d-m-Y h:i a', strtotime($lead['created_at'])) }}<br>
                    <strong>To:</strong><br>
                    <strong>{{ $userInfo['f_name'] ?? '' }} {{ $userInfo['l_name'] ?? '' }}</strong><br>
                    <strong>Phone:</strong> {{ $userInfo['phone'] ?? '' }}<br>
                    <strong>Email:</strong> {{ $userInfo['email'] ?? '' }}
                </td>
                <td>
                    {!! $imageData !!}
                </td>
            </tr>
        </table>


        <table class="table-bordered">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Service</th>
                    <th>Package</th>
                    <th>Booking Date</th>
                    <th>Time Slot</th>
                    <th>Locker Item</th>
                    <th>Customers</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leadDetails as $key=>$item)
                    @php
                        $customers = json_decode($item['customers'], true);
                        $lockerItems = json_decode($item['locker_items'], true);
                    @endphp
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$item->type=='puja'?'Pooja Booking':($item->type=='darshan'?'Darshan Booking':($item->type=='bhojan'?'Bhojan Booking':'Locker Booking'))}}
                        </td>
                        <td>{{ $item['package']['varient_name'] }}</td>
                        <td>{{ date('d-m-Y', strtotime($item['booking_date']))}}</td>
                        <td>{{!empty($item->time_slot_id)?$item['timeslot']['start_time'] . '-' . $item['timeslot']['end_time']:''}}</td>
                        <td>{{!empty($lockerItems)?collect($lockerItems)->map(fn($v, $k) => "$k($v)")->implode(', '):''}}</td>
                        <td>{{!empty($customers)?collect($customers)->pluck('name')->implode(', '):''}}</td>
                        <td>{{ webCurrencyConverter(amount:  ($item['amount']??0)) }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>

        <table class="summary-final-table">
            <tr>
                <td class="summary-left">
                    <strong>Note:</strong> This is a system-generated invoice and does not require a physical signature.
                </td>
                <td class="summary-right">
                    <table class="summary-table-inner">
                        <tr>
                            <td>Sub Total</td>
                            <td>{{ setCurrencySymbol(amount: (($lead['amount']??0)), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Tax</td>
                            <td>{{ setCurrencySymbol(amount: (0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Amount</td>
                            <td><strong>{{ setCurrencySymbol(amount: (($lead['amount']??0)), currencyCode: getCurrencyCode()) }} </strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            <strong></strong>
        </div>
    </div>
</body>

</html>