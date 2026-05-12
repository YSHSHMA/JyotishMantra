function loadTempleServices(templeId) {
    let tabSection = $('#tabSection');
    tabSection.html('<div class="text-center py-4"><b>Loading services...</b></div>');

    if (!templeId) {
        tabSection.html('');
        return;
    }
    $.ajax({
        url: $('.create-ticket-url').data('url'),
        type: 'GET',
        data: {
            temple_id: templeId
        },
        success: function (response) {
            tabSection.html(response.html);
            setActivePayment();
            console.log($(response.html).first().hasClass('alert'));
            if (!$(response.html).first().hasClass('alert')) {
                appendHtmlbhojan();
                appendHtmldarshan();
                appendHtmlPuja();
                if (document.querySelector('.phone-input-with-country-picker-locker-0')) {
                    initializePhoneInput(`.phone-input-with-country-picker-locker-0`, `.country-picker-phone-number-locker-0`);
                }
            }
        },
        error: function () {
            tabSection.html('<div class="alert alert-danger mt-3">Error loading services.</div>');
        }
    });
}

//  Load on dropdown change
$(document).on('change', '#temple_id', function () {
    loadTempleServices($(this).val());
});

// document.getElementById('orderSearchForm').addEventListener('submit', function (e) {
$(document).on('submit', '#orderSearchForm', function (e) {
    e.preventDefault();
    let orderId = document.getElementById('orderIdInput').value.trim();
    let urls = $('.get-ordermanagement-getorderdetails').data('url');
    if (!orderId) return;
    fetch(`${urls}?order_id=${orderId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('all_recipt_order_Details_Section').style.display = 'block';
                document.getElementById('all_recept_thermal_Receipts').innerHTML = data.html3;
                document.getElementById('orderDetailsSection').style.display = 'block';
                document.getElementById('thermalReceipts').innerHTML = data.html;
                // document.getElementById('order_Details_Section').style.display = 'block';
                document.getElementById('thermal_Receipts2').innerHTML = data.html2;
                document.querySelectorAll('.qr-code').forEach(qr => {
                    let text = qr.dataset.text;
                    qr.innerHTML = '';
                    new QRCode(qr, {
                        text: text,
                        width: 70,
                        height: 70
                    });
                });
                if ($('.get_receipt_info').data('type') == 'other') {
                    $('#printall_ReceiptBtn').click();
                    printStatusUpdate();
                    $(`.firstoption.${$('#serviceTabs').find('.active').attr('id')}`).focus();
                }
            } else {
                toastr.error("Order Not Found!");
                document.getElementById('all_recipt_order_Details_Section').style.display = 'none';
                document.getElementById('orderDetailsSection').style.display = 'none';
                // document.getElementById('order_Details_Section').style.display = 'none';
            }
        })
        .catch(() => toastr.error("Something Went Wrong!"));
});

function initializeEmployeeSearch() {
    const purohitEmpNamesInput = document.getElementById('purohit_employee_name_show');
    const purohit_Emp_Names = document.getElementById('purohitEmpNames');
    const suggestionBox = document.querySelector('.emp_suggestion_lists');

    if (!purohitEmpNamesInput) return;
    let debounceTimer;
    ['click', 'focus'].forEach(event => {
        purohitEmpNamesInput.addEventListener(event, function (e) {
            clearTimeout(debounceTimer);
            const keyword = e.target.value.trim();
            debounceTimer = setTimeout(() => {
                searchEmployees(keyword);
            }, 200);
        });
    });
    purohitEmpNamesInput.addEventListener('input', function (e) {
        clearTimeout(debounceTimer);
        const keyword = e.target.value.trim();
        // if (keyword.length < 2) {
        //     suggestionBox.style.display = 'none';
        //     return;
        // }

        debounceTimer = setTimeout(() => {
            searchEmployees(keyword);
        }, 300);
    });

    suggestionBox.addEventListener('click', function (e) {
        if (e.target.tagName === 'LI') {
            const selectedName = e.target.textContent.trim();
            const selectedName2 = e.target.dataset.name.trim();
            purohitEmpNamesInput.value = selectedName;
            purohit_Emp_Names.value = selectedName2
            document.getElementById('purohitstatus').value = 1;
            suggestionBox.style.display = 'none';
        }
    });

    let currentIndex = -1;

    purohitEmpNamesInput.addEventListener('keydown', function (e) {
        const items = suggestionBox.querySelectorAll('li');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentIndex = (currentIndex + 1) % items.length;
            updateActive(items);
        }

        if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            updateActive(items);
        }

        if (e.key === 'Tab') {
            e.preventDefault();
            if (currentIndex >= 0) {
                selectItem(items[currentIndex]);
            }
        }
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#purohit_employee_name_show') && !e.target.closest('.emp_suggestion_lists')) {
            suggestionBox.style.display = 'none';
        }
    });
    function updateActive(items) {
        items.forEach(item => item.classList.remove('active'));

        if (currentIndex >= 0) {
            items[currentIndex].classList.add('active');
            items[currentIndex].scrollIntoView({
                block: 'nearest'
            });
        }
    }

    function selectItem(item) {
        const purohitEmpNamesInput = document.getElementById('purohit_employee_name_show');
        const purohit_Emp_Names = document.getElementById('purohitEmpNames');

        purohitEmpNamesInput.value = item.textContent.trim();
        purohit_Emp_Names.value = item.dataset.name || '';
        document.getElementById('purohitstatus').value = 1;

        document.querySelector('.emp_suggestion_lists').style.display = 'none';
        currentIndex = -1;
    }

}

function paymentCollect(num) {
    let status = $('#purohitstatus').val();
    if (status == 0) {
        toastr.error('Please Select Valid Assign Tickit Pandit Id');
        return false;
    }
    $.ajax({
        url: $('.get-collect-paymant-order-update').data('url'),
        type: "post",
        data: {
            purohit: $('#purohit_ids').val(),
            order_id: $('#order_ids').val(),
            ex_id: $('#purohitEmpNames').val(),
            num: num
        },
        success: function (res) {
            $('#print_Btn2').click();
            printStatusUpdate();
            $(`.firstoption.${$('#serviceTabs').find('.active').attr('id')}`).focus();
            Swal.close();
        }
    });
}

function searchEmployees(keyword) {
    const purohitId = document.getElementById('purohit_ids').value;
    const suggestionBox = document.querySelector('.emp_suggestion_lists');
    let urls = $('.get-purohit-all-employee-list').data('url');
    fetch(`${urls}?search=${encodeURIComponent(keyword)}&purohit=${purohitId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(res => {
            if (res.status && res.data && res.data.length > 0) {
                let listHTML = '';
                res.data.forEach(item => {
                    listHTML += `
                        <li class="list-group-item" data-id="${item.id}" data-name="${item.name}" style="cursor: pointer;">
                            ${item.full_name}
                        </li>
                    `;
                });
                suggestionBox.innerHTML = listHTML;
                suggestionBox.style.display = 'block';
            } else {
                suggestionBox.innerHTML = '<li class="list-group-item text-muted">No employees found</li>';
                suggestionBox.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error fetching employees:', error);
            suggestionBox.innerHTML = '<li class="list-group-item text-danger">Error loading employees</li>';
            suggestionBox.style.display = 'block';
        });
}


initDataTable({
    tableId: '#orderlistpayment',
    ajaxUrl: $('.get-order-list-booking-receipt-filter').data('url'),
    exportTitle: "Trust Puja Orders",
    pageLength: 25,
    notshowfooter: 1,
    heightSet: 300,
    buttonStatus: 0,
    columns: [
        {
            data: 'id',
            name: 'id'
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
        },
        {
            data: 'order_id',
            name: 'order_id'
        },
        {
            data: 'temple_name',
            name: 'temple_name',
            orderable: false,
            searchable: false
        },
        {
            data: 'service_name',
            name: 'service_name',
            orderable: false,
            searchable: false
        },
        {
            data: 'yajman_name',
            name: 'yajman_name',
            orderable: false,
            searchable: false
        },
        {
            data: 'payment_mode',
            name: 'payment_mode',
        },
        {
            data: 'platform',
            name: 'platform',
        },
        {
            data: 'pandit_amount',
            name: 'pandit_amount',
            orderable: false,
            searchable: false
        },
        {
            data: 'trust_amount',
            name: 'trust_amount',
            orderable: false,
            searchable: false
        }, {
            data: 'gst',
            name: 'gst',
            orderable: false,
            searchable: false
        }, {
            data: 'platform_fee',
            name: 'platform_fee',
            orderable: false,
            searchable: false
        },
        {
            data: 'amount',
            name: 'amount',
            orderable: false,
            searchable: false
        },
    ],
    extraOptions: {
        serverSide: true,
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('row-order-id-' + data.order_ids);
            $(row).addClass('get-order-recodes');
            $(row).attr('row-order-id', data.order_ids);
            $(row).attr('row-order-status', data.order_status);
            // if (data.order_status === 'pending') {
            //     updateOrderArray(data.order_ids);
            // }
        },
        initComplete: function () {
            console.log('DataTable initialized successfully');
        },
        ajax: {
            data: function (d) {
                d.searchValue = $('#datatableSearch_').val();
                d.start_date = $('.start_date').val();
                d.end_date = $('.end_date').val();
                d.payment_mode = $('.payment_mode').val();
                d.payment_status = $('.payment_status').val();
                d.temple_name = $('.temple_name').val();
                d.puja_name = $('.puja_name').val();
                d.print_status = 1;
            }
        }
    }
});
$('.payment_mode, .start_date, .end_date, .payment_status, .temple_name').on('change', function () {
    $('#orderlistpayment').DataTable().draw();
});
$(document).on('click', '[data-target="#cashConfirmModal"]', function () {
    let orderId = $(this).data('id');
    $('#confirmOrderId').val(orderId);
});

$(document).on('click', '#confirmCashBtn', function () {
    let orderId = $('#confirmOrderId').val();
    $.ajax({
        url: $('.cash-paymant-confirm-url').data('url'),
        type: "POST",
        data: {
            _token: $('meta[name="_token"]').attr('content'),
            order_id: orderId
        },
        success: function (res) {
            if (res.success) {
                toastr.success('Cash payment confirmed successfully!');
                $('#cashConfirmModal').modal('hide');
                $('#orderlistpayment').DataTable().draw();
                $('#orderIdInput').val(orderId);
                $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'other');;
                $('.get_receipt_info').click();
            } else {
                toastr.error('Failed to confirm payment.');
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
            toastr.error('An error occurred while processing your request.');
        }
    });
});

function printNow(that) {
    let stats = $(that).data('employee');
    if (stats == 0 && $(that).data('purohit') != 0 && $('.thisloginstatus').val() == 1) {
        showPurohitAssignmentModal();
        if ($(that).data('platform') == 'cash') {
            $('.cash-counter-slip').removeClass('d-none');
            $('.online-counter-slip').addClass('d-none');
        } else {
            $('.cash-counter-slip').addClass('d-none');
            $('.online-counter-slip').removeClass('d-none');
        }
        $('.purohitstatus').val(0);
        $('#purohitEmpNames').val('');
        $('.purohit-name-show').val('');
        // $('#purohit-modal-show').modal('show');
        $('.purohit-name-show').text($(that).data('purohit_name'));
        $('.pandit-select-option').val($(that).data('purohit'));
        $('#purohit_ids').val($(that).data('purohit'));
        $('#order_ids').val($(that).data('id'));
        $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'puja');
    } else {
        $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'other');
    }
    $("#orderIdInput").val($(that).data('id'));
    $('.get_receipt_info').click();
    document.getElementById('orderSearchForm').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}
// $(document).ready(function() {
//     let activeInput = null;
//     $(document).on('keyup click', '.purohit_employee_name_show', function() {
//         let keyword = $(this).val();
//         activeInput = $(this);
//         let suggestionBox = $(this).next('.emp_suggestion_lists');
//         // if (keyword.length < 2) {
//         //     suggestionBox.hide();
//         //     return;
//         // }
//         $.ajax({
//             url: "{{url('api/v1/purohit-all-employee-list')}}",
//             type: "GET",
//             data: {
//                 search: keyword,
//                 purohit: $('.purohit_ids').val(),
//             },
//             success: function(res) {
//                 let list = '';
//                 if (res.status && res.data.length > 0) {
//                     $.each(res.data, function(i, item) {
//                         list += `
//                       <li class="list-group-item" data-name="${item.name}" data-id="${item.id}">
//                           ${item.full_name}
//                       </li>`;
//                     });
//                     suggestionBox.html(list).show();
//                 } else {
//                     suggestionBox.hide();
//                 }
//             }
//         });
//     });
//     $(document).on('click', '.emp_suggestion_lists li', function(e) {
//         e.preventDefault();
//         e.stopPropagation();
//         let selectedName = $(this).text().trim();
//         let selectedName2 = $(this).data('name').trim();
//         let suggestionBox = $(this).closest('.emp_suggestion_lists');
//         let inputBox = suggestionBox.prev('.purohit_employee_name_show');
//         inputBox.val(selectedName);
//         $('.purohitEmpNames').val(selectedName2);
//         $('.purohitstatus').val(1);
//         suggestionBox.hide();
//     });
//     $(document).on('click', function(e) {
//         if (!$(e.target).closest('.purohit_employee_name_show, .emp_suggestion_lists').length) {
//             $('.emp_suggestion_lists').hide();
//         }
//     });
// });

// function paymentCollect(num) {
//     let status = $('.purohitstatus').val();
//     if (status == 0) {
//         toastr.error('Please Select Valid Assign Tickit Pandit Id');
//         return false;
//     }
//     $.ajax({
//         url: "{{url('api/v1/collect-paymant-order-update')}}",
//         type: "post",
//         data: {
//             purohit: $('.purohit_ids').val(),
//             order_id: $('.order_ids').val(),
//             ex_id: $('.purohitEmpNames').val(),
//             num: num
//         },
//         success: function(res) {
//             $('#orderlistpayment').DataTable().draw();
//             $('#purohit-modal-show').modal('hide');
//             $('.purohit-name-show').text('');
//             $('#print_Btn2').click();
//         }
//     });
// }
$(document).on('click', '#toggleTable', function () {
    const body = $('#tableCardBody');
    const icon = $(this).find('i');

    body.slideToggle(200);

    if (icon.hasClass('tio-exit_fullscreen_1_1')) {
        icon.removeClass('tio-exit_fullscreen_1_1').addClass('tio-map_zoom_out');
        icon.text('map_zoom_out');
    } else {
        icon.removeClass('tio-map_zoom_out').addClass('tio-exit_fullscreen_1_1');
        icon.text('exit_fullscreen_1_1');
    }
});

function setActivePayment() {
    $('.payment-method-flex-add').removeClass('active');
    let newClass = $('#serviceTabs').find('.active').attr('id');
    $(`.payment-method-flex-add.${newClass}`).addClass('active');
    $(`.firstoption.${$('#serviceTabs').find('.active').attr('id')}`).focus();
}
$(document).on('shown.bs.tab', '#serviceTabs .nav-link', function (e) {
    setActivePayment();
});


// let activeInput = null;
// $(document).on('keyup', '.customerNames', function () {
//     let keyword = $(this).val();
//     activeInput = $(this);
//     let suggestionBox = $(this).next('.suggestion_lists');
//     if (keyword.length < 2) {
//         suggestionBox.hide();
//         return;
//     }
//     $.ajax({
//         url: $('.get-user-suggestion-list').data('url'),
//         type: "GET",
//         data: {
//             search: keyword
//         },
//         success: function (res) {
//             let list = '';
//             if (res.status && res.data.length > 0) {
//                 $.each(res.data, function (i, item) {
//                     list += `
//                                                     <li class="list-group-item"
//                                                         data-id="${item.id}">
//                                                         ${item.name}
//                                                     </li>`;
//                 });
//                 suggestionBox.html(list).show();
//             } else {
//                 suggestionBox.hide();
//             }
//         }
//     });
// });

// $(document).on('click', '.suggestion_lists li', function (e) {
//     e.preventDefault();
//     e.stopPropagation();
//     let selectedName = $(this).text().trim();
//     let suggestionBox = $(this).closest('.suggestion_lists');
//     let inputBox = suggestionBox.prev('.customerNames');
//     inputBox.val(selectedName);
//     suggestionBox.hide();
// });
// $(document).on('click', function (e) {
//     if (!$(e.target).closest('.customerNames, .suggestion_lists').length) {
//         $('.suggestion_lists').hide();
//     }
// });


async function fetchUserSuggestions(keyword) {
    try {
        const response = await $.ajax({
            url: $('.get-user-suggestion-list').data('url'),
            type: "GET",
            data: { search: keyword }
        });

        return response;
    } catch (error) {
        console.error('Error fetching suggestions:', error);
        return { status: false, data: [] };
    }
}


let activeInput = null;
let currentSuggestionIndex = -1;
let currentSuggestions = [];

$(document).on('keyup', '.customerNames', function (e) {
    let keyword = $(this).val();
    activeInput = $(this);
    let suggestionBox = $(this).next('.suggestion_lists');

    // Skip if arrow keys or enter
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter') {
        return;
    }

    if (keyword.length < 2) {
        suggestionBox.hide();
        currentSuggestionIndex = -1;
        currentSuggestions = [];
        return;
    }

    $.ajax({
        url: $('.get-user-suggestion-list').data('url'),
        type: "GET",
        data: {
            search: keyword
        },
        success: function (res) {
            let list = '';
            if (res.status && res.data.length > 0) {
                currentSuggestions = res.data;
                $.each(res.data, function (i, item) {
                    let activeClass = i === 0 ? 'active' : '';
                    list += `
                        <li class="list-group-item suggestion-item ${activeClass}"
                            data-id="${item.id}"
                            data-index="${i}">
                            ${item.name}
                        </li>`;
                });
                suggestionBox.html(list).show();
                currentSuggestionIndex = 0;
            } else {
                suggestionBox.hide();
                currentSuggestionIndex = -1;
                currentSuggestions = [];
            }
        }
    });
});

