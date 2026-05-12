function appendHtmlPuja() {
    $('#package_id').trigger('change');
    if (document.querySelector('.phone-input-with-country-picker-0')) {
        initializePhoneInput(`.phone-input-with-country-picker-0`, `.country-picker-phone-number-0`);
        $('#countDevoteepuja').attr('tabindex', '0');
        $('input[name="payment_mode"][class="puja-payment-mode"]').attr('tabindex', '0');
        $('.submit-button-class-puja').attr('tabindex', '0');
    }
}

$(document).on('click', '.puja-date-btn', function () {
    $('.puja-date-btn').removeClass('btn-primary active').addClass('btn-outline-primary');
    $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
    $('#selected_puja_date').val($(this).data('value'));
});
$(document).on('change', '#package_id', function () {
    let selected = $(this).find(':selected');

    let base = parseFloat(selected.data('base')) || 0;
    let gst = parseFloat(selected.data('gst')) || 0;
    let platform = parseFloat(selected.data('platform')) || 0;
    let receipt = parseFloat(selected.data('receipt')) || 0;

    let total = base;
    total += (base * platform / 100);
    total += (base * receipt / 100);
    total += (base * gst / 100);

    $('#base_price_puja').val(base.toFixed(2));
    $('#receipt_amount_puja').val((base * receipt / 100).toFixed(2));
    $('#total_amount_puja').val(total.toFixed(2));
});

//  Trigger calculation once on page load (for default selected package)
// $(function() {

function toggleAddButton() {
    // const parent = $('#pujadevoteeWrapper');
    // const name = parent.find('#main_name').val().trim();
    // const mobile = parent.find('#main_mobile').val().trim();
    // const aadhaar = parent.find('#main_aadhaar').val().trim();
    // const address = parent.find('#main_address').val().trim();

    // const validMobile = /^[6-9]\d{9}$/.test(mobile);
    // const validAadhaar = /^\d{12}$/.test(aadhaar);

    // // const allValid = (name && validMobile && validAadhaar && address);
    // // $('#pujaaddDevotee').prop('disabled', !allValid);
    // if (aadhaar && !validAadhaar) {
    //     toastr.error('Please enter a valid 12-digit Aadhaar number 🪪');
    //     return;
    // }

    // if (!name || !validMobile || !validAadhaar || !address) {
    //     toastr.warning('Please fill Name, Mobile, Aadhaar, and Address before proceeding.');
    //     return;
    // }
    calculatepujaAmount();
    // toastr.success('All details are valid');
}

$('#pujadevoteeWrapper #main_name,#pujadevoteeWrapper #main_mobile,#pujadevoteeWrapper #main_aadhaar,#pujadevoteeWrapper #main_address').on('input', toggleAddButton);

const userEdited = new Set();

$(document).on('blur', '.puja-main-address', function () {
    const index = $('.puja-main-address').index(this);
    const value = $(this).val().trim();
    if (index === 0 && value !== '') {
        $('.puja-main-address').each(function (i) {
            if (i > 0 && !userEdited.has(i) && $(this).val().trim() === '') {
                $(this).val(value);
            }
        });
    } else {
        userEdited.add(index);
    }
});


// $('#pujaaddDevotee').on('click', function() {
//     addPujalebal();
// });

// Remove devotee
$(document).on('click', '.pujaremoveDevotee', function () {
    $(this).closest('.puja-devotee-item').fadeOut(200, function () {
        $(this).remove();
        updateDevoteeNumbers();
        calculatepujaAmount();
        darshancount = $('#pujadevoteeWrapper .puja-devotee-item').length;
        $('#countDevoteepuja').val(darshancount);
    });
});

function updateDevoteeNumbers() {
    $('#pujadevoteeWrapper .puja-devotee-item').each(function (index) {
        $(this).find('.devotee-heading').text('Devotee ' + (index + 1));
    });
    darshancount = $('#pujadevoteeWrapper .puja-devotee-item').length;
    $('#show_devotees_puja').text(darshancount);
}

