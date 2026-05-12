<div class="row">
    <div class="form-group col-md-6">
        <label for="primary_skills" class="form-label">Primary Skills</label>
        <select name="primary_skills" id="primary-skill" class="form-control" disabled>
            @foreach ($skills as $skill)
                <option value="{{ $skill['id'] }}"
                    {{ $astrologer['primary_skills'] == $skill['id'] ? 'selected' : '' }}>
                    {{ $skill['name'] }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6">
        @if ($astrologer['primary_skills']==3)
        <label for="other_skills" class="form-label">Other Skills (if you have any)</label>
        <select name="other_skills[]" id="other-skill" class="form-control multi-select" multiple>
            <option value="" disabled>Select Other Skills</option>
            @foreach ($skills as $skill)
                @if ($skill['id'] != $astrologer['primarySkill']['id'])
                    <option value="{{ $skill['id'] }}"
                        @if ($astrologer['other_skills'] != null) {{ array_search($skill['id'], array_column($astrologer['other_skills']->toArray(), 'id')) !== false ? 'selected' : '' }} @endif>
                        {{ $skill['name'] }}</option>
                @endif
            @endforeach
        </select>
        @endif
    </div>

    <div id="pandit-div" class="col-12" style="display:{{ $astrologer['primary_skills'] == 3 ? 'block' : 'none' }}">
        <div class="row">
            <div class="form-group col-6">
                <label for="pandit_category" class="form-label">Pooja Category</label>
                <select name="is_pandit_pooja_category[]" id="pandit-category" multiple
                    class="form-control multi-select">
                    @foreach ($panditCategories as $category)
                        <option value="{{ $category['id'] }}"
                            @if ($astrologer['is_pandit_pooja_category'] != null) {{ array_search($category['id'], array_column($astrologer['is_pandit_pooja_category']->toArray(), 'id')) !== false ? 'selected' : '' }} @endif>
                            {{ $category['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="panda" class="form-label">Panda</label>
                <input type="text" name="is_pandit_panda" id="panda" class="form-control"
                    placeholder="Your Panda" value="{{ $astrologer['is_pandit_panda'] }}">
            </div>
            <div class="form-group col-md-6">
                <label for="gotra" class="form-label">Gotra</label>
                <input type="text" name="is_pandit_gotra" id="gotra" class="form-control"
                    placeholder="Your Gotra" value="{{ $astrologer['is_pandit_gotra'] }}">
            </div>
            <div class="form-group col-md-6">
                <label for="primary_mandir" class="form-label">Primary Mandir/Ghat (where you perform pooja)</label>
                <input type="text" name="is_pandit_primary_mandir" id="primary-mandir" class="form-control"
                    placeholder="Your Mandir/Ghat" value="{{ $astrologer['is_pandit_primary_mandir'] }}">
            </div>
            <div class="form-group col-md-6">
                <label for="primary_mandir_location" class="form-label">Primary Mandir/Ghat Address</label>
                <input type="text" name="is_pandit_primary_mandir_location" id="primary-mandir-location"
                    class="form-control" placeholder="Your Mandir/Ghat Address"
                    value="{{ $astrologer['is_pandit_primary_mandir_location'] }}">
            </div>
            <div class="form-group col-md-6">
                <label for="pooja_per_day" class="form-label">No. of Pooja Per Day</label>
                <input type="number" name="pooja_per_day" class="form-control" placeholder="Pooja per day" value="{{$astrologer['is_pandit_pooja_per_day']}}">
            </div>
            <div class="form-group col-md-6">
                <label for="min_charge" class="form-label">Minimum Charge Per Pooja</label>
                <input type="number" name="min_charge" class="form-control" placeholder="Minimum Charge" value="{{$astrologer['is_pandit_min_charge']}}">
            </div>
            <div class="form-group col-md-6">
                <label for="max_charge" class="form-label">Maximum Charge Per Pooja</label>
                <input type="number" name="max_charge" class="form-control" placeholder="Maximum Charge" value="{{$astrologer['is_pandit_max_charge']}}">
            </div>
        </div>
    </div>

    
    <div class="form-group col-md-6">
        <label for="category" class="form-label">Category</label>
        <select name="category[]" id="" class="form-control multi-select" multiple required>
              @php
            // Prevent error by defaulting to empty array if null
                $selectedCategories = $astrologer['category'] ? $astrologer['category']->toArray() : [];
            @endphp
            @foreach ($categories as $category)
            <option value="{{ $category['id'] }}"
                {{ array_search($category['id'], array_column($selectedCategories, 'id')) !== false ? 'selected' : '' }}>
                {{ $category['name'] }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="language" class="form-label">Language</label>
        <select name="language[]" id="" class="form-control multi-select" multiple required>
            <option value="hi" {{ in_array('hi', $astrologer['language']) ? 'selected' : '' }}>Hindi
            </option>
            <option value="en" {{ in_array('en', $astrologer['language']) ? 'selected' : '' }}>English
            </option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="experience" class="form-label">Experience In Years</label>
        <input type="number" name="experience" class="form-control" placeholder="Experience In Years"
            value="{{ $astrologer['experience'] }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="daily_hours_contribution" class="form-label">How many
            hours you can contribute daily?</label>
        <input type="number" name="daily_hours_contribution" class="form-control" placeholder="Daily Contribution"
            value="{{ $astrologer['daily_hours_contribution'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="office_address" class="form-label">Your office address</label>
        <textarea name="office_address" id="" class="form-control" rows="2">{{ $astrologer['office_address'] }}</textarea>
    </div>
</div>
