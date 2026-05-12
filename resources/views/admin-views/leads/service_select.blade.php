<label class="title-color">
    Select Service @if (!empty($type))
        ({{ ucfirst($type) }})
    @endif
</label>
<select name="service_id" class="form-control service-select" id="service-select" required>
    <option value="">-- Select Service --</option>
    @foreach ($services as $service)
        <option value="{{ $service->id }}"
            data-pooja-type="{{ $service->pooja_type }}"
            data-week-days='@json($service->week_days)'
            data-schedule='@json($service->schedule)'>
            {{ $service->name }}
        </option>
    @endforeach
</select>

<!-- Display area -->
<div id="service-info" class="mt-2 text-success fw-bold" style="display:none;">
    Selected: <span id="selected-package-text"></span>
</div>