// Calculate amount dynamically
function calculatepujaAmount() {
    let selected = $('#package_id').find(':selected');
    // if (!selected.length) return;
    // $('#pujadevoteeWrapper .puja-devotee-item').filter(function() {
    //     return !$(this).is(':visible');
    // }).remove();
    var base = parseFloat(selected.data('base')) || 0;
    var gst = parseFloat(selected.data('gst')) || 0;
    var platform = parseFloat(selected.data('platform')) || 0;
    var receipt = parseFloat(selected.data('receipt')) || 0;

    var devoteeCount = pujadavcount = $('#pujadevoteeWrapper .puja-devotee-item:visible').length || 1;


    var gstAmount = (base * gst / 100);
    var perDevotee = base + gstAmount + platform + receipt;
    var total = perDevotee * devoteeCount;
    // Update summary display
    $('#show_base_puja').text(base.toFixed(2));
    $('#show_platform_puja').text(platform.toFixed(2));
    $('#show_receipt_puja').text(receipt.toFixed(2));
    $('#show_gst_puja').text(gst);
    $('#show_devotees_puja').text(pujadavcount);
    $('#show_per_person_puja').text(perDevotee.toFixed(2));

    var freeOptionPuja = document.querySelector('.payment-option-puja-free');
    var freeRadioPuja = freeOptionPuja ? freeOptionPuja.querySelector('input[type="radio"]') : null;
    var cashOptionPuja = document.querySelector('.payment-option-puja-cash');
    var cashRadioPuja = cashOptionPuja ? cashOptionPuja.querySelector('input[type="radio"]') : null;
    var onlineOptionPuja = document.querySelector('.payment-option-puja-online');
    var onlineRadioPuja = onlineOptionPuja ? onlineOptionPuja.querySelector('input[type="radio"]') : null;

    var onlineOptionPuja1 = document.querySelector('.payment-option-puja-online1');
    var onlineRadioPuja1 = onlineOptionPuja1 ? onlineOptionPuja1.querySelector('input[type="radio"]') : null;
    if (1 > total) {
        onlineRadioPuja.checked = false;
        onlineRadioPuja1.checked = false;
        cashRadioPuja.checked = false;
        freeRadioPuja.checked = true;
        freeOptionPuja.classList.remove('d-none');
        cashOptionPuja.classList.add('d-none');
        onlineOptionPuja.classList.add('d-none');
        onlineOptionPuja1.classList.add('d-none');
    } else {
        onlineRadioPuja.checked = false;
        onlineRadioPuja1.checked = false;
        cashRadioPuja.checked = true;
        freeRadioPuja.checked = false;
        freeOptionPuja.classList.add('d-none');
        cashOptionPuja.classList.remove('d-none');
        onlineOptionPuja.classList.remove('d-none');
        onlineOptionPuja1.classList.remove('d-none');
    }

    $('#show_total_puja').text(total.toFixed(2));

    // Update hidden fields for backend
    $('#base_price_puja').val(base.toFixed(2));
    $('#receipt_amount_puja').val((receipt * devoteeCount).toFixed(2));
    $('#total_amount_puja').val(total.toFixed(2));
}

// Recalculate on package change or count change
$(document).on('change', '#package_id', calculatepujaAmount);
// calculatepujaAmount();
// });

$(document).on('keyup', '#countDevoteepuja', function () {
    setCountDevoteePuja();
});
// setCountDevoteePuja();

function setCountDevoteePuja() {
    $('.puja-devotee-item:not(:first)').each(function () {
        const item = $(this);
        item.addClass('removing');
        setTimeout(() => {
            item.remove();
            updateDevoteeNumbers();
            calculatepujaAmount();
        }, 10);
    });

    for (let i = 0; i < (Number($('#countDevoteepuja').val()) - 1); i++) {
        addPujalebal(i + 1);
    }
    updateDevoteeNumbers();
    calculatepujaAmount();
}

