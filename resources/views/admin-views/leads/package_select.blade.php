@if ($packages->count())
    <label>Select Package: {{ $packages->count() }}</label>
    <select name="selected_package_id" class="form-control package-select">
        <option value="">-- Select Package --</option>
        @foreach ($packages as $pkg)
            <option value="{{ $pkg->id }}" 
                data-price="{{ $pkg->custom_price ?? $pkg->price }}"
                data-title="{{ $pkg->title }}" 
                data-person="{{ $pkg->person }}">
                {{ $pkg->title }} - ₹{{ $pkg->custom_price ?? $pkg->price }} ({{ $pkg->person }} Person)
            </option>
        @endforeach
    </select>

    <!-- Hidden fields -->
    <input type="hidden" name="package_price" id="package_price">
    <input type="hidden" name="package_title" id="package_title">
    <input type="hidden" name="package_person" id="package_person">
    <input type="hidden" name="package_name" id="package_name">

    <!-- Visible selected package display -->
    <div id="selected-package-display" class="mt-2 text-success fw-bold" style="display:none;">
        Selected: <span id="selected-package-text"></span>
    </div>
@else
    <p class="text-danger">No packages available.</p>
@endif