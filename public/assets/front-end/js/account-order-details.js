"use strict";

$('.action-get-refund-details').on('click', function (){
    let route = $(this).data('route');
    getRefundDetails(route)
})

function getRefundDetails(route) {
    $.get(route, (response) => {
        $("#refund_details_field").html(response);
        $('#refund_details_modal').modal().show();
    })
}

$('.action-digital-product-download').on('click', function (){
    let link = $(this).data('link');
    digitalProductDownload(link);
})

function digitalProductDownload(link) {
    $.ajax({
        type: "GET",
        url: link,
        responseType: 'blob',
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            if (data.status == 1 && data.file_path) {
                const a = document.createElement('a');
                a.href = data.file_path;
                a.download = data.file_name;
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(data.file_path);
            }
        },
        error: function () {
        },
        complete: function () {
            $('#loading').hide();
        },
    });
}

let selectedFiles = [];
$(document).on('ready', () => {
    $(".msgfilesValue").on('change', function () {
        for (let i = 0; i < this.files.length; ++i) {
            selectedFiles.push(this.files[i]);
        }
        let pre_container = $(this).closest('.upload_images_area');
        displaySelectedFiles(pre_container);
    });

    function displaySelectedFiles(pre_container = null) {
        let container;
        if (pre_container == null) {
            container = document.getElementsByClassName("selected-files-container");
        } else {
            container = pre_container.find('.selected-files-container');
        }
        container.innerHTML = "";
        selectedFiles.forEach((file, index) => {
            const input = document.createElement("input");
            input.type = "file";
            input.name = `images[${index}]`;
            input.classList.add(`image_index${index}`);
            input.hidden = true;
            container.append(input);
            const blob = new Blob([file], {type: file.type});
            const file_obj = new File([file], file.name);
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file_obj);
            input.files = dataTransfer.files;
        });

        pre_container.find(".filearray").empty();
        for (let i = 0; i < selectedFiles.length; ++i) {
            let filereader = new FileReader();
            let uploadDiv = jQuery.parseHTML("<div class='upload_img_box'><span class='img-clear'><i class='tio-clear'></i></span><img src='' alt=''></div>");

            filereader.onload = function () {
                let imageData = this.result;
                $(uploadDiv).find('img').attr('src', imageData);
            };

            filereader.readAsDataURL(selectedFiles[i]);
            pre_container.find(".filearray").append(uploadDiv);
            $(uploadDiv).find('.img-clear').on('click', function () {
                $(this).closest('.upload_img_box').remove();

                selectedFiles.splice(i, 1);
                $('.image_index' + i).remove();
            });
        }
    }
});


let reviewSelectedFiles = [];
$(document).on('ready', () => {
    $(".reviewFilesValue").on('change', function () {
        for (let i = 0; i < this.files.length; ++i) {
            reviewSelectedFiles.push(this.files[i]);
        }
        let pre_container = $(this).closest('.upload_images_area');
        reviewFilesValueDisplaySelectedFiles(pre_container);
    });

    function reviewFilesValueDisplaySelectedFiles(pre_container = null) {
        let container;
        if (pre_container == null) {
            container = document.getElementsByClassName("selected-files-container");
        } else {
            container = pre_container.find('.selected-files-container');
        }
        container.innerHTML = "";
        reviewSelectedFiles.forEach((file, index) => {
            const input = document.createElement("input");
            input.type = "file";
            input.name = `fileUpload[${index}]`;
            input.classList.add(`image_index${index}`);
            input.hidden = true;
            container.append(input);
            const blob = new Blob([file], {type: file.type});
            const file_obj = new File([file], file.name);
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file_obj);
            input.files = dataTransfer.files;
        });

        pre_container.find(".filearray").empty();
        for (let i = 0; i < reviewSelectedFiles.length; ++i) {
            let filereader = new FileReader();
            let uploadDiv = jQuery.parseHTML("<div class='upload_img_box'><span class='img-clear'><i class='tio-clear'></i></span><img src='' alt=''></div>");

            filereader.onload = function () {
                let imageData = this.result;
                $(uploadDiv).find('img').attr('src', imageData);
            };

            filereader.readAsDataURL(reviewSelectedFiles[i]);
            pre_container.find(".filearray").append(uploadDiv);
            $(uploadDiv).find('.img-clear').on('click', function () {
                $(this).closest('.upload_img_box').remove();
                reviewSelectedFiles.splice(i, 1);
                $('.image_index' + i).remove();
            });
        }
    }
});
// offline pooja schedule modal
function scheduleModal(orderId) {
    const baseUrl = $('#schedule-modal-trigger').data('base-url');
    $.ajax({
        type: "get",
        url: `${baseUrl}/offlinepooja-schedule/${orderId}`,
        success: function (response) {
            if (response.status == true) {
                let payOnline = parseInt(response.walletAmount, 10) - parseInt(response.schedulePrice, 10);
                let walletDeduction = 0;
                if (payOnline >= 0) {
                    payOnline = 0;
                    walletDeduction = response.schedulePrice;
                } else {
                    payOnline = Math.abs(payOnline);
                    walletDeduction = response.walletAmount;
                }
                $('#schedule-wallet-deduction-input').val(walletDeduction);
                $('#schedule-pay-online-input').val(payOnline);
                $('#schedule-pooja-charge').text(response.schedulePrice);
                $('#schedule-available-wallet-balance').text(response.walletAmount);
                $('#schedule-wallet-deduction').text(walletDeduction);
                $('#schedule-pay-online').text(payOnline);
                $('#schedule-modal').modal('show');
            } else {
                alert('an error occurred');
            }
        }
    });
}