// Handle keyboard navigation
$(document).on('keydown', '.customerNames', function (e) {
    let suggestionBox = $(this).next('.suggestion_lists');

    if (!suggestionBox.is(':visible') || currentSuggestions.length === 0) {
        return;
    }

    const suggestionItems = suggestionBox.find('.suggestion-item');

    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            if (currentSuggestionIndex < currentSuggestions.length - 1) {
                currentSuggestionIndex++;
                updateActiveSuggestion(suggestionItems);
            } else if (currentSuggestionIndex === -1) {
                currentSuggestionIndex = 0;
                updateActiveSuggestion(suggestionItems);
            }
            break;

        case 'ArrowUp':
            e.preventDefault();
            if (currentSuggestionIndex > 0) {
                currentSuggestionIndex--;
                updateActiveSuggestion(suggestionItems);
            } else if (currentSuggestionIndex === 0) {
                currentSuggestionIndex = -1;
                suggestionItems.removeClass('active');
            }
            break;

        case 'Enter':
            e.preventDefault();
            if (currentSuggestionIndex >= 0 && currentSuggestionIndex < currentSuggestions.length) {
                selectSuggestion(currentSuggestionIndex);
            }
            break;

        case 'Escape':
            suggestionBox.hide();
            currentSuggestionIndex = -1;
            currentSuggestions = [];
            break;

        case 'Tab':
            if (currentSuggestionIndex >= 0 && currentSuggestionIndex < currentSuggestions.length) {
                selectSuggestion(currentSuggestionIndex);
                $('.suggestion_lists').hide();
                $('.suggestion_lists').text('');
            }
            break;
    }
});

