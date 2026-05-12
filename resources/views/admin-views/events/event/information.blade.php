@extends('layouts.back-end.app')
@section('title', translate('Event_Details'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="content container-fluid">
    <div class="mb-4">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{dynamicAsset(path: 'public/assets/back-end/img/all-orders.png')}}" alt="">
            {{translate('Event_Details')}}
        </h2>
    </div>

    <div class="row gy-3" id="printableArea">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">
                        <div class="d-flex flex-column gap-10">
                            <h4 class="text-capitalize">{{translate('Event_Id')}} #{{ $getData['unique_id']}}</h4>
                        </div>
                        <div class="text-sm-right flex-grow-1">
                            <div class="d-flex flex-column gap-10 justify-content-end">
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">

                                    <span class="title-color font-weight-bold">{{translate('status')}}: </span>
                                    @if($getData['status'] == 1)
                                    <span class="badge badge-soft-success font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('active')}} </span>
                                    @else
                                    <span class="badge badge-soft-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2"> {{translate('inactive')}}</span>
                                    @endif
                                </div>
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">

                                    <span class="title-color font-weight-bold">{{translate('verification_status')}}: </span>
                                    @if($getData['is_approve'] == 1)
                                    <span class="badge badge-soft-success font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('Go To Live')}} </span>
                                    @elseif($getData['is_approve'] == 2)
                                    <span class="badge badge-soft-warning font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('Wait_pay_request')}} </span>
                                    @else
                                    <select class="form-control" style="width: 30%;" id="verificationStatus">
                                        <option value="">Select Verification Status</option>
                                        <option value="2" data-type='2' data-href="{{ route('admin.event-managment.event.event_approvel',[$getData['id'],2]) }}" {{ ($getData['is_approve'] == 2) ? 'selected' : '' }}>{{translate('Create_a_Pay_Request')}}</option>
                                        <option value="3" data-type='3' data-href="{{ route('admin.event-managment.event.event_approvel',[$getData['id'],3]) }}" {{ ($getData['is_approve'] == 3) ? 'selected' : '' }}>{{translate('Reject')}}</option>
                                        <option value="0" data-type='0' data-href="{{ route('admin.event-managment.event.event_approvel',[$getData['id'],0]) }}" {{ ($getData['is_approve'] == 0) ? 'selected' : '' }}>{{translate('Pending')}}</option>
                                    </select>
                                    @endif
                                </div>
                                @if($getData['approve_amount_status'] == 1 || $getData['approve_amount_status'] == 2 || $getData['approve_amount_status'] == 3)
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                    <span class="title-color font-weight-bold">{{translate('event_approve_amount')}}: </span>
                                    <span class="badge badge-soft-{{ (($getData['approve_amount_status'] != 2 && $getData['approve_amount_status'] != 3)?'success':(($getData['approve_amount_status'] != 1 && $getData['approve_amount_status'] != 3)?'warning':'danger'))}} font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{ ($getData['event_approve_amount']??'')}} / {{ (($getData['approve_amount_status'] != 2 && $getData['approve_amount_status'] != 3)?'Complete':(($getData['approve_amount_status'] != 1 && $getData['approve_amount_status'] != 3)?'Pending':'Fail'))}} </span>
                                </div>
                                @endif
                                @if($getData['approve_amount_status'] == 2)
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                    <a href="{{ route('admin.event-managment.event.event_approvel',[$getData['id'],2,'amount'=>$getData['event_approve_amount']]) }}" class="badge badge-soft-info font-weight-bold radius-50 d-flex align-items-center py-1 px-2">ReSend Link</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class='form-label font-weight-bold w-100'>{{translate('Event_name')}}</label>
                            <span class='text-center'>{{ ($getData['event_name']??"")}}</span>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class='form-label font-weight-bold w-100'>{{translate('Event_category')}}</label>
                            <span class='text-center'>{{ ($getData['categorys']['category_name']??"")}}</span>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class='form-label font-weight-bold w-100'>{{translate('Organized_by')}}</label>
                            <span class='text-center'>{{ ucwords($getData['organizer_by']??"")}}</span>
                        </div>


                        <div class="col-md-4 form-group">
                            <label class='form-label font-weight-bold w-100'>{{translate('Event_Organizer')}}</label>
                            <span class='text-center'>{{ ($getData['organizers']['organizer_name']??"")}}</span>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class='form-label font-weight-bold w-100'>{{translate('Age_group')}}</label>
                            <span class='text-center'>{{ ($getData['age_group']??"")}}</span>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class='form-label font-weight-bold w-100'>{{translate('Event_artist')}}</label>
                            <span class='text-center'>{{ ($getData['eventArtist']['name']??"")}}</span>
                        </div>

                        <div class="col-md-4 form-group">
                            <label class='form-label font-weight-bold w-100'>{{translate('Event_Total_days')}}</label>
                            <span class='text-center'>{{ ($getData['days']??"")}}</span>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class='form-label font-weight-bold w-100'>{{translate('Date')}}</label>
                            <span class='text-center'>{{ ($getData['start_to_end_date']??"")}}</span>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class='form-label font-weight-bold w-100'>{{translate('language')}}</label>
                            <span class='text-center'>{{ ($getData['language']??"")}}</span>
                        </div>
                        <div class="col-md-12 form-group">
                            <hr />
                            <label class='form-label font-weight-bold w-100'>{{translate('all_Venues')}}</label>
                        </div>
                        @if(!empty($getData['all_venue_data']) && json_decode($getData['all_venue_data'],true))
                        @foreach(json_decode($getData['all_venue_data'],true) as $show_ven)
                        <div class="col-md-3 form-group col-6">
                            <label class='form-label font-weight-bold w-100'>{{translate('venue')}}</label>
                            <span class='text-center'>{{ ($show_ven['en_event_venue']??"")}}</span>
                        </div>
                        <div class="col-md-3 form-group col-6">
                            <label class='form-label font-weight-bold w-100'>{{translate('Date')}}</label>
                            <span class='text-center'>{{ ($show_ven['date']??"")}}</span>
                        </div>
                        <div class="col-md-3 form-group col-6">
                            <label class='form-label font-weight-bold w-100'>{{translate('time')}}</label>
                            <span class='text-center'>{{ ($show_ven['start_time']??"")}} to {{ ($show_ven['end_time']??"") }}</span>
                        </div>
                        <div class="col-md-3 form-group col-6">
                            <label class='form-label font-weight-bold w-100'>{{translate('duration')}}</label>
                            <span class='text-center'>{{ ($show_ven['event_duration']??"") }}</span>
                            <a class="float-end btn btn-sm btn-primary" onclick="getAllPackage(this)" data-html="
                            <div class='row'>
                                <div class='col-3'>package Name</div>
                                <div class='col-3'>Seats</div>
                                <div class='col-3'>Amount</div>
                                <div class='col-3'>Total</div>
                                <div class='col-12'><hr/></div>
                                @if(!empty($show_ven['package_list']))
                                @foreach($show_ven['package_list'] as $pa)
                                <div class='col-3'>{{ \App\Models\EventPackage::where('id',$pa['package_name'])->first()['package_name']??''}}</div>
                                <div class='col-3'>{{$pa['seats_no']}}</div>
                                <div class='col-3'>{{$pa['price_no']}}</div>
                                <div class='col-3'>{{ ($pa['seats_no'] * $pa['price_no']) }}</div>
                                @endforeach
                                @endif
                            </div>
                            "><i class="tio-inboxes"></i></a>
                            
                        </div>
                        @endforeach
                        @endif

                        
                        <div class="col-md-12 form-group">
                            <hr />
                            <label class='form-label font-weight-bold w-100'>{{translate('service video')}}</label>
                            <span class='text-center'>{{ ($getData['youtube_video']??"") }}</span>
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
                            <i class="tio-photo_landscape_outlined">photo_landscape_outlined</i> {{translate('Event_thumbnail')}}
                        </h4>
                    </div>
                    <div>
                        <div class="custom_upload_input" style="    aspect-ratio: 1;">
                            <div class="img_area_with_preview position-absolute z-index-2">
                                <img id="pre_img_viewer" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/event/events/'.$getData['event_image'], type: 'backend-product')  }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-capitalize d-flex flex-column gap-4">
                    <div class="d-flex gap-2 align-items-center justify-content-between">
                        <h4 class="d-flex gap-2">
                            <i class="tio-photo_gallery_outlined">photo_gallery_outlined</i> {{translate('additional_Image')}}
                        </h4>
                    </div>
                    <div class='row'>
                        @if(!empty($getData['images']) && json_decode($getData['images']))
                        @foreach(json_decode($getData['images']) as $key2=>$img)
                        <div class="col-2"></div>
                        <div class="col-8 custom_upload_input mb-1" style="aspect-ratio: 1;">
                            <div class="img_area_with_preview position-absolute z-index-2" styles='margin: 0px 0px 0px {{ ((($key2%2) == 0)?"-3px":"3px")}};'>
                                <img id="pre_img_viewer" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/event/events/'.$img, type: 'backend-product')  }}" alt="">
                            </div>
                        </div>
                        <div class="col-2"></div>
                        @endforeach
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">Package Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBodyContent">
                <!-- Dynamic content will be injected here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.getElementById('verificationStatus').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var url = selectedOption.getAttribute('data-href');
    var type = selectedOption.getAttribute('data-type');
    if (type === '2') {
        Swal.fire({
            title: 'Enter the amount required for the Event',
            input: 'number',
            inputAttributes: {
                'placeholder': 'Enter an amount',
                'min': '1',
                'max': '999999',
            },
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Submit',
            preConfirm: (number) => {
                if (!number) {
                    Swal.showValidationMessage('Please enter a valid Amount');
                    return false; 
                }
                return number;
            }
        }).then((result) => {
            if (result && result.value) {
                url += `?amount=${result.value}`;
                window.location.href = url;
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


function getAllPackage(that){
        var htmlContent = $(that).data('html');
        $('#modalBodyContent').html(htmlContent);
        $('#dataModal').modal('show');
}


    document.getElementById('showDetailsButton').addEventListener('click', function() {
        var point = $(this).data('point');
        const availableSoldRows = $(`.available-sold-row${point}`);
            if(availableSoldRows.check == style)
            row.style.display = row.style.display === 'none' ? 'block' : 'none';
    });

</script>
@endpush