// $(document).ready(function() {
$(document).on('click', '.locker-date-btn', function () {
    $('.locker-date-btn').removeClass('btn-primary active').addClass('btn-outline-primary');
    $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
    $('#locker-date').val($(this).data('value'));

    $('#customer_locker_address').attr('tabindex', '0');
    $('input[name="payment_mode"][class="puja-payment-locker-mode"]').attr('tabindex', '0');
    $('.submit-button-class-locker').attr('tabindex', '0');
});

// ✅ Devotee logic

function toggleAddButton() {
    const name = $('#customer_locker_name').val().trim();
    const mobile = $('#customer_locker_mobile').val().trim();
    const aadhaar = $('#customer_locker_aadhaar').val().trim();
    const address = $('#customer_locker_address').val().trim();

    const validMobile = /^[6-9]\d{9}$/.test(mobile);
    const validAadhaar = /^\d{12}$/.test(aadhaar);
    const allValid = name && validMobile && validAadhaar && address;
}

$('#customer_locker_name, #customer_locker_mobile, #customer_locker_aadhaar, #customer_locker_address').on('input', toggleAddButton);

function updateDevoteeNum() {
    $('#devoteeLockerAcddsWrapper .devotee-lockertems').each(function (index) {
        $(this).find('.devotee-locker').text('Devotee ' + (index + 1));
    });
    var countlocker = $('#devoteeLockerAcddsWrapper .devotee-lockertems').length;
    $('#show_locker_devotees_qty').text(countlocker);
    $('#customer_locker_qty').val(countlocker);
}

// ✅ Amount calculation

function calculatelockerAmount() {
    let selected = $('#locker_package_id').find(':selected');
    // if (!selected.length) return;

    let base = parseFloat(selected.data('base')) || 0;
    let gst = parseFloat(selected.data('gst')) || 0;
    let platform = parseFloat(selected.data('platform')) || 0;
    let receipt = parseFloat(selected.data('receipt')) || 0;
    let devoteecountlocker = $('#devoteeLockerAcddsWrapper .devotee-lockertems').length || 1;

    let gstAmount = (base * gst / 100);
    let perDevotee = base + gstAmount + platform + receipt;
    let total = perDevotee * devoteecountlocker;

    $('#show_locker_base_price').text(base.toFixed(2));
    $('#show_locker_platform_price').text(platform.toFixed(2));
    $('#show_locker_receipt_price').text(receipt.toFixed(2));
    $('#show_locker_gst_per').text(gst);
    $('#show_locker_devotees_qty').text(devoteecountlocker);
    $('#show_locker_per_person_qty').text(perDevotee.toFixed(2));

    var freeOptionLocker = document.querySelector('.payment-option-locker-free');
    var freeRadioLocker = freeOptionLocker ? freeOptionLocker.querySelector('input[type="radio"]') : null;
    var cashOptionLocker = document.querySelector('.payment-option-locker-cash');
    var cashRadioLocker = cashOptionLocker ? cashOptionLocker.querySelector('input[type="radio"]') : null;
    var onlineOptionLocker = document.querySelector('.payment-option-locker-online');
    var onlineRadioLocker = onlineOptionLocker ? onlineOptionLocker.querySelector('input[type="radio"]') : null;

    var onlineOptionLocker1 = document.querySelector('.payment-option-locker-online1');
    var onlineRadioLocker1 = onlineOptionLocker1 ? onlineOptionLocker1.querySelector('input[type="radio"]') : null;

    if (1 > total) {
        onlineRadioLocker.checked = false;
        onlineRadioLocker1.checked = false;
        cashRadioLocker.checked = false;
        freeRadioLocker.checked = true;
        freeOptionLocker.classList.remove('d-none');
        cashOptionLocker.classList.add('d-none');
        onlineOptionLocker.classList.add('d-none');
        onlineOptionLocker1.classList.add('d-none');
    } else {
        onlineRadioLocker.checked = false;
        onlineRadioLocker1.checked = false;
        cashRadioLocker.checked = true;
        freeRadioLocker.checked = false;
        freeOptionLocker.classList.add('d-none');
        cashOptionLocker.classList.remove('d-none');
        onlineOptionLocker.classList.remove('d-none');
        onlineOptionLocker1.classList.remove('d-none');
    }
    $('#show_locker_total_amount').text(total.toFixed(2));

    $('#base_locker_price').val(base.toFixed(2));
    $('#receipt_locker_amount').val((receipt * devoteecountlocker).toFixed(2));
    $('#total_locker_amount').val(total.toFixed(2));
    $('#customer_locker_qty').val(devoteecountlocker);
}

// Trigger recalculation on package change
$(document).on('change', '#locker_package_id', calculatelockerAmount);

// Initial
// calculatelockerAmount();
// });

if (typeof payId === 'undefined') {
    let payId = '';
}

