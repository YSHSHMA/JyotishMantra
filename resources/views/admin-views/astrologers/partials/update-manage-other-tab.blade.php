<div class="row">
    <div class="form-group col-md-6">
        <label for="highest_qualification" class="form-label">Select your highest qualification</label>
        <select name="highest_qualification" id="" class="form-control" onchange="qualification(this)">
            <option value="">Select qualification</option>
            <option value="diploma"
                {{ !empty($astrologer['highest_qualification']) && $astrologer['highest_qualification'] == 'diploma' ? 'selected' : '' }}>
                Diploma</option>
            <option value="10th"
                {{ !empty($astrologer['highest_qualification']) && $astrologer['highest_qualification'] == '10th' ? 'selected' : '' }}>
                10th</option>
            <option value="12th"
                {{ !empty($astrologer['highest_qualification']) && $astrologer['highest_qualification'] == '12th' ? 'selected' : '' }}>
                12th</option>
            <option value="graduate"
                {{ !empty($astrologer['highest_qualification']) && $astrologer['highest_qualification'] == 'graduate' ? 'selected' : '' }}>
                Graduate</option>
            <option value="post graduate"
                {{ !empty($astrologer['highest_qualification']) && $astrologer['highest_qualification'] == 'post graduate' ? 'selected' : '' }}>
                Post Graduate</option>
            <option value="phd"
                {{ !empty($astrologer['highest_qualification']) && $astrologer['highest_qualification'] == 'phd' ? 'selected' : '' }}>
                PHD</option>
            <option value="others"
                {{ !empty($astrologer['highest_qualification']) && $astrologer['highest_qualification'] == 'others' ? 'selected' : '' }}>
                Others</option>
        </select>
    </div>

    <div class="form-group col-md-6" id="other-qualification" style="{{!empty($astrologer['other_qualification'])?'display: block':'display: none'}}">
        <label for="other_qualification" class="form-label">Other Qualification</label>
        <input type="text" name="other_qualification" id="other-qualification-text" class="form-control" placeholder="other qualification" value="{{!empty($astrologer['other_qualification'])?$astrologer['other_qualification']:''}}">
    </div>

    {{-- <div class="form-group col-md-6">
        <label for="primary_degree" class="form-label">Degree / Diploma</label>
        <select name="primary_degree" id="" class="form-control">
            <option value="">Select degree / diploma</option>
            <option value="btech"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'btech' ? 'selected' : '' }}>B.tech
            </option>
            <option value="bsc"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'bsc' ? 'selected' : '' }}>B.Sc
            </option>
            <option value="ba"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'ba' ? 'selected' : '' }}>B.A
            </option>
            <option value="be"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'be' ? 'selected' : '' }}>B.E
            </option>
            <option value="bcom"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'bcom' ? 'selected' : '' }}>B.com
            </option>
            <option value="bpharma"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'bpharma' ? 'selected' : '' }}>
                B.Pharma</option>
            <option value="mtech"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'mtech' ? 'selected' : '' }}>
                M.tech</option>
            <option value="ma"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'ma' ? 'selected' : '' }}>M.A
            </option>
            <option value="msc"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'msc' ? 'selected' : '' }}>M.Sc
            </option>
            <option value="mbbs"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'mbbs' ? 'selected' : '' }}>MBBS
            </option>
            <option value="other"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'other' ? 'selected' : '' }}>other
            </option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="secondary_qualification" class="form-label">Select your secondary
            qualification</label>
        <select name="secondary_qualification" id="" class="form-control">
            <option value="">Select qualification</option>
            <option value="diploma"
                {{ !empty($astrologer['secondary_qualification']) && $astrologer['secondary_qualification'] == 'diploma' ? 'selected' : '' }}>
                Diploma</option>
            <option value="10th"
                {{ !empty($astrologer['secondary_qualification']) && $astrologer['secondary_qualification'] == '10th' ? 'selected' : '' }}>
                10th</option>
            <option value="12th"
                {{ !empty($astrologer['secondary_qualification']) && $astrologer['secondary_qualification'] == '12th' ? 'selected' : '' }}>
                12th</option>
            <option value="graduate"
                {{ !empty($astrologer['secondary_qualification']) && $astrologer['secondary_qualification'] == 'graduate' ? 'selected' : '' }}>
                Graduate</option>
            <option value="post graduate"
                {{ !empty($astrologer['secondary_qualification']) && $astrologer['secondary_qualification'] == 'post graduate' ? 'selected' : '' }}>
                Post Graduate</option>
            <option value="phd"
                {{ !empty($astrologer['secondary_qualification']) && $astrologer['secondary_qualification'] == 'phd' ? 'selected' : '' }}>
                PHD</option>
            <option value="others"
                {{ !empty($astrologer['secondary_qualification']) && $astrologer['secondary_qualification'] == 'others' ? 'selected' : '' }}>
                Others</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="secondary_degree" class="form-label">Degree / Diploma</label>
        <select name="secondary_degree" id="" class="form-control">
            <option value="">Select degree / diploma</option>
            <option value="btech"
                {{ !empty($astrologer['primary_degree']) && $astrologer['primary_degree'] == 'btech' ? 'selected' : '' }}>
                B.tech</option>
            <option value="bsc"
                {{ !empty($astrologer['secondary_degree']) && $astrologer['secondary_degree'] == 'bsc' ? 'selected' : '' }}>
                B.Sc</option>
            <option value="ba"
                {{ !empty($astrologer['secondary_degree']) && $astrologer['secondary_degree'] == 'ba' ? 'selected' : '' }}>B.A
            </option>
            <option value="be"
                {{ !empty($astrologer['secondary_degree']) && $astrologer['secondary_degree'] == 'be' ? 'selected' : '' }}>B.E
            </option>
            <option value="bcom"
                {{ !empty($astrologer['secondary_degree']) && $astrologer['secondary_degree'] == 'bcom' ? 'selected' : '' }}>
                B.com</option>
            <option value="bpharma"
                {{ !empty($astrologer['secondary_degree']) && $astrologer['secondary_degree'] == 'bpharma' ? 'selected' : '' }}>
                B.Pharma</option>
            <option value="mtech"
                {{ !empty($astrologer['secondary_degree']) && $astrologer['secondary_degree'] == 'mtech' ? 'selected' : '' }}>
                M.tech</option>
            <option value="ma"
                {{ !empty($astrologer['secondary_degree']) && $astrologer['secondary_degree'] == 'ma' ? 'selected' : '' }}>M.A
            </option>
            <option value="msc"
                {{ !empty($astrologer['secondary_degree']) && $astrologer['secondary_degree'] == 'msc' ? 'selected' : '' }}>
                M.Sc</option>
            <option value="mbbs"
                {{ !empty($astrologer['secondary_degree']) && $astrologer['secondary_degree'] == 'mbbs' ? 'selected' : '' }}>
                MBBS</option>
            <option value="other"
                {{ !empty($astrologer['secondary_degree']) && $astrologer['secondary_degree'] == 'other' ? 'selected' : '' }}>
                other</option>
        </select>
    </div> --}}

    <div class="form-group col-md-6">
        <label for="college" class="form-label">College/School/University</label>
        <input type="text" name="college" class="form-control" placeholder="Enter your College/School/University"
            value="{{ $astrologer['college'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="onboard_you" class="form-label">Why do you think we should
            onboard you?</label>
        <input type="text" name="onboard_you" class="form-control" placeholder="Why we should on board you"
            value="{{ $astrologer['onboard_you'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="interview_time" class="form-label">What is suitable time
            for interview?</label>
        <input type="text" name="interview_time" class="form-control" placeholder="Enter suitable time for interview"
            value="{{ $astrologer['interview_time'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="business_source" class="form-label">Main Source of
            business</label>
        <select name="business_source" id="" class="form-control">
            <option value="">Select your business source</option>
            <option value="own business" {{ $astrologer['business_source'] == 'own business' ? 'selected' : '' }}>Own
                Business</option>
            <option value="private job" {{ $astrologer['business_source'] == 'private job' ? 'selected' : '' }}>Private Job
            </option>
            <option value="goverment job" {{ $astrologer['business_source'] == 'goverment job' ? 'selected' : '' }}>Goverment
                Job</option>
            <option value="studying in college"
                {{ $astrologer['business_source'] == 'studying in college' ? 'selected' : '' }}>Studying in College</option>
            <option value="none of the above" {{ $astrologer['business_source'] == 'none of the above' ? 'selected' : '' }}>
                None of the above</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="learn_primary_skill" class="form-label">From where did you
            learn your primary skill?</label>
        <input type="text" name="learn_primary_skill" class="form-control" placeholder="From where did you learn"
            value="{{ $astrologer['learn_primary_skill'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="instagram" class="form-label">Instagram profile
            link</label>
        <input type="url" name="instagram" class="form-control"
            placeholder="Please let us know your Instagram profile" value="{{ $astrologer['instagram'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="facebook" class="form-label">Facebook profile link</label>
        <input type="url" name="facebook" class="form-control"
            placeholder="Please let us know your Facebook profile" value="{{ $astrologer['facebook'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="linkedin" class="form-label">Linkedin profile link</label>
        <input type="url" name="linkedin" class="form-control"
            placeholder="Please let us know your Linkedin profile" value="{{ $astrologer['linkedin'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="youtube" class="form-label">Youtube profile link</label>
        <input type="url" name="youtube" class="form-control"
            placeholder="Please let us know your Youtube profile" value="{{ $astrologer['youtube'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="website" class="form-label">Website profile link</label>
        <input type="url" name="website" class="form-control"
            placeholder="Please let us know your Website profile" value="{{ $astrologer['website'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="min_earning" class="form-label">Minimum Earning Expection
            from Mahakal</label>
        <input type="text" name="min_earning" class="form-control" placeholder="Minimum Earning"
            value="{{ $astrologer['min_earning'] }}">
    </div>
    <div class="form-group col-md-6">
        <label for="max_earning" class="form-label">Maximum Earning Expection
            from Mahakal</label>
        <input type="text" name="max_earning" class="form-control" placeholder="Maximum Earning"
            value="{{ $astrologer['max_earning'] }}">
    </div>
    {{-- <div class="form-group col-md-6">
        <label for="bank_name" class="form-label">Bank Name</label>
        <input type="text" name="bank_name" class="form-control" placeholder="Enter bank name" value="{{ $astrologer['bank_name'] }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="holder_name" class="form-label">Accound Holder Name</label>
        <input type="text" name="holder_name" class="form-control" placeholder="Enter account holder name" value="{{ $astrologer['holder_name'] }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="branch_name" class="form-label">Branch Name</label>
        <input type="text" name="branch_name" class="form-control" placeholder="Enter branch name" value="{{ $astrologer['branch_name'] }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="bank_ifsc" class="form-label">Bank IFSC</label>
        <input type="text" name="bank_ifsc" class="form-control" placeholder="Enter IFSC code" value="{{ $astrologer['bank_ifsc'] }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="account_no" class="form-label">Bank Account No.</label>
        <input type="number" name="account_no" class="form-control" placeholder="Enter account no" value="{{ $astrologer['account_no'] }}" required>
    </div> --}}
    <div class="form-group col-md-6">
        <label for="foreign_country" class="form-label">Number of the foreign
            countries you lived/travelled to?</label>
        <select name="foreign_country" id="" class="form-control">
            <option value="0" {{ $astrologer['foreign_country'] == '0' ? 'selected' : '' }}>0</option>
            <option value="1-2" {{ $astrologer['foreign_country'] == '1-2' ? 'selected' : '' }}>1-2</option>
            <option value="3-5" {{ $astrologer['foreign_country'] == '3-5' ? 'selected' : '' }}>3-5</option>
            <option value="6+" {{ $astrologer['foreign_country'] == '6+' ? 'selected' : '' }}>6+</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="working" class="form-label">Are you currently working a
            fulltime job?</label>
        <select name="working" id="" class="form-control">
            <option value="">Select your working</option>
            <option value="no i am working as part timer or freelancer"
                {{ $astrologer['working'] == 'no i am working as part timer or freelancer' ? 'selected' : '' }}>No, I
                am working as part-timer or freelancer</option>
            <option value="yes i am working somewhere as a full timer"
                {{ $astrologer['working'] == 'yes i am working somewhere as a full timer' ? 'selected' : '' }}>Yes, I
                am working somewhere as a full-timer</option>
            <option value="no i am not working anywhere else"
                {{ $astrologer['working'] == 'no i am not working anywhere else' ? 'selected' : '' }}>No, I am not
                working anywhere else</option>
            <option value="i own a business" {{ $astrologer['working'] == 'i own a business' ? 'selected' : '' }}>I own a
                business</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="bio" class="form-label">Your Bio</label>
        <textarea name="bio" id="" rows="2" maxlength="200" class="form-control"
            placeholder="Describe Bio" >{{ $astrologer['bio'] }}</textarea>
    </div>
    <div class="form-group col-md-6">
        <label for="qualities" class="form-label">What are some good qualities
            of perfect influencer?</label>
        <textarea name="qualities" id="" rows="2" maxlength="200" class="form-control"
            placeholder="Describe Here" >{{ $astrologer['qualities'] }}</textarea>
    </div>
    <div class="form-group col-md-6">
        <label for="challenge" class="form-label">What was the biggest
            challenge faced and how did you overcome it?</label>
        <textarea name="challenge" id="" rows="2" maxlength="200" class="form-control"
            placeholder="Describe Here" >{{ $astrologer['challenge'] }}</textarea>
    </div>
    <div class="form-group col-md-6">
        <label for="repeat_question" class="form-label">A customer is asking
            the same question repeatedly: what will you do?</label>
        <textarea name="repeat_question" id="" rows="2" maxlength="200" class="form-control"
            placeholder="Describe Here" >{{ $astrologer['repeat_question'] }}</textarea>
    </div>
</div>