// offline pooja remaining pay modal
function remainingPayModal(that) {
    const baseUrl = $('#schedule-modal-trigger').data('base-url');
    var orderId = $(that).data('orderid');
    var customerId = $(that).data('customerid');
    $.ajax({
        type: "get",
        url: `${baseUrl}/offlinepooja-remaining-pay/${orderId}/${customerId}`,
        success: function (response) {
            if (response.status == true) {
                let payOnline = parseInt(response.walletAmount, 10) - parseInt(response.remainAmount, 10);
                let walletDeduction = 0;
                if (payOnline >= 0) {
                    payOnline = 0;
                    walletDeduction = response.remainAmount;
                } else {
                    payOnline = Math.abs(payOnline);
                    walletDeduction = response.walletAmount;
                }
                $('#remaining-wallet-deduction-input').val(walletDeduction);
                $('#remaining-pay-online-input').val(payOnline);
                $('#remaining-available-wallet-balance').text(response.walletAmount);
                $('#remaining-wallet-balance-deduction').text(walletDeduction);
                $('#remaining-pay-online').text(payOnline);
                $('#remaining-pay-modal').modal('show');
            } else {
                alert('an error occurred');
            }
        }
    });
}

//offline pooja cancel modal
function cancelModal(orderId) {
    const baseUrl = $('#schedule-modal-trigger').data('base-url');
    $.ajax({
        type: "get",
        url: `${baseUrl}/offlinepooja-cancle-order/${orderId}`,
        success: function (response) {
            if (response.status == true) {
                $('#refund-amount-input').val(response.refundPrice);
                $('#refund-amount').text(response.refundPrice);
                $('#cancel-pooja-modal').modal('show');
            } else {
                alert('an error occurred');
            }
        }
    });
}

//google address search
let autocomplete;
let autocomplete2;

function initAutocomplete() {
    const input = document.getElementById("google-search");
    const options = {
        componentRestrictions: { country: "IN" }
    }
    autocomplete = new google.maps.places.Autocomplete(input, options);
    autocomplete.addListener("place_changed", onPlaceChange)

    const input2 = document.getElementById("google-search2");
    autocomplete2 = new google.maps.places.Autocomplete(input2, options);
    autocomplete2.addListener("place_changed", onPlaceChanged)
}

function onPlaceChange() {
    const place = autocomplete.getPlace();
    const addressComponents = place.address_components;

    let latitude = place.geometry.location.lat();
    let longitude = place.geometry.location.lng();
    let address = place.formatted_address;
    let state = '';
    let city = '';
    let postalCode = '';

    addressComponents.forEach(component => {
        const componentType = component.types[0];

        switch (componentType) {
            case 'administrative_area_level_1':
                state = component.long_name;
                break;
            case 'locality':
                city = component.long_name;
                break;
            case 'postal_code':
                postalCode = component.long_name;
                break;
        }
    });

    $('#state').val(state);
    $('#city').val(city);
    $('#pincode').val(postalCode);
    $('#latitude').val(latitude);
    $('#longitude').val(longitude);
}

function onPlaceChanged() {
    const place = autocomplete2.getPlace();
    const addressComponents = place.address_components;

    let latitude = place.geometry.location.lat();
    let longitude = place.geometry.location.lng();
    let address = place.formatted_address;
    let state = '';
    let city = '';
    let postalCode = '';

    addressComponents.forEach(component => {
        const componentType = component.types[0];

        switch (componentType) {
            case 'administrative_area_level_1':
                state = component.long_name;
                break;
            case 'locality':
                city = component.long_name;
                break;
            case 'postal_code':
                postalCode = component.long_name;
                break;
        }
    });

    $('#state2').val(state);
    $('#city2').val(city);
    $('#pincode2').val(postalCode);
    $('#latitude2').val(latitude);
    $('#longitude2').val(longitude);
}