function paymantLockerNow() {
    let isValid = true;
    $('#lockerForm').find('[required]').each(function () {
        if (typeof $(this).val === 'function') {
            let value = $(this).val();
            if (!value || !value.trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        } else { }
    });
    if (!isValid) {
        toastr.error('Please fill all required fields.');
        return false;
    }
    let formData = new FormData($('#lockerForm')[0]);
    $('.locker-form-submit').prop('disabled', true);
    $("#payment-locker-details-Success").addClass('d-none');
    $.ajax({
        url: $('.form-url-new-link').data('url'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
            console.log('Submitting...');
        },
        success: function (response) {
            $('.locker-form-submit').prop('disabled', false);
            if (response.status) {
                if ($('.puja-payment-locker-mode:checked').val() === 'online' || $('.puja-payment-locker-mode:checked').val() === 'qr_code') {
                    payId = response.paymentId;
                    $('.online-qr-code-locker-show').html(response.imageData);
                } else {
                    $('.online-qr-code-locker-show').html('');
                    $('#orderIdInput').val(response.data.order_id ?? '');
                    $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type','other');;
                    $('.get_receipt_info').click();
                    document.getElementById('lockerForm').reset();
                    $('.btn locker-date-btn').removeClass('btn-primary');
                    $('.btn locker-date-btn').addClass('btn-outline-primary');
                }
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr) {
            $('.locker-form-submit').prop('disabled', false);
            console.error(xhr.responseText);
            toastr.error('❌ Something went wrong. Please try again.');
        }
    });
}

setInterval(() => {
    let qrDiv = document.querySelector('.online-qr-code-locker-show');
    if (qrDiv) {
        if (qrDiv.innerHTML.trim() === "") {

        } else {
            $.ajax({
                url: $('.get-payment-check-status-url').data('url'),
                data: {
                    id: payId,
                    _token: $('meta[name="_token"]').attr('content'),
                },
                dataType: "json",
                type: "post",
                success: function (data) {
                    if (data.is_paid == 1) {
                        toastr.success("Payment Successfully Received!", "Success");
                        $('.online-qr-code-locker-show').html('');
                        $('#payment-locker-details-Success').removeClass('d-none');
                        $('#paymant-locker-id').text(data.data.transaction_id ?? 'N/A');
                        $('#paymant-locker-amount').text(data.data.payment_amount ?? '0');
                        $('#orderIdInput').val(data.data.order_id ?? '');
                        $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type','other');;
                        $('.get_receipt_info').click();
                        payId = '';
                        document.getElementById('lockerForm').reset();
                        $('.btn locker-date-btn').removeClass('btn-primary');
                        $('.btn locker-date-btn').addClass('btn-outline-primary');
                    } else if (data.is_paid == 2) {
                        $('#orderIdInput').val('');
                        toastr.error("Payment Not Successfully Received!", "Error");
                        $('.online-qr-code-locker-show').html('');
                        $('#payment-locker-details-Success').removeClass('d-none');
                        $('#payment-locker-details-Success').addClass('d-none');

                    }
                }

            });
        }
    } else {
        $('#payment-locker-details-Success').removeClass('d-none');
        $('#payment-locker-details-Success').addClass('d-none');
        $('#paymant-locker-id').text('');
        $('#paymant-locker-amount').text('0');
    }
}, 10000);



$(document).on('keydown', '#customer_locker_address', function (e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        $(this).blur();
        const $checkedRadio = $('input[name="payment_mode"][class="puja-payment-locker-mode"]:checked');
        const $radioToFocus = $checkedRadio.length > 0 ? $checkedRadio : $('input[name="payment_mode"][class="puja-payment-locker-mode"]').first();
        $radioToFocus.focus();
        $('.suggestion_lists').hide();
        $('.suggestion_lists').text('');
        return false;
    }
});

$(document).on('keydown', 'input[name="payment_mode"][class="puja-payment-locker-mode"]', function (e) {
    const radios = $('input[name="payment_mode"][class="puja-payment-locker-mode"]');
    const currentIndex = radios.index(this);

    switch (e.key) {
        case 'ArrowRight':
            e.preventDefault();
            if (currentIndex < radios.length - 1) {
                radios.eq(currentIndex + 1).focus().prop('checked', true);
            }
            break;

        case 'ArrowLeft':
            e.preventDefault();
            if (currentIndex > 0) {
                radios.eq(currentIndex - 1).focus().prop('checked', true);
            }
            break;

        case 'Tab':
            e.preventDefault();
            if (!e.shiftKey) {
                // Tab forward to submit button
                $('.submit-button-class-locker').focus();
            } else {
                // Shift+Tab goes back to count input
                // $('#customer_locker_name').focus();
            }
            break;
        case 'Backspace':
            e.preventDefault();
            $('#customer_locker_name').focus();
            $('#customer_locker_name').select();
            break;
        case ' ':
        case 'Spacebar':
            e.preventDefault();
            $(this).prop('checked', true);
            break;
    }
});
$(document).on('keydown', '.submit-button-class-locker', function (e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        if (!e.shiftKey) {
            $('#customer_locker_name').focus();
        } else {
            $('input[name="payment_mode"][class="puja-payment-locker-mode"]:checked').focus();
        }
    } else if (e.key === 'Enter') {
        e.preventDefault();
        $(this).click();
    }
});

$(document).on('keypress', '.submit-button-class-locker', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        $(this).click();
    }
});