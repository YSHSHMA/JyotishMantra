<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ translate('chadhava') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Helvetica:700,400');

        body {
            font-family: 'Helvetica', sans-serif;
            background-color: #ececec;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            height: 50px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 700;
        }

        .message {
            text-align: center;
            margin-bottom: 30px;
        }

        .message p {
            font-size: 16px;
            color: #727272;
            margin: 10px 0;
        }

        .details {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .details img {
            height: 50px;
            width: 50px;
            margin-right: 15px;
        }

        .details h2 {
            font-size: 18px;
            font-weight: 700;
            color: #182E4B;
        }

        .details h3 {
            font-size: 14px;
            font-weight: 400;
            color: #727272;
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #5D6774;
        }

        .social-media {
            text-align: center;
            margin-top: 20px;
        }
        

        .social-media img {
            height: 14px;
            width: 14px;
            margin: 0 5px;
        }

        .contact {
            text-align: center;
            font-size: 11px;
            color: #242A30;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyLogo = getWebConfig(name: 'company_web_logo');
    ?>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if(is_file('storage/app/public/company/'.$companyLogo))
                <img src="{{ dynamicStorage(path: 'storage/app/public/company/'.$companyLogo) }}" alt="{{ $companyName }}">
            @endif
            <h1>{{ $companyName }}</h1>
        </div>

        <!-- Message Section -->
        <div class="message">
            <img src="{{ dynamicAsset(path: 'public/assets/front-end/img/icons/add_fund_vector.png') }}" alt="Icon">
            <p><strong>{{ translate('Thank_You_for_Choosing_Chadhava_service') }}</strong></p>
            <p>{{ translate("Your Chadhava order " . ($bookingDetails['order_id'] ?? '') . " has been successfully confirmed. Thank you for choosing our services!") }}</p>
            <p><strong>{{ translate('dear') }}: {{ $userInfo['name'] }}</strong></p>
        </div>

        <!-- Details Section -->
        <div class="details">
            <img src="{{ dynamicStorage(path: 'storage/app/public/chadhava/thumbnail/'.$service_name['thumbnail']) }}" alt="">
            <h2>{{ $service_name['name'] }}</h2>
            <h3>{{ translate('Order_Id') }}: {{ $bookingDetails['order_id'] }}</h3>
            <h3>{{ translate('amount') }}: {{ webCurrencyConverter($bookingDetails['pay_amount']) }}</h3>
            <h3>{{ translate('chadhava_Date') }}: {{ date('l, d F Y', strtotime($bookingDetails['booking_date'])) }}</h3>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ translate('Thank you for joining with') }} <a href="#">{{ $companyName }}</a>!</p>
            <p>{{ translate('If you require any assistance or have feedback, email us at') }} <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a></p>
        </div>

        <!-- Social Media Links -->
        <div class="social-media">
            @php($social_media = \App\Models\SocialMedia::where('active_status', 1)->get())
            @if(isset($social_media))
                @foreach ($social_media as $item)
                    <a href="{{ $item->link }}" target="_blank">
                        <img src="{{ dynamicAsset(path: 'public/assets/admin/img/'.$item->name.'.png') }}" alt="{{ $item->name }}">
                    </a>
                @endforeach
            @endif
        </div>

        <!-- Contact Info -->
        <div class="contact">
            <p>{{ translate('phone') }}: <a href="tel:{{ $companyPhone }}">{{ $companyPhone }}</a></p>
            <p>{{ translate('email') }}: <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a></p>
            <p>{{ translate('All_copy_right_reserved') }}, {{ date('Y') }} {{ $companyName }}</p>
        </div>
    </div>
</body>
</html>
