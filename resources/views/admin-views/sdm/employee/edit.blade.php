@extends('layouts.back-end.app')

@section('title', translate('SDM_Employee_Edit'))

@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body text-start">
                    <form action="{{ route('admin.sdm.employee.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="id" value="{{$edit->id}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sdm" class="title-color">
                                        {{ translate('SDM') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select id="sdm" name="sdm_id" class="form-control" disabled required>
                                        @forelse ($sdms as $sdm)
                                            <option value="{{$sdm->id}}" {{$sdm->id==$edit->sdm_id?'selected':''}}>{{$sdm->name}}</option>                                            
                                        @empty
                                            <option value="">No SDM Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="title-color">
                                        {{ translate('name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control" value="{{$edit->name}}"
                                        placeholder="{{ translate('enter_name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="title-color">
                                        {{ translate('email') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" class="form-control" value="{{$edit->email}}"
                                        placeholder="{{ translate('enter_email') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mobile" class="title-color">
                                        {{ translate('mobile') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="mobile" class="form-control"
                                        placeholder="{{ translate('enter_mobile') }}"
                                        value="{{ preg_replace('/^\+91/', '', $edit->mobile) }}"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="my-3">
                            <h4>Temples</h4>
                            <div class="row py-2" id="temple-div">
                                <div class="text-center w-100">
                                    <p class="text-danger">No Temples Found</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 justify-content-end">
                            <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
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

            function loadTemples() {
                let sdm = $('#sdm').val();
                let empId = $('#id').val();

                $('#temple-div').html(`
                    <div class="text-center w-100">
                        <p>Loading temples...</p>
                    </div>
                `);

                $.ajax({
                    url: "{{ route('admin.sdm.employee.get-selected-temple') }}",
                    type: "GET",
                    data: {
                        empId: empId,
                        sdm: sdm
                    },
                    success: function(response) {
                        if (!response.status || response.temples.length === 0) {
                            $('#temple-div').html(`
                                <div class="text-center w-100">
                                    <p class="text-danger">No Temples Found</p>
                                </div>
                            `);
                            return;
                        }

                        let selectedTemples = (response.selectedTemple || []).map(Number);
                        let html = '';

                        $.each(response.temples, function(index, temple) {

                            let isChecked = selectedTemples.includes(temple.id)
                            ? 'checked'
                            : '';

                            html += `
                                <div class="col-md-6 mb-2">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox"
                                            class="temple-checkbox"
                                            name="temples[]"
                                            value="${temple.id}" ${isChecked}>
                                        ${temple.name}
                                    </label>
                                </div>
                            `;
                        });

                        $('#temple-div').html(html);
                    },
                    error: function() {
                        $('#temple-div').html(`
                            <div class="text-center w-100">
                                <p class="text-danger">Something went wrong</p>
                            </div>
                        `);
                    }
                });
            }

            // 🔹 Page load
            loadTemples();

            // 🔹 District change
            // $('#sdm').on('change', function() {
            //     loadTemples();
            // });

        });
    </script>
@endpush
