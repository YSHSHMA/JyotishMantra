{{-- =======================
     Devotee Section Partial
     Usage: @include('partials.devotee-section')
======================= --}}

<div class="col-md-12 mt-4">
    <label>Devotee Details</label>

    <!-- Wrapper -->
    <div id="devoteeWrapper">
        <div class="devotee-item border rounded p-3 mb-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="customers[0][name]" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Mobile</label>
                    <input type="text" id="main_mobile" name="customers[0][mobile]" class="form-control" required maxlength="10" inputmode="numeric">
                </div>
                <div class="col-md-3">
                    <label>Aadhaar</label>
                    <input type="text" id="main_aadhaar" name="customers[0][aadhaar]" class="form-control" maxlength="12" inputmode="numeric">
                </div>
                <div class="col-md-3">
                    <label>Address</label>
                    <input type="text" id="main_address" name="customers[0][address]" class="form-control" placeholder="Address">
                </div>
            </div>
        </div>
    </div>

    <!-- Add More -->
    <button type="button" id="addDevotee" class="btn btn-sm btn-success mt-2">+ Add More</button>
</div>

@stack('scripts')
<script>
$(document).ready(function(){
    let count = 1;

    // Add Devotee
    $(document).on('click', '#addDevotee', function(){
        let name = $('input[name="customers[0][name]"]').val().trim();
        let mobile = $('#main_mobile').val().trim();

        if (name === '' || mobile === '') {
            alert('Please fill Name and Mobile before adding more devotees.');
            return false;
        }

        if (!/^[6-9]\d{9}$/.test(mobile)) {
            alert('Please enter a valid 10-digit mobile number.');
            return false;
        }

        let aadhaar = $('#main_aadhaar').val().trim();
        let address = $('#main_address').val().trim();

        let newItem = `
        <div class="devotee-item border rounded p-3 mb-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="customers[${count}][name]" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Mobile</label>
                    <input type="text" name="customers[${count}][mobile]" class="form-control" value="${mobile}" readonly>
                </div>
                <div class="col-md-3">
                    <label>Aadhaar</label>
                    <input type="text" name="customers[${count}][aadhaar]" class="form-control" value="${aadhaar}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <input type="text" name="customers[${count}][address]" class="form-control" value="${address}" placeholder="Address">
                    <button type="button" class="btn btn-danger btn-sm ms-2 removeDevotee">x</button>
                </div>
            </div>
        </div>`;

        $('#devoteeWrapper').append($(newItem).hide().fadeIn(300));
        count++;
    });

    // Remove Devotee
    $(document).on('click', '.removeDevotee', function(){
        $(this).closest('.devotee-item').fadeOut(200, function(){ $(this).remove(); });
    });
});
</script>