function updateActiveSuggestion(suggestionItems) {
    suggestionItems.removeClass('active');

    if (currentSuggestionIndex >= 0 && currentSuggestionIndex < currentSuggestions.length) {
        suggestionItems.eq(currentSuggestionIndex).addClass('active');

        const selectedSuggestion = currentSuggestions[currentSuggestionIndex];
        activeInput.val(selectedSuggestion.name);
        const activeItem = suggestionItems.eq(currentSuggestionIndex);
        const suggestionBox = activeItem.closest('.suggestion_lists');

        if (activeItem.length) {
            const scrollTop = suggestionBox.scrollTop();
            const itemTop = activeItem.position().top;
            const itemHeight = activeItem.outerHeight();
            const boxHeight = suggestionBox.height();

            if (itemTop + itemHeight > boxHeight + scrollTop) {
                suggestionBox.scrollTop(itemTop + itemHeight - boxHeight);
            } else if (itemTop < scrollTop) {
                suggestionBox.scrollTop(itemTop);
            }
        }
    }
}

function selectSuggestion(index) {
    const selectedItem = currentSuggestions[index];
    if (!selectedItem) return;

    const suggestionBox = activeInput.next('.suggestion_lists');
    activeInput.val(selectedItem.name);

    suggestionBox.hide();
    currentSuggestionIndex = -1;
    currentSuggestions = [];
}
$(document).on('click', '.suggestion_lists li', function (e) {
    e.preventDefault();
    e.stopPropagation();
    let selectedName = $(this).text().trim();
    let selectedId = $(this).data('id');
    let suggestionBox = $(this).closest('.suggestion_lists');
    let inputBox = suggestionBox.prev('.customerNames');
    inputBox.val(selectedName);
    suggestionBox.hide();
    currentSuggestionIndex = -1;
    currentSuggestions = [];
});

