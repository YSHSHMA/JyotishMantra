<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pandit Puja Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body { font-family: Arial, sans-serif; color:#222; }
        .invoice-box {
            max-width: 900px;
            margin: auto;
            padding: 25px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px #ccc;
        }
        table { width: 100%; border-collapse: collapse; }
        .heading { background: #f5f5f5; font-weight: bold; }
        .right { text-align: right; }
        .mt-2 { margin-top: 10px; }
        .mt-4 { margin-top: 20px; }
        .border { border: 1px solid #ddd; }
        th, td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        .no-border td { border: none !important; }
    </style>
</head>

<body>

<div class="invoice-box">

    {{-- COMPANY + INVOICE HEADER --}}
    <table class="no-border">
        <tr>
            <td>
                <img src="{{ dynamicStorage(path: "storage/app/public/company/$companyWebLogo") }}" height="50" alt="">
            </td>
            <td class="right">
                <h2 style="margin:0;">INVOICE</h2>
                <small>Date: {{ date('d M, Y h:i A', strtotime($details->created_at)) }}</small>
            </td>
        </tr>
    </table>

    {{-- FROM / TO DETAILS --}}
    <table class="mt-4">
        <tr class="heading">
            <td>From</td>
            <td>To</td>
        </tr>
        <tr>
            <td>
                <strong>{{ $companyName }}</strong><br>
                {{ $companyAddress }}<br>
                GSTIN: {{ $companygst }}<br>
                PAN: {{ $companypan }}
            </td>

            <td>
                <strong>{{ $details->customers->f_name }} {{ $details->customers->l_name }}</strong><br>
                Phone: {{ $details->customers->phone }}<br>
                Email: {{ $details->customers->email }}
            </td>
        </tr>
    </table>

    {{-- ORDER DETAILS --}}
    <table class="mt-4">
        <tr class="heading">
            <td colspan="2">Order Details</td>
        </tr>
        <tr>
            <td><strong>Order ID:</strong> {{ $details->order_id }}</td>
            <td><strong>Booking Date:</strong> {{ date('d M, Y', strtotime($details->booking_date)) }}</td>
        </tr>
        <tr>
            <td><strong>Puja Name:</strong> {{ $details->services->name }}</td>
            <td><strong>Package:</strong> {{ $details->packages->title ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Member Name:</strong> {{ $details->members }}</td>
            <td><strong>Gotra:</strong> {{ $details->gotra ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Payment Method:</strong> {{ $details->payments->payment_method ?? 'N/A' }}</td>
            <td><strong>Payment Status:</strong> {{ ucfirst($details->payment_status) }}</td>
        </tr>
    </table>

    {{-- ITEM TABLE --}}
    @php
        $currency = getCurrencyCode();

        $subtotal = usdToDefaultCurrency(amount: $details->package_price);
        $basePrice = round($subtotal / 1.18, 2);
        $gstAmount = round($subtotal - $basePrice, 2);
        $serial = 1;
        $totalBeforeTax = $basePrice;
    @endphp

    <h3 class="mt-4">Order Summary</h3>

    <table>
        <thead class="heading">
            <tr>
                <th>S.No</th>
                <th>Description</th>
                <th>Qty</th>
                <th class="right">Rate</th>
                <th class="right">Tax %</th>
                <th class="right">Tax Amount</th>
                <th class="right">Total</th>
            </tr>
        </thead>

        <tbody>
            {{-- Puja --}}
            <tr>
                <td>{{ $serial++ }}</td>
                <td>{{ $details->services->name }}</td>
                <td>1</td>
                <td class="right">{{ setCurrencySymbol(amount: $basePrice, currencyCode: $currency) }}</td>
                <td class="right">18%</td>
                <td class="right">{{ setCurrencySymbol(amount: $gstAmount, currencyCode: $currency) }}</td>
                <td class="right">{{ setCurrencySymbol(amount: $subtotal, currencyCode: $currency) }}</td>
            </tr>

            {{-- Charity / Products --}}
            @foreach ($details->product_leads as $p)
                @php
                    $qty = $p->qty;
                    $rate = usdToDefaultCurrency(amount: $p->product_price);
                    $total = usdToDefaultCurrency(amount: $p->final_price);

                    $totalBeforeTax += $total;
                @endphp

                <tr>
                    <td>{{ $serial++ }}</td>
                    <td>{{ $p->productsData->name }}</td>
                    <td>{{ $qty }}</td>
                    <td class="right">{{ setCurrencySymbol(amount: $rate, currencyCode: $currency) }}</td>
                    <td class="right">0%</td>
                    <td class="right">0.00</td>
                    <td class="right">{{ setCurrencySymbol(amount: $total, currencyCode: $currency) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTAL SUMMARY --}}
    @php
        $grandTotal = $totalBeforeTax + $gstAmount;
        $finalAmount = $grandTotal - ($details->coupon_amount ?? 0);
    @endphp

    <table class="mt-4">
        <tr>
            <td class="no-border">
                <strong>Note:</strong> This is a system-generated invoice; no signature required.
            </td>
            <td class="right no-border">
                <table class="border" style="width: 300px;">
                    <tr>
                        <td>Subtotal</td>
                        <td class="right">{{ setCurrencySymbol(amount: $totalBeforeTax, currencyCode: $currency) }}</td>
                    </tr>
                    <tr>
                        <td>Total Tax</td>
                        <td class="right">{{ setCurrencySymbol(amount: $gstAmount, currencyCode: $currency) }}</td>
                    </tr>
                    <tr>
                        <td>Coupon Discount</td>
                        <td class="right">- {{ setCurrencySymbol(amount: $details->coupon_amount ?? 0, currencyCode: $currency) }}</td>
                    </tr>
                    <tr class="heading">
                        <td><strong>Total Amount</strong></td>
                        <td class="right"><strong>{{ setCurrencySymbol(amount: $finalAmount, currencyCode: $currency) }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>

</body>
</html>
