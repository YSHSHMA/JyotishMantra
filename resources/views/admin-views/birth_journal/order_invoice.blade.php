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
                    <h4 style="margin: 0;"><strong>Kundali Invoice</strong></h4>
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
                    Order No: {{ $details['order_id'] }}<br>
                    Date: {{ date('d-m-Y h:i a', strtotime($details['created_at'])) }}<br>
                    <strong>To:</strong><br>
                    <strong>{{ $details['userData']['f_name'] ?? '' }} {{ $details['userData']['l_name'] ?? '' }}</strong><br>
                    <strong>Phone:</strong> {{ $details['userData']['phone'] ?? '' }}<br>
                    @if (str_contains($details['userData']['email'], '.com'))
                    <strong>Email:</strong> {{ $details['userData']['email'] ?? '' }}
                    @endif
                </td>
            </tr>
        </table>


        <table class="table-bordered">
            <thead>
                <tr>
                    <th>{{ 'Name' }}</th>
                    <th>{{ 'Type' }}</th>
                    <th>{{ 'Price' }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td> {{ ucwords(str_replace('_',' ',($details['birthJournal']['name']??""))) }} </td>
                    <td> {{ ($details['birthJournal']['type']??"")}} </td>
                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details['amount']), currencyCode: getCurrencyCode(type: 'default')) }} </td>
                </tr>
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
                            <td>{{ setCurrencySymbol(amount: (($details['amount']??0)), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Tax</td>
                            <td>{{ setCurrencySymbol(amount: (0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Amount</td>
                            <td><strong>{{ setCurrencySymbol(amount: (($details['amount']??0)), currencyCode: getCurrencyCode()) }} </strong></td>
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