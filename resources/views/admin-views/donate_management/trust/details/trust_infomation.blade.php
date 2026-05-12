<div class="content container-fluid">
    <div class="row gy-3" id="printableArea">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">
                        <div class="d-flex flex-column gap-10">
                            <h4 class="text-capitalize">{{translate('Trust_Id')}} #{{ $trust_data['trust_id']}}</h4>
                        </div>
                        <div class="text-sm-right flex-grow-1">
                            <div class="d-flex flex-column gap-10 justify-content-end">
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">

                                    <span class="title-color font-weight-bold">{{translate('status')}}: </span>
                                    @if($trust_data['status'] == 1)
                                    <span class="badge badge-soft-success font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('active')}} </span>
                                    @else
                                    <span class="badge badge-soft-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2"> {{translate('inactive')}}</span>
                                    @endif
                                </div>
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">

                                    <span class="title-color font-weight-bold">{{translate('verification_status')}}: </span>
                                    @if($trust_data['is_approve'] == 1)
                                    <span class="badge badge-soft-success font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('Approved')}} </span>
                                    @elseif($trust_data['is_approve'] == 2)
                                    <span class="badge badge-soft-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('Not_Approved')}} </span>
                                    @else
                                    <select class="form-control" style="width: 30%;" id="verificationStatus">
                                        <option value="">Select Verification Status</option>
                                        <option value="1" data-type='1' data-href="{{ route('admin.donate_management.trust.trust_verify_approvel',[$trust_data['id'],1]) }}" {{ ($trust_data['is_approve'] == 1) ? 'selected' : '' }}>{{translate('Approve')}}</option>
                                        <option value="2" data-type='2' data-href="{{ route('admin.donate_management.trust.trust_verify_approvel',[$trust_data['id'],2]) }}" {{ ($trust_data['is_approve'] == 2) ? 'selected' : '' }}>{{translate('Reject')}}</option>
                                        <option value="0" data-type='0' data-href="{{ route('admin.donate_management.trust.trust_verify_approvel',[$trust_data['id'],0]) }}" {{ ($trust_data['is_approve'] == 0) ? 'selected' : '' }}>{{translate('Pending')}}</option>
                                    </select>
                                    @endif
                                </div>

                                @if($trust_data['is_approve'] == 1)
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                    <span class="title-color font-weight-bold">{{translate('profile_Status')}}: </span>
                                    @if(\App\Models\Seller::where('type','trust')->where('relation_id',$trust_data['id'])->where('status','approved')->exists())
                                    <span class="btn btn-outline-warning btn-sm square-btn reject-artist_data" data-title="Hold Trustess" title="Hold Trustess" data-id="approve-profile-hold">
                                        <i class="tio-hand_basic">hand_basic</i>
                                    </span>
                                    @else
                                    <span class="btn btn-outline-success btn-sm square-btn reject-artist_data" data-title="approved Trustess" title="approved Trustess" data-id="approve-profile-approve">
                                        <i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i>
                                    </span>
                                    @endif
                                    <form action="{{ route('admin.donate_management.trust.approve-profile-hold',['id'=>$trust_data['id'],'type'=>'hold']) }}" method="get" id="approve-profile-hold">
                                    </form>
                                    <form action="{{ route('admin.donate_management.trust.approve-profile-hold',['id'=>$trust_data['id'],'type'=>'approved']) }}" method="get" id="approve-profile-approve">
                                    </form>
                                </div>
                                @endif
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                    <?php
                                    $getdata_show = \App\Models\Seller::where('relation_id', $trust_data['id'])->where('type', 'trust')->first();
                                    ?>
                                    <button class="btn btn-danger btn-sm" onclick="resend_doc()">Send Re-Upload</button>
                                    @if(!empty($getdata_show) && ($getdata_show['reupload_doc_status'] == 2))
                                    Pending Doc
                                    @elseif(!empty($getdata_show) && ($getdata_show['reupload_doc_status'] == 3))
                                    New Doc Updated
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                            $check_data_orders = [];
                            if (!empty($getdata_show) && json_decode($getdata_show['all_doc_info'], true)) {
                                $check_data_orders = json_decode($getdata_show['all_doc_info'], true);
                            }
                            ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="form-label font-weight-bold send_wrong_data-trust_category">{{translate('Category')}}</span></td>
                                        <td>{{ ($trust_data['category']['name']??"")}}</td>
                                        <td> @if(($check_data_orders['trust_category']??0) == 1)
                                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                            @else
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="trust_category" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="trust_category" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="form-label font-weight-bold send_wrong_data-name">{{translate('name')}}</span></td>
                                        <td>{{ ($trust_data['name']??"")}}</td>
                                        <td> @if(($check_data_orders['name']??0) == 1)
                                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                            @else
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="name" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="name" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="form-label font-weight-bold send_wrong_data-trust_name">{{translate('Trust_Name')}}</span></td>
                                        <td>{{ ($trust_data['trust_name']??"")}}</td>
                                        <td> @if(($check_data_orders['trust_name']??0) == 1)
                                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                            @else
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="trust_name" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="trust_name" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="form-label font-weight-bold send_wrong_data-trust_email">{{translate('Email_id')}}</span></td>
                                        <td>{{ ($trust_data['trust_email']??"")}}</td>
                                        <td> @if(($check_data_orders['trust_email']??0) == 1)
                                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                            @else
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="trust_email" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="trust_email" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="form-label font-weight-bold send_wrong_data-full_address">{{translate('Full_address')}}</span></td>
                                        <td>{{ ($trust_data['full_address']??"")}}</td>
                                        <td> @if(($check_data_orders['full_address']??0) == 1)
                                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                            @else
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="full_address" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="full_address" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="form-label font-weight-bold send_wrong_data-pan_card">{{translate('pan_Number')}}</span></td>
                                        <td>{{ ($trust_data['pan_card']??"")}}</td>
                                        <td> @if(($check_data_orders['pan_card']??0) == 1)
                                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                            @else
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="pan_card" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="pan_card" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="form-label font-weight-bold send_wrong_data-trust_pan_card">{{translate('Trust_pan_number')}}</span></td>
                                        <td>{{ ($trust_data['trust_pan_card']??"")}}</td>
                                        <td> @if(($check_data_orders['trust_pan_card']??0) == 1)
                                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                            @else
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="trust_pan_card" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="trust_pan_card" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="form-label font-weight-bold send_wrong_data-description">{{translate('description')}}</span></td>
                                        <td>{!! ($trust_data['description']??"") !!}</td>
                                        <td> @if(($check_data_orders['description']??0) == 1)
                                            <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                            @else
                                            <span class="btn btn-sm btn-success send_wrong_data" data-name="description" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                            @endif
                                            <span class="btn btn-sm btn-danger send_wrong_data" data-name="description" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                            <i class="tio-photo_landscape_outlined">photo_landscape_outlined</i> {{translate('verified_document')}}
                        </h4>
                    </div>
                    <div>
                        <div class="custom_upload_input" style=" aspect-ratio: 1;">
                            <div class="img_area_with_preview position-absolute z-index-2">
                                {{-- <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/verified/'.$trust_data['verified_access_certificate'], type: 'backend-product')  }}" alt=""> --}}
                                @php
                                $file = $trust_data['verified_access_certificate'];
                                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                                $imageExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                                $docExtensions = ['doc', 'docx'];
                                @endphp

                                @if(in_array($extension, $imageExtensions))
                                <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/verified/' . $file, type: 'backend-product') }}" alt="">
                                @elseif($extension === 'pdf')
                                <a href="{{ asset('storage/app/public/donate/verified/' . $file) }}" target="_blank">
                                    <img src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                </a>
                                @elseif(in_array($extension, $docExtensions))
                                <a href="{{ asset('storage/app/public/donate/verified/' . $file) }}" target="_blank">
                                    <img src="{{ asset('public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                </a>
                                @else
                                <span>File is Not uploaded</span>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="form-label font-weight-bold send_wrong_data-bank_name">{{translate('bank_name')}}</span></td>
                                <td>{{ ($trust_data['bank_name']??"")}}</td>
                                <td> @if(($check_data_orders['bank_name']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="bank_name" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="bank_name" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="form-label font-weight-bold send_wrong_data-beneficiary_name">{{translate('beneficiary_name')}}</span></td>
                                <td>{{ ($trust_data['beneficiary_name']??"")}}</td>
                                <td> @if(($check_data_orders['beneficiary_name']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="beneficiary_name" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="beneficiary_name" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="form-label font-weight-bold send_wrong_data-ifsc_code">{{translate('ifsc_code')}}</span></td>
                                <td>{{ ($trust_data['ifsc_code']??"")}}</td>
                                <td> @if(($check_data_orders['ifsc_code']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="ifsc_code" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="ifsc_code" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="form-label font-weight-bold send_wrong_data-account_type">{{translate('account_type')}}</span></td>
                                <td>{{ ($trust_data['account_type']??"")}}</td>
                                <td> @if(($check_data_orders['account_type']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="account_type" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="account_type" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="form-label font-weight-bold send_wrong_data-account_no">{{translate('account_number')}}</span></td>
                                <td>{{ ($trust_data['account_no']??"")}}</td>
                                <td> @if(($check_data_orders['account_no']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="account_no" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="account_no" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row gy-3">
        <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class='form-label font-weight-bold w-100  send_wrong_data-members'>{{translate('Members_list')}}
                                @if(($check_data_orders['members']??0) == 1)
                                <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                @else
                                <span class="btn btn-sm btn-success send_wrong_data" data-name="members" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                @endif
                                <span class="btn btn-sm btn-danger send_wrong_data" data-name="members" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                            </label>
                            <hr />
                        </div>
                        @if(!empty($trust_data['memberlist']) && json_decode($trust_data['memberlist']))
                        @foreach(json_decode($trust_data['memberlist']) as $show_ven)
                        <div class="col-md-4 form-group col-6">
                            <label class='form-label font-weight-bold w-100'>{{translate('Name')}}</label>
                            <span class='text-center'>{{ ($show_ven->member_name??"")}}</span>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <label class='form-label font-weight-bold w-100'>{{translate('Phone_Number')}}</label>
                            <span class='text-center'>{{ ($show_ven->member_phone_no??"")}}</span>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <label class='form-label font-weight-bold w-100'>{{translate('Position')}}</label>
                            <span class='text-center'>{{ ($show_ven->member_position??"")}}</span>
                        </div>
                        @endforeach
                        @endif

                        <div class="col-md-12 form-group">
                            <hr />
                        </div>
                         <div class="col-md-6 form-group">
                            <label class='form-label font-weight-bold w-100 send_wrong_data-website_link'>{{translate('website_link')}}
                                @if(($check_data_orders['website_link']??0) == 1)
                                <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                @else
                                <span class="btn btn-sm btn-success send_wrong_data" data-name="website_link" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                @endif
                                <span class="btn btn-sm btn-danger send_wrong_data" data-name="website_link" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>

                            </label>
                            <span class='text-center'>{{ ($trust_data['website']??"")}}</span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class='form-label font-weight-bold w-100 send_wrong_data-gst_number'>{{translate('gst_number')}}
                                @if(($check_data_orders['gst_number']??0) == 1)
                                <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                @else
                                <span class="btn btn-sm btn-success send_wrong_data" data-name="gst_number" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                @endif
                                <span class="btn btn-sm btn-danger send_wrong_data" data-name="gst_number" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>

                            </label>
                            <span class='text-center'>{{ ($trust_data['gst_number']??"")}}</span>
                        </div>
                        <div class="col-md-12 form-group">
                            <hr />
                            <label class='form-label font-weight-bold w-100'>{{translate('Document_image')}}</label>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-user_image'>{{ translate('theme_image')}}
                                    @if(($check_data_orders['user_image']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="user_image" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="user_image" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/trust/'.$trust_data['theme_image'], type: 'backend-product')  }}" alt="" onclick="openModal(this.src)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-pan_card_image'>{{ translate('pan_card_image')}}
                                    @if(($check_data_orders['pan_card_image']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="pan_card_image" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="pan_card_image" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        @php
                                        $PanCardImg = $trust_data['pan_card_image'];
                                        $PanCardImgextension = strtolower(pathinfo($PanCardImg, PATHINFO_EXTENSION));
                                        @endphp

                                        @if(in_array($PanCardImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $PanCardImg, type: 'backend-product') }}" alt="" onclick="openModal(this.src)">
                                        @elseif($PanCardImgextension === 'pdf')
                                        <a href="{{ asset('storage/app/public/donate/document/' . $PanCardImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                        </a>
                                        @elseif(in_array($PanCardImgextension, ['doc', 'docx']))
                                        <a href="{{ asset('storage/app/public/donate/document/' . $PanCardImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                        </a>
                                        @else
                                        <span>Unsupported File</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-trust_pan_card_image'>{{ translate('trust_pan_card_image')}}
                                    @if(($check_data_orders['trust_pan_card_image']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="trust_pan_card_image" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="trust_pan_card_image" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        @php
                                        $TrustPanCardImg = $trust_data['trust_pan_card_image'];
                                        $TrustPanCardImgextension = strtolower(pathinfo($TrustPanCardImg, PATHINFO_EXTENSION));
                                        @endphp

                                        @if(in_array($TrustPanCardImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $TrustPanCardImg, type: 'backend-product') }}" alt="" onclick="openModal(this.src)">
                                        @elseif($TrustPanCardImgextension === 'pdf')
                                        <a href="{{ asset('storage/app/public/donate/document/' . $TrustPanCardImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                        </a>
                                        @elseif(in_array($TrustPanCardImgextension, ['doc', 'docx']))
                                        <a href="{{ asset('storage/app/public/donate/document/' . $TrustPanCardImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                        </a>
                                        @else
                                        <span>Unsupported File</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-twelve_a_number'>{{ translate('12A certificate Number')}}
                                    <br>
                                    {{ $trust_data['twelve_a_number']??'null' }}
                                    @if(($check_data_orders['twelve_a_number']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="twelve_a_number" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="twelve_a_number" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-twelve_a_certificate'>{{ translate('12A certificate')}}
                                    @if(($check_data_orders['twelve_a_certificate']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="twelve_a_certificate" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="twelve_a_certificate" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        @php
                                        $TwelveCertificateImg = $trust_data['twelve_a_certificate'];
                                        $TwelveCertificateImgextension = strtolower(pathinfo($TwelveCertificateImg, PATHINFO_EXTENSION));
                                        @endphp

                                        @if(in_array($TwelveCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $TwelveCertificateImg, type: 'backend-product') }}" alt="" onclick="openModal(this.src)">
                                        @elseif($TwelveCertificateImgextension === 'pdf')
                                        <a href="{{ asset('storage/app/public/donate/document/' . $TwelveCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                        </a>
                                        @elseif(in_array($TwelveCertificateImgextension, ['doc', 'docx']))
                                        <a href="{{ asset('storage/app/public/donate/document/' . $TwelveCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                        </a>
                                        @else
                                        <span>Unsupported File</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-eighty_g_number'>{{ translate('Eighty G Number')}}
                                    <br>
                                    {{ $trust_data['eighty_g_number']??'null' }}
                                    @if(($check_data_orders['eighty_g_number']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="eighty_g_number" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="eighty_g_number" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-eighty_g_certificate'>{{ translate('Eighty G certificate') }}
                                    @if(($check_data_orders['eighty_g_certificate']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="eighty_g_certificate" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="eighty_g_certificate" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>

                                </label>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        @php
                                        $EightyCertificateImg = $trust_data['eighty_g_certificate'];
                                        $EightyCertificateImgextension = strtolower(pathinfo($EightyCertificateImg, PATHINFO_EXTENSION));
                                        @endphp
                                        @if(in_array($EightyCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $EightyCertificateImg, type: 'backend-product') }}" alt="" onclick="openModal(this.src)">
                                        @elseif($EightyCertificateImgextension === 'pdf')
                                        <a href="{{ asset('storage/app/public/donate/document/' . $EightyCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                        </a>
                                        @elseif(in_array($EightyCertificateImgextension, ['doc', 'docx']))
                                        <a href="{{ asset('storage/app/public/donate/document/' . $EightyCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                        </a>
                                        @else
                                        <span>Unsupported File</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-niti_aayog_number'>{{ translate('Niti aayog Number')}}
                                    <br>
                                    {{ $trust_data['niti_aayog_number']??'null' }}
                                    @if(($check_data_orders['niti_aayog_number']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="niti_aayog_number" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="niti_aayog_number" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-niti_aayog_certificate'>{{ translate('Niti aayog certificate') }}
                                    @if(($check_data_orders['niti_aayog_certificate']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="niti_aayog_certificate" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="niti_aayog_certificate" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        @php
                                        $NitiCertificateImg = $trust_data['niti_aayog_certificate'];
                                        $NitiCertificateImgextension = strtolower(pathinfo($NitiCertificateImg, PATHINFO_EXTENSION));
                                        @endphp
                                        @if(in_array($NitiCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $NitiCertificateImg, type: 'backend-product') }}" alt="" onclick="openModal(this.src)">
                                        @elseif($NitiCertificateImgextension === 'pdf')
                                        <a href="{{ asset('storage/app/public/donate/document/' . $NitiCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                        </a>
                                        @elseif(in_array($NitiCertificateImgextension, ['doc', 'docx']))
                                        <a href="{{ asset('storage/app/public/donate/document/' . $NitiCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                        </a>
                                        @else
                                        <span>Unsupported File</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-csr_number'>{{ translate('CSR Number')}}
                                    <br>
                                    {{ $trust_data['csr_number']??'null' }}
                                    @if(($check_data_orders['csr_number']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="csr_number" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="csr_number" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-csr_certificate'>{{translate('CSR certificate')}}
                                    @if(($check_data_orders['csr_certificate']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="csr_certificate" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="csr_certificate" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        @php
                                        $CSRCertificateImg = $trust_data['csr_certificate'];
                                        $CSRCertificateImgextension = strtolower(pathinfo($CSRCertificateImg, PATHINFO_EXTENSION));
                                        @endphp
                                        @if(in_array($CSRCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $CSRCertificateImg, type: 'backend-product') }}" alt="" onclick="openModal(this.src)">
                                        @elseif($CSRCertificateImgextension === 'pdf')
                                        <a href="{{ asset('storage/app/public/donate/document/' . $CSRCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                        </a>
                                        @elseif(in_array($CSRCertificateImgextension, ['doc', 'docx']))
                                        <a href="{{ asset('storage/app/public/donate/document/' . $CSRCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                        </a>
                                        @else
                                        <span>Unsupported File</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-e_anudhan_number'>{{ translate('E anudhan Number')}}
                                    <br>
                                    {{ $trust_data['e_anudhan_number']??'null' }}
                                    @if(($check_data_orders['e_anudhan_number']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="e_anudhan_number" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="e_anudhan_number" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-e_anudhan_certificate'>{{translate('E anudhan certificate')}}
                                    @if(($check_data_orders['e_anudhan_certificate']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="e_anudhan_certificate" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="e_anudhan_certificate" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        @php
                                        $EAnudhanCertificateImg = $trust_data['e_anudhan_certificate'];
                                        $EAnudhanCertificateImgextension = strtolower(pathinfo($EAnudhanCertificateImg, PATHINFO_EXTENSION));
                                        @endphp
                                        @if(in_array($EAnudhanCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $EAnudhanCertificateImg, type: 'backend-product') }}" alt="" onclick="openModal(this.src)">
                                        @elseif($EAnudhanCertificateImgextension === 'pdf')
                                        <a href="{{ asset('storage/app/public/donate/document/' . $EAnudhanCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                        </a>
                                        @elseif(in_array($EAnudhanCertificateImgextension, ['doc', 'docx']))
                                        <a href="{{ asset('storage/app/public/donate/document/' . $EAnudhanCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                        </a>
                                        @else
                                        <span>Unsupported File</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-frc_number'>{{ translate('FRC Number')}}
                                    <br>
                                    {{ $trust_data['frc_number']??'null' }}
                                    @if(($check_data_orders['frc_number']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="frc_number" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="frc_number" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-frc_certificate'>{{translate('FRC certificate')}}
                                    @if(($check_data_orders['frc_certificate']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="frc_certificate" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="frc_certificate" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        @php
                                        $FRCCertificateImg = $trust_data['frc_certificate'];
                                        $FRCCertificateImgextension = strtolower(pathinfo($FRCCertificateImg, PATHINFO_EXTENSION));
                                        @endphp
                                        @if(in_array($FRCCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $FRCCertificateImg, type: 'backend-product') }}" alt="" onclick="openModal(this.src)">
                                        @elseif($FRCCertificateImgextension === 'pdf')
                                        <a href="{{ asset('storage/app/public/donate/document/' . $FRCCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                        </a>
                                        @elseif(in_array($FRCCertificateImgextension, ['doc', 'docx']))
                                        <a href="{{ asset('storage/app/public/donate/document/' . $FRCCertificateImg) }}" target="_blank">
                                            <img src="{{ asset('public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                        </a>
                                        @else
                                        <span>Unsupported File</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <label class='form-label font-weight-bold w-100 send_wrong_data-cancelled_cheque_image'>{{translate('cancelled_cheque_image')}}
                                    @if(($check_data_orders['cancelled_cheque_image']??0) == 1)
                                    <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                    @else
                                    <span class="btn btn-sm btn-success send_wrong_data" data-name="cancelled_cheque_image" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                    @endif
                                    <span class="btn btn-sm btn-danger send_wrong_data" data-name="cancelled_cheque_image" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>
                                </label>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/'.$trust_data['cancelled_cheque_image'], type: 'backend-product')  }}" alt="" onclick="openModal(this.src)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <hr />
                            <label class='form-label font-weight-bold w-100  send_wrong_data-gallery_image'>{{translate('Trust_image')}}
                                @if(($check_data_orders['gallery_image']??0) == 1)
                                <i class="tio-all_done text-success font-weight-bold" style="font-size: 22px;">all_done</i>
                                @else
                                <span class="btn btn-sm btn-success send_wrong_data" data-name="gallery_image" data-value="1"><i class="tio-checkmark_circle_outlined">checkmark_circle_outlined</i></span>
                                @endif
                                <span class="btn btn-sm btn-danger send_wrong_data" data-name="gallery_image" data-value="2"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></span>

                            </label>
                        </div>
                        @if(!empty($trust_data['gallery_image']) && json_decode($trust_data['gallery_image']))
                        @foreach(json_decode($trust_data['gallery_image']) as $show_ven)
                        <div class="col-md-4 form-group col-6">
                            <div>
                                <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/trust/'.$show_ven, type: 'backend-product')  }}" alt="" onclick="openModal(this.src)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>