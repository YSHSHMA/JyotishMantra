<div class="card-body">
    <div class="d-flex flex-wrap gap-3 justify-content-between">
        <div class="media flex-column flex-sm-row gap-3">
            @if($view_type == 1)
            <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['image'], type: 'backend-product') }}" alt="Image">
            @else
            <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['image'], type: 'backend-product') }}" alt="Image">
            @endif
            <div class="media-body">
                <div class="d-block">
                    @if($view_type == 1)
                    <h2 class="mb-2 pb-1">{{ ($getData['owner_name']??"") }} ({{ ($getData['traveller_id']??"") }})</h2>
                    @else
                    <h2 class="mb-2 pb-1">{{ ($getData['owner_name']??"") }} ({{ ($getData['traveller_id']??"") }})</h2>
                    @endif
                    <div class="d-flex gap-3 flex-wrap mb-3 lh-1">
                        <div class="review-hover position-relative cursor-pointer font-weight-bold gap-2 align-items-center">
                            <a class="text-dark">order cancellation count: {{ $getData['cancel_order']}}</a>
                        </div>
                    </div>
                    <div class="d-flex gap-3 flex-wrap mb-3 lh-1">
                        <div class="review-hover position-relative cursor-pointer font-weight-bold gap-2 align-items-center">
                            <a class="text-dark">Profile Status : {{ ucwords(\App\Models\Seller::where(['type' => 'tour', 'relation_id' => ($getData['id'] ?? '')])->first()['status'] ?? "") }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="">
            <div class="d-flex justify-content-start gap-2">
                {{--

                @if(($getData['is_approve'] == 0) || ($getData['is_approve'] == 2))
                <span class="btn btn-outline-success btn-sm square-btn reject-artist_data" data-title="Approve Company" title="Approve Company" data-id="approve-{{ ($getData['id']??'')}}">
                <i class="tio-accept">checkmark_circle_outlined</i>
                </span>
                @endif
                @if(($getData['is_approve'] == 0) || ($getData['is_approve'] == 1))
                <span class="btn btn-outline-danger btn-sm square-btn reject-artist_data" data-title="Reject Company" title="Reject Company" data-id="reject-{{ ($getData['id']??'')}}">
                    <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                </span>
                @endif

                @if(($getData['is_approve'] == 0) && ($getData['is_approve'] == 0) && (\App\Models\Seller::where(['type'=>'tour','relation_id'=>($getData['id']??'')])->first()['status']??"") == 'pending')
                <span class="btn btn-outline-info btn-sm square-btn reject-artist_data" data-title="Only login" title="Only login" data-id="onlyapproved-{{ ($getData['id']??'')}}">
                    <i class="tio-sign-in"></i>
                </span>
                @endif

                @if(($getData['is_approve'] == 0) && ($getData['is_approve'] == 0) && (\App\Models\Seller::where(['type'=>'tour','relation_id'=>($getData['id']??'')])->first()['status']??"") == 'hold')
                <span class="btn btn-outline-success btn-sm square-btn reject-artist_data" data-title="Approve asdd" title="Approve" data-id="onlyapproved-{{ ($getData['id']??'')}}">
                    <i class="tio-sign-in"></i>
                </span>
                @endif
                --}}

                <?php
                $getseller_status = (\App\Models\Seller::where(['type' => 'tour', 'relation_id' => ($getData['id'] ?? '')])->first()['status'] ?? "");
                ?>

                @if(\App\Models\Seller::where(['type' => 'tour', 'relation_id' => $getData['id'],'update_seller_status' => 3])->exists())
                <span class="btn btn-outline-info btn-sm square-btn reject-artist_data" data-title="Profile Update Approve" title="Profile Update Approve" data-id="proUpdateApprove-{{ ($getData['id']??'')}}">
                    <i class="tio-marker_outlined">marker_outlined</i>
                </span>
                <form action="{{ route('admin.tour_and_travels.traveller-company-status',[$getData['id']])}}" method="post" id="proUpdateApprove-{{ ($getData['id']??'')}}">
                    @csrf
                    <input type="hidden" name="status" value="2">
                    <input type="hidden" name="type" value="tour">
                    <input type="hidden" name="tour_id" value="{{$getData['id']}}">
                    <input type="hidden" name="seller_status" value="update_profile_update">
                </form>
                @endif
                @if($getseller_status == 'approved')
                <span class="btn btn-outline-secondary btn-sm square-btn reject-artist_data" data-title="Hold Company" title="Hold Company" data-id="hold-{{ ($getData['id']??'')}}">
                    <i class="tio-hand_basic">hand_basic</i>
                </span>
                <form action="{{ route('admin.tour_and_travels.traveller-company-status',[$getData['id']])}}" method="post" id="hold-{{ ($getData['id']??'')}}">
                    @csrf
                    <input type="hidden" name="status" value="3">
                    <input type="hidden" name="type" value="tour">
                    <input type="hidden" name="tour_id" value="{{$getData['id']}}">
                    <input type="hidden" name="seller_status" value="hold">
                </form>
                @endif

                @if($getseller_status == 'hold' || $getseller_status =='pending')
                <span class="btn btn-outline-success btn-sm square-btn reject-artist_data" data-title="Approve Company" title="Approve Company" data-id="approve-{{ ($getData['id']??'')}}">
                    <i class="tio-accept">checkmark_circle_outlined</i>
                </span>
                <form action="{{ route('admin.tour_and_travels.traveller-company-status',[$getData['id']])}}" method="post" id="approve-{{ ($getData['id']??'')}}">
                    @csrf
                    <input type="hidden" name="status" value="1">
                    <input type="hidden" name="type" value="tour">
                    <input type="hidden" name="tour_id" value="{{$getData['id']}}">
                    <input type="hidden" name="seller_status" value="approved">
                </form>
                @endif
                @if($getseller_status == 'approved' || $getseller_status == 'hold' || $getseller_status =='pending')
                <span class="btn btn-outline-danger btn-sm square-btn reject-artist_data" data-title="Suspended Company" title="Suspended Company" data-id="reject-{{ ($getData['id']??'')}}">
                    <i class="tio-clear_circle_outlined">clear_circle_outlined</i>
                </span>
                <form action="{{ route('admin.tour_and_travels.traveller-company-status',[$getData['id']])}}" method="post" id="reject-{{ ($getData['id']??'')}}">
                    @csrf
                    <input type="hidden" name="status" value="2">
                    <input type="hidden" name="type" value="tour">
                    <input type="hidden" name="tour_id" value="{{$getData['id']}}">
                    <input type="hidden" name="seller_status" value="suspended">
                </form>
                @endif
            </div>
        </div>
    </div>
    <hr>
    <div class="d-flex gap-3 flex-wrap flex-lg-nowrap">
        <div class="border p-3 w-170">
            <div class="d-flex flex-column mb-1">
                <h6 class="font-weight-normal">Total Tours :</h6>
                <h3 class="text-primary fs-18"> 
                    @php
                    echo (\App\Models\TourVisits::where(['created_id'=>$getData['id']])->count());
                    @endphp</h3>
            </div>           
        </div>
        <div class="row">
            <div class="col-sm-12 mb-2">
                <div class="row">
                    <div class="col-md-8 p-0 pl-2">
                        <h4 class="mb-3 text-capitalize">{{ translate('traveler_information') }} </h4>
                        <div class="pair-list">
                            <div>
                                <span class="key">{{ translate('Company_name') }} </span>
                                <span>:</span>
                                @if($view_type == 1)
                                <span class="value text-capitalize">{{ ($getData['company_name']??'')}}</span>
                                @else
                                <span class="value text-capitalize">{{ ($getData['organizers']['company_name']??'')}}</span>
                                @endif
                            </div>
                            <div>
                                <span class="key">{{ translate('owner_name') }} </span>
                                <span>:</span>
                                @if($view_type == 1)
                                <span class="value text-capitalize">{{ ($getData['owner_name']??'')}}</span>
                                @else
                                <span class="value text-capitalize">{{ ($getData['organizers']['owner_name']??'')}}</span>
                                @endif
                            </div>
                            <div>
                                <span class="key">{{ translate('phone') }} </span>
                                <span>:</span>

                                @if($view_type == 1)
                                <span class="value text-capitalize">{{ ($getData['phone_no']??'')}}</span>
                                @else
                                <span class="value text-capitalize">{{ ($getData['organizers']['phone_no']??'')}}</span>
                                @endif
                            </div>
                            <div>
                                <span class="key">{{ translate('address') }} </span>
                                <span>:</span>

                                @if($view_type == 1)
                                <span class="value text-capitalize">{{ ($getData['address']??'')}}</span>
                                @else
                                <span class="value text-capitalize">{{ ($getData['organizers']['address']??'')}}</span>
                                @endif
                            </div>
                            <div>
                                <span class="key">{{ translate('web_link') }} </span>
                                <span>:</span>

                                @if($view_type == 1)
                                <span class="value text-capitalize">{{ ($getData['web_site_link']??'')}}</span>
                                @else
                                <span class="value text-capitalize">{{ ($getData['organizers']['web_site_link']??'')}}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 p-0">

                        <h4 class="mb-3 text-capitalize">{{ translate('Person_information') }} </h4>
                        <div class="pair-list">
                            <div>
                                <span class="key">{{ translate('name') }} </span>
                                <span>:</span>
                                <span class="value text-capitalize">{{ ($getData['person_name']??'')}}</span>
                            </div>
                            <div>
                                <span class="key">{{ translate('phone') }} </span>
                                <span>:</span>
                                <span class="value text-capitalize">{{ ($getData['person_phone']??'')}}</span>
                            </div>
                            <div>
                                <span class="key">{{ translate('email') }} </span>
                                <span>:</span>
                                <span class="value text-capitalize">{{ ($getData['person_email']??'')}}</span>
                            </div>
                            <div>
                                <span class="key">{{ translate('address') }} </span>
                                <span>:</span>
                                <span class="value text-capitalize">{{ ($getData['person_address']??'')}}</span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-xxl-6">
                <div class="bg-light p-3 border border-primary-light rounded">
                    <h4 class="mb-3 text-capitalize">
                        {{ translate('bank_information') }} <a onclick="$('#bankModal_traveller').modal('show')">View All Bank</a>
                    </h4>

                    <div class="d-flex gap-5">
                        <div class="pair-list">
                            <div>
                                <span class="key text-nowrap">{{ translate('bank_Name') }}</span>
                                <span class="px-2">:</span>
                                @if($view_type == 1)
                                <span class="value ">{{ ($getData['bank_name']??'')}}</span>
                                @else
                                <span class="value ">{{ ($getData['organizers']['bank_name']??'')}}</span>
                                @endif
                            </div>
                            <div>
                                <span class="key text-nowrap">{{ translate('IFSC') }}</span>
                                <span class="px-2">:</span>
                                @if($view_type == 1)
                                <span class="value ">{{ ($getData['ifsc_code']??'')}}</span>
                                @else
                                <span class="value">{{ ($getData['organizers']['ifsc_code']??'')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="pair-list">
                            <div>
                                <span class="key text-nowrap">{{ translate('Holder_name ') }}</span>
                                <span class="px-2">:</span>
                                @if($view_type == 1)
                                <span class="value ">{{ ($getData['bank_holder_name']??'')}}</span>
                                @else
                                <span class="value">{{ ($getData['organizers']['bank_holder_name']??'')}}</span>
                                @endif
                            </div>

                            <div>
                                <span class="key text-nowrap">{{ translate('A/C_No') }}</span>
                                <span class="px-2">:</span>
                                @if($view_type == 1)
                                <span class="value ">{{ ($getData['account_number']??'')}}</span>
                                @else
                                <span class="value">{{ ($getData['organizers']['account_number']??'')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="pair-list">
                            <div>
                                <span class="key text-nowrap"> {{ translate('Branch') }}</span>
                                <span class="px-2">:</span>
                                @if($view_type == 1)
                                <span class="value ">{{ ($getData['bank_branch']??'')}}</span>
                                @else
                                <span class="value">{{ ($getData['organizers']['bank_branch']??'')}}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mt-2">
                    <h4>Accept Tour</h4>
                    <br>
                    <table class="table">
                        <thead>
                            <tr>
                                <td>Sno</td>
                                <td>Tour Name</td>
                            </tr>
                        </thead>
                        <tbody>
                            @if($getcheckbox)
                            @foreach($getcheckbox as $va)
                            <tr>
                                <td>{{ $loop->iteration  }}</td>
                                <td>{{ ($va['TourVisit']['tour_name']??"") }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="gap-3 flex-wrap flex-lg-nowrap">
        <div class="row">
            <div class="col-md-3">
                <span>Gst No.: {{ $getData['gst_number']??"" }}</span><br>
                <span>Gst Image</span><br>
                <a href="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['gst_image'], type: 'backend-product') }}" target="_blank">
                    <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['gst_image'], type: 'backend-product') }}" alt="Image">
                </a>
            </div>
            <div class="col-md-3">
                <span>PanCard No.: {{ $getData['pan_card_number']??"" }}</span><br>
                <span>PanCard Image</span><br>
                <a href="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['pan_card_image'], type: 'backend-product') }}" target="_blank">
                    <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['pan_card_image'], type: 'backend-product') }}" alt="Image">
                </a>
            </div>
            <div class="col-md-3">
                <span>Aadhar No.: {{ $getData['aadhar_card_number']??"" }}</span><br>
                <span>Aadhar Image</span><br>
                <a href="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['aadhaar_card_image'], type: 'backend-product') }}" target="_blank">
                    <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['aadhaar_card_image'], type: 'backend-product') }}" alt="Image">
                </a>
            </div>
            <div class="col-md-3">
                <span>Address Proof Image</span><br>
                <a href="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['address_proof_image'], type: 'backend-product') }}" target="_blank">
                    <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['address_proof_image'], type: 'backend-product') }}" alt="Image">
                </a>
            </div>
            <div class="col-md-3">
                <span>Banner Image</span><br>
                <a href="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['banner'], type: 'backend-product') }}" target="_blank">
                    <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$getData['banner'], type: 'backend-product') }}" alt="Image">
                </a>
            </div>
        </div>
    </div>
