"use strict";

function initializePhoneInput(selector, outputSelector) {
    const phoneInput = document.querySelector(selector);
    const phoneNumber = phoneInput.value;
    const countryCodeMatch = phoneNumber.replace(/[^0-9]/g, '');
    const initialCountry = countryCodeMatch ? `+${countryCodeMatch}` : $('.system-default-country-code').data('value').toLowerCase();

    let phoneInputInit = window.intlTelInput(phoneInput, {
        initialCountry: initialCountry.toLowerCase(),
        showSelectedDialCode: true,
    });
    if (!phoneInputInit.selectedCountryData.dialCode ){
        phoneInputInit.destroy();
        phoneInputInit = window.intlTelInput(phoneInput, {
            initialCountry: $('.system-default-country-code').data('value').toLowerCase(),
            showSelectedDialCode: true,
        })
    }
    $(outputSelector).val('+' + phoneInputInit.selectedCountryData.dialCode + phoneInput.value.replace(/[^0-9]/g, ''));

    $(".iti__country").on("click", function() {
        $(outputSelector).val('+' + $(this).data('dial-code') + phoneInput.value.replace(/[^0-9]/g, ''));
    });

    $(selector).on("keyup keypress change", function() {
        $(outputSelector).val('+' + phoneInputInit.selectedCountryData.dialCode + phoneInput.value.replace(/[^0-9]/g, ''));
        $(selector).val(phoneInput.value.replace(/[^0-9]/g, ''));
    });
}
