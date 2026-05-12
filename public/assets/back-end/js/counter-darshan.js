function appendHtmldarshan() {
    var selecteddarshanPackageId = $('#darshan_package_id').val();

    var selecteddarshanSlotId = $('#slot_id_darshan').data('selected');
    console.log(selecteddarshanPackageId);
    var isdarshanAvailable = $('#darshan_package_id').find(':selected').data('available');


    if (selecteddarshanPackageId && isdarshanAvailable == 1) {
        loadSlotsdarshan(selecteddarshanPackageId, selecteddarshanSlotId);
    }
    setCountDevoteeDarshan();
    if (document.querySelector('.phone-input-with-country-picker-darshan-0')) {
        initializePhoneInput(`.phone-input-with-country-picker-darshan-0`, `.country-picker-phone-number-darshan-0`);
        $('#countDevoteedarshan').attr('tabindex', '0');
        $('input[name="payment_mode"][class="puja-payment-darshan-mode"]').attr('tabindex', '0');
        $('.submit-button-class-darshan').attr('tabindex', '0');
    }
}

$(document).on('click', '.darshan-date-btn', function () {
    $('.darshan-date-btn').removeClass('btn-primary active').addClass('btn-outline-primary');
    $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
    $('#selected_darshan_date').val($(this).data('value'));
});

