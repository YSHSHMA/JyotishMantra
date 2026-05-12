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
                    <img height="40"
                        src="{{ dynamicStorage(path: 'storage/app/public/company/' . getWebConfig(name: 'company_web_logo')) }}"
                        alt="">
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
                    Order No: {{ $orderData['trans_id'] }}<br>
                    Date: {{ date('d-m-Y h:i a', strtotime($orderData['created_at'])) }}<br>
                    <strong>To:</strong><br>
                    <strong>{{ $orderData['users']['f_name'] ?? '' }}
                        {{ $orderData['users']['l_name'] ?? '' }}</strong><br>
                    <strong>Phone:</strong> {{ $orderData['users']['phone'] ?? '' }}<br>
                    <strong>Email:</strong> {{ $orderData['users']['email'] ?? '' }}
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
                <?php
                $order_information = json_decode($orderData['information'] ?? '[]', true);
                $getadsnew = json_decode($orderData['adsTrust']['set_json'] ?? '[]', true);
                $newArray = [];
                $product_amount = 0;
                if ($order_information && !empty($getadsnew) && count($order_information) > 0) {
                    $adsData = $getadsnew['en'] ?? ($getadsnew['in'] ?? []);
                    $adsById = [];
                    foreach ($adsData as $adsItem) {
                        $adsById[$adsItem['id']] = $adsItem;
                    }
                    foreach ($order_information as $inlist) {
                        $id = $inlist['id'] ?? null;
                        if (!empty($id)) {
                            $newItem = ['id' => $id];
                            if (isset($adsById[$id])) {
                                $adsItem = $adsById[$id];
                                $newItem['name'] = $adsItem['set_title'] ?? '';
                                 $product_amount += (float)$inlist['fullamount'] ?? 0;
                            } else {
                                $newItem['name'] = $inlist['title'] ?? '';
                            }
                            $newItem['title'] = $inlist['subtitle'] ?? '';
                            $newItem['amount'] = $inlist['amount'] ?? '';
                            $newItem['qty'] = $inlist['qty'] ?? '';
                            $newItem['fullamount'] = $inlist['fullamount'] ?? '';
                            $newArray[] = $newItem;
                        }
                    }
                }
                $indkey = 1;
                ?>
                @if((($orderData['amount'] ?? 0) - $product_amount) > 0)
                <tr>
                    <td><?php echo  $indkey++; ?></td>
                    <td>
                        @if ($orderData['type'] == 'donate_trust')
                            {{ $orderData['getTrust']['trust_name'] ?? '' }}
                        @else
                            {{ $orderData['adsTrust']['name'] ?? '' }}
                        @endif
                    </td>
                    <td>{{ webCurrencyConverter(amount: ($orderData['amount'] ?? 0) - $product_amount) }}</td>
                    <td>Not Applicable</td>
                    <td>{{ webCurrencyConverter(amount: 0) }}</td>
                    <td>{{ webCurrencyConverter(amount: ($orderData['amount'] ?? 0) - $product_amount) }}</td>
                </tr>
                @endif
                  @if(($product_amount) > 0)
                    @foreach($newArray as $ky => $vl)
                         <tr>
                    <td><?php echo  $indkey++; ?></td>
                    <td>
                        <span>{{$vl['name']}}</span><br>
                        <span>{{$vl['title']}}</span><br>
                        <span>{{$vl['amount']}} * {{$vl['qty']}}</span><br>
                    </td>                    
                    <td>{{ webCurrencyConverter(amount: ($vl['fullamount']??0)) }}</td>
                    <td>Not Applicable</td>
                    <td>{{ webCurrencyConverter(amount: 0) }}</td>
                    <td>{{ webCurrencyConverter(amount: ($vl['fullamount']??0)) }}</td>
                </tr>
                    @endforeach
                  @endif 
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
                            <td>{{ setCurrencySymbol(amount: $orderData['amount'] ?? 0, currencyCode: getCurrencyCode()) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Total Tax</td>
                            <td>{{ setCurrencySymbol(amount: 0, currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Amount</td>
                            <td><strong>{{ setCurrencySymbol(amount: $orderData['amount'] ?? 0, currencyCode: getCurrencyCode()) }}
                                </strong></td>
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
