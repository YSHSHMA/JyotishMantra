
function appendHtmlbhojan() {
    var selectedPackageId = $('#bhojan_package_id').val();
    var selectedSlotId = $('#bhojan_slot_id').data('selected');
    var isAvailable = $('#bhojan_package_id').find(':selected').data('available');

    if (selectedPackageId && isAvailable == 1) {
        loadSlotsbhojan(selectedPackageId, selectedSlotId);
    }
    if (document.querySelector('.phone-input-with-country-picker-bhojan-0')) {
        initializePhoneInput(`.phone-input-with-country-picker-bhojan-0`, `.country-picker-phone-number-bhojan-0`);
        $('#countDevoteebhojan').attr('tabindex', '0');
        $('input[name="payment_mode"][class="puja-payment-bhojan-mode"]').attr('tabindex', '0');
        $('.submit-button-class-bhojan').attr('tabindex', '0');
    }
}

$(document).on('click', '.bhojan-date-btn', function () {
    $('.bhojan-date-btn').removeClass('btn-primary active').addClass('btn-outline-primary');
    $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
    $('#bhojan-date').val($(this).data('value'));
});

function loadSlotsbhojan(packageId, selectedSlotId = null) {
    const slotSelect = $('#bhojan_slot_id');
    const selectedDate = $('#selected_bhojan_date').val(); // from hidden input
    slotSelect.html('<option>Loading slots...</option>').prop('disabled', true);

    if (!packageId) {
        slotSelect.html('<option value="">No slots available</option>');
        return;
    }

    $.ajax({
        url: $('.get-ordermanagement-timeslots').data('url'),
        type: "GET",
        data: {
            package_id: packageId
        },
        success: function (res) {
            if (res.success && res.slots.length > 0) {
                const now = new Date();
                const today = now.toISOString().split('T')[0];
                let options = '<option value="">Select Slot</option>';
                let validSlots = 0;

                res.slots.forEach(slot => {
                    // Build a Date object for the selected darshan date + slot end time
                    const slotEnd = new Date(`${selectedDate || today} ${slot.end_time}`);

                    // Skip past slots only if booking date is today
                    if (selectedDate === today && slotEnd <= now) return;

                    validSlots++;
                    const selected = selectedSlotId == slot.id ? 'selected' : '';
                    options += `<option value="${slot.id}" ${selected}>
                                ${slot.start_time} - ${slot.end_time}
                            </option>`;
                });

                if (validSlots > 0) {
                    slotSelect.html(options).prop('disabled', false);
                } else {
                    slotSelect.html('<option value="">No upcoming slots available</option>');
                }
            } else {
                slotSelect.html('<option value="">No slots available</option>');
            }
        },
        error: function () {
            slotSelect.html('<option value="">Error loading slots</option>');
        }
    });
}
$(document).on('change', "#bhojan_package_id", function () {
    var packageId = $(this).val();
    var isAvailable = $(this).find(':selected').data('available');
    if (isAvailable == 1) {
        loadSlotsbhojan(packageId);
    } else {
        $('#bhojan_slot_id').html('<option value="">No slots available</option>');
    }
});


//  Devotee logic

function toggleAddButton() {
    const name = $('#customer_bhojan_name').val().trim();
    const mobile = $('#customer_bhojan_mobile').val().trim();
    const aadhaar = $('#customer_bhojan_aadhaar').val().trim();
    const address = $('#customer_bhojan_address').val().trim();

    const validMobile = /^[6-9]\d{9}$/.test(mobile);
    const validAadhaar = /^\d{12}$/.test(aadhaar);
    const allValid = name && validMobile && validAadhaar && address;

    // $('#addDevoteeBhojan').prop('disabled', allValid);
    if (allValid) calculatebhojanAmount();
}

$('#customer_bhojan_name, #customer_bhojan_mobile, #customer_bhojan_aadhaar, #customer_bhojan_address').on('input', toggleAddButton);