function loadSlotsdarshan(packageId, selecteddarshanSlotId = null) {
    const slotSelect = $('#slot_id_darshan');
    const selectedDate = $('#selected_darshan_date').val(); // from hidden input
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
                    const start = new Date(`${selectedDate || today} ${slot.start_time}`);
                    const end = new Date(`${selectedDate || today} ${slot.end_time}`);

                    // const slotEnd = new Date(`${selectedDate || today} ${slot.end_time}`);
                    // if (selectedDate === today && slotEnd <= now) return;
                    validSlots++;
                    const selected = selecteddarshanSlotId == slot.id ? 'selected' : '';
                    const isCurrent = now >= start && now <= end;
                    options += `<option value="${slot.id}" ${selected} ${isCurrent ? 'selected' : ''}>
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
$(document).on('change', "#darshan_package_id", function () {
    var packageId = $(this).val();
    var isdarshanAvailable = $(this).find(':selected').data('available');
    if (isdarshanAvailable == 1) {
        loadSlotsdarshan(packageId);
    } else {
        $('#slot_id_darshan').html('<option value="">No slots available</option>');
    }
});
// $(function() {
// Enable Add Button only when all main fields valid
function toggleAddButton() {
    // const name = $('#customer_name').val().trim();
    // const mobile = $('#customer_mobile').val().trim();
    // const aadhaar = $('#customer_aadhaar').val().trim();
    // const address = $('#customer_address').val().trim();

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
    calculateAmounts();
    // toastr.success('All details are valid');
}

$('#customer_name, #customer_mobile, #customer_aadhaar, #customer_address').on('input', toggleAddButton);

// Add devotee block

function addDarshanlebal(darshancount) {
    var newItem = `
            <div class="darshan-devotee-items border rounded p-3 mb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-12 mb-2 d-flex justify-content-between">
                        <h6 class="fw-bold text-primary devotee-headings">Devotee ${darshancount + 1}</h6>
                        <button type="button" class="btn btn-danger btn-sm removeDevotees">Remove</button>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="customers[${darshancount}][name]" class="form-control customerNames"  autocomplete="off" placeholder="Name" required value="devotees${darshancount + 1}">
                        <ul class="list-group suggestion_lists"  style="display:none; position:absolute; z-index:1000; width:100%;">
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="customers[${darshancount}][mobile]" class="form-control" placeholder="Mobile" value="0000000000" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,13)">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="customers[${darshancount}][aadhaar]" class="form-control" placeholder="Aadhaar" value="000000000000" maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,12)">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="customers[${darshancount}][address]" class="form-control darshan-main-address" placeholder="Address">
                    </div>
                </div>
            </div>`;
    $('#darshandevoteeWrapper').append($(newItem).hide().fadeIn(300));
}
// Remove devotee
$(document).on('click', '.removeDevotees', function () {
    $(this).closest('.darshan-devotee-items').fadeOut(200, function () {
        $(this).remove();
        updateDevoteeNum();
        calculateAmounts();
        darshancount = $('#darshandevoteeWrapper .darshan-devotee-items').length;
        $('#countDevoteedarshan').val(darshancount);
    });
});

function updateDevoteeNum() {
    $('#darshandevoteeWrapper .darshan-devotee-items').each(function (index) {
        $(this).find('.devotee-headings').text('Devotee ' + (index + 1));
    });
    darshancount = $('#darshandevoteeWrapper .darshan-devotee-items').length;
    $('#show_devotees_qty_darshan').text(darshancount);
    // $('#show_per_person_qty_darshan').val(darshancount);
}

// Calculate amount dynamically
function calculateAmounts() {
    let selected = $('#darshan_package_id').find(':selected');
    if (!selected.length) return;
    // $('#darshandevoteeWrapper .darshan-devotee-items').filter(function() {
    //     return !$(this).is(':visible');
    // }).remove();
    let base = parseFloat(selected.data('base')) || 0;
    let gst = parseFloat(selected.data('gst')) || 0;
    let platform = parseFloat(selected.data('platform')) || 0;
    let receipt = parseFloat(selected.data('receipt')) || 0;

    let devoteeCount = newdevoteeCount = $('#darshandevoteeWrapper .darshan-devotee-items:visible').length || 1;


    let gstAmount = (base * gst / 100);
    let perDevotee = base + gstAmount + platform + receipt;
    let total = perDevotee * devoteeCount;

    // Update summary display
    $('#show_base_price_darshan').text(base.toFixed(2));
    $('#show_platform_price_darshan').text(platform.toFixed(2));
    $('#show_receipt_price_darshan').text(receipt.toFixed(2));
    $('#show_gst_per_darshan').text(gst);
    $('#show_devotees_qty_darshan').text(newdevoteeCount);
    $('#show_per_person_qty_darshan').text(perDevotee.toFixed(2));

    var freeOptionDarshan = document.querySelector('.payment-option-darshan-free');
    var freeRadioDarshan = freeOptionDarshan ? freeOptionDarshan.querySelector('input[type="radio"]') : null;
    var cashOptionDarshan = document.querySelector('.payment-option-darshan-cash');
    var cashRadioDarshan = cashOptionDarshan ? cashOptionDarshan.querySelector('input[type="radio"]') : null;
    var onlineOptionDarshan = document.querySelector('.payment-option-darshan-online');
    var onlineRadioDarshan = onlineOptionDarshan ? onlineOptionDarshan.querySelector('input[type="radio"]') : null;

    var onlineOptionDarshan1 = document.querySelector('.payment-option-darshan-online1');
    var onlineRadioDarshan1 = onlineOptionDarshan1 ? onlineOptionDarshan1.querySelector('input[type="radio"]') : null;

    if (1 > total) {
        onlineRadioDarshan.checked = false;
        onlineRadioDarshan1.checked = false;
        cashRadioDarshan.checked = false;
        freeRadioDarshan.checked = true;
        freeOptionDarshan.classList.remove('d-none');
        cashOptionDarshan.classList.add('d-none');
        onlineOptionDarshan.classList.add('d-none');
        onlineOptionDarshan1.classList.add('d-none');
    } else {
        onlineRadioDarshan.checked = false;
        onlineRadioDarshan1.checked = false;
        cashRadioDarshan.checked = true;
        freeRadioDarshan.checked = false;
        freeOptionDarshan.classList.add('d-none');
        cashOptionDarshan.classList.remove('d-none');
        onlineOptionDarshan.classList.remove('d-none');
        onlineOptionDarshan1.classList.remove('d-none');
    }

    $('#show_total_amount_darshan').text(total.toFixed(2));

    // Update hidden fields for backend
    $('#base_price_darshan').val(base.toFixed(2));
    $('#receipt_amount_darshan').val((receipt * devoteeCount).toFixed(2));
    $('#total_amount_darshan').val(total.toFixed(2));
}

// Recalculate on package change or count change
$(document).on('change', '#darshan_package_id', calculateAmounts);
// calculateAmounts();
// });
$(document).on('keyup', '#countDevoteedarshan', function () {
    setCountDevoteeDarshan();
});

function setCountDevoteeDarshan() {
    $('.darshan-devotee-items:not(:first)').each(function () {
        const item = $(this);
        item.addClass('removing');
        setTimeout(() => {
            item.remove();
            updateDevoteeNum();
            calculateAmounts();
        }, 10);
    });
    for (let i = 0; i < (Number($('#countDevoteedarshan').val()) - 1); i++) {
        addDarshanlebal((i + 1));
    }
    updateDevoteeNum();
    calculateAmounts();
}
function paymantDarshanNow() {
    let isValid = true;
    $('#darshanForm').find('[required]').each(function () {
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
    let formData = new FormData($('#darshanForm')[0]);
    $('.darshan-form-submit').prop('disabled', true);
    $('#payment-darshn-details-Success').addClass('d-none');
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
            $('.darshan-form-submit').prop('disabled', false);
            // $('.darshan-form-submit').on('click', function (e) {
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
                if ($('.puja-payment-darshan-mode:checked').val() === 'online' || $('.puja-payment-darshan-mode:checked').val() === 'qr_code') {
                    payId = response.paymentId;
                    $('.online-qr-code-darshan-show').html(response.imageData);
                } else {
                    document.getElementById('darshanForm').reset();
                    $('#orderIdInput').val(response.data.order_id ?? '');
                    $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type','other');
                    $('.get_receipt_info').click();
                    $("#countDevoteedarshan").val(1);
                    setCountDevoteeDarshan();
                    $('.btn darshan-date-btn').removeClass('btn-primary');
                    $('.btn darshan-date-btn').addClass('btn-outline-primary');
                    $('.online-qr-code-darshan-show').html('');
                }
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr) {
            $('.darshan-form-submit').prop('disabled', false);
            console.error(xhr.responseText);
            toastr.error('❌ Something went wrong. Please try again.');
        }
    });
}
if (typeof payId === 'undefined') {
    let payId = '';
}
setInterval(() => {
    let qrDiv = document.querySelector('.online-qr-code-darshan-show');
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
                        $('.online-qr-code-darshan-show').html('');
                        $('#payment-darshn-details-Success').removeClass('d-none');
                        $('#paymant-darshan-id').text(data.data.transaction_id ?? 'N/A');
                        $('#paymant-darshan-amount').text(data.data.payment_amount ?? '0');
                        $('#orderIdInput').val(data.data.order_id ?? '');
                        $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type','other');
                        $('.get_receipt_info').click();
                        document.getElementById('darshanForm').reset();
                        $("#countDevoteedarshan").val(1);
                        setCountDevoteeDarshan();
                        $('.btn darshan-date-btn').removeClass('btn-primary');
                        $('.btn darshan-date-btn').addClass('btn-outline-primary');
                        payId = '';
                    } else if (data.is_paid == 2) {
                        toastr.error("Payment Not Successfully Received!", "Error");
                        $('.online-qr-code-darshan-show').html('');
                        $('#payment-darshn-details-Success').removeClass('d-none');
                        $('#payment-darshn-details-Success').addClass('d-none');
                        $('#orderIdInput').val('');
                    }
                }

            });
        }
    } else {
        console.log("Div not found in DOM");
        $('#payment-darshn-details-Success').removeClass('d-none');
        $('#payment-darshn-details-Success').addClass('d-none');
        $('#paymant-darshan-id').text('');
        $('#paymant-darshan-amount').text('0');
    }
}, 10000);

const userEdited2 = new Set();

$(document).on('blur', '.darshan-main-address', function () {
    const index = $('.darshan-main-address').index(this);
    const value = $(this).val().trim();
    if (index === 0 && value !== '') {
        $('.darshan-main-address').each(function (i) {
            if (i > 0 && !userEdited2.has(i) && $(this).val().trim() === '') {
                $(this).val(value);
            }
        });
    } else {
        userEdited2.add(index);
    }
});



$(document).on('keydown', '#countDevoteedarshan', function (e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        $(this).blur();
        const $checkedRadio = $('input[name="payment_mode"][class="puja-payment-darshan-mode"]:checked');
        const $radioToFocus = $checkedRadio.length > 0 ? $checkedRadio : $('input[name="payment_mode"][class="puja-payment-darshan-mode"]').first();
        $radioToFocus.focus();
        $('.suggestion_lists').hide();
        $('.suggestion_lists').text('');
        return false;
    }
});

$(document).on('keydown', 'input[name="payment_mode"][class="puja-payment-darshan-mode"]', function (e) {
    const radios = $('input[name="payment_mode"][class="puja-payment-darshan-mode"]');
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
                $('.submit-button-class-darshan').focus();
            } else {
                // Shift+Tab goes back to count input
                // $('#countDevoteedarshan').focus();
            }
            break;
        case 'Backspace':
            e.preventDefault();
            $('#countDevoteedarshan').focus();
            $('#countDevoteedarshan').select();
            break;
        case ' ':
        case 'Spacebar':
            e.preventDefault();
            $(this).prop('checked', true);
            break;
    }
});
$(document).on('keydown', '.submit-button-class-darshan', function (e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        if (!e.shiftKey) {
            $('#countDevoteedarshan').focus();
        } else {
            $('input[name="payment_mode"][class="puja-payment-darshan-mode"]:checked').focus();
        }
    } else if (e.key === 'Enter') {
        e.preventDefault();
        $(this).click();
    }
});

$(document).on('keypress', '.submit-button-class-darshan', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        $(this).click();
    }
});