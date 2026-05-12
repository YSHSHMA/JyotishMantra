@extends('layouts.back-end.app-trustees')

@php
use App\Utils\Helpers;
@endphp
@section('title', translate('scanner_qr'))

@push('css_or_js')
<style>
    #reader {
        width: 300px;
        border-radius: 30px;
    }

    #result {
        font-size: 1.5rem;
    }

    #html5-qrcode-button-camera-stop,
    #html5-qrcode-select-camera,
    #html5-qrcode-button-camera-start,
    #html5-qrcode-anchor-scan-type-change,
    #html5-qrcode-button-camera-permission {
        border: .0625rem solid transparent;
        padding: .54688rem .875rem;
        font-size: .875rem;
        line-height: 1.6;
        border-radius: .3125rem;
        transition: all .2s ease-in-out;
    }

    #html5-qrcode-button-camera-stop {
        color: #fff;
        background-color: #ed4c78;
        border-color: #ed4c78;
    }

    #html5-qrcode-button-camera-start,
    #html5-qrcode-button-camera-permission {
        color: #fff;
        background-color: #377dff;
        border-color: #377dff;
        margin: 12px 11px;
    }

    #html5-qrcode-anchor-scan-type-change {
        margin-top: 12px;
        color: #fff;
        background-color: #00c9db;
        border-color: #00c9db;
    }

    .lightbox {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        justify-content: center;
        align-items: center;
    }

    .lightbox-content {
        max-width: 90%;
        max-height: 90%;
        margin: auto;
        display: grid;
    }

    .lightbox img {
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 80vh;
        border: 3px solid white;
        border-radius: 5px;
    }

    .close-btn {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10000;
    }

    .close-btn:hover {
        color: #ccc;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="card mb-4 no-print">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('scanner_qr') }}</h5>
        </div>
        <div class="card-body">
            <div class="row mt-3">
                <div class="col-md-4">
                    <div id="reader" class="rounded border p-2"></div>
                    <div id="result" class="mt-2"></div>
                    <div class="row">
                        <?php $showButtonVerify = false;
                        $userListArray = []; ?>
                        @if(request('type') == 'all-order' || request('type') == 'single-order' || request('type') == 'puja-slip')
                        <?php if ($order && $order->details && count($order->details) > 0) { ?>
                            <?php foreach ($order->details as $detail) { ?>
                                @if($detail->customers && json_decode($detail->customers, true))
                                @foreach(json_decode($detail->customers, true) as $index => $vval)
                                <?php $getAddhar = \App\Models\UserAadhaarKyc::where('aadhaar_number', ($vval['aadhaar'] ?? ""))->first();
                                if(($vval['verify_status']??"") == '1'){
                                    $userListArray[] = ['service'=>ucfirst($detail->type ?? '-')." ".($detail->package->varient_name ?? '-'),'name'=> $vval['name'],"start_date"=>$vval['verify_date']??"","end_date"=>$vval['end_date']??""];
                                }
                                ?>
                                @if($getAddhar)
                                <div class="col-md-6">
                                    <div>
                                        <img src="{{$getAddhar['image']}}" alt="image" style="max-width: 66px !important; cursor: zoom-in;border-radius: 9px;" class="enlargeable-image" data-fullsize="{{ $getAddhar['image'] }}" data-number="{{ $getAddhar['aadhaar_number'] }}">
                                        <br><span>{{ $getAddhar['full_name'] }}</span>
                                        <br><span>{{ $getAddhar['aadhaar_number'] }}</span>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                                @endif

                        <?php }
                        } ?>
                        @endif
                    </div>
                </div>
                <div class="col-md-8">
                    @if(request('type') == 'all-order' || request('type') == 'single-order' || request('type') == 'puja-slip')
                    <?php if ($order && $order->details && count($order->details) > 0) {
                        $totalAmount = 0;
                        foreach ($order->details as $keys => $value) {
                            if (is_numeric($value['final_amount']) && request('type') == 'puja-slip') {
                                $totalAmount += $value['receipt_fee'];
                            } elseif (is_numeric($value['final_amount'])) {
                                $totalAmount += $value['final_amount'];
                            }
                        }
                    ?>

                        <h6 style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 5px;">{{ strtoupper($order->temple->name ?? 'TEMPLE NAME') }}</h6>
                        <div style="height:1px; background:#ccc; margin:10px 0;"></div>
                        <div class="flex-wrap justify-content-between" style="margin-bottom: 15px;">
                            <div class="d-flex mt-1">
                                <div style="font-weight: bold;">{{ translate('Order ID') }} :</div>
                                <div>{{ $order->order_id }}</div>
                            </div>
                            <div class="d-flex mt-1">
                                <div style="font-weight: bold;">{{ translate('Date') }} :</div>
                                <div>{{ $order->created_at->format('d M Y') }}</div>
                            </div>
                            <div class="d-flex mt-1">
                                <div style="font-weight: bold;">{{ translate('Total_Amount') }} :</div>
                                <div>{{ number_format(($totalAmount?? 0), 2) }}</div>
                            </div>
                            <div class="d-flex mt-1">
                                <div style="font-weight: bold;">{{ translate('Payment Mode') }} :</div>
                                <span>{{ ucfirst($order->payment_mode) }}</span>
                            </div>
                        </div>

                        <?php foreach ($order->details as $detail) { ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td><span style="font-weight: bold;">Service</span></td>
                                        <td><span style="font-weight: bold;">{{ translate('Yajman') }}</span></td>
                                        @if(strtolower($detail->type ?? '') == 'puja')
                                        <td><span style="font-weight: bold;">{{ translate('Purohit') }}</span></td>
                                        @endif
                                        <td><span style="font-weight: bold;">{{ translate('Payment') }}</span></td>
                                        <td><span style="font-weight: bold;">{{ translate('User') }}</span></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span>{{ ucfirst($detail->type ?? '-') }} ({{ $detail->package->varient_name ?? '-' }})</span></td>
                                        <td> <span>{{ $order->user->name ?? '-' }}</span></td>
                                        @if(strtolower($detail->type ?? '') == 'puja')
                                        <td><span>{{ $detail->purohit->name ?? '-' }}</span> </td>
                                        @endif
                                        <td>
                                            <?php
                                            if (request('type') == 'puja-slip') {
                                                $amount = number_format($detail->receipt_fee ?? 0, 2);
                                            } else {
                                                $amount = number_format($detail->final_amount ?? 0, 2);
                                            }
                                            ?>
                                            @php
                                            $mode = ucfirst($order->payment_mode);
                                            $status = strtolower($detail->booking_status);

                                            if ($status === 'confirmed') {
                                            $statusText = "<span style='color:green; font-weight:bold;'>Confirmed</span>";
                                            } elseif ($status === 'cancelled') {
                                            $statusText = "<span style='color:red; font-weight:bold;'>Cancelled</span>";
                                            } else {
                                            $statusText = "<span style='color:orange; font-weight:bold;'>Pending</span>";
                                            }
                                            @endphp
                                            <p>
                                                <span style="font-weight: bold;">{{ translate('Mode') }}</span>
                                                <span>: {{ $mode }}</span>
                                            </p>

                                            <p>
                                                <span style="font-weight: bold;">{{ translate('Status') }}</span>
                                                <span>
                                                    : (₹{{ $amount }}) {!! $statusText !!}
                                                </span>
                                            </p>
                                            @if(strtolower($detail->type ?? '') == 'locker')
                                            <hr>
                                            <?php $getDataList = json_decode($detail->locker_items ?? "[]", true);
                                            $getCustomerl = json_decode($detail->customers ?? "[]", true); ?>
                                            <p>
                                                <span style="font-weight: bold;">{{ translate('locker_status') }}</span>
                                                <span>: {{ ((($getCustomerl[0]['verify_status']??0) == 0) ? 'Pending' : ((($getCustomerl[0]['verifyend_status']??0) == 1) ? "Delivered" : "Received" ) ) }}</span>
                                            </p>
                                            <p>
                                                <span style="font-weight: bold;">{{ translate('Number_of_bag') }}</span>
                                                @if(($getCustomerl[0]['verify_status']??0) == 1)
                                                <span>: {{ $getDataList['luggage'] }}</span>
                                                @else
                                            <form class="form-save_dates">
                                                <input type="hidden" name="id" value="{{ $detail['id'] }}">
                                                <input type="hidden" name="type" value="luggage">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="value" placeholder="Enter Number of bag" value="{{ $getDataList['luggage'] }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" type="button" onclick="UpdateABages(this)">Add</button>
                                                    </div>
                                                </div>
                                            </form>
                                            @endif

                                            </p>
                                            <p>
                                                <span style="font-weight: bold;">{{ translate('Number_of_phone') }}</span>
                                                @if(($getCustomerl[0]['verify_status']??0) == 1)
                                                <span>: {{ $getDataList['mobile'] }}</span>
                                                @else
                                            <form class="form-save_dates">
                                                <input type="hidden" name="id" value="{{ $detail['id'] }}">
                                                <input type="hidden" name="type" value="mobile">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="value" placeholder="Enter Number of phone" value="{{ $getDataList['mobile'] }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" type="button" onclick="UpdateABages(this)">Add</button>
                                                    </div>
                                                </div>
                                            </form>
                                            @endif
                                            </p>
                                            @endif
                                        </td>
                                        <td>
                                            @if($detail->customers && json_decode($detail->customers, true))
                                            <!-- Select All for this detail -->
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input select-all"
                                                    data-detail-id="{{ $detail->id }}"
                                                    id="select_all_{{ $detail->id }}">
                                                <label class="form-check-label" for="select_all_{{ $detail->id }}">
                                                    <strong>Select All</strong>
                                                </label>
                                            </div>
                                            <hr style="margin: 5px 0;">
                                            @foreach(json_decode($detail->customers, true) as $index => $vval)
                                            <div class="form-check">
                                                <?php $getVerify_status = ($vval['verify_status'] ?? 0);
                                                if ($getVerify_status == 0) {
                                                    $showButtonVerify = true;
                                                }elseif (($vval['verify_status'] ?? 0) == 1 && ($vval['verifyend_status'] ?? 0) == 0 && strtolower($detail->type ?? '') == 'locker') {
                                                    $showButtonVerify = true;
                                                }
                                                $getVerifyend_status = ($vval['verifyend_status'] ?? 0); ?>
                                                @if(($getVerify_status) == 0 || ($getVerifyend_status == 0 && strtolower($detail->type ?? '') == 'locker'))
                                                <input type="checkbox" class="form-check-input user-checkbox"
                                                    name="selected_users[{{ $detail->id }}][]"
                                                    value="{{ $index }}"
                                                    data-detail-id="{{ $detail->id }}"
                                                    id="user_{{ $detail->id }}_{{ $index }}">
                                                @endif
                                                <label class="form-check-label {{ ((($getVerify_status) == 1)?'text-success font-weight-bolder':'') }}" for="user_{{ $detail->id }}_{{ $index }}">
                                                    {{ $vval['name'] }}
                                                    <?php $getAddhar = \App\Models\UserAadhaarKyc::where('aadhaar_number', ($vval['aadhaar'] ?? ""))->first(); ?>
                                                    @if($getAddhar)
                                                    <img src="{{$getAddhar['image']}}" alt="image" style="max-width: 12px !important; cursor: zoom-in;" class="enlargeable-image" data-fullsize="{{ $getAddhar['image'] }}" data-number="{{ $getAddhar['aadhaar_number'] }}">
                                                    @endif
                                                </label>
                                            </div>
                                            @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                    <?php }
                    } ?>
                    @endif
                </div>
                @if($showButtonVerify)
                <div class="col-md-12">
                    <button type="button" class="float-end btn btn-success" onclick="verifyUsers()">Verify Update</button>
                </div>
                @endif
            </div>
            @if($userListArray)
            <div class="row mt-3">
                <div class="col-md-12">
                    <label for="">Verify Customer List</label>
                </div>
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>SNo.</th>
                                <th>Service Name</th>
                                <th>User Name</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userListArray as $vuser)
                            <tr>
                                <td>{{ $loop->iteration}}</td>
                                <td>{{ $vuser['service']}}</td>
                                <td>{{ $vuser['name']}}</td>
                                <td>
                                    <span>{{$vuser['start_date']}}</span><br>
                                    @if(strtolower($vuser['service']??"") == 'locker')<span>{{ $vuser['end_date'] }}</span>@endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<div id="lightbox" class="lightbox">
    <span class="close-btn" onclick="closeLightbox()">&times;</span>
    <div class="lightbox-content">
        <img id="lightbox-img" src="" alt="Full size image">
        <span id="aadharnumbers" class="text-white mt-2"></span>
    </div>
