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
                    Order No: {{ $orderData['order_no'] }}<br>
                    Date: {{ date('d-m-Y h:i:s a', strtotime($orderData['created_at'])) }}<br>
                    <strong>To:</strong><br>
                    <strong>{{ $orderData['userdata']['f_name'] ?? '' }} {{ $orderData['userdata']['l_name'] ?? '' }}</strong><br>
                    <strong>Phone:</strong> {{ $orderData['userdata']['phone'] ?? '' }}<br>
                    <strong>Email:</strong> {{ $orderData['userdata']['email'] ?? '' }}
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
                @foreach($orderData['orderitem'] as $key=>$details)
                <?php $subTotal = ($details['price']) * $details->qty ?>
                <tr>
                    <td>{{$key+1}}</td>
                    <td style="width: 31% !important;">
                        <span> {{ ($orderData['eventid']['event_name']??"") }}</span><br>
                        <span>({{ (\App\Models\EventPackage::where('id',$details['package_id'])->first()['package_name']??"") }})</span>
                        <br>
                    </td>
                    <td>{{($details['no_of_seats']??0)}}</td>
                    <td>
                        <?php
                        $singleMan = ($details['sub_amount'] ?? 0);
                        $singlegst = ($details['gst'] ?? 0);

                        $govtTax = (($singleMan * ($singlegst ?? 0)) / 100);
                        $orderamount = ($details['sub_amount'] ?? 0) - $govtTax;
                        $allPricess = ($orderamount * ($details['no_of_seats'] ?? 1))
                        ?>

                        {{ webCurrencyConverter(amount: (($orderamount??0)) ) }}
                    </td>
                    <td>{{ webCurrencyConverter(amount: (($allPricess??0) ) ) }}</td>
                    <td>{{ ($details['gst']??0) }}%</td>
                    <td>{{ webCurrencyConverter(amount: (($details['gst_amount']??0)) ) }}</td>
                    <td class="text-right">{{ webCurrencyConverter(amount: ($details['amount']??0)) }}</td>
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
                            <td>{{ setCurrencySymbol(amount: ((($orderData['amount'] ?? 0) +($orderData['coupon_amount'] ?? 0)) - ($orderData['gst_amount'] ?? 0)), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Tax</td>
                            <td>{{ setCurrencySymbol(amount: ($orderData['gst_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Discount Amount</td>
                            <td>-{{ setCurrencySymbol(amount: ($orderData['coupon_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Amount</td>
                            <td><strong>{{ setCurrencySymbol(amount: ($orderData['amount']??0), currencyCode: getCurrencyCode()) }}</strong></td>
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