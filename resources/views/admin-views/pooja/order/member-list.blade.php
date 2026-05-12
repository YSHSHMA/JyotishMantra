<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIP Pooja - Member List</title>
    <style>
        @page {
            margin: 130px 25px 100px 25px;
            /* Top / Right / Bottom / Left */
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* ----------- FIXED HEADER ----------- */
        .pdf-header {
            position: fixed;
            top: -110px;
            left: 0;
            right: 0;
            height: 90px;
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-top: 5px;
        }

        .pdf-header img {
            width: 70px;
            /* LOGO SIZE */
            height: auto;
        }

        /* ----------- FIXED FOOTER ----------- */
        .pdf-footer {
            position: fixed;
            bottom: -70px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
            font-size: 12px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        /* Main content space */
        .content {
            margin-top: 20px;
            margin-bottom: 70px;
        }

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
            font-size: 13px;
        }

        th {
            background-color: #f2f2f2;
        }

        .social-icons img {
            height: 22px;
            margin: 0 5px;
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


    <!-- HEADER -->
    <div class="pdf-header" style="position: fixed; top:-120px; left:0; right:0;
            height:110px; border-bottom:1px solid #ccc;
            padding:0 15px;">

        <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">

            <!-- LOGO -->
            <div style="text-align:left;">
                <img src="{{ public_path('images/logo.png') }}"
                    style="width:180px; height:auto;">
            </div>

            <!--TEXT -->
            <div style="text-align:right; line-height:20px;">

                <div style="font-size:35px; font-weight:bold;">
                    {{ $pooja_name ?? 'Pooja Name Not Available' }}
                </div>

                <div style="font-size:25px; font-weight:600;">
                    {{ $pooja_venue ?? 'Pooja Venue Not Available' }}
                </div>

                <div style="font-size:25px; font-weight:600;">
                    Performing Date: {{ $bookingDate }}
                </div>

            </div>

        </div>

    </div>

    <!-- CONTENT -->
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Member Name</th>
                    <th>Gotra</th>
                    <th>Product</th>
                </tr>
            </thead>
            <tbody>
                @php $sl = 1; @endphp

                @foreach ($orders as $order)
                @php
                $members_array = [];

                if (!empty($order->members)) {
                $decoded_members = json_decode($order->members, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_members)) {
                $members_array = $decoded_members;
                }
                }

                $productName = 'N/A';

                if($order->leads){
                if(isset($order->leads->product)){
                $productName = $order->leads->product->name;
                } elseif(method_exists($order->leads, 'addProducts')){
                $products = $order->leads->addProducts()->pluck('name')->toArray();
                if(count($products)){
                $productName = implode(', ', $products);
                }
                }
                }
                @endphp

                @if (!empty($members_array))
                @foreach ($members_array as $member)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ is_array($member) ? $member['name'] ?? 'Unknown' : trim($member, '[]"') }}</td>
                    <td>{{ $order->gotra ?? 'N/A' }}</td>
                    <td>{{ $productName }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>No Members</td>
                    <td>{{ $order->gotra ?? 'No Gotra' }}</td>
                    <td>{{ $productName }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!--  FOOTER -->
    <div class="pdf-footer"
        style="position:fixed; bottom:-70px; left:0; right:0; height:60px; border-top:1px solid #ccc; text-align:center; padding-top:5px; font-size:12px;">

        <div style="font-weight:bold; font-size:13px;">
            {{ $companyName }}
        </div>

        <div>
            Mobile: {{ $companyPhone }} | {{ $companyEmail }}
        </div>

        <div class="social-icons" style="margin-top:4px;">
            @php($social_media = \App\Models\SocialMedia::where('active_status', 1)->get())
            @if(isset($social_media))
            @foreach ($social_media as $item)
            <img src="{{ dynamicAsset(path: 'public/assets/admin/img/'.$item->name.'.png') }}"
                style="height:18px; margin:0 3px;">
            @endforeach
            @endif
        </div>
    </div>


</body>

</html>