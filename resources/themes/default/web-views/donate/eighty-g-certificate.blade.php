<html>

<head>
    <meta charset="UTF-8">
    <title>{{ ucwords('invoice')}}</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/invoice.css') }}">
    <style>
        .badge {
            /* display: inline-block; */
            padding: .3125em .5em;
            /* font-size: 75%; */
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .3125rem;
            transition: all .2s ease-in-out;
        }
    </style>
</head>

<body>

    <div class="first">
        <table class="content-position mb-30">
            <tr>

                <th class="p-0 text-right">
                    {{-- <img height="40" src="{{dynamicStorage(path: "storage/app/public/company/".getWebConfig(name: 'company_web_logo'))}}" alt=""> --}}
                    <img height="40" src="https://mahakal.com/storage/app/public/company/2025-02-07-67a5b1849142e.gif" alt="">
                </th>
            </tr>
        </table>

        <table class="bs-0 mb-30 px-10">
            <tr>
                <th class="content-position-y" style="text-align: center;">
                    <h2> 80G Certificate </h2>
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
                                <td colspan="2">
                                    <span>CERTIFICATE UNDER SECTION 80G OF THE INCOME TAX ACT, 1961</span><br>
                                    <span>This is to certify that <span style="font-weight: bolder;">{{($orderData['user_name']??"")}}</span> has made a voluntary donation of <span style="font-weight: bolder;">{{ webCurrencyConverter(amount: ($orderData['amount']??"")) }} </span> via mahakal.com to <span style="font-weight: bolder;"> @if($orderData['type'] == 'donate_trust')
                                        {{($orderData['getTrust']['trust_name']??"")}}
                                        @else
                                        {{($orderData['adsTrust']['name']??"")}}
                                        @endif </span> on <span style="font-weight: bolder;">{{date('d M,Y H:i A',strtotime($orderData['created_at']))}}</span>.</span><br>
                                    <span>This donation is eligible for deduction under Section 80G of the Income Tax Act, 1961. Details of the donation are as follows:</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <ul style="margin-left: 14px;">
                                        <li>Receipt Number: {{ ($orderData['trans_id']??'') }}</li>
                                        <li>Date of Donation: {{date('d M,Y H:i A',strtotime($orderData['created_at']))}}</li>
                                        <li>Donation Amount: {{ webCurrencyConverter(amount: ($orderData['amount']??"")) }}</li>
                                        <?php $checkmethod = \App\Models\PaymentRequest::where('transaction_id', $orderData['transaction_id'])->first(); ?>
                                        <li>Mode of Payment: {{ ucwords(str_replace('_',' ',$checkmethod['payment_method']??'wallet')) }}</li>
                                        <li>PAN of Donor: {{ strtoupper($orderData['pan_card']??'') }}</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span>This certificate is being issued as a confirmation of receipt of the donation and to enable the
                                        donor to claim income tax deduction under section 80G.</span>
                                    <br>
                                    <span>Thank you for your generous support.</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span style="font-weight: bolder;">For any kind of query & support contact us,</span><br>
                                    <span>Email : {{ getWebConfig(name: 'company_email') }} </span><br>
                                    <span>Phone : {{ getWebConfig(name: 'company_phone') }}</span><br>
                                    <span>Website : {{url('/')}}</span><br>
                                    <span>{{ getWebConfig(name: 'company_name') }} Organization</span>
                                </td>
                                <td>
                                    <span style="font-weight: bolder;">Authorized Signatory</span><br>
                                    <span>{{ getWebConfig(name: 'company_name') }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </section>
    </div>
    <br>    
    

</body>

</html>