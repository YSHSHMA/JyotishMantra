<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        @media screen and (max-width: 600px) {
            .footer-content {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
                width: 100% !important;
            }

            .footer-left,
            .footer-right {
                width: 50% !important;
                text-align: left !important;
                padding: 5px;
            }

            .footer-right {
                text-align: right !important;
            }
        }
    </style>
</head>
<?php
$companyPhone = getWebConfig(name: 'company_phone');
$companyEmail = getWebConfig(name: 'company_email');
$companyName = getWebConfig(name: 'company_name');
$companyLogo = getWebConfig(name: 'company_web_logo');
$companyEmailTop = getWebConfig(name: 'email_top_bar');
$companyEmailBottom = getWebConfig(name: 'email_bottom_bar');
?>

<body style="margin: 0; padding: 0; background-color: #f5f5f5;">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center"
        style="max-width: 600px; background-color: #ffffff;">
        <!-- Header Section -->
        <tr>
            @if (is_file('storage/app/public/company/' . $companyEmailTop))
                <td align="center">
                    @if (is_file('storage/app/public/company/' . $companyEmailTop))
                        <img src="{{ dynamicStorage(path: 'storage/app/public/company/' . $companyEmailTop) }}"
                            alt="Header Image" style="width: 100%; max-width: 600px; display: block;">
                    @endif
                </td>
            @endif
        </tr>
        <!-- Greeting Section -->
        <tr>
            <td style="padding: 10px; text-align: center; background-color: #f0f0f0;">
                <h2 style="font-size: 22px; color: #333; margin: 0;">🌸 Namaste {{ $userInfo['name'] }} Ji 🙏</h2>
                <p style="font-size: 16px; color: #555; margin: 5px 0;">
                    Aapki {{ $lead['temple']['name'] }} ke liye Services ki booking safaltapurvak ho gayi hai.
                </p>
            </td>
        </tr>
        <!-- Content Section -->
        @foreach ($leadDetails as $detail)                    
            <tr>
                <td style="padding:5px; background-color: #f9f9f9;">
                    <h2 style="font-size: 20px; color: #333;">{{$detail->type=='puja'?'Pooja Booking':($detail->type=='darshan'?'Darshan Booking':($detail->type=='bhojan'?'Bhojan Booking':'Locker Booking'))}}</h2>
                    <p style="font-size: 16px; color: #555; margin:5px;">
                        <strong>🆔 Order ID:</strong> {{ $detail['order_id'] }}
                    </p>
                    <p style="font-size: 16px; color: #555; margin:5px;">
                        <strong>📿 Package Name:</strong> {{ $detail['package']['varient_name'] }}
                    </p>
                    <p style="font-size: 16px; color: #555; margin:5px;">
                        <strong>📿 Booking Date:</strong> {{ date('d-m-Y', strtotime($detail['booking_date']))}}
                    </p>
                    {!! !empty($detail['time_slot_id'])
                        ? '<p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📿 Time Slot:</strong> ' . $detail['timeslot']['start_time'] . '-' . $detail['timeslot']['end_time'] . '
                        </p>'
                        : '' !!}
                    
                    @php
                        $customers = json_decode($detail['customers'], true);
                        $lockerItems = json_decode($detail['locker_items'], true);
                    @endphp

                    @if (!empty($customers))
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📿 Customers:</strong> {{ collect($customers)->pluck('name')->implode(', ') }}
                        </p>
                    @endif

                    @if (!empty($lockerItems))
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>🔒 Locker Items:</strong> {{ collect($lockerItems)->map(fn($v, $k) => "$k($v)")->implode(', ') }}
                        </p>
                    @endif
                
                    
                    <p style="font-size: 16px; color: #555; margin:5px;">
                        <strong>💰 Rashi:</strong> {{ webCurrencyConverter($detail['amount']) }}
                    </p>
                    
                </td>
            </tr>
        @endforeach
        <!-- Footer Image with Transparent Overlay Text -->
        <tr>
            @if (is_file('storage/app/public/company/' . $companyEmailBottom))
                <td align="center"
                    style="position: relative; 
                      background-image: url('{{ asset('storage/app/public/company/' . $companyEmailBottom) }}'); 
                      background-repeat: no-repeat; 
                      background-position: center; 
                      background-size: 100% 100%;
                      padding: 0; 
                      height: 110px; 
                      width: 100%;">

                    <!-- Transparent Strip -->
                    <div
                        style="position: absolute; left: 0; width: 100%; color: white; background: rgba(231, 179, 207, 0.17);margin-top: 5rem;">
                        <table width="100%" cellspacing="0" cellpadding="3" border="0">
                            <tr>
                                <td style="text-align: left; font-size: 14px; padding-left: 10px;">
                                    <strong>📞 Phone:</strong> +91 {{ $companyPhone }} | <strong>✉️ Email:</strong>
                                    {{ $companyEmail }}
                                </td>
                                @php($social_media = \App\Models\SocialMedia::where('active_status', 1)->get())
                                @if (isset($social_media))
                                    @foreach ($social_media as $item)
                                        <td style="text-align: right; font-size: 12px; padding-right: 10px;">
                                            <a href="{{ $item->link }}" target="_blank">
                                                <img src="{{ asset('public/assets/front-end/img/email/' . $item->name . '.png') }}"
                                                    alt="{{ $item->name }}"
                                                    style="height: 20px; width:20px; padding: 0 3px;">
                                            </a>
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        </table>
                    </div>
                </td>
            @endif
        </tr>
    </table>
</body>

</html>