</div>
<!--  -->
<div class="card-body mt-3">
    <div class="row justify-content-between align-items-center g-2 mb-3">
        <div class="col-sm-6">
            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                <img width="20" class="mb-1" src="{{ dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png') }}" alt="">
                {{ translate('tour_wallet') }}
            </h4>
        </div>
        <div class='col-sm-6 text-end'>

        </div>
    </div>

    <div class="row g-2" id="order_stats">
        <div class="col-lg-4">
            <div class="card h-100 d-flex justify-content-center align-items-center">
                <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                    <img width="48" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw.png') }}" alt="">
                    <h3 class="for-card-count mb-0 fz-24">
                        @if($view_type == 1)
                        {{ $getData['wallet_amount']??'0.00'}}₹
                        @else
                        {{ $getData['organizers']['wallet_amount']??'0.00'}}₹
                        @endif
                    </h3>
                    <div class="font-weight-bold text-capitalize mb-30">
                        {{ translate('withdrawable_balance') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row g-2">

                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        @php
                        if($view_type == 1){
                        $pending_amount = ($getData['withdrawal_pending_amount']??'0');
                        $organizer_id = ($getData['id']??'');
                        }else{
                        $pending_amount = ($getData['organizers']['withdrawal_pending_amount']??'0');
                        $organizer_id = ($getData['organizers']['id']??'');
                        }
                        @endphp
                        <div class="d-flex {{ (($pending_amount <= 0)?'gap-4 justify-content-between':'gap-4') }} align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    @if($view_type == 1)
                                    {{ $getData['withdrawal_pending_amount']??'0.00'}}₹
                                    @else
                                    {{ $getData['organizers']['withdrawal_pending_amount']??'0.00'}}₹
                                    @endif
                                </h3>
                                <div class="text-capitalize mb-0"> {{ translate('Pending_withdraw') }}</div>
                            </div>
                            @if($pending_amount > 0)
                            <div class="d-flex flex-column justify-content-between ">
                                <a class='btn btn-success btn-sm' href="{{ route('admin.event-managment.event.requestapprove',[$organizer_id,1])}}">approve</a>
                                <a class='btn btn-danger btn-sm' href="{{ route('admin.event-managment.event.requestapprove',[$organizer_id,2])}}">cancel</a>
                            </div>
                            @endif
                            <div>
                                <img width="40" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pw.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    @if($view_type == 1)
                                    {{ $getData['withdrawal_amount']??"0.00" }}₹
                                    @else
                                    {{ $getData['organizers']['withdrawal_amount']??"0.00" }}₹
                                    @endif
                                </h3>
                                <div class="text-capitalize mb-0"> {{ translate('Collected_cash') }}</div>
                            </div>
                            <div>
                                <img width="40" src="{{ dynamicAsset(path: 'public/assets/back-end/img/cc.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    @if($view_type == 1)
                                    {{ $getData['admin_commission']??'0.00'}}₹
                                    @else
                                    {{ $getData['organizers']['admin_commission']??'0.00'}}₹
                                    @endif
                                </h3>
                                <div class="text-capitalize mb-0">
                                    {{ translate('Total_commission_given') }}
                                </div>
                            </div>
                            <div>
                                <img width="40" src="{{ dynamicAsset(path: 'public/assets/back-end/img/tcg.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    @if($view_type == 1)
                                    {{ $getData['gst_amount']??'0.00'}}₹
                                    @else
                                    {{ $getData['organizers']['gst_amount']??'0.00'}}₹
                                    @endif
                                </h3>
                                <div class="text-capitalize mb-0">
                                    {{ translate('Total_tax_given') }}
                                </div>
                            </div>
                            <div>
                                <img width="40" src="{{ dynamicAsset(path: 'public/assets/back-end/img/ttg.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="bankModal_traveller" tabindex="-1" role="dialog" aria-labelledby="bankModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Bank Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="bankUpiTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="bank-tab" data-toggle="tab" href="#bankTab" role="tab" aria-controls="bankTab" aria-selected="true">
                            Bank Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="upi-tab" data-toggle="tab" href="#upiTab" role="tab" aria-controls="upiTab" aria-selected="false">
                            UPI Info
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content mt-3">
                    <!-- Bank Table -->
                    <div class="tab-pane fade show active" id="bankTab" role="tabpanel" aria-labelledby="bank-tab">
                        <table id="bankTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Bank Name</th>
                                    <th>User Name</th>
                                    <th>Account No</th>
                                    <th>IFSC Code</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getAllBankInfor as $bank)
                                <tr>
                                    <td>{{ $bank->bank_name ?? "" }}</td>
                                    <td>{{ $bank->holder_name ?? "" }}</td>
                                    <td>{{ $bank->account_number ?? "" }}</td>
                                    <td>{{ $bank->ifsc_code ?? "" }}</td>
                                    <td>{{ (($bank->primary_status??"" == 1)?"Primary":"") }} </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- UPI Table -->
                    <div class="tab-pane fade" id="upiTab" role="tabpanel" aria-labelledby="upi-tab">
                        <table id="upiTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>UPI ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getAllUpiInfor as $upi)
                                <tr>
                                    <td>{{ $upi->upi_code }}</td>
                                </tr>
                                @endforeach
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