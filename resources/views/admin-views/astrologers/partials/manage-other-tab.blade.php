@php
$selectedQualification = old('highest_qualification', $astrologer->highest_qualification);
@endphp
<div class="row">
   <!-- Highest Qualification -->
   <div class="form-group col-md-6">
      <label for="highest_qualification" class="form-label">Select your highest qualification</label>
      <select name="highest_qualification" class="form-control">
         <option value="">Select qualification</option>
         @foreach (['10th', '12th', 'diploma', 'graduate', 'post graduate', 'phd', 'others'] as $option)
         <option value="{{ $option }}" {{ $selectedQualification == $option ? 'selected' : '' }}>
         {{ ucfirst($option) }}
         </option>
         @endforeach
      </select>
   </div>
   <!-- Other Qualification -->
   @if ($selectedQualification === 'others')
   <div class="form-group col-md-6">
      <label for="other_qualification" class="form-label">Other Qualification</label>
      <input type="text" name="other_qualification" class="form-control"
         value="{{ old('other_qualification', $astrologer->other_qualification) }}"
         placeholder="Other qualification">
   </div>
   @endif
   <!-- College -->
   <div class="form-group col-md-6">
      <label for="college" class="form-label">College/School/University</label>
      <input type="text" name="college" class="form-control"
         value="{{ old('college', $astrologer->college) }}" placeholder="Enter your College/School/University">
   </div>
   <!-- Onboard -->
   <div class="form-group col-md-6">
      <label for="onboard_you" class="form-label">Why should we onboard you?</label>
      <input type="text" name="onboard_you" class="form-control"
         value="{{ old('onboard_you', $astrologer->onboard_you) }}" placeholder="Why we should onboard you">
   </div>
   <!-- Interview Time -->
   <div class="form-group col-md-6">
      <label for="interview_time" class="form-label">Suitable time for interview</label>
      <input type="text" name="interview_time" class="form-control"
         value="{{ old('interview_time', $astrologer->interview_time) }}" placeholder="Enter suitable time">
   </div>
   <!-- Business Source -->
   <div class="form-group col-md-6">
      <label for="business_source" class="form-label">Main Source of Business</label>
      <select name="business_source" class="form-control">
      @foreach(['own business', 'private job', 'goverment job', 'studying in college', 'none of the above'] as $source)
      <option value="{{ $source }}" {{ old('business_source', $astrologer->business_source) == $source ? 'selected' : '' }}>
      {{ ucfirst($source) }}
      </option>
      @endforeach
      </select>
   </div>
   <!-- Learn Primary Skill -->
   <div class="form-group col-md-6">
      <label for="learn_primary_skill" class="form-label">Where did you learn your primary skill?</label>
      <input type="text" name="learn_primary_skill" class="form-control"
         value="{{ old('learn_primary_skill', $astrologer->learn_primary_skill) }}"
         placeholder="From where did you learn">
   </div>
   <!-- Social Links -->
   @foreach (['instagram', 'facebook', 'linkedin', 'youtube', 'website'] as $social)
   <div class="form-group col-md-6">
      <label for="{{ $social }}" class="form-label">{{ ucfirst($social) }} profile link</label>
      <input type="url" name="{{ $social }}" class="form-control"
         value="{{ old($social, $astrologer->$social) }}" placeholder="Enter {{ $social }} profile link">
   </div>
   @endforeach
   <!-- Earnings -->
   <div class="form-group col-md-6">
      <label for="min_earning" class="form-label">Minimum Earning Expectation</label>
      <input type="text" name="min_earning" class="form-control"
         value="{{ old('min_earning', $astrologer->min_earning) }}" placeholder="Minimum Earning">
   </div>
   <div class="form-group col-md-6">
      <label for="max_earning" class="form-label">Maximum Earning Expectation</label>
      <input type="text" name="max_earning" class="form-control"
         value="{{ old('max_earning', $astrologer->max_earning) }}" placeholder="Maximum Earning">
   </div>
   <!-- Foreign Country -->
   <div class="form-group col-md-6">
      <label for="foreign_country" class="form-label">No. of foreign countries visited</label>
      @php $foreignValue = old('foreign_country', $astrologer->foreign_country); @endphp
      <select name="foreign_country" class="form-control">
      @foreach (['0', '1-2', '3-5', '6+'] as $option)
      <option value="{{ $option }}" {{ $foreignValue == $option ? 'selected' : '' }}>{{ $option }}</option>
      @endforeach
      </select>
   </div>
   <!-- Working -->
   <div class="form-group col-md-6">
      <label for="working" class="form-label">Are you currently working a fulltime job?</label>
      @php $workingValue = old('working', $astrologer->working); @endphp
      <select name="working" class="form-control">
      @foreach([
      'no i am working as part timer or freelancer',
      'yes i am working somewhere as a full timer',
      'no i am not working anywhere else',
      'i own a business'
      ] as $option)
      <option value="{{ $option }}" {{ $workingValue == $option ? 'selected' : '' }}>
      {{ ucfirst($option) }}
      </option>
      @endforeach
      </select>
   </div>
   <!-- Textareas -->
   @foreach (['bio', 'qualities', 'challenge', 'repeat_question'] as $field)
   <div class="form-group col-md-6">
      <label for="{{ $field }}" class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
      <textarea name="{{ $field }}" rows="2" maxlength="200" class="form-control"
         placeholder="Describe here">{{ old($field, $astrologer->$field) }}</textarea>
   </div>
   @endforeach
</div>