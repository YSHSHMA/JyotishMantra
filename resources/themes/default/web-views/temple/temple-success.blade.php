<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Booking Successful - {{ $orderinfo->temple->name ?? 'Temple' }}</title>

    <!-- Bootstrap CSS -->
    <link href="{{ theme_asset('public/assets/front-end/css/poojafilter/bootstrapnew.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        body {
            background: linear-gradient(to bottom, #f97316, #ea580c, #c2410c);
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
        }

        .card {
            border-radius: 12px;
        }

        .header img {
            max-height: 70px;
        }

        .success-icon {
            font-size: 50px;
            color: #28a745;
        }

        .ticket-card {
            border: 2px dashed #6c757d;
            border-radius: 12px;
            padding: 15px;
            background: #fff;
        }

        .footer {
            background: #f8f9fa;
            font-size: 14px;
        }

        .alert {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .list-group-item {
            font-size: 14px;
            padding: 6px 12px;
        }

        @media (max-width: 576px) {
            .success-icon {
                font-size: 40px;
            }
        }
    </style>
</head>

<body>
    @php $ecommerceLogo = getWebConfig('company_web_logo'); @endphp

    <!-- Header -->
    <div class="header text-center my-3">
        <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
            alt="Logo">
    </div>

    <!-- Main Container -->
    <div class="container">
        <div class="card shadow-sm p-3 mb-4">

            <!-- Payment Mode Alert -->
            @if (isset($orderinfo->payment_mode))
                @if ($orderinfo->payment_mode === 'cash')
                    <div class="alert alert-warning text-center">
                        <strong>Cash Payment:</strong> Please pay at the temple counter to receive your ticket.<br>
                        <small>Ticket is valid for <strong>1 hour</strong> only. After that, it will be automatically
                            canceled.</small>
                    </div>
                @elseif($orderinfo->payment_mode === 'online')
                    <div class="alert alert-success text-center">
                        <strong> {{ translate('Payment_Confirmed') }}:</strong> Your ticket has been successfully confirmed.
                    </div>
                @endif
            @endif
            <!-- Success Message -->
            <div class="row mb-3 align-items-stretch g-3">
                {{-- Temple Info --}}
                <div class="col-md-5">
                    <div class="p-4 bg-white border rounded shadow-sm h-100">
                        <h5 class="text-primary fw-bold mb-1">
                            {{ $orderinfo->temple->name ?? '-' }}
                        </h5>

                        <p class="mb-0 text-muted">
                            @if (!empty($orderinfo->temple->cities))
                                {{ $orderinfo->temple->cities['city'] ?? '' }}
                            @endif
                            @if (!empty($orderinfo->temple->states))
                                , {{ ucwords(strtolower($orderinfo->temple->states['name'] ?? '')) }}
                            @endif
                            @if (!empty($orderinfo->temple->country))
                                , {{ $orderinfo->temple->country['name'] ?? '' }}
                            @endif
                        </p>
                    </div>
                </div>
                {{-- Booking Info --}}
                <div class="col-md-5">
                    <div class="p-4 bg-white border rounded shadow-sm h-100 position-relative">
                        {{--  Horizontal Info Row --}}
                        <div class="row text-center text-md-start">
                            <div class="col-md-6 col-lg-4 mb-2">
                                <strong class="text-secondary">{{ translate('Booking_Date') }}:</strong><br>
                                <span class="text-dark">
                                    {{ !empty($orderinfo->created_at) ? \Carbon\Carbon::parse($orderinfo->created_at)->format('d M Y') : '-' }}
                                </span>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-2">
                                <strong class="text-secondary">{{ translate('Payment_Mode') }}:</strong><br>
                                <span class="text-dark">{{ ucfirst($orderinfo->payment_mode ?? '-') }}</span>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <strong class="text-secondary">{{ translate('Order_id') }}:</strong><br>
                                <span class="text-dark">{{ ucfirst($orderinfo->order_id ?? '-') }}</span>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-2">
                                <strong class="text-secondary">{{ translate('Total_Amount') }}:</strong><br>
                                <span class="fw-bold text-success">₹{{ number_format($orderinfo->total_amount ?? 0) }}</span>
                            </div>

                            @php
                                $totalCustomers = 0;
                                $services = [];
                                foreach ($orderDetails as $detail) {
                                    $members = json_decode($detail->customers ?? '[]', true);
                                    $totalCustomers += count($members);
                                    $services[] = ucfirst($detail->type ?? '-');
                                }
                            @endphp

                            <div class="col-md-6 col-lg-4 mb-2">
                                <strong class="text-secondary">{{ translate('Customers') }}:</strong><br>
                                <span class="text-dark">{{ $totalCustomers }}</span>
                            </div>

                            <div class="col-md-6 col-lg-8 mb-2">
                                <strong class="text-secondary">{{ translate('Services') }}:</strong><br>
                                <span class="text-dark">{{ implode(', ', $services) }}</span>
                            </div>
                        </div>
                        {{--  Payment Seal --}}
                        @if ($orderinfo->payment_mode === 'cash')
                            <img src="{{ asset('public/assets/front-end/img/icons/unpaid-seal.png') }}" 
                                alt="Unpaid Seal"
                                class="position-absolute" 
                                style="top:7rem; right:6px; width:40px; opacity:0.9;">
                        @elseif ($orderinfo->payment_mode === 'online')
                            <img src="{{ asset('public/assets/front-end/img/icons/paid-seal.png') }}" 
                                alt="Paid Seal"
                                class="position-absolute" 
                                style="top:7rem; right:6px; width:40px; opacity:0.9;">
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <h6>Your QR</h6>
                    {!!$qrs!!}
                </div>
            </div>
            <!-- Tickets Grid -->
            <div class="row">
                @foreach ($orderDetails as $detail)
                    <div class="col-md-4 mb-3">
                        <div class="ticket-card h-100">
                            
                            <h6 class="mb-1 text-bold">{{ translate('Service') }}: {{ ucfirst($detail->type ?? '-') }} -
                                {{ $detail->package->varient_name ?? '-' }}</h6>
                            <p class="mb-1">{{ translate('Booking_Date') }}:
                                {{ !empty($detail->booking_date) ? \Carbon\Carbon::parse($detail->booking_date)->format('d M Y') : '-' }}
                            </p>
                            <p class="mb-1">{{ translate('Booking_Status') }}: {{ ucfirst($detail->booking_status ?? '-') }}</p>
                            <p class="mb-1">{{ translate('Orde_id') }}: {{ ucfirst($detail->type_order_id ?? '-') }}</p>

                            @php
                                $members = json_decode($detail->customers ?? '[]', true);
                            @endphp

                            @if (!empty($members))
                                <p class="mb-1">{{ translate('Members') }}:</p>
                                <ul class="list-group">
                                    @foreach ($members as $member)
                                        @php
                                            $img = null;
                                            // Only fetch image if Aadhaar exists
                                            if (!empty($member['aadhar'])) {
                                                $img = App\Models\UserAadhaarKyc::select('image')
                                                    ->where('aadhaar_number', $member['aadhar'])
                                                    ->first();
                                            }
                                        @endphp
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{-- Show image only if Aadhaar exists and image is present --}}
                                            @if (!empty($member['aadhar']) && !empty($img['image']))
                                                <img src="{{ $img['image'] }}" class="me-2" alt=""
                                                    width="40" height="40">
                                            @endif

                                            {{-- Show name or mobile --}}
                                            {{ $member['name'] ?? ($member['mobile'] ?? 'N/A') }}

                                            {{-- Show Aadhaar or Passport --}}
                                            @if (!empty($member['aadhar']))
                                                <span>Aadhaar: {{ $member['aadhar'] }}</span>
                                            @elseif(!empty($member['passport']))
                                                <span>Passport: {{ $member['passport'] }}</span>
                                            @endif
                                        </li>
                                    @endforeach

                                </ul>
                            @else
                                <p class="mb-0">{{ translate('No_members_added') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Back Button -->
            <div class="text-center mt-3">
                @if (!empty($orderinfo->temple->slug))
                    <a href="{{ route('temple.templeservice', ['slug' => $orderinfo->temple->slug]) }}"
                        class="btn btn-primary px-4">
                        <i class="fas fa-home me-1"></i> {{ translate('Back_to_Home') }}
                    </a>
                @else
                    <a href="{{ route('home') }}" class="btn btn-primary px-4">
                        <i class="fas fa-home me-1"></i> {{ translate('Back_to_Home') }}
                    </a>
                @endif
            </div>

        </div>
    </div>

    <!-- Footer -->
    <div class="footer p-2 text-center">
        &copy; {{ date('Y') }} {{ getWebConfig('company_name') ?? 'Temple Services' }}. All Rights Reserved.
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ theme_asset('public/assets/front-end/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- delete local storage key --}}
    <script>
        localStorage.removeItem('templecustomer');
    </script>
</body>

</html>
