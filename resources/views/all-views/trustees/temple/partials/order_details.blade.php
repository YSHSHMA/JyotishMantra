<div class="receipt" style="max-width: 400px; margin: 20px auto; padding: 15px; border: 1px solid #ccc; border-radius: 8px; font-family: Arial, sans-serif; background: #fff;">
    <h6 style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 5px;">{{ strtoupper($order->temple->name ?? 'TEMPLE NAME') }}</h6>
    <div style="height:1px; background:#ccc; margin:10px 0;"></div>

    <!-- Order Info -->
    <div class="d-flex flex-wrap justify-content-between" style="margin-bottom: 15px;">
        <div>
            <div style="font-weight: bold;">{{ translate('Order ID') }} :</div>
            <div>{{ $order->order_id }}</div>
        </div>
        <div>
            <div style="font-weight: bold;">{{ translate('Date') }} :</div>
            <div>{{ $detail->booking_date->format('d M Y') }}</div>
        </div>
    </div>

    <div style="height:1px; background:#ccc; margin:10px 0;"></div>

    <!-- Service / Package Info -->
    <div class="info" style="margin-top: 10px;">
        <p style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span style="font-weight: bold;">{{ translate('Service') }}</span>
            @if($puja__status != 2)
            <span>{{ ucfirst($detail->type ?? '-') }}</span>
            @endif
        </p>


        <!-- Extra spacing after Service for clarity -->
        <p style="height:10px;"></p>

        @if($puja__status == 2)
        <?php
        $totalAmount = 0;
        $purohit_name = '-';
        if (!empty($order->details) && count($order->details) > 0) {
            foreach ($order->details as $keys => $value) {
                if (is_numeric($value['final_amount'])) {
                    $totalAmount += $value['final_amount'];
                }
                if (strtolower($value['type'] ?? '') == 'puja') {
                    $purohit_name = $value['purohit']->name ?? '-';
                } ?>
                <p style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-weight: bold;">{{ ucfirst($value['type'] ?? '-') }}</span>                  
                    <span> {{ $value['package']->varient_name ?? '-' }}({{ ucfirst($value['people_count'] ?? '0') }})</span>
                </p>
        <?php   }
        }
        ?>
        <br>
        @if(strtolower($detail->type ?? '') == 'puja')
        <p style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span style="font-weight: bold;">{{ translate('Purohit') }}</span>
            <span>{{ $purohit_name ?? '-' }}</span>
        </p>
        @endif
        @else
        <p style="text-align:center; font-weight: bold; margin: 10px 0;">
            {{ $detail->package->varient_name ?? '-' }}
        </p>

        @if(strtolower($detail->type ?? '') == 'puja')
        <p style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span style="font-weight: bold;">{{ translate('Purohit') }}</span>
            <span>{{ $detail->purohit->name ?? '-' }}</span>
        </p>
        @endif
        @endif

        <p style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span style="font-weight: bold;">{{ translate('Yajman') }}</span>
            <span>
                @if($detail['customers'] && json_decode($detail['customers']??"[]",true))
                {{ (json_decode($detail['customers']??"[]",true)[0]['name']??"") }}
                @else
                {{ $order->user->name ?? '-' }}
                @endif
                
            </span>
        </p>
        <?php if ($puja__status == 1) {
            $amount = number_format((($detail->platform_fee ?? 0) + ($detail->receipt_fee ?? 0)), 2);
        } elseif ($puja__status == 2) {
            $amount = number_format(($totalAmount ?? 0), 2);
        } else {
            $amount = number_format($detail->final_amount ?? 0, 2);
        } ?>
        @php
        $mode = ucfirst($order->payment_mode);
        $status = strtolower($detail->booking_status);

        if ($status === 'confirmed') {
        $statusText = "<span style='color:green; font-weight:bold;'>Confirmed</span>";
        } elseif ($status === 'cancelled') {
        $statusText = "<span style='color:red; font-weight:bold;'>Cancelled</span>";
        } else {
        $statusText = "<span style='color:orange; font-weight:bold;'>Pending</span>";
        }
        @endphp

        <p style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span style="font-weight: bold;">{{ translate('Amount') }}</span>
            <span>₹{{ $amount }}</span>
        </p>

        <p style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span style="font-weight: bold;">{{ translate('Payment Mode') }}</span>
            <span>{{ $mode }}</span>
        </p>

        <p style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span style="font-weight: bold;">{{ translate('Payment Status') }}</span>
            <span>
                {!! ucfirst($mode) !!} (₹{{ $amount }}) {!! $statusText !!}
            </span>
        </p>

    </div>
    <div class="divider"></div>
    <div class="qr" style="text-align: center;">
        <img src="{{ $qrUrl }}" alt="QR Code" width="80" height="80">
    </div>
    <div style="height:1px; background:#ccc; margin:15px 0;"></div>
    <!-- Footer -->
    <div style="margin-top: 15px; font-family: Arial, sans-serif;">
        <p style="font-size:12px; margin: 2px 0;">
            Powered by Mahakal.com - 100 करोड़ सनातनियों का अपना Spiritual-Tech Platform
        </p>
        <p style="font-size:10px; margin: 2px 0;">
            <strong>Note:</strong> This is a system-generated invoice and does not require a physical signature.
        </p>
    </div>
</div>