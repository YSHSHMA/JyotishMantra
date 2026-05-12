@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Trust_details'))
@push('css_or_js')
    <style>
        .close_image_model {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }

        .rainbow {
            background-color: #343A40;
            border-radius: 4px;
            color: #000;
            cursor: pointer;
            padding: 8px 16px;
        }

        .rainbow-1 {
            background-image: linear-gradient(359deg, #90e979d9 13%, #f8f8f8 54%, #ebd859 103%);
            animation: slidebg 5s linear infinite;
        }

        @keyframes slidebg {
            to {
                background-position: 20vw;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
                {{ translate('Trust_details') }}
            </h2>
        </div>
        <div class="row">
            <div class="card w-100">
                <div class="card-body">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        <li class="nav-item text-capitalize">
                            <a class="nav-link {{ $type != 'adlist' && $type != 'donate_ad' && $type != 'donate_trust' && $type != 'donate_tran' && $type != 'trust_tran' && $type != 'withdrowal_tran' ? 'active' : '' }}"
                                id="overview-tab" data-toggle="tab" href="#overview-content">
                                {{ translate('overview') }}
                            </a>
                        </li>
                        @if (Helpers::modules_permission_check('Donate', 'Trust Detail', 'all-information'))
                            <li class="nav-item text-capitalize">
                                <a class="nav-link" id="event-info-tab" data-toggle="tab" href="#trust-information">
                                    {{ translate('All_information') }}
                                </a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Donate', 'Trust Detail', 'ads-list'))
                            <li class="nav-item text-capitalize">
                                <a class="nav-link {{ $type == 'adlist' ? 'active' : '' }}" id="order-tab" data-toggle="tab"
                                    href="#create-ad-list">
                                    {{ translate('ad_List') }}
                                </a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Donate', 'Trust Detail', 'donate-ads'))
                            <li class="nav-item text-capitalize">
                                <a class="nav-link {{ $type == 'donate_ad' ? 'active' : '' }}" id="service-tab"
                                    data-toggle="tab" href="#donate_with_ad">
                                    {{ translate('Donate_ads') }}
                                </a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Donate', 'Trust Detail', 'donate-trust'))
                            <li class="nav-item text-capitalize">
                                <a class="nav-link {{ $type == 'donate_trust' ? 'active' : '' }}" id="service-tab"
                                    data-toggle="tab" href="#donate_with_trust">
                                    {{ translate('Donate_trust') }}
                                </a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Donate', 'Trust Detail', 'setting'))
                            <li class="nav-item text-capitalize">
                                <a class="nav-link" id="setting-tab" data-toggle="tab" href="#setting-content">
                                    {{ translate('setting') }}
                                </a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Donate', 'Trust Detail', 'transaction'))
                            <li class="nav-item text-capitalize">
                                <a class="nav-link {{ $type == 'donate_tran' ? 'active' : '' }}" id="transaction-tab"
                                    data-toggle="tab" href="#transaction-content">
                                    {{ translate('transaction') }}
                                </a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Donate', 'Trust Detail', 'transaction-trust'))
                            <li class="nav-item text-capitalize">
                                <a class="nav-link {{ $type == 'trust_tran' ? 'active' : '' }}" id="transaction-tab"
                                    data-toggle="tab" href="#transaction-content-trust">
                                    {{ translate('approval_transaction') }}
                                </a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Donate', 'Trust Detail', 'transaction-trust'))
                            <li class="nav-item text-capitalize">
                                <a class="nav-link {{ $type == 'withdrowal_tran' ? 'active' : '' }}" id="transaction-tab"
                                    data-toggle="tab" href="#transaction-withdrowal-trust">
                                    {{ translate('withdrawal_trust') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade {{ $type != 'adlist' && $type != 'donate_ad' && $type != 'donate_trust' && $type != 'donate_tran' && $type != 'trust_tran' && $type != 'withdrowal_tran' ? 'show active' : '' }}"
                            id="overview-content">
                            <div class="row">
                                @include('admin-views.donate_management.trust.details.overview')
                            </div>
                        </div>
                        <div class="tab-pane fade" id="trust-information">
                            <div class="row">
                                @include('admin-views.donate_management.trust.details.trust_infomation')
                            </div>
                        </div>
                        <div class="tab-pane fade {{ $type == 'adlist' ? 'show active' : '' }}" id="create-ad-list">
                            <div class="row">
                                @include('admin-views.donate_management.trust.details.ad_list')
                            </div>
                        </div>
                        <div class="tab-pane fade {{ $type == 'donate_ad' ? 'show active' : '' }}" id="donate_with_ad">
                            <div class="row">
                                @include('admin-views.donate_management.trust.details.ad_donate_trust')
                            </div>
                        </div>
                        <div class="tab-pane fade {{ $type == 'donate_trust' ? 'show active' : '' }}"
                            id="donate_with_trust">
                            <div class="row">
                                @include('admin-views.donate_management.trust.details.donate_trust')
                            </div>
                        </div>
                        <div class="tab-pane fade" id="setting-content">
                            <div class="row">
                                @include('admin-views.donate_management.trust.details.setting')
                            </div>
                        </div>
                        <div class="tab-pane fade {{ $type == 'donate_tran' ? 'show active' : '' }}"
                            id="transaction-content">
                            <div class="row">
                                @include('admin-views.donate_management.trust.details.donate_amount_transaction')
                            </div>
                        </div>
                        <div class="tab-pane fade {{ $type == 'trust_tran' ? 'show active' : '' }}"
                            id="transaction-content-trust">
                            <div class="row">
                                @include('admin-views.donate_management.trust.details.given_trust_amountTransaction')
                            </div>
                        </div>
                        <div class="tab-pane fade {{ $type == 'withdrowal_tran' ? 'show active' : '' }}"
                            id="transaction-withdrowal-trust">
                            <div class="row">
                                @include('admin-views.donate_management.trust.details.withdrowal_trust_Transaction')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="modal_order_view" class="modal fade modal-center modal-order" role="dialog" aria-label="modal order">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><i class="icon-close"
                            aria-hidden="true"></i></button>
                    <h4 class="modal-title">Order view</h4>
                    <div class="form-group view_orders_items">

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="imageModal_hd" class="modal" onclick="closeModal()" style="display: none;">
        <span class="close_image_model">&times;</span>
        <img class="modal-content" id="fullImage_hd">
    </div>
@endsection

@push('script')
    <script>
        document.getElementById('verificationStatus').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var url = selectedOption.getAttribute('data-href');
            var type = selectedOption.getAttribute('data-type');

            if (type === '1') {
                Swal.fire({
                    title: 'Upload a proof document to join us',
                    html: `
        <input type="file" id="customFileInput" class="form-control" accept="image/png, image/jpeg, image/webp, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" placeholder="Choose a file">
        <div id="fileError" class="text-danger mt-2" style="display:none;"></div>
    `,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Submit',
                    preConfirm: () => {
                        const fileInput = document.getElementById('customFileInput');
                        const file = fileInput.files[0];
                        const errorDiv = document.getElementById('fileError');
                        errorDiv.style.display = 'none';
                        if (!file) {
                            errorDiv.textContent = 'Please choose a valid file';
                            errorDiv.style.display = 'block';
                            return false;
                        }
                        const allowedTypes = ['image/png', 'image/jpeg', 'image/webp',
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ];
                        if (!allowedTypes.includes(file.type)) {
                            errorDiv.textContent =
                                'Only PDF, DOC , PNG, JPEG, or WEBP files are allowed';
                            errorDiv.style.display = 'block';
                            return false;
                        }
                        const maxSize = 5 * 1024 * 1024; // 2 MB
                        if (file.size > maxSize) {
                            errorDiv.textContent = 'The file size must be under 5MB';
                            errorDiv.style.display = 'block';
                            return false;
                        }
                        return file;
                    }
                }).then((result) => {
                    if (result.value) {
                        const formData = new FormData();
                        formData.append('file', result.value);
                        formData.append('_token', '{{ csrf_token() }}');
                        fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(response => {
                            if (response.ok) {
                                window.location.href = ``;
                            } else {
                                Swal.fire('Error!', 'There was an error uploading the file',
                                    'error');
                            }
                        });
                    }
                });

            } else if (url) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to change the verification status?",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.value) {
                        window.location.href = url;
                    }
                });
            }
        });

        function openModal(src) {
            document.getElementById('fullImage_hd').src = src;
            document.getElementById('imageModal_hd').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('imageModal_hd').style.display = 'none';
        }
    </script>

    <script>
        let all_doc_check = [];
        let messsage_use_doc = 0;
        $('.send_wrong_data').click(function() {
            var name = $(this).data('name');
            var value = $(this).data('value');
            if (value == 1) {
                $(`.send_wrong_data-${name}`).addClass('text-success');
                $(`.send_wrong_data-${name}`).removeClass('text-danger');
            } else {
                $(`.send_wrong_data-${name}`).addClass('text-danger');
                $(`.send_wrong_data-${name}`).removeClass('text-success');
                if (messsage_use_doc == 0) {
                    messsage_use_doc = 1;
                }
            }

            let existingEntry = all_doc_check.find(entry => entry.name === name);
            if (existingEntry) {
                existingEntry.value = value;
            } else {
                all_doc_check.push({
                    name: name,
                    value: value
                });
            }
            console.log(all_doc_check);
        });

        function resend_doc() {
            if (all_doc_check.length > 0) {
                if (messsage_use_doc == 1) {
                    Swal.fire({
                        title: 'Enter a vendor Resend reason',
                        input: 'textarea',
                        inputValue: all_doc_check.map(item =>
                                `${item.name.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase())} : `)
                            .join('\n'),
                        inputPlaceholder: 'Type your reason here',
                        showCancelButton: true,
                        confirmButtonText: 'Submit',
                        cancelButtonText: 'Cancel',
                        inputValidator: (value) => {
                            if (!value.trim()) {
                                return 'You need to write something!';
                            }
                        }
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: "{{ route('admin.donate_management.trust.doc_verified_resend') }}", // Replace with your server endpoint
                                type: 'POST',
                                data: {
                                    reason: result.value,
                                    arrays: all_doc_check,
                                    vendor_id: "{{ $trust_data['id'] ?? '' }}",
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Success!',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    window.location.href = ``;
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Are You Sure!',
                        showCancelButton: true,
                        confirmButtonText: 'Submit',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: "{{ route('admin.donate_management.trust.doc_verified_resend') }}",
                                type: 'POST',
                                data: {
                                    reason: result.value,
                                    arrays: all_doc_check,
                                    vendor_id: "{{ $trust_data['id'] ?? '' }}",
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Success!',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    window.location.href = ``;
                                }
                            });
                        }
                    });
                }
            } else {
                toastr.error('Please Choose Invalid Information !', 'Error!', {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 5000,
                    positionClass: 'toast-top-right',
                });
            }
        }

        $('.reject-artist_data').on('click', function() {
            let astrologerId = $(this).attr("data-id");
            Swal.fire({
                title: 'Are You Sure To ' + $(this).data('title'),
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {

                    $('#' + astrologerId).submit();
                }
            });
        });
    </script>
@endpush
