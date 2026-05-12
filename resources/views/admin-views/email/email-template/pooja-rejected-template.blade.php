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
                @if ($bookingDetails && isset($bookingDetails->type))
                    @if ($bookingDetails->type == 'pooja')
                        <p style="font-size: 16px; color: #555; margin: 5px 0;">
                            Aapki {{ $service_name['name'] }} Puja ab naye samay par nirdhharit ki gayi hai. 😊
                        </p>
                    @elseif ($bookingDetails->type == 'vip')
                        <p style="font-size: 16px; color: #555; margin: 5px 0;">
                            Aapki {{ $service_name['name'] }} Puja ab naye samay par nirdhharit ki gayi hai. 🌟
                        </p>
                    @elseif ($bookingDetails->type == 'anushthan')
                        <p style="font-size: 16px; color: #555; margin: 5px 0;">
                            Aapka {{ $service_name['name'] }} Anushthan ab naye samay par nirdhharit kiya gayi hai. 🙌
                        </p>
                    @elseif ($bookingDetails->type == 'chadhava')
                        <p style="font-size: 16px; color: #555; margin: 5px 0;">
                            Aapka {{ $service_name['name'] }} chadhava ab naye samay par nirdhharit kiya gayi hai. 😊
                        </p>
                    @else
                        <p style="font-size: 16px; color: #555; margin: 5px 0;">
                            Aapki Puja ab naye samay par nirdhharit ki gayi hai. 😊
                        </p>
                    @endif
                @else
                    <p style="font-size: 16px; color: #555; margin: 5px 0;">Aapki Puja ab naye samay par nirdhharit ki
                        gayi hai!</p>
                @endif
            </td>
        </tr>
        <!-- Content Section -->
        @if ($bookingDetails)
            @if ($bookingDetails->type == 'pooja')
                <tr>
                    <td style="padding:5px; background-color: #f9f9f9;">
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📿 Puja Name:</strong> {{ $service_name['name'] }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>💰 Puja Rashi:</strong> {{ webCurrencyConverter($bookingDetails['pay_amount']) }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📅 Puja Tithi:</strong>
                            {{ date('l, d F Y', strtotime($bookingDetails['booking_date'])) }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>🆔 Order ID:</strong> {{ $bookingDetails['order_id'] }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>🏛️ Puja Sthal:</strong> {{ $service_name['pooja_venue'] }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📌 Reschedule Reason:</strong> {!! $bookingDetails['reject_reason'] !!}
                        </p>
                    </td>
                </tr>
            @elseif($bookingDetails->type == 'vip')
                <tr>
                    <td style="padding:5px; background-color: #f9f9f9;">
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📿 Puja Name:</strong> {{ $service_name['name'] }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>💰 Puja Rashi:</strong> {{ webCurrencyConverter($bookingDetails['pay_amount']) }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📅 Puja Tithi:</strong>
                            {{ date('l, d F Y', strtotime($bookingDetails['booking_date'])) }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>🆔 Order ID:</strong> {{ $bookingDetails['order_id'] }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📌 Reschedule Reason:</strong> {!! $bookingDetails['reject_reason'] !!}
                        </p>
                    </td>
                </tr>
            @elseif($bookingDetails->type == 'anushthan')
                <tr>
                    <td style="padding:5px; background-color: #f9f9f9;">
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📿 Anushthan Name:</strong> {{ $service_name['name'] }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>💰 Anushthan Rashi:</strong>
                            {{ webCurrencyConverter($bookingDetails['pay_amount']) }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📅 Anushthan Tithi:</strong>
                            {{ date('l, d F Y', strtotime($bookingDetails['booking_date'])) }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>🆔 Order ID:</strong> {{ $bookingDetails['order_id'] }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📌 Reschedule Reason:</strong> {!! $bookingDetails['reject_reason'] !!}
                        </p>
                    </td>
                </tr>
            @elseif($bookingDetails->type == 'chadhava')
                <tr>
                    <td style="padding:5px; background-color: #f9f9f9;">
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📿 Chadhava Name:</strong> {{ $service_name['name'] }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>💰 Chadhava Rashi:</strong>
                            {{ webCurrencyConverter($bookingDetails['pay_amount']) }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📅 Chadhava Tithi:</strong>
                            {{ date('l, d F Y', strtotime($bookingDetails['booking_date'])) }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>🆔 Order ID:</strong> {{ $bookingDetails['order_id'] }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>🏛️ Chadhava Sthal:</strong> {{ $service_name['chadhava_venue'] }}
                        </p>
                        <p style="font-size: 16px; color: #555; margin:5px;">
                            <strong>📌 Reschedule Reason:</strong> {!! $bookingDetails['reject_reason'] !!}
                        </p>
                    </td>
                </tr>
            @endif
        @endif
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
