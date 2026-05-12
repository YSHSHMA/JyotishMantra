<!DOCTYPE html>
<html lang="en">

<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <style>
        .preview-box {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 200px;
            background: #f9f9f9;
        }

        @import url('https://fonts.googleapis.com/css?family=Helvetica:700,400');
        @media screen and (max-width: 768px) {
            div {
                height: auto !important;
                padding: 10px;
            }

            table {
                width: 100% !important;
            }

            td {
                display: block;
                text-align: center !important;
                padding: 8px 0;
            }

            img {
                max-width: 100% !important;
                height: auto !important;
            }
        }

        body {
            font-size: 16px;
        }

        .footer-class {
            background-size: cover;
        }

        @media (max-width: 768px) {
            body {
                font-size: 11px;
            }

            .footer-class {
                background-size: cover;
            }
        }

        @media (max-width: 480px) {
            body {
                font-size: 10px;
            }

            .footer-class {
                background-size: contain;
            }
        }
    </style>
    <style>
        @media only screen and (max-width: 600px) {
            .footer-class table {
                width: 100% !important;
            }

            .footer-class td {
                font-size: 12px !important;
                padding: 8px !important;
            }
        }
    </style>

    <div class="content container-fluid">
        <div class="row">
            <?php
            $companyPhone = getWebConfig(name: 'company_phone');
            $companyEmail = getWebConfig(name: 'company_email');
            $companyName = getWebConfig(name: 'company_name');
            $email_top_bar = getWebConfig(name: 'email_top_bar');
            $email_bottom_bar = getWebConfig(name: 'email_bottom_bar');
            $companyLogo = getWebConfig(name: 'company_fav_icon');
            ?>
            <div class="col-md-12">
                <img class="mail-img-2" src='{{ getValidImage(path: "storage/app/public/company/".$email_top_bar, type:"backend-logo")}}' id="logoViewer" style="width: 100%; background-size: contain; background-repeat: no-repeat; background-position: center; height: auto; display: flex; flex-direction: column;justify-content: flex-end;">
            </div>
            <div style="height: auto;background-color: #ececec;padding:1px 0px 10px 26px">
                {!! ($html_content??"") !!}
            </div>
            <div>
                <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" style=" background-color: #ffffff;">
                    <tr>
                        <td align="center"
                            style="position: relative; 
                  background-image: url('{{ getValidImage(path: "storage/app/public/company/".$email_bottom_bar, type:"backend-logo")}}'); 
                  background-repeat: no-repeat; 
                  background-position: center; 
                  background-size: 100% 100%;
                  padding: 0; 
                  height: 120px; 
                  width: 100%;">
                            <!-- Transparent Strip -->
                            <div style="position: absolute; top: 6rem; left: 0; width: 100%; color: white; background: rgba(231, 179, 207, 0.17);margin-top: 6rem;">
                                <table width="100%" cellspacing="0" cellpadding="3" border="0">
                                    <tr>
                                        <td style="text-align: left; font-size: 14px; padding-left: 10px;">
                                            <strong>📞 Phone:</strong> +91 {{ $companyPhone }} | <strong>✉️ Email:</strong>
                                            <span style="font-weight: bolder; color: white;">{{ $companyEmail }}</span>
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
                    </tr>
            </div>


        </div>
    </div>
</body>

</html>