//  Add devotee        
//  Remove devotee
$(document).on('click', '.removeDevoteesBhojan', function () {
    $(this).closest('.devotee-bhojanitems').fadeOut(200, function () {
        $(this).remove();
        updateDevoteeNum();
        calculatebhojanAmount();
        darshancount = $('#devoteeBhojanAddsWrapper .devotee-bhojanitems').length;
        $('#countDevoteebhojan').val(darshancount);
    });
});

//  Update numbering
function updateDevoteeNum() {
    $('#devoteeBhojanAddsWrapper .devotee-bhojanitems').each(function (index) {
        $(this).find('.devotee-bhojan').text('Devotee ' + (index + 1));
    });
    countbhojan = $('#devoteeBhojanAddsWrapper .devotee-bhojanitems').length;
    $('#show_bhojan_devotees_qty').text(countbhojan);
    $('#customer_bhojan_qty').val(countbhojan);
}

//  Amount calculation
function calculatebhojanAmount() {
    let selected = $('#bhojan_package_id').find(':selected');
    // if (!selected.length) return;

    let base = parseFloat(selected.data('base')) || 0;
    let gst = parseFloat(selected.data('gst')) || 0;
    let platform = parseFloat(selected.data('platform')) || 0;
    let receipt = parseFloat(selected.data('receipt')) || 0;
    let devoteecountbhojan = $('#devoteeBhojanAddsWrapper .devotee-bhojanitems').length || 1;

    let gstAmount = (base * gst / 100);
    let perDevotee = base + gstAmount + platform + receipt;
    let total = perDevotee * devoteecountbhojan;

    $('#show_bhojan_base_price').text(base.toFixed(2));
    $('#show_bhojan_platform_price').text(platform.toFixed(2));
    $('#show_bhojan_receipt_price').text(receipt.toFixed(2));
    $('#show_bhojan_gst_per').text(gst);
    $('#show_bhojan_devotees_qty').text(devoteecountbhojan);
    $('#show_bhojan_per_person_qty').text(perDevotee.toFixed(2));

    var freeOptionBhojan = document.querySelector('.payment-option-bhojan-free');
    var freeRadioBhojan = freeOptionBhojan ? freeOptionBhojan.querySelector('input[type="radio"]') : null;

    var cashOptionBhojan = document.querySelector('.payment-option-bhojan-cash');
    var cashRadioBhojan = cashOptionBhojan ? cashOptionBhojan.querySelector('input[type="radio"]') : null;

    var onlineOptionBhojan = document.querySelector('.payment-option-bhojan-online');
    var onlineRadioBhojan = onlineOptionBhojan ? onlineOptionBhojan.querySelector('input[type="radio"]') : null;

    var onlineOptionBhojan1 = document.querySelector('.payment-option-bhojan-online1');
    var onlineRadioBhojan1 = onlineOptionBhojan1 ? onlineOptionBhojan1.querySelector('input[type="radio"]') : null;

    if (1 > total) {
        onlineRadioBhojan.checked = false;
        onlineRadioBhojan1.checked = false;
        cashRadioBhojan.checked = false;
        freeRadioBhojan.checked = true;
        freeOptionBhojan.classList.remove('d-none');
        cashOptionBhojan.classList.add('d-none');
        onlineOptionBhojan.classList.add('d-none');
        onlineOptionBhojan1.classList.add('d-none');
    } else {
        onlineRadioBhojan.checked = false;
        onlineRadioBhojan1.checked = false;
        cashRadioBhojan.checked = true;
        freeRadioBhojan.checked = false;
        freeOptionBhojan.classList.add('d-none');
        cashOptionBhojan.classList.remove('d-none');
        onlineOptionBhojan.classList.remove('d-none');
        onlineOptionBhojan1.classList.remove('d-none');
    }
    $('#show_bhojan_total_amount').text(total.toFixed(2));

    $('#base_bhojan_price').val(base.toFixed(2));
    $('#receipt_bhojan_amount').val((receipt * devoteecountbhojan).toFixed(2));
    $('#total_bhojan_amount').val(total.toFixed(2));
    $('#customer_bhojan_qty').val(devoteecountbhojan);
}