function addPujalebal(count) {
    let newItem = `
            <div class="puja-devotee-item border rounded p-3 mb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-12 mb-2 d-flex justify-content-between">
                        <h6 class="fw-bold text-primary devotee-heading">Devotee ${count + 1}</h6>
                        <button type="button" class="btn btn-danger btn-sm pujaremoveDevotee">Remove</button>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="customers[${count}][name]" class="form-control customerNames"  autocomplete="off" value="devotees${count + 1}" placeholder="Name" required>
                        <ul class="list-group suggestion_lists"  style="display:none; position:absolute; z-index:1000; width:100%;">
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <input class="form-control text-align-direction" type="tel" name="customers[${count}][mobile]" value="0000000000" placeholder="Enter User Phone Number"  oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,13)">
                        
                    </div>
                    <div class="col-md-3">
                        <input type="number" id="main_aadhaar"name="customers[${count}][aadhaar]" class="form-control" value="000000000000" placeholder="Aadhaar" maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,12)">
                    </div>
                    <div class="col-md-3">
                        <input type="text"  id="main_address" name="customers[${count}][address]" class="form-control puja-main-address" placeholder="Address">
                    </div>
                </div>
            </div>`;
    $('#pujadevoteeWrapper').append($(newItem).hide().fadeIn(300));
}
if (typeof payId === 'undefined') {
    let payId = '';
}

