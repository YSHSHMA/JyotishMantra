"use strict";

// multi select
$('.multi-select').select2({
    placeholder: 'Select an option'
});

//insert availability
$(document).ready(function () {
    var packageIncrementVIP = 1;
    var packageIncrement = 1;
    var sundayIncrement = 1;
    var mondayIncrement = 1;
    var tuesdayIncrement = 1;
    var wednesdayIncrement = 1;
    var thursdayIncrement = 1;
    var fridayIncrement = 1;
    var saturdayIncrement = 1;

    $("#sunday-add").click(function () {
        sundayIncrement++;
        $('#sunday-dynamic-field').append(`<tr id="sunday-row${sundayIncrement}"><td><input type="time" name="sunday_from[]" class="form-control" /></td><td><input type="time" name="sunday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${sundayIncrement}" class="btn btn-danger sunday-btn-remove">x</button></td></tr>`);
    });

    $(document).on('click', '.sunday-btn-remove', function () {
        var button_id = $(this).attr("id");
        $('#sunday-row' + button_id + '').remove();
    });

    $("#monday-add").click(function () {
        mondayIncrement++;
        $('#monday-dynamic-field').append(`<tr id="monday-row${mondayIncrement}"><td><input type="time" name="monday_from[]" class="form-control" /></td><td><input type="time" name="monday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${mondayIncrement}" class="btn btn-danger monday-btn-remove">x</button></td></tr>`);
    });

    $(document).on('click', '.monday-btn-remove', function () {
        var button_id = $(this).attr("id");
        $('#monday-row' + button_id + '').remove();
    });

    $("#tuesday-add").click(function () {
        tuesdayIncrement++;
        $('#tuesday-dynamic-field').append(`<tr id="tuesday-row${tuesdayIncrement}"><td><input type="time" name="tuesday_from[]" class="form-control" /></td><td><input type="time" name="tuesday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${tuesdayIncrement}" class="btn btn-danger tuesday-btn-remove">x</button></td></tr>`);
    });

    $(document).on('click', '.tuesday-btn-remove', function () {
        var button_id = $(this).attr("id");
        $('#tuesday-row' + button_id + '').remove();
    });

    $("#wednesday-add").click(function () {
        wednesdayIncrement++;
        $('#wednesday-dynamic-field').append(`<tr id="wednesday-row${wednesdayIncrement}"><td><input type="time" name="wednesday_from[]" class="form-control" /></td><td><input type="time" name="wednesday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${wednesdayIncrement}" class="btn btn-danger wednesday-btn-remove">x</button></td></tr>`);
    });

    $(document).on('click', '.wednesday-btn-remove', function () {
        var button_id = $(this).attr("id");
        $('#wednesday-row' + button_id + '').remove();
    });

    $("#thursday-add").click(function () {
        thursdayIncrement++;
        $('#thursday-dynamic-field').append(`<tr id="thursday-row${thursdayIncrement}"><td><input type="time" name="thursday_from[]" class="form-control" /></td><td><input type="time" name="thursday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${thursdayIncrement}" class="btn btn-danger thursday-btn-remove">x</button></td></tr>`);
    });

    $(document).on('click', '.thursday-btn-remove', function () {
        var button_id = $(this).attr("id");
        $('#thursday-row' + button_id + '').remove();
    });

    $("#friday-add").click(function () {
        fridayIncrement++;
        $('#friday-dynamic-field').append(`<tr id="friday-row${fridayIncrement}"><td><input type="time" name="friday_from[]" class="form-control" /></td><td><input type="time" name="friday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${fridayIncrement}" class="btn btn-danger friday-btn-remove">x</button></td></tr>`);
    });

    $(document).on('click', '.friday-btn-remove', function () {
        var button_id = $(this).attr("id");
        $('#friday-row' + button_id + '').remove();
    });

    $("#saturday-add").click(function () {
        saturdayIncrement++;
        $('#saturday-dynamic-field').append(`<tr id="saturday-row${saturdayIncrement}"><td><input type="time" name="saturday_from[]" class="form-control" /></td><td><input type="time" name="saturday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${saturdayIncrement}" class="btn btn-danger saturday-btn-remove">x</button></td></tr>`);
    });

    $(document).on('click', '.saturday-btn-remove', function () {
        var button_id = $(this).attr("id");
        $('#saturday-row' + button_id + '').remove();
    });

    // Package details
  
    $(document).on('click', '#package-ddadd', function(e) {
        e.preventDefault();
        var lastPriceInput = $('input[name="package_price[]"]').last();
        if (lastPriceInput.val() === '') {
            toastr.error('Please enter a valid price for the selected package.');
            return; 
        }
        packageIncrement++;
        var selectedPackages = [];
        $('select[name="packages_id[]"]').each(function() {
            selectedPackages.push($(this).val());
        });
        $.ajax({
            url: "get-packages-dropdown", 
            method: 'GET',
            data: { packageIds: selectedPackages },
            success: function(response) {
                var html = `
                    <tr id="package-row${packageIncrement}">
                        <td>${response.html}</td>
                        <td><input type="number" name="package_price[]" class="form-control" /></td>
                        <td><button type="button" name="remove" id="${packageIncrement}" class="btn btn-danger package-btn-remove">x</button></td>
                    </tr>
                `;
                $('#package-dynamic-field').append(html);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching packages:', error);
            }
        });
    });
    
    $(document).on('click', '.package-btn-remove', function () {
        var button_id = $(this).attr("id");
        $('#package-row' + button_id + '').remove();
    });


// VIP PACKAGE DETAILS
    $("#package-ddadd-vip").on('click',function (e) {
        e.preventDefault();
        var lastPriceInput = $('input[name="package_price[]"]').last();
        if (lastPriceInput.val() === '') {
            toastr.error('Please enter a valid price for the selected package.');
            return; 
        }
        packageIncrementVIP++;
        var selectedPackages = [];
        $('select[name="packages_id[]"]').each(function() {
            selectedPackages.push($(this).val());
        });
   
        $.ajax({
            url: "get-packages-dropdown-vip",
            method: 'GET',
            data: { packageIds: selectedPackages },
            success: function(response) {
                var html = `
                    <tr id="package-row-vip${packageIncrementVIP}">
                        <td>${response.html}</td>
                        <td><input type="number" name="package_price[]" class="form-control" /></td>
                        <td><button type="button" name="remove" id="${packageIncrementVIP}" class="btn btn-danger package-btn-remove-vip">x</button></td>
                    </tr>
                `;
                $('#package-dynamic-field-vip').append(html);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching packages:', error);
            }
        });
    });
    
    $(document).on('click', '.package-btn-remove-vip', function () {
        var button_id = $(this).attr("id");
        $('#package-row-vip' + button_id + '').remove();
    });

    // offline package add
    $(document).on('click', '#offline-package-add', function (e) {
        e.preventDefault();
        var lastPriceInput = $('input[name="price[]"]').last();
        if (lastPriceInput.val() === '') {
            toastr.error('Please enter a valid price for the selected package.');
            return;
        }
        var lastPercentInput = $('input[name="percent[]"]').last();
        if (lastPercentInput.val() === '') {
            toastr.error('Please enter a valid percentage for the selected package.');
            return;
        }
        packageIncrement++;
        var selectedPackages = [];
        $('select[name="package_details[]"]').each(function () {
            selectedPackages.push($(this).val());
        });
        $.ajax({
            url: "offline-pooja-get-packages-dropdown",
            method: 'GET',
            data: { packageIds: selectedPackages },
            success: function (response) {
                var html = `
                    <tr id="package-row${packageIncrement}">
                        <td>${response.html}</td>
                        <td><input type="number" name="price[]" class="form-control" /></td>
                        <td><input type="number" name="percent[]" class="form-control" /></td>
                        <td><button type="button" name="remove" id="${packageIncrement}" class="btn btn-danger package-btn-remove">x</button></td>
                    </tr>
                `;
                $('#offline-pooja-package-dynamic-field').append(html);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching packages:', error);
            }
        });
    });

    $(document).on('click', '.package-btn-remove', function () {
        var button_id = $(this).attr("id");
        $('#package-row' + button_id + '').remove();
    });

});
