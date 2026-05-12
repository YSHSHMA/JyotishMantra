@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app-event')

@section('title', translate('booking_verification'))
@push('css_or_js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<style>
    #reader {
        width: 300px;
        border: 2px solid #333;
        border-radius: 10px;
        padding: 10px;
        background: white;
    }

    video {
        width: 100% !important;
    }

    #result {
        margin-top: 20px;
        text-align: center;
        font-weight: bold;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('booking_verification') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-6 mb-2">
            <select id="cameraSelect" class="form-control"></select><br>
            <div id="reader"></div>
        </div>
        <div class="col-md-6">
            @if(isset($orderId) && !empty($orderId))
            <table class="table">
                <tr>
                    <td>Event Name</td>
                    <td><span class="font-weight-bolder">{{ $orderId['eventid']->getRawOriginal('event_name')??'' }}</span></td>
                </tr>
                <tr>
                    <td>Venue</td>
                    <td>
                        @php
                        $venueData = json_decode($orderId['eventid']['all_venue_data']??'[]', true);
                        $todayVenue = collect($venueData)->firstWhere('id',$orderId['venue_id']);
                        @endphp
                        <span class="font-weight-bolder">{{ ($todayVenue['en_event_venue'])??""}}</span><br>
                        <span>{{ ($todayVenue['en_event_venue_full_address']??'' )}}</span>
                    </td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td><span class="font-weight-bolder">{{ date("d M,Y",strtotime($todayVenue['date']??'') )}} {{ ($todayVenue['start_time']??'' )}}</span></td>
                </tr>
                <tr>
                    <td>Order Id</td>
                    <td><span class="font-weight-bolder">{{ $orderId['order_no'] }}</span></td>
                </tr>
                <?php $getUserData = collect(json_decode($orderId['orderitem'][0]['user_information'] ?? '[]', true));
                $id2Data = $getUserData->firstWhere('id', $member_number);
                ?>
                @if($id2Data && $id2Data['verify'] == 0)
                <tr>
                    <td>Member No.</td>
                    <td><span class="font-weight-bolder">{{ $member_number }}</span></td>
                </tr>
                <tr>
                    <td>Name.</td>
                    <td><span class="font-weight-bolder">{{ $id2Data['name']??"" }} </span></td>
                </tr>
                <tr>
                    <td>Phone No.</td>
                    <td><span class="font-weight-bolder">{{ $id2Data['phone']??"" }}</span></td>
                </tr>
                <tr>
                    <td>Aadhar No.</td>
                    <td><span class="font-weight-bolder">{{ $id2Data['aadhar']??"" }}</span></td>
                </tr>
                @elseif($id2Data && $id2Data['verify'] == 1)
                <tr>
                    <td colspan="2" class="text-center"><span class="font-weight-bolder text-success">already verified User</span></td>
                </tr>
                @else
                <tr>
                    <td colspan="2" class="text-center"><span class="font-weight-bolder text-danger">Invalid User</span></td>
                </tr>
                @endif
                @if($id2Data && $id2Data['verify'] == 0)
                <tr>
                    <td colspan="2">
                        @if (Helpers::Employee_modules_permission('Qr Management', 'Qr Verify', 'Scan'))
                        <form action="{{ route('event-vendor.qr-code-verify.verify',['id'=>$orderId['id'],'num'=>$member_number??''] )}}" method="post" id="items-status{{$orderId['id']}}-form" data-from="default-withdraw-method-status">
                            @csrf
                            <input type="hidden" name="id" value="{{$orderId['id']}}">
                            <input type="hidden" name="verify" value="{{ $id2Data['verify']??'' }}">
                            <label class="switcher mx-auto w-100">
                                <input type="button" class="toggle-switch-message btn btn-success w-100" value="Verify"
                                    id="items-status{{ $orderId['id'] }}"
                                    data-modal-id="toggle-status-modal"
                                    data-toggle-id="items-status{{ $orderId['id'] }}"
                                    data-on-image="items-status-on.png"
                                    data-off-image="items-status-off.png"
                                    data-on-title="Already Verified"
                                    data-off-title="User Verification Confirmation"
                                    data-on-message="<p>This user has already been verified. No further action is required</p>"
                                    data-off-message="<p>Are you sure you want to verify this user? This action will confirm their participation</p>">
                            </label>
                        </form>
                        @endif
                    </td>
                </tr>
                @endif
            </table>
            @endif
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 my-3">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                                    <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-8"></div>
                        <div class="col-12 form-group">
                            <div class="text-start">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                        <thead class="thead-light thead-50 text-capitalize">
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('ID') }}</th>
                                                <th>{{ translate('event_name') }} </th>
                                                <th>{{ translate('User_info') }} </th>
                                                <th>{{ translate('action') }}</th>
                                            </tr>
                                        </thead>
                                        @if(!empty($getOrderList) && count($getOrderList) > 0)
                                        <tbody>
                                            @foreach($getOrderList as $key => $items)
                                            <tr>
                                                <td>{{$getOrderList->firstItem()+$key}}</td>
                                                <td><a class='font-weight-bold text-secondary'>{{ ($items['order_no']??"") }}</a></td>
                                                <td>
                                                    @php
                                                    $venueData = json_decode($items['eventid']['all_venue_data']??'[]', true);
                                                    $todayVenue = collect($venueData)->firstWhere('id', $items['venue_id']);
                                                    @endphp
                                                    <div>
                                                        <strong>Event Name:</strong><span role='tooltip' data-toggle="tooltip" data-placement="left" title="{{ ($items['eventid']['event_name']??'') }}">{{ Str::Limit(($items['eventid']['event_name']??''),30) }}</span><br>
                                                        <strong>Venue:</strong> <span role='tooltip' data-toggle="tooltip" data-placement="left" data-title="{{ $todayVenue['en_event_venue'] ?? 'N/A' }}">{{ Str::limit(($todayVenue['en_event_venue'] ?? 'N/A'),25) }}</span><br>
                                                        <strong>Venue full:</strong> <span role='tooltip' data-toggle="tooltip" data-placement="left" data-title="{{ $todayVenue['en_event_venue_full_address'] ?? 'N/A' }}">{{ Str::limit(($todayVenue['en_event_venue_full_address'] ?? 'N/A'),25) }}</span><br>
                                                        <strong>Date:</strong> {{ $todayVenue['date'] ?? 'N/A' }} <br>
                                                        <strong>start Time:</strong> {{ $todayVenue['start_time'] ?? 'N/A' }} <br>
                                                        <strong>End Time:</strong> {{ $todayVenue['end_time'] ?? 'N/A' }} <br>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>Customer Name</strong>:<span>{{ ($items['userdata']['name']??"") }}</span><br>
                                                        <strong>Phone</strong>:<span>{{ ($items['userdata']['phone']??"") }}</span><br>
                                                        <strong>Email</strong>:<span>{{ ($items['userdata']['email']??"") }}</span><br>
                                                        <hr class="m-0">
                                                        <hr class="m-0">
                                                        <strong>Total Booking</strong>:<span>{{ ($items['orderitem'][0]['no_of_seats']??"") }}</span><br>
                                                        <strong>attendees</strong>:<span>{{ collect(json_decode($items['orderitem'][0]['user_information'] ?? '[]', true))->where('verify', 1)->count(); }}</span><br>
                                                    </div>
                                                </td>
                                                <td><a class="btn btn-outline-info" onclick="$('.modelopen_{{$key}}').modal()"><i class="tio-invisible"></i></a>
                                                    <div class="modal modelopen_{{$key}}" tabindex="-1">
                                                        <div class="modal-dialog modal-xl">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">{{ ($items['eventid']['event_name']??'') }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12 table-responsive">
                                                                            <table class="table">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <td>Id</td>
                                                                                        <td>Name</td>
                                                                                        <td>phone</td>
                                                                                        <td>Aadhar</td>
                                                                                        <td>Time</td>
                                                                                        <td>verify</td>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @if(!empty(($items['orderitem'][0]['user_information']??'')) && json_decode($items['orderitem'][0]['user_information'] ?? '[]', true) )
                                                                                    @foreach(json_decode($items['orderitem'][0]['user_information'] ?? '[]', true) as $people)
                                                                                    <tr>
                                                                                        <td>{{ $loop->index + 1 }}</td>
                                                                                        <td>{{ $people['name']??"" }}</td>
                                                                                        <td>{{ $people['phone']??"" }}</td>
                                                                                        <td>{{ $people['aadhar']??"" }}</td>
                                                                                        <td>{{ $people['time']??'' }}</td>
                                                                                        <td><i class="tio-{{ ((($people['verify']??0) == 1)?'done':'clear') }} text-{{ ((($people['verify']??0) == 1)?'success':'danger') }} text-weight-bolder" style="font-size: 27px;"></i></td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                    @endif
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive mt-4">
                                <div class="d-flex justify-content-lg-end">
                                    {!! $getOrderList->links() !!}
                                </div>
                            </div>
                            @if(count($getOrderList) == 0)
                            <div class="text-center p-4">
                                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<span id="get-withdrawal-method-default-text" data-success="Qr Scan Success" data-error="Qr Scan failed">
    @endsection

    @push('script')
    <!-- Include SweetAlert2 for confirmation dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        const html5QrCode = new Html5Qrcode("reader");
        let currentCameraId = null;
        Html5Qrcode.getCameras()
            .then(devices => {
                if (!devices || !devices.length) {
                    throw new Error("No cameras found");
                }

                const select = document.getElementById("cameraSelect");
                devices.forEach((device, idx) => {
                    const option = document.createElement("option");
                    option.value = device.id;
                    option.text = device.label || `Camera ${idx + 1}`;
                    select.appendChild(option);
                });

                currentCameraId = select.value;
                startScanner(currentCameraId);

                select.addEventListener("change", () => {
                    const newCameraId = select.value;
                    if (newCameraId === currentCameraId) return;

                    html5QrCode.stop()
                        .then(() => {
                            currentCameraId = newCameraId;
                            startScanner(currentCameraId);
                        })
                        .catch(err => console.error("Failed to stop scanner:", err));
                });
            })
            .catch(err => console.error("Camera access error:", err));


        function startScanner(cameraId) {
            html5QrCode
                .start(
                    cameraId, {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    },
                    qrMessage => {
                        window.location.href = qrMessage;
                    },
                    errorMessage => {}
                )
                .catch(err => console.error("Unable to start scanner:", err));
        }
    </script>
    <script>
        $('#toggle-status-modal-ok-button').on('click', function() {
            const toggleId = $('#' + $(this).attr('toggle-ok-button'));
            if (toggleId.is(':checked')) {
                toggleId.prop('checked', false);
            } else {
                toggleId.prop('checked', true);
            }
            let toggleOkButton = $(this).attr('toggle-ok-button') + '-form';
            submitStatusUpdateForm2(toggleOkButton);
        });

        function submitStatusUpdateForm2(formId) {
            const form = $('#' + formId);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(data) {
                    let defaultWithdrawMethodMessage = $('#get-withdrawal-method-default-text');
                    if (data.success) {
                        toastr.success(defaultWithdrawMethodMessage.data('success'));
                    } else {
                        toastr.error(defaultWithdrawMethodMessage.data('error'));
                    }
                    location.reload();
                }
            });
        }
    </script>
    @endpush