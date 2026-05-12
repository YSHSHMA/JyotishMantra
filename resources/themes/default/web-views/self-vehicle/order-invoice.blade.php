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
                    22-Akhand Building Near, Vasaviya Pump, Ujjain,<br>
                    Madhya Pradesh, India, 456001<br>
                    GSTIN: {{ $companyDetails['gstin'] ?? 'N/A' }}<br>
                    PAN: {{ $companyDetails['pan'] ?? 'N/A' }}
                </td>
                <td style="width: 50%; text-align: left;">
                    Order No: {{ $getData['order_id'] }}<br>
                    Date: {{ date('d-m-Y h:i:s a', strtotime($getData['created_at'])) }}<br>
                    <strong>To:</strong><br>
                    <strong>{{ $getData['userData']['f_name'] ?? '' }} {{ $getData['userData']['l_name'] ?? '' }}</strong><br>
                    <strong>Phone:</strong> {{ $getData['userData']['phone'] ?? '' }}<br>
                    <strong>Email:</strong> {{ $getData['userData']['email'] ?? '' }}
                </td>
            </tr>
        </table>


        <table class="table-bordered">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Description</th>
                    <th>Rate</th>
                    <th>Tax</th>
                    <th>Tax Amt</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <span>{{ $getData['SelfCabData']['getCategory']['brand_name'] ?? '' }} | {{ $getData['SelfCabData']['getCabId']['name'] ?? '' }} | {{ $getData['SelfCabData']['getCabId']['seats'] ?? '' }} seats | {{ ucwords($getData['SelfCabData']['car_type'] ?? '') }}</span>
                    </td>
                    <td>
                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$getData['price']) ), currencyCode: getCurrencyCode()) }}
                    </td>
                    <td>{{ $getData['tax'] ?? '' }}%</td>
                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$getData['tax_amount']) ), currencyCode: getCurrencyCode()) }}</td>
                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($getData['price'] + $getData['tax_amount'])) ), currencyCode: getCurrencyCode()) }}</td>
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
                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$getData['price']) ), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Tax</td>
                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$getData['tax_amount']) ), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Coupon Amount</td>
                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $getData['coupan_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Security Amount</td>
                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $getData['security_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Amount</td>
                            <td><strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($getData['price']?? 0) + ($getData['tax_amount']?? 0) + ($getData['security_amount'] ?? 0) - ($getData['coupan_amount']?? 0) )), currencyCode: getCurrencyCode()) }}</strong></td>
                        </tr>
                        @if($getData['drop_status'] == 1)
                        <tr>
                            <td>Over Time Change</td>
                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $getData['ex_change'] ?? 0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Security Deposit Returned</td>
                            <td>-{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $getData['security_deposit'] ?? 0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Final Amount</td>
                            <td><strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($getData['price']?? 0) + ($getData['tax_amount']?? 0) + ($getData['security_amount'] ?? 0) - ($getData['coupan_amount']?? 0) - ($getData['security_deposit']?? 0)  )), currencyCode: getCurrencyCode()) }}</strong></td>
                        </tr>
                        @endif
                        @if ($getData['refund_status'] == 1)
                        <tr>
                            <td>Refund Amount</td>
                            <td><strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $getData['refund_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</strong></td>
                        </tr>
                        @endif
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