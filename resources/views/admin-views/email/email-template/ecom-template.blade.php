<?php
  $subtotal = 0;
  $total = 0;
  $subTotal = 0;
  $totalTax = 0;
  $totalShippingCost = 0;
  $totalDiscountOnProduct = 0;
  $extraDiscount = 0;
  
  
  foreach ($orderDetails as $details) {
      $subtotal += $details['price'] * $details['qty'];
      $totalTax += $details['tax'];
      $totalShippingCost += $details->shipping ? $details->shipping->cost : 0;
      $totalDiscountOnProduct += $details['discount'];
  }
  
  if ($order['extra_discount_type'] == 'percent') {
      $extraDiscount = ($subtotal / 100) * $order['extra_discount'];
  } else {
      $extraDiscount = $order['extra_discount'];
  }
  
  $taxModel = $order->orderdetails->first()?->tax_model; // safe access
  $tax = ($taxModel === 'include') ? 0 : $totalTax;
  $shipping = ($order['is_shipping_free'] == 1) ? 0 : $order['shipping_cost'];
  // $grandTotal = $subtotal + $totalTax + $totalShippingCost - $totalDiscountOnProduct;
  
  ?>
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
    <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" style="max-width: 600px; background-color: #ffffff;">
      <!-- Header Section -->
      @if (is_file('storage/app/public/company/' . $companyEmailTop))
      <tr>
        <td align="center">
          <img src="{{ dynamicStorage(path: 'storage/app/public/company/' . $companyEmailTop) }}" 
            alt="Header Image" style="width: 100%; max-width: 600px; display: block;">
        </td>
      </tr>
      @endif
      <tr>
        <td style="padding:40px 72px 36px">
          <table width="100%" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
            <tbody>
              <tr>
                <td valign="top" style="color:#000f1d;font-family:'Source Sans Pro',sans-serif;font-size:20px;font-weight:bold;line-height:28px;text-align:left;text-transform:capitalize;letter-spacing:0.15px">
                  🌸 Namaste {{ $userInfo['name'] }} Ji 🙏
                  <p style="font-size: 16px; color: #555; margin: 5px 0;">
                    Aapka order safaltapurvak prapt ho gaya hai. 😊
                  </p>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      <tr>
        <td style="padding:40px 72px 36px">
          <table width="100%" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
            <tbody>
              <tr>
                <td valign="top" style="color:#000f1d;font-family:'Source Sans Pro',sans-serif;font-size:20px;font-weight:bold;line-height:28px;text-align:left;text-transform:capitalize;letter-spacing:0.15px">
                  Order {{ $order->id }}
                </td>
              </tr>
              <tr>
                <td style="color:#000f1d;font-family:'Source Sans Pro',sans-serif;font-size:16px;font-weight:500;line-height:24px;text-align:left;letter-spacing:0.25px">
                  {{ $order->created_at->format('d-m-Y') }}
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      @foreach ($orderDetails as $orderDetail)
      <tr>
        <td valign="top" style="padding:0 72px">
          <table width="100%" cellpadding="0" cellspacing="0" align="left" bgcolor="#fff">
            <tbody>
              <tr>
                <td width="23%" valign="top" align="center" style="margin:0;font-size:0pt;line-height:0pt;text-align:center;padding-bottom:24px">
                  <img src="{{ isset($orderDetail->product->thumbnail) ? asset('storage/app/public/product/thumbnail/' . $orderDetail->product->thumbnail) : url('default-product.jpg') }}" 
                    alt="Product Image" class="CToWUd a6T" data-bit="iit" tabindex="0" style="width: 50px; height: 50px;">
                </td>
                <td width="2%" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal">&nbsp;</td>
                <td width="auto" valign="top" style="padding-bottom:24px">
                  <div style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:14px;line-height:20px;letter-spacing:0.15px;text-align:left;padding-bottom:8px">
                    {{ $orderDetail->product->name ?? 'N/A' }}
                  </div>
                  <div style="color:rgba(0,19,37,0.64);font-family:'Source Sans Pro',sans-serif;font-size:14px;line-height:20px;letter-spacing:0.5px;text-align:left">
                    Sold By {{ $orderDetail->seller->shop->name ?? 'N/A' }}
                  </div>
                </td>
                <td width="2%" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal">&nbsp;</td>
                <td width="16%" valign="top" align="center" style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:14px;line-height:20px;letter-spacing:0.5px;text-align:right;padding-bottom:15px;width:16%">
                  <div> x {{ $orderDetail->qty ?? 'N/A' }}</div>
                </td>
                <td width="2%" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal">&nbsp;</td>
                <td width="16%" valign="top" align="center" style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:14px;line-height:20px;letter-spacing:0.5px;text-align:right;padding-bottom:15px;width:16%">
                  {{ webCurrencyConverter(
                  (($orderDetail->price ?? 0) * ($orderDetail->qty ?? 1) - ($orderDetail->discount ?? 0)) + 
                  (($orderDetail->tax_model == 'include') ? ($orderDetail->tax ?? 0) : 0)
                  ) }}
                  <span style="text-decoration:line-through">
                  @if ($orderDetail->tax_model == 'include')
                  {{ webCurrencyConverter((($orderDetail->price ?? 0) + ($orderDetail->tax ?? 0)) * ($orderDetail->qty ?? 1)) }}
                  @else
                  {{ webCurrencyConverter(($orderDetail->price ?? 0) * ($orderDetail->qty ?? 1)) }}
                  @endif
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      @endforeach
      <tr>
        <td style="padding:0 72px">
          <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse;margin:0 auto;border-top:1px dashed rgba(0,19,37,0.16);border-bottom:1px dashed rgba(0,19,37,0.16)">
            <tbody>
              <tr>
                <td style="padding:16px 0">
                  <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse">
                    <tbody>
                      <tr>
                        <td style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal;vertical-align:top">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:16px;line-height:24px;letter-spacing:0.25px;text-align:left;padding-bottom:8px">
                                  Subtotal
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                        <td width="10" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal">
                        </td>
                        <td width="105" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal;vertical-align:top">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:16px;line-height:24px;letter-spacing:0.25px;text-align:right;padding-bottom:15px">
                                  @if ($orderDetail->tax_model == 'include')
                                  {{ webCurrencyConverter($subtotal + ($orderDetail->tax ?? 0)) }}
                                  @else 
                                  {{ webCurrencyConverter($subtotal) }}
                                  @endif
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal;vertical-align:top">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:16px;line-height:24px;letter-spacing:0.25px;text-align:left;padding-bottom:8px">
                                  Shipping
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                        <td width="10" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal">
                        </td>
                        <td width="171" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal;vertical-align:top">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:16px;line-height:24px;letter-spacing:0.25px;text-align:right;padding-bottom:15px">
                                  {{ webCurrencyConverter($shipping) }}                                                                                                  
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal;vertical-align:top">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:16px;line-height:24px;letter-spacing:0.25px;text-align:left;padding-bottom:8px">
                                  Tax
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                        <td width="10" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal">
                        </td>
                        <td width="171" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal;vertical-align:top">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:16px;line-height:24px;letter-spacing:0.25px;text-align:right;padding-bottom:15px">
                                  {{ webCurrencyConverter($tax) }}                                                                                                  
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                      </tr>
                      <tr>
                        <td style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal;vertical-align:top">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:16px;line-height:24px;letter-spacing:0.25px;text-align:left;padding-bottom:8px">
                                  Discount
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                        <td width="10" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal">
                        </td>
                        <td width="105" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal;vertical-align:top">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:16px;line-height:24px;letter-spacing:0.25px;text-align:right;padding-bottom:15px">
                                  - {{ webCurrencyConverter($totalDiscountOnProduct) }}
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal;vertical-align:top">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:16px;line-height:24px;letter-spacing:0.25px;text-align:left;padding-bottom:8px">
                                  Coupon Discount
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                        <td width="10" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal">
                        </td>
                        <td width="105" style="font-size:0pt;line-height:0pt;padding:0;margin:0;font-weight:normal;vertical-align:top">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:16px;line-height:24px;letter-spacing:0.25px;text-align:right;padding-bottom:15px">
                                  - {{ webCurrencyConverter($order->discount_amount) }}
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      <tr>
        <td style="padding:0 72px">
          <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse;margin:0 auto">
            <tbody>
              <tr>
                <td style="font-size:0pt;line-height:0pt;padding:16px 0;margin:0;font-weight:normal;vertical-align:top">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                    <tbody>
                      <tr>
                        <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:20px;font-weight:600;line-height:28px;letter-spacing:0.15px;text-align:left;padding-bottom:8px">
                          Grand Total
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
                <td width="10">
                </td>
                <td width="105" style="font-size:0pt;line-height:0pt;padding:16px 0;margin:0;font-weight:normal;vertical-align:top">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                    <tbody>
                      <tr>
                        <td style="color:#001325;font-family:'Source Sans Pro',sans-serif;font-size:20px;font-weight:600;line-height:28px;letter-spacing:0.15px;text-align:right;padding-bottom:15px">
                          {{ webCurrencyConverter($order->order_amount) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
              </tr>
              <tr>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      <!-- Footer -->
      
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