</div>
@endsection


@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/html5-qrcode.min.js')}}"></script>
<script>
    let scanner;

    function restartScanner() {
        if (scanner) {
            scanner.clear().then(() => {
                document.getElementById('reader').innerHTML = '';
                initScanner();
            }).catch(err => {
                console.error("Clear scanner error:", err);
            });
        } else {
            initScanner();
        }
    }

    function initScanner() {
        scanner = new Html5QrcodeScanner('reader', {
            qrbox: {
                width: 250,
                height: 250
            },
            fps: 20,
        });
        scanner.render(onScanSuccess, onScanError);
        const el = document.getElementById("result");
        while (el.firstChild) {
            el.removeChild(el.firstChild);
        }
    }

    function onScanSuccess(result) {
        window.location.href = result;
    }

    function onScanError(err) {
        console.log("Scan error:", err);
    }

    // Start the scanner on load
    document.addEventListener("DOMContentLoaded", function() {
        restartScanner();
    });

    const selectedUsers = [];
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.select-all').forEach(selectAllCheckbox => {
            selectAllCheckbox.addEventListener('change', function() {
                const detailId = this.getAttribute('data-detail-id');
                const checkboxes = document.querySelectorAll(`.user-checkbox[data-detail-id="${detailId}"]`);

                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    if (this.checked) {
                        addToSelectedUsers(checkbox);
                    } else {
                        removeFromSelectedUsers(checkbox);
                    }
                });
            });
        });

        document.querySelectorAll('.user-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const detailId = this.getAttribute('data-detail-id');
                const checkboxes = document.querySelectorAll(`.user-checkbox[data-detail-id="${detailId}"]`);
                const selectAll = document.querySelector(`.select-all[data-detail-id="${detailId}"]`);

                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                selectAll.checked = allChecked;
                selectAll.indeterminate = anyChecked && !allChecked;

                if (this.checked) {
                    addToSelectedUsers(this);
                } else {
                    removeFromSelectedUsers(this);
                }
            });
        });

        function addToSelectedUsers(checkbox) {
            const userId = checkbox.value;
            const detailId = checkbox.getAttribute('data-detail-id');
            const userName = checkbox.nextElementSibling.textContent.trim();

            const userObject = {
                order_id: detailId,
                user_id: userId,
                username: userName,
            };
            const exists = selectedUsers.some(user =>
                user.detailId === detailId && user.userId === userId
            );

            if (!exists) {
                selectedUsers.push(userObject);
            }
        }

        function removeFromSelectedUsers(checkbox) {
            const userId = checkbox.value;
            const detailId = checkbox.getAttribute('data-detail-id');

            selectedUsers = selectedUsers.filter(user =>
                !(user.detailId === detailId && user.userId === userId)
            );
        }
    });


    function verifyUsers() {
        $.ajax({
            url: "{{ route('trustees-vendor.recepit-management.verify-service-update-status') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user: JSON.stringify(selectedUsers),
            },
            success: function(data) {
                toastr.success(data.message);
                if (data.success) {
                    window.location.href = ``;
                }

            }
        })
    }

    document.querySelectorAll('.enlargeable-image').forEach(img => {
        img.addEventListener('click', function() {
            const fullSizeUrl = this.getAttribute('data-fullsize');
            document.getElementById('aadharnumbers').innerHTML = '';
            const aadharnumbers = this.getAttribute('data-number');
            document.getElementById('aadharnumbers').innerHTML = aadharnumbers;
            document.getElementById('lightbox-img').src = fullSizeUrl;
            document.getElementById('lightbox').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });

    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
    }

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });

    // Close on background click
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });

    function UpdateABages(that) {
        let form = $(that).closest('.form-save_dates');
        let id = form.find('input[name="id"]').val();
        let type = form.find('input[name="type"]').val();
        let value = form.find('input[name="value"]').val();

        if (value === '') {
            toastr.error('Please enter a value');
            return;
        }
        $.ajax({
            url: "{{ route('trustees-vendor.recepit-management.order-luggage-phone-update') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                type: type,
                value: value
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Updated successfully!');
                } else {
                    toastr.error('Update failed!');
                }
            },
            error: function(xhr) {
                toastr.error('Something went wrong!');
            }
        });
    }
</script>

@endpush