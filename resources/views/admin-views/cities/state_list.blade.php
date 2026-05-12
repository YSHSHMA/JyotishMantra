@extends('layouts.back-end.app')

@section('title', translate('state_List'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('state_List') }}
        </h2>
    </div>
    <div class="row">
        <div class="card w-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="policy_table" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('icon') }}</th>
                                        <th>{{ translate('name') }}</th>
                                        <th>{{ translate('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fileUploadModal" tabindex="-1" role="dialog" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h5 class="modal-title" id="fileUploadModalLabel">Upload File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="file" name="upload_file" class="form-control mb-3" id="uploadFileInput" required>
                <button type="button" id="submitFileBtn" class="btn btn-primary w-100">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        initDataTable({
            tableId: '#policy_table',
            ajaxUrl: "{{ route('admin.state.state-list-filter') }}",
            exportTitle: "policy list",
            pageLength: 25,
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'logo',
                    name: 'logo',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'option',
                    name: 'option',
                    orderable: false,
                    searchable: false
                },
            ],
            extraOptions: {
                serverSide: true,
                ajax: {
                    data: function(d) {
                        d.search_by_type = $('.search_by_type').val();
                        d.search_by_name = $('.search_by_name').val();
                    }
                }
            }
        });
    });
    $('.search_by_type').on('change', function() {
        $('#policy_table').DataTable().draw();
    });
    let searchDelay;
    $('.search_by_name').on('keyup', function() {
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function() {
            $('#policy_table').DataTable().draw();
        }, 500);
    });
    $('#policy_table').on('draw.dt', function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $(document).ready(function() {
        let activeForm = null; // store which form was clicked

        // When the switch is clicked
        $(document).on('click', '.toggle-switch-message', function(e) {
            e.preventDefault(); // stop checkbox default action
            activeForm = $('#' + $(this).data('form'));
            console.log( $(this).data('form'));
            $('#fileUploadModal').modal('show');
        });

        // When Submit is clicked in modal
        $('#submitFileBtn').on('click', function() {
            const fileInput = $('#uploadFileInput')[0];

            if (fileInput.files.length === 0) {
                alert('Please choose a file to upload.');
                return;
            }

            // Create a file input dynamically inside the active form
            const newInput = $('<input>', {
                type: 'file',
                name: 'upload_file',
                style: 'display:none'
            });

            // Assign the selected file to the new input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(fileInput.files[0]);
            newInput[0].files = dataTransfer.files;

            // Append file input to the form and submit
            activeForm.append(newInput);
            $('#fileUploadModal').modal('hide');
            activeForm.submit();
        });
    });
</script>
@endpush