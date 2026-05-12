@extends('layouts.back-end.app')
@section('title', translate('Event_organizer_view'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="content container-fluid">
    <div class="mb-4">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{dynamicAsset(path: 'public/assets/back-end/img/all-orders.png')}}" alt="">
            {{translate('Event_organizer_view')}}
        </h2>
    </div>

    <div class="row gy-3" id="printableArea">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">
                        <div class="d-flex flex-column gap-10">
                            <h4 class="text-capitalize">{{translate('organizer_Id')}} #{{ $getData['unique_id']}}</h4>
                        </div>
                        <div class="text-sm-right flex-grow-1">
                            <div class="d-flex flex-column gap-10 justify-content-end">
                                <!-- <a class="btn btn--primary " target="_blank" href=''>
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/uil_invoice.svg') }}" alt="" class="mr-1">
                                    {{translate('print_Invoice')}}
                                </a>
                            </div> -->
                                <!-- <div class="d-flex flex-column gap-2 mt-3"> -->
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                    <span class="">
                                        <b> {{translate('verification_status')}} : </b>
                                        @if($getData['is_approve'] == 1)
                                        <span class="badge badge-soft-success font-weight-bold radius-50 align-items-center py-1 px-2">{{translate('Approve')}} </span>
                                        @elseif($getData['is_approve'] == 2)
                                        <span class="badge badge-soft-danger font-weight-bold radius-50 align-items-center py-1 px-2">{{translate('Reject')}} </span>
                                        @else
                                        <span class="badge badge-soft-warning font-weight-bold radius-50 align-items-center py-1 px-2"> {{translate('Pending')}}</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                    <span class="title-color font-weight-bold">{{translate('status')}}: </span>
                                    @if($getData['status'] == 1)
                                    <span class="badge badge-soft-success font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('active')}} </span>
                                    @else
                                    <span class="badge badge-soft-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2"> {{translate('inactive')}}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <?php
                    $check_data_orders = [];
                    if (!empty($getdata_show) && json_decode($getdata_show['all_doc_info'], true)) {
                        $check_data_orders = json_decode($getdata_show['all_doc_info'], true);
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-4 col-6 font-weight-bold send_wrong_data-organizer_name">{{ translate('Organization_/_Individual_Name') }}</div>
                        <div class="col-md-4 col-6">{{ $getData['organizer_name']??""}}</div>
                        <div class="col-md-4 col-12">
                            @if(($check_data_orders['organizer_name']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="organizer_name" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="organizer_name" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4 col-6 font-weight-bold send_wrong_data-organizer_pan_no">{{ translate('Organization_/_Individual_PAN_Card_Number') }}</div>
                        <div class="col-md-4 col-6">{{ $getData['organizer_pan_no']??""}}</div>
                        <div class="col-md-4 col-12">
                            @if(($check_data_orders['organizer_pan_no']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="organizer_pan_no" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="organizer_pan_no" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4 col-6 font-weight-bold send_wrong_data-organizer_address">{{ translate('Organization_/_Individual_Address') }}</div>
                        <div class="col-md-4 col-6"> {!! $getData['organizer_address'] ?? '' !!}</div>
                        <div class="col-md-4 col-12">
                            @if(($check_data_orders['organizer_address']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="organizer_address" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="organizer_address" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-12 text-center"><span class='font-weight-bold h4 '>{{ translate('contact_Details') }}</span></div>
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-4 col-6 font-weight-bold send_wrong_data-full_name">{{ translate('Full_name') }}</div>
                        <div class="col-md-4 col-6"> {{ $getData['full_name'] ?? '' }}</div>
                        <div class="col-md-4 col-12">
                            @if(($check_data_orders['full_name']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="full_name" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="full_name" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4 col-6 font-weight-bold send_wrong_data-email_address">{{ translate('Email_Address') }}</div>
                        <div class="col-md-4 col-6"> {{ $getData['email_address'] ?? '' }}</div>
                        <div class="col-md-4 col-12">
                            @if(($check_data_orders['email_address']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="email_address" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="email_address" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4 col-6 font-weight-bold send_wrong_data-contact_number">{{ translate('Contact_number') }}</div>
                        <div class="col-md-4 col-6"> {{ $getData['contact_number'] ?? '' }}</div>
                        <div class="col-md-4 col-12">
                            @if(($check_data_orders['contact_number']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="contact_number" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="contact_number" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4 col-6 font-weight-bold send_wrong_data-itr_return">{{ translate('last_2_years_ITR_Return') }}</div>
                        <div class="col-md-4 col-6"> {{ (($getData['itr_return'] ==1)?"Yes":"No") }}</div>
                        <div class="col-md-4 col-12">
                            @if(($check_data_orders['itr_return']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="itr_return" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="itr_return" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4 col-6 font-weight-bold send_wrong_data-gst_no">{{ translate('GST_number') }}</div>
                        <div class="col-md-4 col-6"> {{ ($getData['gst_no']??"") }} {{ ((empty($getData['gst_no']))?"NO":"") }}</div>
                        <div class="col-md-4 col-12">
                            @if(($check_data_orders['gst_no']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="gst_no" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="gst_no" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-4 d-flex flex-column gap-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                        <h4 class="d-flex gap-2">
                            <i class="tio-verified"></i> {{translate('change_verification_status')}}
                        </h4>
                    </div>
                    <div>
                        <form action="{{route('admin.event-managment.organizers.verification-status') }}" method="post" id="items-status{{$getData['id']}}-form" data-from="deal">
                            @csrf
                            <input type="hidden" name="id" value="{{$getData['id']}}">
                            <div class="form-group">
                                <select class="form-control toggle-switch-message_change" name='status' data-modal-id="toggle-status-modal" id="items-status{{ $getData['id'] }}" data-toggle-id="items-status{{ $getData['id'] }}" data-on-title="{{ translate('Organizer_verification_status_verified')}}" data-on-message="<p>{{ translate('this_is_available_on_the_event_organizer_website_and_customer_app.') }}</p>" data-off-title="{{ translate('organizer_verification_status_pending') }}" data-off-message="<p>{{ translate('This_will_not_be_available_on_the_event_organizer_website_and_customer_app.') }}</p>" data-reject-title="{{ translate('organizer_verification_status_being_rejected') }}" data-reject-message="<p>{{ translate('This_will_not_be_available_on_the_event_organizer_website_and_customer_app.') }}</p>">
                                    <option value="" selected disabled>{{ translate('Select Status') }}</option>
                                    <option value="1" {{ (($getData['is_approve'] == 1)?'selected':'') }}>{{ translate('Verified') }}</option>
                                    <option value="2" {{ (($getData['is_approve'] == 2)?'selected':'') }}>{{ translate('Reject') }}</option>
                                    <option value="0" {{ (($getData['is_approve'] == 0)?'selected':'') }}>{{ translate('Pending') }}</option>
                                </select>
                            </div>
                        </form>
                        <button class="btn btn-danger btn-sm" onclick="resend_doc()">Send Re-Upload</button>
                        @if(!empty($getdata_show) && ($getdata_show['reupload_doc_status'] == 2))
                        Pending Doc
                        @elseif(!empty($getdata_show) && ($getdata_show['reupload_doc_status'] == 3))
                        New Doc Updated
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-capitalize d-flex flex-column gap-4">
                    <div class="d-flex flex-column align-items-center gap-2">
                        <h4 class="mb-0 text-center">{{translate('Bank_details')}}</h4>
                    </div>
                    <div class="row">
                        <div class="col-6 font-weight-bold send_wrong_data-beneficiary_name">{{ translate('Beneficiary_Name') }}</div>
                        <div class="col-6">{{ $getData['beneficiary_name']??""}}</div>
                        <div class="col-12">
                            @if(($check_data_orders['beneficiary_name']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="beneficiary_name" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="beneficiary_name" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-6 font-weight-bold send_wrong_data-account_type">{{ translate('Account_type') }}</div>
                        <div class="col-6">{{ $getData['account_type']??""}}</div>
                        <div class="col-12">
                            @if(($check_data_orders['account_type']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="account_type" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="account_type" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-6 font-weight-bold send_wrong_data-bank_name">{{ translate('Bank_name') }}</div>
                        <div class="col-6">{{ $getData['bank_name']??""}}</div>
                        <div class="col-12">
                            @if(($check_data_orders['bank_name']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="bank_name" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="bank_name" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-6 font-weight-bold send_wrong_data-ifsc_code">{{ translate('IFSC_code') }}</div>
                        <div class="col-6">{{ $getData['ifsc_code']??""}}</div>
                        <div class="col-12">
                            @if(($check_data_orders['ifsc_code']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="ifsc_code" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="ifsc_code" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-6 font-weight-bold send_wrong_data-branch_name">{{ translate('branch_code') }}</div>
                        <div class="col-6">{{ $getData['branch_name']??""}}</div>
                        <div class="col-12">
                            @if(($check_data_orders['branch_name']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="branch_name" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="branch_name" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-6 font-weight-bold send_wrong_data-account_no">{{ translate('Account_Number') }}</div>
                        <div class="col-6">{{ $getData['account_no']??""}}</div>
                        <div class="col-12">
                            @if(($check_data_orders['account_no']??0) == 1)
                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                            @else
                            <span class="btn btn-sm btn-success send_wrong_data" data-name="account_no" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                            @endif
                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="account_no" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-3">
            <div class="card h-100">
                <div class="card-header">
                    <span class="font-weight-bolder send_wrong_data-user_image">User Image</span>
                    @if(($check_data_orders['user_image']??0) == 1)
                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                    @else
                    <span class="btn btn-sm btn-success send_wrong_data" data-name="user_image" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                    @endif
                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="user_image" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                </div>
                <div class="card-body">
                    <a href="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['image'], type: 'backend-product') }}" target="_blank">
                        <img src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['image'], type: 'backend-product') }}" class="h-auto aspect-1 bg-white" style="max-width: 200px">
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card h-100">
                <div class="card-header">
                    <span class="font-weight-bolder send_wrong_data-cancelled_cheque_image ">Cancelled cheque</span>
                    @if(($check_data_orders['cancelled_cheque_image']??0) == 1)
                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                    @else
                    <span class="btn btn-sm btn-success send_wrong_data" data-name="cancelled_cheque_image" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                    @endif
                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="cancelled_cheque_image" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                </div>
                <div class="card-body">
                    <a href="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['cancelled_cheque_image'], type: 'backend-product') }}" target="_blank">
                        <img src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['cancelled_cheque_image'], type: 'backend-product') }}" class="h-auto aspect-1 bg-white" style="max-width: 200px">
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card h-100">
                <div class="card-header">
                    <span class="font-weight-bolder send_wrong_data-pan_card_image">Pan card Image</span>
                    @if(($check_data_orders['pan_card_image']??0) == 1)
                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                    @else
                    <span class="btn btn-sm btn-success send_wrong_data" data-name="pan_card_image" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                    @endif
                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="pan_card_image" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                </div>
                <div class="card-body">
                    <a href="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['pan_card_image'], type: 'backend-product') }}" target="_blank">
                        <img src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['pan_card_image'], type: 'backend-product') }}" class="h-auto aspect-1 bg-white" style="max-width: 200px">
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card h-100">
                <div class="card-header">
                    <span class="font-weight-bolder send_wrong_data-aadhar_image">Aadhar Image</span>
                    @if(($check_data_orders['aadhar_image']??0) == 1)
                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                    @else
                    <span class="btn btn-sm btn-success send_wrong_data" data-name="aadhar_image" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                    @endif
                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="aadhar_image" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                </div>
                <div class="card-body">
                    <a href="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['aadhar_image'], type: 'backend-product') }}" target="_blank">
                        <img src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['aadhar_image'], type: 'backend-product') }}" class="h-auto aspect-1 bg-white" style="max-width: 200px">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $('.toggle-switch-message_change').on('change', function(event) {
        event.preventDefault();
        let rootPath = $('#get-root-path-for-toggle-modal-image').data('path');
        const modalId = $(this).data('modal-id')
        const toggleId = $(this).data('toggle-id');
        const onTitle = $(this).data('on-title');
        const onMessage = $(this).data('on-message');
        const offTitle = $(this).data('off-title');
        const offMessage = $(this).data('off-message');

        const rejectTitle = $(this).data('reject-title');
        const rejectMessage = $(this).data('reject-message');

        if ($('#' + toggleId).val() == 1) {
            $('#' + modalId + '-title').empty().append(onTitle);
            $('#' + modalId + '-message').empty().append(onMessage);
            $('#' + modalId + '-ok-button').attr('toggle-ok-button', toggleId);
        } else if ($('#' + toggleId).val() == 2) {
            $('#' + modalId + '-title').empty().append(rejectTitle);
            $('#' + modalId + '-message').empty().append(rejectMessage);
            $('#' + modalId + '-ok-button').attr('toggle-ok-button', toggleId);
        } else {
            $('#' + modalId + '-title').empty().append(offTitle);
            $('#' + modalId + '-message').empty().append(offMessage);
            $('#' + modalId + '-ok-button').attr('toggle-ok-button', toggleId);
        }
        $('#' + modalId).modal('show');
    });


    let all_doc_check = [];
    let messsage_use_doc = 0;
    $('.send_wrong_data').click(function() {
        var name = $(this).data('name');
        var value = $(this).data('value');
        if (value == 1) {
            $(`.send_wrong_data-${name}`).addClass('text-success');
            $(`.send_wrong_data-${name}`).removeClass('text-danger'); // Remove danger class if already added
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
                    inputValue: all_doc_check.map(item => `${item.name.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase())} : `).join('\n'),
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
                            url: "{{ route('admin.event-managment.organizers.doc-verified-resend') }}", // Replace with your server endpoint
                            type: 'POST',
                            data: {
                                reason: result.value,
                                arrays: all_doc_check,
                                vendor_id: "{{ $getData['id']??''}}",
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
                            url: "{{ route('admin.event-managment.organizers.doc-verified-resend') }}",
                            type: 'POST',
                            data: {
                                reason: result.value,
                                arrays: all_doc_check,
                                vendor_id: "{{ $getData['id']??''}}",
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
</script>
@endpush