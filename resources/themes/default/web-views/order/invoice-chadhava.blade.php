<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ 'Invoice' }}</title>
  <meta http-equiv="Content-Type" content="text/html;" />
  <meta charset="UTF-8">
  <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/invoice.css') }}">
</head>
<body>
  <div class="invoice-box">
    <table class="table-no-border" style="width: 100%; margin-bottom: 20px;">
      <tr>
        <td style="text-align: left; vertical-align: middle;">
          <img height="40" src="{{ dynamicStorage(path: "storage/app/public/company/$companyWebLogo") }}" alt="">
        </td>
        <td style="text-align: right; vertical-align: middle;">
          <h4 style="margin: 0;"><strong>INVOICE</strong></h4>
        </td>
      </tr>
    </table>

    <table class="table-no-border" style="margin-bottom: 20px;">
      <tr>
        <td style="width: 50%; text-align: left;">
          <strong>From:</strong><br>
          <strong>Mahakal AstroTech Pvt Ltd</strong><br>
          {{ $companyAddress ?? 'N/A' }}<br>
          GSTIN: {{ $companygst ?? 'N/A' }}<br>
          PAN: {{ $companypan ?? 'N/A' }}
        </td>
        <td style="width: 50%; text-align: left;">
          Order No: {{ $details['order_id'] }}<br>
          Date: {{ date('d-m-Y h:i:s a', strtotime($details['created_at'])) }}<br>
          <strong>To:</strong><br>
          <strong>{{ $details['customers']['f_name'] ?? '' }} {{ $details['customers']['l_name'] ?? '' }}</strong><br>
          <strong>Phone:</strong> {{ $details['customers']['phone'] ?? '' }}<br>
          <strong>Email:</strong> {{ $details['customers']['email'] ?? '' }}
        </td>
      </tr>
    </table>

    @php
      $currencyCode = getCurrencyCode();
      $serial = 1;
      $rateSubTotal = usdToDefaultCurrency(amount: (float)($details['package_price'] ?? 0));
    @endphp

    <table class="table-bordered">
      <thead>
        <tr>
          <th>S.No</th>
          <th>Description</th>
          <th>Qty</th>
          <th>Rate</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        {{-- Daan Products --}}
        @if (!empty($details['product_leads']))
          @foreach ($details['product_leads'] as $productLeads)
            @php
              $qty = $productLeads['qty'] ?? 1;
              $rate = usdToDefaultCurrency(amount: (float)($productLeads['product_price'] ?? 0));
              $total = usdToDefaultCurrency(amount: (float)($productLeads['final_price'] ?? 0));
              $rateSubTotal += $total;
            @endphp
            <tr>
              <td>{{ $serial++ }}</td>
              <td>{{ $productLeads['productsData']['name'] ?? 'Product' }}</td>
              <td>{{ $qty }}</td>
              <td>{{ setCurrencySymbol(amount: $rate * $qty, currencyCode: $currencyCode) }}</td>
              <td>{{ setCurrencySymbol(amount: $total, currencyCode: $currencyCode) }}</td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>

    <table class="summary-final-table">
      <tr>
        <td class="summary-left">
          <strong>Note:</strong> This is a system-generated invoice and does not require a physical signature.
        </td>
        <td class="summary-right">
          <table class="summary-table-inner">
            <tr>
              <td><strong>Total Amount</strong></td>
              <td><strong>{{ setCurrencySymbol(amount: $rateSubTotal, currencyCode: $currencyCode) }}</strong></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <div class="footer-note">
      <strong></strong>
    </div>
  </div>
</body>
</html>
