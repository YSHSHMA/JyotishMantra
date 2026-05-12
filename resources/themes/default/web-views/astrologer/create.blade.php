@extends('layouts.front-end.app')

@section('title', translate('register_with_us'))

@push('css_or_js')
    <meta property="og:image"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
    <meta property="og:title" content="Terms & conditions of {{ $web_config['name']->value }} " />
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <meta property="twitter:card"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
    <meta property="twitter:title" content="Terms & conditions of {{ $web_config['name']->value }}" />
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!--poojafilter-css-->
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
    <link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .error {
            color: red !important;
        }

        #language\[\]-error {
            position: relative;
            top: 5.1em;
            right: 5em;
        }

        #category\[\]-error {
            position: relative;
            top: 5.1em;
            right: 5em;
        }

        .select2-selection {
            height: 45px;
        }

        .select2-container--default .select2-search--inline .select2-search__field {
            height: 20px;
        }

        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    {{-- main page --}}
    <section class="cal_about_wrapper as_padderTop60 as_padderBottom60">
        <div class="container py-2 __inline-7 text-align-direction">
            <div class="login-card">
                <div class="mx-auto __max-w-760">
                    <h2 id="heading" class="text-center h4 mb-4 font-bold text-capitalize fs-18-mobile">Register with us</h2>

                    {{-- personal detail --}}
                    <div id="personal-detail">
                        <form id="personal-form" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">Name</label> <span
                                                class="text-danger">*</span>
                                            <input class="form-control text-align-direction" value="" type="text"
                                                name="name" placeholder="Ex: Jhone" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">Email address</label> <span
                                                class="text-danger">*</span>
                                            <input class="form-control text-align-direction" type="email" name="email"
                                                id="email" placeholder="Enter email address" autocomplete="off"
                                                required>
                                            <p class="text-danger" id="email-validate" style="display: none;">Email already
                                                registered</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">Mobile Number</label> <span
                                                class="text-danger">*</span>
                                            <input type="number" class="form-control text-align-direction" name="mobile_no"
                                                id="mobile-no" placeholder="Enter phone number" required
                                                oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
                                            <p class="text-danger" id="mobile-no-validate" style="display: none;">Mobile no
                                                already register</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">Gender</label> <span
                                                class="text-danger">*</span>
                                            <select name="gender" class="form-control">
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-center col-md-8 offset-md-2 my-3" style="height: 235px;">
                                        <img class="upload-img-view" id="viewer"
                                            src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}"
                                            alt="" style="height: 100%">
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="image" class="title-color">
                                            {{ translate('profile_Image') }}
                                        </label> <span class="text-danger">*</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image"
                                                class="custom-file-input image-preview-before-upload" data-preview="#viewer"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                            <label class="custom-file-label">
                                                {{ translate('choose_file') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Password </label> <span
                                            class="text-danger">*</span>
                                        <div class="password-toggle rtl">
                                            <input class="form-control text-align-direction" name="password"
                                                id="password" type="password" minlength="6"
                                                placeholder="Minimum 6 characters long" required>
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox"><i
                                                    class="tio-hidden password-toggle-indicator"></i><span
                                                    class="sr-only">Show
                                                    password </span>
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Confirm password</label> <span
                                            class="text-danger">*</span>
                                        <div class="password-toggle rtl">
                                            <input class="form-control text-align-direction" name="confirm_password"
                                                id="confirm_password" type="password"
                                                placeholder="Minimum 6 characters long" required>
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input text-align-direction" type="checkbox">
                                                <i class="tio-hidden password-toggle-indicator"></i>
                                                <span class="sr-only">Show password</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">City</label> <span
                                            class="text-danger">*</span>
                                        <input class="form-control text-align-direction" name="city"
                                            placeholder="Enter city" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Date of Birth</label> <span
                                            class="text-danger">*</span>
                                        <input type="date" class="form-control text-align-direction" name="dob"
                                            required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Current Address</label> <span
                                            class="text-danger">*</span>
                                        <textarea name="address" class="form-control text-align-direction" rows="3"
                                            placeholder="Enter current address"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="web-direction">
                                <div class="mx-auto mt-4 __max-w-356">
                                    <button class="w-100 btn btn--primary" id="personal-btn" type="submit">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- skill detail --}}
                    <div id="skill-detail" style="display: none">
                        <form id="skill-form">
                            @csrf
                            <input type="hidden" name="astro_id" class="astro-id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Primary Skill</label> <span
                                            class="text-danger">*</span>
                                        <select name="primary_skills" id="primary-skill" class="form-control">
                                            @foreach ($skills as $skill)
                                                <option value="{{ $skill['id'] }}">{{ $skill['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Other Skills (if You have any)</label>
                                        <select name="other_skills[]" id="other-skill" class="form-control multi-select"
                                            multiple>
                                            <option value="" disabled>Select Other Skills</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Category</label> <span
                                            class="text-danger">*</span>
                                        <select name="category[]" class="form-control multi-select" multiple required>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category['id'] }}">{{ $category['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Language</label> <span
                                            class="text-danger">*</span>
                                        <select name="language[]" class="form-control multi-select" multiple required>
                                            <option value="hi">Hindi</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Experience In Years</label> <span
                                            class="text-danger">*</span>
                                        <input class="form-control text-align-direction" name="experience"
                                            placeholder="Enter your experience" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">How many hours you can contribute
                                            daily?</label> <span class="text-danger">*</span>
                                        <input class="form-control text-align-direction" name="daily_hours_contribution"
                                            placeholder="Enter daily contribution" required>
                                    </div>
                                </div>
                            </div>
                            <div class="web-direction">
                                <div class="mx-auto mt-4 __max-w-356">
                                    <button class="w-100 btn btn--primary" id="skill-btn" type="submit">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- other detail --}}
                    <div id="other-detail" style="display: none">
                        <form id="other-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="astro_id" class="astro-id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Qualification</label>
                                        <select name="primary_qualification" class="form-control">
                                            <option value="">Select qualification</option>
                                            <option value="diploma">Diploma</option>
                                            <option value="10th">10th</option>
                                            <option value="12th">12th</option>
                                            <option value="graduate">Graduate</option>
                                            <option value="post graduate">Post Graduate</option>
                                            <option value="phd">PHD</option>
                                            <option value="others">Others</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Degree / Diploma</label>
                                        <select name="primary_degree" class="form-control">
                                            <option value="">Select degree / diploma</option>
                                            <option value="btech">B.tech</option>
                                            <option value="bsc">B.Sc</option>
                                            <option value="ba">B.A</option>
                                            <option value="be">B.E</option>
                                            <option value="bcom">B.com</option>
                                            <option value="bpharma">B.Pharma</option>
                                            <option value="mtech">M.tech</option>
                                            <option value="ma">M.A</option>
                                            <option value="msc">M.Sc</option>
                                            <option value="mbbs">MBBS</option>
                                            <option value="other">other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">From where did you learn your primary
                                            skill?</label> <span class="text-danger">*</span>
                                        <input class="form-control text-align-direction" name="learn_primary_skill"
                                            placeholder="Enter where you learn" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">What is suitable time for
                                            interview?</label> <span class="text-danger">*</span>
                                        <input class="form-control text-align-direction" name="interview_time"
                                            placeholder="Enter interview time" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Aadharcard</label> <span
                                            class="text-danger">*</span>
                                        <input type="number" name="adharcard" class="form-control text-align-direction"
                                            placeholder="Enter aadharcard number"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);"
                                            required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="text-center mt-3" style="height: 235px;">
                                                <img class="upload-img-view" id="aadhar-front-viewer"
                                                    src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}"
                                                    alt="" style="height: 100%">
                                            </div>
                                            <div class="form-group mt-3 text-center">
                                                <label for="adhar_front_image" class="title-color">
                                                    {{ translate('aadhar_Front_Image') }}
                                                </label> <span class="text-danger">*</span>
                                                <div class="custom-file text-left">
                                                    <input type="file" name="adhar_front_image"
                                                        class="custom-file-input image-preview-before-upload"
                                                        data-preview="#aadhar-front-viewer"
                                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                        required>
                                                    <label class="custom-file-label">
                                                        {{ translate('choose_file') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-center mt-3" style="height: 235px;">
                                                <img class="upload-img-view" id="aadhar-back-viewer"
                                                    src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}"
                                                    alt="" style="height: 100%">
                                            </div>
                                            <div class="form-group mt-3 text-center">
                                                <label for="image" class="title-color">
                                                    {{ translate('aadhar_Back_Image') }}
                                                </label>
                                                <div class="custom-file text-left">
                                                    <input type="file" name="adhar_back_image"
                                                        class="custom-file-input image-preview-before-upload"
                                                        data-preview="#aadhar-back-viewer"
                                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                    <label class="custom-file-label">
                                                        {{ translate('choose_file') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pancard font-semibold" class="form-label">Pancard</label> <span
                                            class="text-danger">*</span>
                                        <input type="text" name="pancard" class="form-control text-align-direction"
                                            placeholder="Enter pancard number" id="pancard" required>
                                        <p class="text-danger" id="pancard-validate" style="display: none;">Pancard is
                                            invalid</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="text-center mt-3" style="height: 235px;">
                                                <img class="upload-img-view" id="pancard-viewer"
                                                    src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}"
                                                    alt="" style="height: 100%">
                                            </div>
                                            <div class="form-group mt-3 text-center">
                                                <label for="image" class="title-color">
                                                    {{ translate('pancard_Image') }}
                                                </label> <span class="text-danger">*</span>
                                                <div class="custom-file text-left">
                                                    <input type="file" name="pancard_image"
                                                        class="custom-file-input image-preview-before-upload"
                                                        data-preview="#pancard-viewer"
                                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                        required>
                                                    <label class="custom-file-label" for="astrologer-image">
                                                        {{ translate('choose_file') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="web-direction">
                                <div class="mx-auto mt-4 __max-w-356">
                                    <button class="w-100 btn btn--primary" id="other-btn" type="submit">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- thank you message --}}
                    <div id="thankyou-detail" style="display: none">
                        <div class="col-md-10 col-lg-10">
                            <div class="mb-3 text-center">
                                <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                            </div>

                            <h6 class="font-black fw-bold text-center">
                                {{ translate('registered_Successfully') }}!
                            </h6>

                            <p class="text-center fs-12">
                                {{ translate('we_will_get_in_touch_with_you_soon') }}
                            </p>
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <a href="{{ route('home') }}" class="btn text-center" style="background-color: #fe9802">
                                        {{ translate('Home') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('script')
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <!-- <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script> -->
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/api.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>



    {{-- multi select --}}
    <script>
        $('.multi-select').select2({
            placeholder: 'Select an option'
        });
    </script>

    {{-- primary skill change --}}
    <script>
        otherSkill();

        $('#primary-skill').change(function(e) {
            e.preventDefault();

            otherSkill();
        });

        function otherSkill() {
            var skillId = $('#primary-skill').val();
            var skills = {!! json_encode($skills) !!};
            var options = "";

            $('#other-skill').html('');
            $.each(skills, function(key, value) {
                if (value.id != skillId) {
                    options += `<option value="${value.id}">${value.name}</option>`;
                }
            });
            $('#other-skill').append(options);
        }
    </script>

    {{-- pancard validation --}}
    <script>
        function validatePAN(pan) {
            const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
            return panRegex.test(pan);
        }

        $('#pancard').blur(function(e) {
            e.preventDefault();

            var pancard = $(this).val();
            if (validatePAN(pancard)) {
                $('#pancard-validate').hide();
                $('#other-btn').prop('disabled', false);
            } else {
                $('#pancard-validate').show();
                $('#other-btn').prop('disabled', true);
            }

        });
    </script>

    {{-- check email --}}
    <script>
        $('#email').blur(function(e) {
            e.preventDefault();

            var email = $(this).val();
            $.ajax({
                type: "get",
                url: "{{ url('astrologers/check/email') }}" + '/' + email,
                success: function(response) {
                    if (response.status == 200) {
                        $('#email-validate').show();
                        $('#personal-btn').prop('disabled', true);
                    } else {
                        $('#email-validate').hide();
                        $('#personal-btn').prop('disabled', false);
                    }
                }
            });
        });
    </script>

    {{-- check mobile no --}}
    <script>
        $('#mobile-no').blur(function(e) {
            e.preventDefault();

            var mobileno = $(this).val();
            $.ajax({
                type: "get",
                url: "{{ url('astrologers/check/mobileno') }}" + '/' + mobileno,
                success: function(response) {
                    if (response.status == 200) {
                        $('#mobile-no-validate').show();
                        $('#personal-btn').prop('disabled', true);
                    } else {
                        $('#mobile-no-validate').hide();
                        $('#personal-btn').prop('disabled', false);
                    }
                }
            });
        });
    </script>

    {{-- save personal detail --}}
    <script>
        $(document).ready(function() {
            $("#personal-form").validate({
                rules: {
                    name: {
                        required: true
                    },
                    email: {
                        required: true
                    },
                    mobile_no: {
                        required: true
                    },
                    image: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    },
                    confirm_password: {
                        required: true,
                        minlength: 6,
                        equalTo: "#password"
                    },
                    city: {
                        required: true
                    },
                    dob: {
                        required: true
                    },
                    address: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "Please enter name",
                    },
                    email: {
                        required: "Please enter email",
                    },
                    mobile_no: {
                        required: "Please enter mobile no",
                    },
                    image: {
                        required: "Please select an image",
                    },
                    password: {
                        required: "Please enter a password",
                        minlength: "Password must be at least 6 characters long"
                    },
                    confirm_password: {
                        required: "Please confirm your password",
                        equalTo: "Passwords do not match"
                    },
                    city: {
                        required: "Please enter city",
                    },
                    dob: {
                        required: "Please enter dob",
                    },
                    address: {
                        required: "Please enter address",
                    }
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);

                    $.ajax({
                        type: "POST",
                        url: "{{ url('register-personal-detail') }}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#personal-detail').hide();
                                $('#skill-detail').show();
                                $('.astro-id').val(response.id);
                                form.reset();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('unable to process');
                        }
                    });
                }
            });
        });
    </script>

    {{-- save skil detail --}}
    <script>
        $(document).ready(function() {
            $("#skill-form").validate({
                rules: {
                    category: {
                        required: true
                    },
                    language: {
                        required: true
                    },
                    experience: {
                        required: true
                    },
                    daily_hours_contribution: {
                        required: true
                    }
                },
                messages: {
                    category: {
                        required: "Please enter category",
                    },
                    language: {
                        required: "Please enter language",
                    },
                    experience: {
                        required: "Please enter experience",
                    },
                    daily_hours_contribution: {
                        required: "Please enter daily hour contribution",
                    }
                },
                submitHandler: function(form) {
                    var formData = $(form).serialize();

                    $.ajax({
                        type: "POST",
                        url: "{{ url('register-skill-detail') }}",
                        data: formData,
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#skill-detail').hide();
                                $('#other-detail').show();
                                form.reset();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('unable to process');
                        }
                    });
                }
            });
        });
    </script>

    {{-- save skil detail --}}
    <script>
        $(document).ready(function() {
            $("#other-form").validate({
                rules: {
                    learn_primary_skill: {
                        required: true
                    },
                    interview_time: {
                        required: true
                    },
                    adharcard: {
                        required: true
                    },
                    adhar_front_image: {
                        required: true
                    },
                    pancard: {
                        required: true
                    },
                    pancard_image: {
                        required: true
                    }
                },
                messages: {
                    learn_primary_skill: {
                        required: "Please enter your learn skill",
                    },
                    interview_time: {
                        required: "Please enter interview time",
                    },
                    adharcard: {
                        required: "Please enter aadharcard number",
                    },
                    adhar_front_image: {
                        required: "Please select aadhar image",
                    },
                    pancard: {
                        required: "Please enter pancard number",
                    },
                    pancard_image: {
                        required: "Please select pancard image",
                    }
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);

                    $.ajax({
                        type: "POST",
                        url: "{{ url('register-other-detail') }}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#heading').hide();
                                $('#other-detail').hide();
                                $('#thankyou-detail').show();
                                form.reset();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('unable to process');
                        }
                    });
                }
            });
        });
    </script>
@endpush