// Trigger recalculation on package change
$(document).on('change', '#bhojan_package_id', calculatebhojanAmount);

// Initial
// calculatebhojanAmount();

$(document).on('keyup', '#countDevoteebhojan', function () {
    setCountDevoteebhojan();
});
setCountDevoteebhojan();

function setCountDevoteebhojan() {
    $('.devotee-bhojanitems:not(:first)').each(function () {
        const item = $(this);
        item.addClass('removing');
        setTimeout(() => {
            item.remove();
        }, 10);
    });
    for (let i = 0; i < (Number($('#countDevoteebhojan').val()) - 1); i++) {
        addBhojanlebal((i + 1));
    }

}

function addBhojanlebal(countbhojan) {
    const newItem = `
            <div class="devotee-bhojanitems border rounded p-3 mb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-12 mb-2 d-flex justify-content-between">
                        <h6 class="fw-bold text-primary devotee-bhojan">Devotee ${countbhojan + 1}</h6>
                        <button type="button" class="btn btn-danger btn-sm removeDevoteesBhojan">Remove</button>
                    </div>
                    <div class="col-md-3">
                    <input type="text" name="customers[${countbhojan}][name]" class="form-control customerNames"  autocomplete="off" placeholder="Name"  value="devotees${darshancount + 1}">
                    <ul class="list-group suggestion_lists"  style="display:none; position:absolute; z-index:1000; width:100%;">
                        </ul>
                    </div>
                    <div class="col-md-3"><input type="text" name="customers[${countbhojan}][mobile]" class="form-control" placeholder="Mobile" value="0000000000" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,13)"></div>
                    <div class="col-md-3"><input type="text" name="customers[${countbhojan}][aadhaar]" class="form-control" placeholder="Aadhaar" value="000000000000" maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,12)"></div>
                    <div class="col-md-3"><input type="text" name="customers[${countbhojan}][address]" class="form-control bhojan-main-address" placeholder="Address"></div>
                </div>
            </div>`;
    $('#devoteeBhojanAddsWrapper').append($(newItem).hide().fadeIn(300));
    updateDevoteeNum();
    calculatebhojanAmount();
}
if (typeof payId === 'undefined') {
    let payId = '';
}

function paymantBhojanNow() {
    let isValid = true;
    $('#bhojanForm').find('[required]').each(function () {
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
    let formData = new FormData($('#bhojanForm')[0]);
    $('.bhojan-form-submit').prop('disabled', true);
    $("#payment-bhojan-details-Success").addClass('d-none');
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
            $('.bhojan-form-submit').prop('disabled', false);
            // $('.bhojan-form-submit').on('click', function (e) {
            //     e.preventDefault();
            //     const btn = $(this);
            //     const originalText = btn.text();
            //     btn.html('<i class="fa fa-spinner fa-spin me-1"></i> Please wait...');
            //     toggleAddButton();
            //     setTimeout(() => {
            //         btn.removeClass('disabled').prop('disabled', false).text(originalText);
            //     }, 2000);
            // });
            if (response.status) {
                if ($('.puja-payment-bhojan-mode:checked').val() === 'online' || $('.puja-payment-bhojan-mode:checked').val() === 'qr_code') {
                    payId = response.paymentId;
                    $('.online-qr-code-bhojan-show').html(response.imageData);
                } else {
                    document.getElementById('bhojanForm').reset();
                    $('#orderIdInput').val(response.data.order_id ?? '');
                    $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type','other');;
                    $('.get_receipt_info').click();
                    $('#countDevoteebhojan').val(1);
                    setCountDevoteebhojan();
                    $('.btn bhojan-date-btn').removeClass('btn-primary');
                    $('.btn bhojan-date-btn').addClass('btn-outline-primary');
                    $('.online-qr-code-bhojan-show').html('');
                }
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr) {
            $('.bhojan-form-submit').prop('disabled', false);
            toastr.error('Something went wrong. Please try again.');
        }
    });
}

