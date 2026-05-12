@extends('layouts.front-end.app')
@section('title', translate('order_Track'))
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush
@section('content')
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.chadhava-details.chadhava-order-partial')
                <div class="card border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <h5 class="font-bold m-0 fs-16">Sankalp Details Updates</h5>
                            <div class="text-end d-none d-lg-block">
                              @if($order['is_edited'] == '0')
                                  <button class="btn btn-danger px-2 py-1" type="button" id="editButton">Edit Details</button>
                              @else
                                  <button class="btn btn-primary px-2 py-1" type="button" id="editButton">Show Details</button>
                              @endif
                            </div>
                        </div>
                        @if ($order)
                            <form class="needs-validation" id="sankalp_check" action="{{ route('chadhavasanklpUpdate', $order['order_id']) }}" method="post"
                               >
                                @csrf
                                <input type="hidden" name="orer_id" value="{{ $order['order_id'] }}">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('phone_number') }}
                                              <small class="text-primary">( *{{ translate('country_code_is_must_like_for_IND') }} 91)</small></label>
                                              <input class="form-control text-align-direction phone-input-with-country-picker"
                                              type="tel"  value="{{ isset($order['customers']['phone']) ? $order['leads']['person_phone'] : '' }}"  required readonly
                                              inputmode="numeric"   maxlength="10"   minlength="10">
                                            <input type="hidden" class="country-picker-phone-number w-50"
                                                name="newphone" readonly>
                                        </div>
                                    </div>
                                    
                                </div>
                                <hr class="my-2">
                                <h2 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">Name of member
                                    participating in Puja</h2>
                                <span>Panditji will take these names along with gotra during the puja.</span>
                                <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">Member Name</label>
                                                <input class="form-control text-align-direction" type="text"
                                                    name="members" value="{{ $order['members'] }}"
                                                    placeholder="Member Name" {{ $order['is_edited'] == '1' ? 'readonly' : '' }}  required>
                                            </div>
                                        </div>
                                        
                                </div>
                                <hr class="my-2">
                                <h2 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">Fill
                                    participant’s gotra</h2>
                                <span>Gotra will be recited during the puja.</span>
                                <div class="row mt-2">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('gotra') }}</label>
                                            <input class="form-control" type="text" name="gotra" id="GotraId"
                                                placeholder="{{ translate('gotra') }}" 
                                                value="{{ isset($order['gotra']) && $order['gotra'] != 'on' ? $order['gotra'] : 'Kashyap' }}"
                                                data-gotra="{{ isset($order['gotra']) && $order['gotra'] != 'on' ? $order['gotra'] : '' }}"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12">
                                    <div class="rtl">
                                        <label class="custom-control custom-checkbox m-0 d-flex">
                                            <input type="checkbox" class="custom-control-input" id="gotraCheck"
                                                {{ !isset($order['gotra']) || $order['gotra'] == 'on' ? 'checked' : '' }}>
                                            <span class="custom-control-label"><span>I do not know my gotra</span></span>
                                        </label>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                      <label class="form-label font-semibold">Chadava Reason</label>
                                      <textarea class="form-control text-align-direction" name="reason" placeholder="Enter Reason" {{ $order['is_edited'] == '1' ? 'readonly' : '' }} required>{{ $order['reason'] }}</textarea>

                                    </div>
                                  </div>
                                @if($order['is_edited'] == '0')
                                <div class="web-direction">
                                  <div class="mx-auto mt-4 __max-w-356">
                                      <button class="w-100 btn btn--primary" id="" type="submit">{{ translate('Update_Details') }}
                                      </button>
                                  </div>
                                </div>
                              
                                @endif
                            </form>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#NewNumberAdd").change(function() {
                if ($(this).is(":checked")) {
                    $("#newPhoneAdd").show();
                    $("#newPhoneAdd input[name='newPhone']").prop("required", true);
                } else {
                    $("#newPhoneAdd").hide();
                    $("#newPhoneAdd input[name='newPhone']").prop("required", false);
                }
            });
            // Check the Gutra 
           // Check the Gutra 
            $(document).ready(function () {
                $("#gotraCheck").change(function () {
                    if ($(this).is(":checked")) {
                        $("#GotraId").prop("readonly", true).val("Kashyapa");
                    } else {
                        let gotraValue = $(this).data("gotra"); // Retrieve the stored gotra value
                        $("#GotraId").prop("readonly", false).val(gotraValue);
                    }
                });

                // Ensure the input field is set correctly on page load
                if ($("#gotraCheck").is(":checked")) {
                    $("#GotraId").prop("readonly", true).val("Kashyapa");
                }
            });

            // add the condition button YES ANd NO
            $(".hideable-div").toggle($('#is_prashad').val() == 1); // Show/hide based on database value

            $("button.yes-btn").click(function () {
                $('#is_prashad').val(1); // Update hidden input value
                $(".hideable-div").show();
                $(this).css("background-color", "orange");
                $("button.no-btn").css("background-color", "");
                updateDatabase(1); // Update database dynamically
            });

            $("button.no-btn").click(function () {
                $('#is_prashad').val(0); // Update hidden input value
                $(".hideable-div").hide();
                $(this).css("background-color", "orange");
                $("button.yes-btn").css("background-color", "");
                updateDatabase(0); // Update database dynamically
            });


        });
    </script>
    <script>
        $(document).ready(function() {
            $('#editButton').click(function() {
                $('#sankalp_check').toggle();
            });
        });
    </script>
@endpush
