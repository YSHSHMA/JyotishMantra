<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anushthan - Member List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }


        /* Adjust page content to avoid overlap */
        .content {
            margin-top: 20px;
            margin-bottom: 100px;
            padding: 0 20px;
        }

        /* Space for header & footer */

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }


       

        /* Right-align company info */
        .social-icons img {
            height: 30px;
            margin: 0 5px;
        }

        /* Social media icons */
    </style>
</head>

<body>
    <?php
    
    $companyPhone = getWebConfig(name: 'company_phone');
    $companyEmail = getWebConfig(name: 'company_email');
    $companyName = getWebConfig(name: 'company_name');
    $companyLogo = getWebConfig(name: 'company_web_logo');
    ?>
    <!-- Fixed Header Section -->
  

    <!-- Content Section -->
    <div class="content">
        <h2 style="text-align: center;">Anushthan - {{ $vipooja_name ?? 'Pooja Name Not Available' }}</h2>
        <h4 style="text-align: left;">Booking Date - {{ $bookingDate }}</h4>

        <table>
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Member Name</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sl = 1; // Initialize serial number
                @endphp
                @foreach ($orders as $order)
                    @php
                        $members_array = [];

                        if (!empty($order->members)) {
                            $decoded_members = json_decode($order->members, true);

                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_members)) {
                                foreach ($decoded_members as $member) {
                                    if (is_array($member)) {
                                        $members_array = array_merge($members_array, $member);
                                    } else {
                                        $members_array[] = $member;
                                    }
                                }
                            }
                        }
                    @endphp

                    @if (!empty($members_array))
                        @foreach ($members_array as $member)
                            <tr>
                                <td>{{ $sl++ }}</td>
                                <td>{{ trim($member, '[]"') }}</td> <!-- Removing extra brackets -->
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>No Members</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
{{-- 
    <!-- Fixed Footer Section -->
    <div class="footer">
        <div class="social-icons">
            @php($social_media = \App\Models\SocialMedia::where('active_status', 1)->get())
            @if(isset($social_media))
          
                @foreach ($social_media as $item)
                    <div style="display: inline-block;">
                        <a href="{{$item->link}}" target=”_blank”>
                            <img src="{{dynamicAsset(path: 'public/assets/admin/img/'.$item->name.'.png') }}" alt=""
                                 style="height: 14px; width:14px; padding: 0 3px 0 5px;">
                        </a>
                    </div>
                @endforeach
            
        @endif
        </div>
        <a href="https://play.google.com/store"><img src="{{ public_path('images/playstore.png') }}"
                alt="Play Store"></a>
        <a href="https://www.apple.com/app-store/"><img src="{{ public_path('images/appstore.png') }}"
                alt="App Store"></a>
        <p>Website: <a href="https://www.mahakal.com/">mahakal.com</a> | Mobile: {{$companyPhone}}</p>
    </div> --}}

</body>

</html>