setInterval(() => {
    let qrDiv = document.querySelector('.online-qr-code-bhojan-show');
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
                        $('.online-qr-code-bhojan-show').html('');
                        $('#payment-bhojan-details-Success').removeClass('d-none');
                        $('#paymant-bhojan-id').text(data.data.transaction_id ?? 'N/A');
                        $('#paymant-bhojan-amount').text(data.data.payment_amount ?? '0');
                        $('#orderIdInput').val(data.data.order_id ?? '');
                        $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type','other');;
                        $('.get_receipt_info').click();
                        document.getElementById('bhojanForm').reset();
                        $('#countDevoteebhojan').val(1);
                        setCountDevoteebhojan();
                        $('.btn bhojan-date-btn').removeClass('btn-primary');
                        $('.btn bhojan-date-btn').addClass('btn-outline-primary');
                        payId = '';
                    } else if (data.is_paid == 2) {
                        toastr.error("Payment Not Successfully Received!", "Error");
                        $('.online-qr-code-bhojan-show').html('');
                        $('#payment-bhojan-details-Success').removeClass('d-none');
                        $('#payment-bhojan-details-Success').addClass('d-none');
                        $('#orderIdInput').val('');
                    }
                }

            });
        }
    } else {
        console.log("Div not found in DOM");
        $('#payment-bhojan-details-Success').removeClass('d-none');
        $('#payment-bhojan-details-Success').addClass('d-none');
        $('#paymant-bhojan-id').text('');
        $('#paymant-bhojan-amount').text('0');
    }
}, 10000);

const userEdited4 = new Set();
$(document).on('blur', '.bhojan-main-address', function () {
    const index = $('.bhojan-main-address').index(this);
    const value = $(this).val().trim();
    if (index === 0 && value !== '') {
        $('.bhojan-main-address').each(function (i) {
            if (i > 0 && !userEdited4.has(i) && $(this).val().trim() === '') {
                $(this).val(value);
            }
        });
    } else {
        userEdited4.add(index);
    }
});



$(document).on('keydown', '#countDevoteebhojan', function (e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        $(this).blur();
        const $checkedRadio = $('input[name="payment_mode"][class="puja-payment-bhojan-mode"]:checked');
        const $radioToFocus = $checkedRadio.length > 0 ? $checkedRadio : $('input[name="payment_mode"][class="puja-payment-bhojan-mode"]').first();
        $radioToFocus.focus();
        $('.suggestion_lists').hide();
        $('.suggestion_lists').text('');
        return false;
    }
});

$(document).on('keydown', 'input[name="payment_mode"][class="puja-payment-bhojan-mode"]', function (e) {
    const radios = $('input[name="payment_mode"][class="puja-payment-bhojan-mode"]');
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
                $('.submit-button-class-bhojan').focus();
            } else {
                // Shift+Tab goes back to count input
                // $('#countDevoteebhojan').focus();
            }
            break;
        case 'Backspace':
            e.preventDefault();
            $('#countDevoteebhojan').focus();
            $('#countDevoteebhojan').select();
            break;
        case ' ':
        case 'Spacebar':
            e.preventDefault();
            $(this).prop('checked', true);
            break;
    }
});
$(document).on('keydown', '.submit-button-class-bhojan', function (e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        if (!e.shiftKey) {
            $('#countDevoteebhojan').focus();
        } else {
            $('input[name="payment_mode"][class="puja-payment-bhojan-mode"]:checked').focus();
        }
    } else if (e.key === 'Enter') {
        e.preventDefault();
        $(this).click();
    }
});

$(document).on('keypress', '.submit-button-class-bhojan', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        $(this).click();
    }
});