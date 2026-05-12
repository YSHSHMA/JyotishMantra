<div class="row">
   <div class="form-group col-md-6">
      <label for="primary_skills" class="form-label">Primary Skills</label>
      <select name="primary_skills" id="primary-skill" class="form-control">
         <option value="" disabled>Select Primary Skill</option>
         @foreach ($skills as $skill)
         <option value="{{ $skill['id'] }}"
         {{ old('primary_skills', $astrologer['primary_skills'] ?? '') == $skill['id'] ? 'selected' : '' }}>
         {{ $skill['name'] }}
         </option>
         @endforeach
      </select>
   </div>
   <div class="form-group col-md-6" id="other-skill-div">
      <label for="other_skills" class="form-label">Other Skills (if any)</label>
      <select name="other_skills[]" id="other-skill" class="form-control multi-select" multiple>
      {{-- Options will be populated via JS --}}
      </select>
   </div>
   <div id="pandit-div" class="col-12" style="display: none">
      <div class="row">
         <div class="form-group col-6">
            <label for="pandit-category" class="form-label">Pooja Category</label>
            <select name="is_pandit_pooja_category[]" id="pandit-category" multiple class="form-control multi-select">
     
            </select>
         </div>
         <div class="form-group col-md-6">
            <label for="panda" class="form-label">Panda</label>
            <input type="text" name="is_pandit_panda" id="panda" class="form-control"
               placeholder="Your Panda" value="{{ old('is_pandit_panda', $astrologer->is_pandit_panda) }}">
         </div>
         <div class="form-group col-md-6">
            <label for="gotra" class="form-label">Gotra</label>
            <input type="text" name="is_pandit_gotra" id="gotra" class="form-control"
               placeholder="Your Gotra" value="{{ old('is_pandit_gotra', $astrologer->is_pandit_gotra) }}">
         </div>
         <div class="form-group col-md-6">
            <label for="primary_mandir" class="form-label">Primary Mandir/Ghat (where you perform pooja)</label>
            <input type="text" name="is_pandit_primary_mandir" id="primary-mandir" class="form-control"
               placeholder="Your Mandir/Ghat" value="{{ old('is_pandit_primary_mandir', $astrologer->is_pandit_primary_mandir) }}">
         </div>
         <div class="form-group col-md-6">
            <label for="primary_mandir_location" class="form-label">Primary Mandir/Ghat Address</label>
            <input type="text" name="is_pandit_primary_mandir_location" id="primary-mandir-location" class="form-control"
               placeholder="Your Mandir/Ghat Address" 
               value="{{ old('is_pandit_primary_mandir_location', $astrologer->is_pandit_primary_mandir_location) }}">
         </div>
         <div class="form-group col-md-6">
            <label for="pooja_per_day" class="form-label">No. of Pooja Per Day</label>
            <input type="number" name="pooja_per_day" class="form-control" placeholder="Pooja per day" 
               value="{{ old('is_pandit_pooja_per_day', $astrologer->is_pandit_pooja_per_day) }}">
         </div>
         <div class="form-group col-md-6">
            <label for="min_charge" class="form-label">Minimum Charge Per Pooja</label>
            <input type="number" name="min_charge" class="form-control" placeholder="Minimum Charge" 
               value="{{ old('is_pandit_min_charge', $astrologer->is_pandit_min_charge) }}">
         </div>
         <div class="form-group col-md-6">
            <label for="max_charge" class="form-label">Maximum Charge Per Pooja</label>
            <input type="number" name="max_charge" class="form-control" placeholder="Maximum Charge"
               value="{{ old('is_pandit_max_charge', $astrologer->is_pandit_max_charge) }}">
         </div>
      </div>
   </div>
   <div class="form-group col-md-6">
      <label for="category" class="form-label">Category</label>
      @php
      $selectedCategories = $astrologer['category']?->pluck('id')->toArray() ?? [];
      @endphp
      <select name="category[]" class="form-control multi-select" multiple id="validationCustom20">
      @foreach ($categories as $category)
      <option value="{{ $category['id'] }}"
      {{ in_array($category['id'], $selectedCategories) ? 'selected' : '' }}>
      {{ $category['name'] }}
      </option>
      @endforeach
      </select>
      <div class="invalid-feedback">
         Please select category.
      </div>
   </div>
   <div class="form-group col-md-6">
      <label for="language" class="form-label">Language</label>
      <select name="language[]" class="form-control multi-select" multiple>
      <option value="hi" {{ in_array('hi', $astrologer['language'] ?? []) ? 'selected' : '' }}>Hindi</option>
      <option value="en" {{ in_array('en', $astrologer['language'] ?? []) ? 'selected' : '' }}>English</option>
      </select>
   </div>
   <div class="form-group col-md-6">
      <label for="experience" class="form-label">Experience In Years</label>
      <input type="number" name="experience" class="form-control"
         placeholder="Experience In Years" id="validationCustom22" value="{{ old('experience', $astrologer->experience) }}">
      <div class="invalid-feedback">
         Please enter your experience.
      </div>
   </div>
   <div class="form-group col-md-6">
      <label for="daily_hours_contribution" class="form-label">How many
      hours you can contribute daily?</label>
      <input type="number" name="daily_hours_contribution"
         class="form-control" placeholder="Daily Contribution"
         value="{{ old('daily_hours_contribution', $astrologer->daily_hours_contribution) }}">
   </div>
   <div class="form-group col-md-6">
      <label for="office_address" class="form-label">Your office address</label>
      <textarea name="office_address" class="form-control" rows="2">{{ old('office_address', $astrologer->office_address) }}</textarea>
   </div>
</div>