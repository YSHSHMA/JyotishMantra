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
            <td align="center">
                @if(is_file('storage/app/public/company/'.$companyEmailTop))
                <img src="{{ dynamicStorage(path: 'storage/app/public/company/'.$companyEmailTop) }}"
                    alt="Header Image" style="width: 100%; max-width: 600px; display: block;">
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding: 0px 20px;">
                <h4>We Received a Request to Reset Your Password. Click the button below to set a New Password</h4>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px; text-align: center;">
            👉 <a class="btn btn--primary d-block text-center width-100" style="color: #fff; background-color: #377dff; border-color: #377dff;    min-width: 80px; padding: 9px 16px; margin-left: -16px; outline: none; text-decoration: none;" href="{{$url}}">
                    {{ translate('Click to Reset') }}
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding: 0px 20px;">
                <p>Thank You,</p>
                <p>{{ $companyName }}</p>
            </td>
        </tr>
        <!-- Footer Image with Transparent Overlay Text -->
        <tr>
            @if(is_file('storage/app/public/company/'.$companyEmailBottom))
            <td align="center"
                style="position: relative; 
                  background-image: url('{{ asset('storage/app/public/company/'.$companyEmailBottom) }}'); 
                  background-repeat: no-repeat; 
                  background-position: center; 
                  background-size: 100% 100%;
                  padding: 0; 
                  height: 110px; 
                  width: 100%;">

                <!-- Transparent Strip -->
                <div style="position: absolute; left: 0; width: 100%; color: white; background: rgba(231, 179, 207, 0.17);margin-top: 5rem;">
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