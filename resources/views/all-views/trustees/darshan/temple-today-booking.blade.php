@extends('layouts.back-end.app-trustees')
@section('title', translate('VIP_darshan_Booking'))
@php
use App\Utils\Helpers;
@endphp
@push('css_or_js')
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

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

    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }


    .bg-label-primary {
        background-color: #007bff;
        color: #fff;
    }

    .bg-label-primary:hover {
        background-color: #0056b3;
    }

    .bg-label-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .bg-label-danger:hover {
        background-color: #c82333;
    }

    .bg-label-success {
        background-color: #28a745;
        color: #fff;
    }

    .bg-label-success:hover {
        background-color: #218838;
    }

    .bg-label-info {
        background-color: #17a2b8;
        color: #fff;
    }

    .bg-label-info:hover {
        background-color: #117a8b;
    }

    .bg-label-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .bg-label-warning:hover {
        background-color: #e0a800;
    }

    .dropdown-menufollow {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        padding: 1rem;
        width: 225px;
        margin-right: 13rem;
        text-align: center;
        display: flex;
        gap: 0.5rem;
        position: absolute;
    }

    .d-flex {
        display: flex;
    }

    .justify-content-center {
        justify-content: center;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .myactionbtn {
        width: 1.625rem !important;
        height: 1.625rem !important;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
            {{ translate('VIP_darshan_Booking') }}
            <span class="badge badge-soft-dark radius-50 fz-14">{{ ($getData->total()??'') }}</span>
        </h2>
    </div>

    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-sm-6 col-md-6 col-lg-4">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" 
                                        type="search" 
                                        name="searchValue" 
                                        class="form-control"
                                        placeholder="{{ translate('search_by_name') }}"
                                        aria-label="{{ translate('search_by_name') }}"
                                        value="{{ request('searchValue') }}" 
                                        required>
                                    <button type="submit"
                                        class="btn btn--primary input-group-text">
                                        {{ translate('search') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'scanner'))
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div id="reader" class="rounded border p-2"></div>
                                <div id="result" class="mt-2"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-md-12">
                            <table id="QrDataTable" class="table table-striped table-bordered table-hover">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('User_Info') }}</th>
                                        <th>{{ translate('Temple_Name') }}</th>
                                        <th>{{ translate('package_Info') }}</th>
                                        <th>{{ translate('date') }}/{{ translate('time_slot') }}</th>
                                        <th>{{ translate('Trust_name') }}</th>
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'price'))
                                        <th>{{ translate('Price') }}</th>
                                        @endif
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'gst'))
                                        <th>{{ translate('gst') }}</th>
                                        @endif
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'admin commission'))
                                        <th>{{ translate('admin_commission') }}</th>
                                        @endif
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'final amount'))
                                        <th>{{ translate('final_amount') }}</th>
                                        @endif
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'details'))
                                        <th class="text-center"> {{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                @if($getData && count($getData) > 0)
                                <tbody>
                                    @foreach($getData as $key=>$val)
                                    <tr>
                                        <td>{{ $getData->firstItem()+$key }}</td>
                                        <td>
                                            <small>{{ ($val['userData']['name']??"") }}</small><br>
                                            <small>{{ ($val['userData']['phone']??"") }}</small><br>
                                            <small>{{ date('d M,Y h:i A',strtotime($val['created_at']??"")) }}</small><br>
                                            <small>Total No.: {{ $val['total_counts']??""}}</small><br>
                                            <small>Available No.:{{ $val['verified_count']??""}} </small><br>
                                            <small>No Available No.:{{ $val['not_verified_count']??""}} </small><br>
                                        </td>
                                        <td><span data-toggle="tooltip" data-title="{{ (($val['Temple']) ? ($val['Temple']['name']??''):'' )}}" data-placement="left">{{ (($val['Temple']) ? Str::Limit($val['Temple']['name']??'',20):"" )}}</span></td>
                                        <td>
                                            <span>{{ ($val['title']??"") }}</span><br>
                                            <span>{{ ($val['package_name']??"") }}</span><br>
                                        </td>
                                        <td>
                                            <span>Date : {{ date('d M,Y',strtotime($val['date']??"")) }}</span><br>
                                            <span>Slot : {{ ($val['time']??"") }}</span><br>
                                        </td>
                                        <td><span class="font-weight-bolder">{{ (($val['Temple']) ? ($val['Temple']->matchingTrust()['trust_name']??"") : '') }}</span></td>
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'price'))
                                        <td>{{ ($val['price']??"") }}</td>
                                        @endif
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'gst'))
                                        <td>{{ ($val['gst_amount']??"") }}</td>
                                        @endif
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'admin commission'))
                                        <td>{{ ($val['admin_commission']??"") }}</td>
                                        @endif
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'final amount'))
                                        <td>{{ ($val['final_amount']??"") }}</td>
                                        @endif
                                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'details'))
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('trustees-vendor.vip-darshan.darshan-booking-information',['id'=>$val['id']]) }}" class="btn btn-outline-info btn-sm"><i class="tio-invisible"></i></a>
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
              
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/html5-qrcode.min.js')}}"></script>

