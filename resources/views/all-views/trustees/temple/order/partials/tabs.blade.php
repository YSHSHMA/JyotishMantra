@if (!empty($plans))
<?php
if (auth('trust')->check()) {
    $relationEmployees = auth('trust')->user()->relation_id;
    $roleTabs = 1;
} elseif (auth('trust_employee')->check()) {
    $relationEmployees = auth('trust_employee')->user()->relation_id;
    $roleTabs = 0;
} elseif (auth('purohit')->check()) {
    $roleTabs = 1;
    $relationEmployees = auth('purohit')->user()->relation_id;
}
$vendorEmp = \App\Models\VendorEmployees::where('type', "trust")->when(auth('trust_employee')->check(), function ($q) {
    $q->where(['id' => auth('trust_employee')->user()->id]);
})->where('relation_id', $relationEmployees)->first();
$decoded = [];
if ($vendorEmp && $vendorEmp->selected_services) {
    $raw = $vendorEmp->selected_services ?? '[]';
    $firstDecode = json_decode($raw, true);
    if (is_string($firstDecode)) {
        $decoded = json_decode($firstDecode, true);
    } else {
        $decoded = $firstDecode;
    }
}
?>

<ul class="nav nav-tabs" id="serviceTabs" role="tablist">
    @php $firstActive = true; @endphp
    @foreach ($plans as $plan)
    <?php
    if (in_array($plan['name'], $decoded)) {
        $roleTabs2 = 1;
    } else {
        $roleTabs2 = 0;
    }
    ?>
    @if ($plan['status'] == 1 && ($roleTabs2 == 1 || $roleTabs == 1) && ((auth('trust')->check()) || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit') && $plan['name'] == 'puja') || (auth('purohit')->check() && $plan['name'] == 'puja')))
    <li class="nav-item">
        <a class="nav-link {{ $firstActive ? 'active' : '' }}" id="{{ Str::slug($plan['name']) }}-tab" data-toggle="tab" href="#{{ Str::slug($plan['name']) }}-content" role="tab">
            {{ $plan['name'] }}
        </a>
    </li>
    @php $firstActive = false; @endphp
    @endif
    @endforeach
</ul>

<div class="tab-content mt-3">
    @php $firstShow = true; @endphp
    @foreach ($plans as $plan)
    <?php
    if (in_array($plan['name'], $decoded)) {
        $roleTabs2 = 1;
    } else {
        $roleTabs2 = 0;
    }

    ?>
    @if ($plan['status'] == 1 && ($roleTabs2 == 1 || $roleTabs == 1) && ((auth('trust')->check()) || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit') && $plan['name'] == 'puja') || (auth('purohit')->check() && $plan['name'] == 'puja')))
    @php
    $slug = Str::slug($plan['name']);
    $partial = 'all-views.trustees.temple.order.partials.' . $slug;

    @endphp

    <div class="tab-pane fade {{ $firstShow ? 'show active' : '' }}" id="{{ $slug }}-content" role="tabpanel">
        {{-- Include service-specific form partial --}}
        @if (View::exists($partial))
        @include($partial, ['temple' => $temple, 'plan' => $plan, 'package' => $packages])
        @else
        @include('all-views.trustees.temple.order.partials.default', ['plan' => $plan])
        @endif
    </div>
    @php $firstShow = false; @endphp

    @endif
    @endforeach
</div>
@else
<div class="alert alert-warning mt-3">
    No services available for this temple.
</div>
@endif