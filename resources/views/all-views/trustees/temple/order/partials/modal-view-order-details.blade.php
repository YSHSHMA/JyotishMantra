<div class="d-flex align-items-center justify-content-between mb-2">
    <div>
        <strong>Payment Mode:</strong> {{ $getData[0]['order']['payment_mode']??"" }}
    </div>
    <div>
        <strong>Payment Status:</strong>
        @if ($getData[0]['order']['payment_status'] == 0)
        <span class="badge badge-danger">Pending</span>
        @elseif($getData[0]['order']['payment_status'] == 1)
        <span class="badge badge-success">Confirmed</span>
        @elseif($getData[0]['order']['payment_status'] == 2)
        <span class="badge badge-warning">Cancelled</span>
        @else
        <span class="badge badge-secondary">Unknown</span>
        @endif
    </div>
    <div>
        <strong>Amount:</strong>
        <span style="font-size: 1.3rem; font-weight: bold;">
            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData[0]['order']['total_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</span>
    </div>
</div>
<hr>
<h6>Services Booked:</h6>
<div class="row">
    <?php $first_customerGet = '';?>
    @foreach ($getData as $detail)
    <div class="col-md-4 mb-3">
        <div class="ticket-card h-100 border p-3 rounded shadow-sm">
            <h6 class="mb-1">{{ ucfirst($detail->type ?? '-') }} -
                {{ $detail->package->varient_name ?? '-' }}
            </h6>
            <p class="mb-1">
                {{ !empty($detail->booking_date) ? \Carbon\Carbon::parse($detail->booking_date)->format('d M Y') : '-' }}
            </p>
            <p class="mb-1">
                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $detail->final_amount ?? 0), currencyCode: getCurrencyCode()) }}
            </p>
            <p class="mb-1">{{ $detail->type_order_id }}</p>
            <p class="mb-1">
                {{ !empty($detail->timeslot) ? \Carbon\Carbon::parse($detail->timeslot->start_time)->format('h:i A') : '-' }}
                -
                {{ !empty($detail->timeslot) ? \Carbon\Carbon::parse($detail->timeslot->end_time)->format('h:i A') : '-' }}
            </p>



            @php
            $members = json_decode($detail->customers ?? '[]', true);
            @endphp

            @if (!empty($members))
            <p class="mb-1">Yajman Information:</p>
            <ul class="list-group">
                @foreach ($members as $member)
                <li
                    class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $member['name'] ?? ($member['mobile'] ?? 'N/A') }}
                    @if (!empty($member['aadhar']))
                    <span class="badge badge-secondary">Aadhaar:
                        {{ $member['aadhar'] }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
            @else
            <p class="mb-0">No members added.</p>
            @endif
            @if (strtolower($detail->type ?? '') == 'locker')
            <?php
            $lockeritems = json_decode($detail['locker_items'], true);
            ?>
            @if (!empty($lockeritems))
            <p class="mb-1">Number Of Phone : {{ $lockeritems['mobile'] ?? '' }}
            </p>
            <p class="mb-1">Number Of luggage :
                {{ $lockeritems['luggage'] ?? '' }}
            </p>
            @endif
            @endif
            <p class="mt-2"><strong>Purohit / Pandit:</strong>
                {{ $detail->purohit->name ?? '-' }}
            </p>
        </div>
    </div>
    <?php if(empty($first_customerGet)){
                        $first_customerGet = (json_decode($detail['customers'] ?? "[]", true)[0]['name'] ?? "");
                    }
                    ?>
    @endforeach
</div>

<hr>
<p><strong>Temple Location:</strong>
    {{ $getData[0]['order']['temple']['name'] ?? '-' }},
    {{ $getData[0]['order']['temple']['cities']['city'] ?? '' }},
    {{ ucwords(strtolower($getData[0]['order']['temple']['states']['name'] ?? '')) }},
    {{ $getData[0]['order']['temple']['country']['name'] ?? '' }}
</p>
<p><strong>Yajman Name:</strong> 

{{ ($getData[0]['order']['user']['name'] ?? ($first_customerGet)) }}
    ({{ $getData[0]['order']['total_people_count'] }} persons)</p>