function paymantNow() {
    let isValid = true;
    $('#poojaForm').find('[required]').each(function () {
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
    let formData = new FormData($('#poojaForm')[0]);
    $('.puja-form-submit').prop('disabled', true);
    $('#paymentDetailsSuccess').addClass('d-none');
    $.ajax({
        url: $('#poojaForm').attr('action'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
            console.log('Submitting...');
        },
        success: function (response) {
            $('.puja-form-submit').prop('disabled', false);
            // $('.puja-form-submit').on('click', function(e) {
            //     e.preventDefault();
            //     const btn = $(this);
            //     btn.prop('disabled', true);
            //     const originalText = btn.text();
            //     $('.puja-form-submit').html('<i class="tio tio-autorenew fa-spin me-1"></i> Please wait...');
            //     toggleAddButton();
            //     setTimeout(() => {
            //         btn.removeClass('disabled').prop('disabled', false).text(originalText);
            //     }, 2000);
            // });
            if (response.status) {
                if ($('.puja-payment-mode:checked').val() == 'online' || $('.puja-payment-mode:checked').val() == 'qr_code') {
                    payId = response.paymentId;
                    $('.online-qr-code-show').html(response.imageData);
                } else {
                    if ($('.thisloginstatus').val() == 1) {
                        showPurohitAssignmentModal();
                    } else {
                        $(`.firstoption.${$('#serviceTabs').find('.active').attr('id')}`).focus();
                    }
                    document.getElementById('poojaForm').reset();
                    $('#purohit_ids').val(response.purohit_id ?? '');
                    $('.pandit-select-option').val(response.purohit_id ?? '');
                    $('.purohit-name-show').text(response.purohit_name ?? '');
                    $('#order_ids').val(response.data.order_id ?? '');
                    $('#purohitstatus').val(0);
                    $('.cash-counter-slip').removeClass('d-none');
                    $('.online-counter-slip').addClass('d-none');
                    $('#orderIdInput').val(response.data.order_id ?? '');
                    $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'puja');
                    $('.get_receipt_info').click();
                    $("#countDevoteepuja").val(1);
                    setCountDevoteePuja();
                    $('.btn puja-date-btn').removeClass('btn-primary');
                    $('.btn puja-date-btn').addClass('btn-outline-primary');
                    $('.online-qr-code-show').html('');
                }
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr) {
            $('.puja-form-submit').prop('disabled', false);
            console.error(xhr.responseText);
            toastr.error('Something went wrong. Please try again.');
        }
    });
}


setInterval(() => {
    let qrDiv = document.querySelector('.online-qr-code-show');
    if (qrDiv) {
        if (qrDiv.innerHTML.trim() === "") { } else {
            $.ajax({
                url: $('.get-payment-check-status-url').data('url'),
                data: {
                    id: payId,
                    _token: $('meta[name="_token"]').attr('content'),
                },
                dataType: "json",
                type: "post",
                success: function (data) {
                    $('.puja-form-submit').prop('disabled', false);
                    if (data.is_paid == 1) {
                        toastr.success("Payment Successfully Received!", "Success");
                        $('.online-qr-code-show').html('');
                        $('#paymentDetailsSuccess').removeClass('d-none');
                        $('#paymentId').text(data.data.transaction_id ?? 'N/A');
                        $('#paymentAmount').text(data.data.payment_amount ?? '0');
                        $('#orderIdInput').val(data.data.order_id ?? '');
                        $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'puja');;
                        $('.get_receipt_info').click();
                        document.getElementById('poojaForm').reset();
                        $("#countDevoteepuja").val(1);
                        setCountDevoteePuja();
                        $('.btn puja-date-btn').removeClass('btn-primary');
                        $('.btn puja-date-btn').addClass('btn-outline-primary');
                        payId = '';
                        if ($('.thisloginstatus').val() == 1) {
                            showPurohitAssignmentModal();
                        } else {
                            $(`.firstoption.${$('#serviceTabs').find('.active').attr('id')}`).focus();
                        }
                        $('#purohit_ids').val(data.purohit_id ?? '');
                        $('.purohit-name-show').text(data.purohit_name ?? '');
                        $('#order_ids').val(data.data.order_id ?? '');
                        $('#purohitstatus').val(0);
                        $('.cash-counter-slip').addClass('d-none');
                        $('.online-counter-slip').removeClass('d-none');
                    } else if (data.is_paid == 2) {
                        toastr.error("Payment Not Successfully Received!", "Error");
                        $('.online-qr-code-show').html('');
                        $('#paymentDetailsSuccess').removeClass('d-none');
                        $('#paymentDetailsSuccess').addClass('d-none');
                        $('#orderIdInput').val('');
                    }
                }

            });
        }
    } else {
        console.log("Div not found in DOM");
        $('#paymentDetailsSuccess').removeClass('d-none');
        $('#paymentDetailsSuccess').addClass('d-none');
        $('#paymentId').text('');
        $('#paymentAmount').text('0');
    }
}, 10000);
// $('.phone-input-with-country-picker').each(function() {
//     if (!$(this).data('iti-initialized')) {
//         window.intlTelInput(this, {
//             initialCountry: "in",
//             separateDialCode: true,
//             utilsScript: "{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/utils.js') }}",
//         });
//         $(this).data('iti-initialized', true);
//     }
// });



$(document).on('keydown', '#countDevoteepuja', function (e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        $(this).blur();
        const $checkedRadio = $('input[name="payment_mode"][class="puja-payment-mode"]:checked');
        const $radioToFocus = $checkedRadio.length > 0 ? $checkedRadio : $('input[name="payment_mode"][class="puja-payment-mode"]').first();
        $radioToFocus.focus();
        $('.suggestion_lists').hide();
        $('.suggestion_lists').text('');
        return false;
    }
});

$(document).on('keydown', 'input[name="payment_mode"][class="puja-payment-mode"]', function (e) {
    const radios = $('input[name="payment_mode"][class="puja-payment-mode"]');
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
                $('.submit-button-class-puja').focus();
            } else {
                // Shift+Tab goes back to count input
                // $('#countDevoteepuja').focus();
            }
            break;
        case 'Backspace':
            e.preventDefault();
            $('#countDevoteepuja').focus();
            $('#countDevoteepuja').select();
            break;
        case ' ':
        case 'Spacebar':
            e.preventDefault();
            $(this).prop('checked', true);
            break;
    }
});
$(document).on('keydown', '.submit-button-class-puja', function (e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        if (!e.shiftKey) {
            $('#countDevoteepuja').focus();
        } else {
            $('input[name="payment_mode"][class="puja-payment-mode"]:checked').focus();
        }
    } else if (e.key === 'Enter') {
        e.preventDefault();
        $(this).click();
    }
});

$(document).on('keypress', '.submit-button-class-puja', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        $(this).click();
    }
});