$(document).on('click', function (e) {
    if (!$(e.target).closest('.customerNames, .suggestion_lists').length) {
        $('.suggestion_lists').hide();
        currentSuggestionIndex = -1;
        currentSuggestions = [];
    }
});
setInterval(() => {
    $('#newcountGet').load(location.href + ' #newcountGet > *', function () {
        tablerset();
    });
}, 7000);

function tablerset() {
    let newnum = parseInt($('.order-count-show').val()) || 0;
    let oldnum = parseInt($('.order-count-show-old').val()) || 0;
    if (newnum > oldnum) {
        $('#orderlistpayment').DataTable().draw();
        $('.order-count-show-old').val(newnum);
    }
}

$(document).on('click', '.show-order-details-now', function () {
    $.ajax({
        url: $('.modal-order-details-view').data('url'),
        type: "POST",
        data: {
            _token: $('meta[name="_token"]').attr('content'),
            order_id: $(this).data('orderid'),
        },
        success: function (res) {
            $('#leadDetailsModal').modal('show');
            $("#leadDetailsModalLabel").text(`Lead Details - Order #${$(this).data('orderid')}`);
            $('.add-new-order-details').html(res.html);
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
            toastr.error('An error occurred while processing your request.');
        }
    });
});



$('#orderlistpayment').on('draw.dt', function () {
    updateOrderArray();
});


