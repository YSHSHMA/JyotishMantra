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
                    <th>Qty</th>
                    <th>per Rate</th>
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
                        <span>{{ $getData['title'] }}</span><br>
                        <span>{{ $getData['package_name'] }}</span>
                    </td>
                    <td>{{ $getData['people_qty'] }}</td>
                    <td><?php
                        $get_single = (($getData['gst_amount'] ?? 0) / ($getData['people_qty'] ?? 1));
                        $price_single = (($getData['price'] ?? 0) / ($getData['people_qty'] ?? 1));
                        if (0 >= $price_single) {
                            $price_single =  1;
                        }
                        ?>
                        @if(($getData['price'] ?? 0) > 0)
                        {{ ($price_single??1) - ($get_single??0) }}
                        @else
                        Free
                        @endif
                    </td>
                    <td>{{ (($getData['price'] ?? 0) - ($getData['gst_amount'] ?? 0)) }}</td>
                    <td>{{ ((($get_single??0) / ($price_single??1))*100)  }}%</td>
                    <td>{{ ($getData['gst_amount']??0) }}</td>
                    <td>{{ ($getData['price']??0) }}</td>
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
                            <td>{{ setCurrencySymbol(amount: (($getData['price'] ?? 0) - ($getData['gst_amount'] ?? 0)), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Tax</td>
                            <td>{{ setCurrencySymbol(amount: ($getData['gst_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>

                        <tr>
                            <td>Total Amount</td>
                            <td><strong>{{ setCurrencySymbol(amount: ($getData['price']??0), currencyCode: getCurrencyCode()) }}</strong></td>
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