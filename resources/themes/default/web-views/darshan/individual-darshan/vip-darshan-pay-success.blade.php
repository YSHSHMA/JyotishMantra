<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        .toast-error {
            background-color: #d32f2f !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .max-w-md {
            max-width: 40rem !important;
        }

        .toast-success {
            background-color: #28a745 !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .toast {
            opacity: 1 !important;
            /* ensure it's not semi-transparent */
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6B21A8',
                        secondary: '#FACC15',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-br from-purple-100 via-pink-100 to-yellow-100 min-h-screen font-sans overflow-x-hidden">
    <div class="max-w-3xl mx-auto p-4 md:p-6">

        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="https://mahakal.com/storage/app/public/company/2025-02-07-67a5b1849142e.gif" alt="Logo" class="h-20">
        </div>

        <!-- Temple and Title -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h2 class="text-2xl font-bold text-purple-900 mb-2 text-center">{{ $temple->name }}</h2>
            <div class="card-body">
                <div class="mb-3 text-center">
                    <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                </div>
                <h6 class="text-center">
                    Your
                    @if(!empty($plans) && isset($plans[0]['package'][0]['name']))
                        <strong class="font-black fw-bold text-center">{{ $plans[0]['package'][0]['name'] }}</strong>
                    @endif
                    booking has been successful !
                </h6>
                <p class="text-center fs-12">
                 {{ translate('We_have_received_your_booking._Thank_you_for_choosing_our_service.') }}
                </p>
                <!-- <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="{{ route('account-order-darshan') }}" class="btn btn--primary mb-3 text-center">
                            {{ translate('view_booking') }}
                        </a>
                    </div>
                    <div class="col-12 text-center">
                        <a href="{{ route('darshan') }}" class=" text-center">
                            {{ translate('continue') }}
                        </a>
                    </div>
                </div> -->
                @if(!empty($orderId))
        <br>
        {{ translate('Order_ID') }}: <strong>{{ $orderId }}</strong>
    @endif
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</body>

</html>