<script>
    let scanner; // Use 'let' to allow reassignment

    function restartScanner() {
        // Recreate scanner if needed
        if (scanner) {
            scanner.clear().then(() => {
                document.getElementById('reader').innerHTML = ''; // clear DOM
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
        $.ajax({
            url: "{{ route('trustees-vendor.vip-darshan.check-member-valid') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                barcode: result,
                "type": 'not'
            },
            success: function(data) {
                if (data.success == 1) {
                    let UserInfo = data.data;
                    document.getElementById('result').innerHTML = `
                        <div class="card-body">
                                <div class="row">
                                    <div class="col-12" style="font-size: 14px;">User Name: ${UserInfo.name}</div>
                                    <div class="col-12" style="font-size: 14px;">User Phone: ${UserInfo.phone}</div>
                                    <div class="col-12" style="font-size: 14px;">User Aadhar: ${UserInfo.aadhar}</div>
                                    <div class="col-12 " style="font-size: 14px;">Verify Status: <span class="font-weight-bolder text-${(UserInfo.verify == 0 ? "info" : "success")}" style="font-size: 23px;">${(UserInfo.verify == 0 ? "verify" : "already verified")}</span></div>
                                    <button type="submit" name="submit" class="btn btn-outline-primary btn-sm" onclick="${(UserInfo.verify == 0 ? `verifyUsers('${result}')` : "initScanner()")} ">${(UserInfo.verify == 0 ? `Verify Now` : "🔄 Re-Setup")}</button>
                                </div>       
                        </div>
                    `;
                } else {
                    document.getElementById('result').innerHTML = `
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 font-weight-bolder text-danger my-2" style="font-size: 14px;">Invalid Pass</div>
                                <div class="col-12">
                                    <a class="btn btn-outline-primary btn-sm" onclick="initScanner()">🔄 Re-Setup</a>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Stop scanner
                scanner.clear().then(() => {
                    document.getElementById('reader').innerHTML = ''; // clear camera box
                }).catch(err => {
                    console.error("Failed to clear scanner", err);
                });
            },
            error: function(xhr) {
                console.error("AJAX error:", xhr);
            }
        });
    }

    function onScanError(err) {
        console.warn("Scan error:", err);
    }

    // Start the scanner on load
    document.addEventListener("DOMContentLoaded", function() {
        restartScanner();
    });

    function verifyUsers(barcode) {
        $.ajax({
            url: "{{ route('trustees-vendor.vip-darshan.check-member-valid') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                barcode: barcode,
                "type": 'verify'
            },
            success: function(data) {
                initScanner()
                toastr.success('verify Successfully');
            }
        })
    }
    $(document).ready(function() {
        let table = $('#QrDataTable').DataTable({
            pageLength: 10,
            scrollY: '500px',
            scrollCollapse: true,
            paging: true,
            fixedHeader: true,
            fixedFooter: true,
            lengthMenu: [
                [5, 10, 25, -1],
                [5, 10, 25, "All"]
            ],
        });
    });
</script>


@endpush