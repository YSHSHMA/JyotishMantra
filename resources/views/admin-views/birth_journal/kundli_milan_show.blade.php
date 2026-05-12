@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@if($kundalis['birthJournal']['name'] == 'kundali')
@section('title', translate('Kundali'))
@else
@section('title', translate('Kundali_Milan'))
@endif

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            @if($kundalis['birthJournal']['name'] == 'kundali')
            {{ translate('Kundali') }}
            @else
            {{ translate('Kundali_Milan') }}
            @endif
            <span class="badge badge-soft-dark radius-50 fz-14"></span>
        </h2>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card mt-20">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-20">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-12"><label class='form-label'>{{ translate('Kundali_Type') }} : {{ translate($kundalis['chart_style']??"") }}</label></div>
                                        <?php
                                        if (($kundalis['language'] ?? "") == 'hi') {
                                            $kundli_lan = 'Hindi';
                                        } elseif (($kundalis['language'] ?? "") == 'en') {
                                            $kundli_lan = 'English';
                                        } elseif (($kundalis['language'] ?? "") == 'bn') {
                                            $kundli_lan = 'Bengali';
                                        } elseif (($kundalis['language'] ?? "") == 'ma') {
                                            $kundli_lan = 'Marathi';
                                        } elseif (($kundalis['language'] ?? "") == 'kn') {
                                            $kundli_lan = 'Kannada';
                                        } elseif (($kundalis['language'] ?? "") == 'ml') {
                                            $kundli_lan = 'Malayalam';
                                        } elseif (($kundalis['language'] ?? "") == 'te') {
                                            $kundli_lan = 'Telogu';
                                        } elseif (($kundalis['language'] ?? "") == 'ta') {
                                            $kundli_lan = 'Tamil';
                                        } else {
                                            $kundli_lan = '';
                                        }
                                        ?>
                                        <div class="col-12">
                                            <spam class=''> {{ translate('language') }} : {{ translate($kundli_lan??"") }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-12 text-end">
                                            <a class="btn btn--primary px-4" target="_blank"
                                                href="{{ route('admin.birth_journal.order.generate-invoice', $kundalis['id']) }}">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/uil_invoice.svg') }}" alt="" class="mr-1">
                                                {{ translate('print_Invoice') }}
                                            </a>
                                        </div>
                                        <div class="col-12 text-end mt-2">
                                            <span>Status : <span class="badge badge-{{ (($kundalis['milan_verify'] == 1)?'success':'danger')}} font-weight-bold radius-50 align-items-center py-1 px-2">{{ (($kundalis['milan_verify'] == 1)?"Success":'Pending')}}</span></span>
                                        </div>
                                        <div class="col-12 text-end mt-2">
                                            <span>Payment Method : <span class="font-weight-bold">{{ (($kundalis['transaction_id'] == 'wallet')?"wallet":'Online')}}</span></span>
                                        </div>
                                        <div class="col-12 text-end mt-2">
                                            <span>Payment status : <span class="font-weight-bold text-{{ (($kundalis['payment_status'] == 1)?'success':'danger')}}">{{ (($kundalis['payment_status'] == 1)?"Paid":'Unpaid')}}</span></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-{{ (($kundalis['birthJournal']['name'] == 'kundali')? '12' : '6') }}">
                            <div class="row mb-2">
                                <div class="col-12 text-center"><b>{{ ucwords(($kundalis['gender']??"")) }}</b>
                                    <hr>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6  font-weight-bold">{{ translate('Name') }}</div>
                                <div class="col-6">{{ ($kundalis['name']??"") }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 font-weight-bold">{{ translate('date_of_birth') }}</div>
                                <div class="col-6">{{ ($kundalis['bod']??"") }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 font-weight-bold">{{ translate('time') }}</div>
                                <div class="col-6">{{ ($kundalis['time']??"") }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 font-weight-bold">{{ translate('Country') }}</div>
                                <div class="col-6">{{ ($kundalis['country']['name']??"") }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 font-weight-bold">{{ translate('place') }}</div>
                                <div class="col-6">{{ ($kundalis['state']??"") }}</div>
                            </div>
                        </div>
                        @if($kundalis['birthJournal']['name'] == 'kundali_milan')
                        <div class="col-md-6">
                            <div class="row mb-2">
                                <div class="col-12 text-center"><b>{{ ucwords(($kundalis['female_gender']??"")) }}</b>
                                    <hr>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6  font-weight-bold">{{ translate('Name') }}</div>
                                <div class="col-6">{{ ($kundalis['female_name']??"") }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 font-weight-bold">{{ translate('date_of_birth') }}</div>
                                <div class="col-6">{{ ($kundalis['female_dob']??"") }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 font-weight-bold">{{ translate('time') }}</div>
                                <div class="col-6">{{ ($kundalis['female_time']??"") }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 font-weight-bold">{{ translate('Country') }}</div>
                                <div class="col-6">{{ ($kundalis['country_female']['name']??"") }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 font-weight-bold">{{ translate('place') }}</div>
                                <div class="col-6">{{ ($kundalis['female_place']??"") }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row mt-4">
                        <div class="table-responsive datatable-custom">
                            <table
                                class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('name') }}</th>
                                        <th>{{ translate('type') }}</th>
                                        <th>{{ translate('price') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="media align-items-center gap-10">
                                                <div>
                                                    <h6 class="title-color">
                                                        {{ ucwords(str_replace("_"," ",($kundalis['birthJournal']['name']??""))) }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $kundalis['birthJournal']['type'] }}
                                        </td>
                                        <td>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $kundalis['amount']), currencyCode: getCurrencyCode()) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body ">
                            <table class="calculation-table table table-borderless mb-0">
                                <tbody class="totals">
                                    <tr class="border-top">
                                        <td>
                                            <div class="text-start">
                                                <span class="product-qty">{{ translate('Service_price') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-end">
                                                <span
                                                    class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $kundalis['amount']), currencyCode: getCurrencyCode()) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td>
                                            <div class="text-start">
                                                <span class="font-weight-bold">
                                                    <strong>{{ translate('total_Price') }}</strong>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-end">
                                                <span class="font-weight-bold amount">
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $kundalis['amount']), currencyCode: getCurrencyCode()) }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if($kundalis['birthJournal']['name'] == 'kundali_milan')
            <div class="row">
                <div class="col-12">
                    <div class="card mt-20">
                        <div class="card-body">
                            <div class="row astrologer-view-info {{ (($kundalis['assign_pandit'] == 0)?'d-none':'') }}">
                                <div class="col-12">
                                    <a class="float-end btn btn-outline-primary btn-sm" onclick="$('.astrologer-view-info').addClass('d-none');$('.chnage-astrologer').removeClass('d-none')"><i class="tio-edit"></i></a>
                                </div>
                                <div class="col-12">
                                    @if (!empty($kundalis['astrologer']))
                                    <div class="media flex-wrap gap-3">
                                        <div class="">
                                            <img class="avatar rounded-circle a vatar-70" src="{{ getValidImage(path: 'storage/app/public/astrologers/' . $kundalis['astrologer']['image'], type: 'backend-basic') }}" alt="{{ translate('Image') }}">
                                        </div>
                                        <div class="media-body d-flex flex-column gap-1">
                                            <span class="title-color"><strong>{{ $kundalis['astrologer']['name'] }}
                                                </strong></span>
                                            <span
                                                class="title-color break-all"><strong>{{ $kundalis['astrologer']['mobile_no'] }}</strong></span>
                                            <span class="title-color break-all"
                                                style="text-transform: lowercase !important;">{{ $kundalis['astrologer']['email'] }}</span>
                                        </div>
                                    </div>
                                    @else
                                    <p>Astrologer Detail Not Available</p>
                                    @endif
                                </div>
                            </div>
                            <div class="row chnage-astrologer {{ (($kundalis['assign_pandit'] != 0)?'d-none':'') }}">
                                @if($kundalis['assign_pandit'] != 0)
                                <div class="col-12">
                                    <a class="float-end btn btn-outline-danger btn-sm" onclick="$('.chnage-astrologer').addClass('d-none');$('.astrologer-view-info').removeClass('d-none');"><i class="tio-clear_circle_outlined">clear_circle_outlined</i></a>
                                </div>
                                @endif
                                <div class="col-12">
                                    <div class="">
                                        <label class="font-weight-bold title-color fz-14">{{ translate('type') }}</label>
                                        <select name="astrologer_type" id="astrologer-type" class="form-control">
                                            <option value="in house">In house</option>
                                            <option value="freelancer">Freelancer</option>
                                        </select>
                                        <br>
                                        <div class="" id="in-house">
                                            <label
                                                class="font-weight-bold title-color fz-14">{{ translate('inhouse_Astrologer') }}</label>
                                            <select name="assign_astrologer" class="assign-astrologer form-control">
                                                <option value="">Select Astrologer</option>
                                                @if (count($inHouseAstrologers) > 0)
                                                @foreach ($inHouseAstrologers as $inhouse)
                                                <option value="{{ $inhouse['id'] }}">{{ $inhouse['name'] }}</option>
                                                @endforeach
                                                @else
                                                <option disabled>No Astrologer Found</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="" id="freelancer" style="display: none;">
                                            <label
                                                class="font-weight-bold title-color fz-14">{{ translate('freelancer_Astrologer') }}</label>
                                            <select name="assign_astrologer" class="assign-astrologer form-control">
                                                <option value="">Select Astrologer</option>
                                                @if (count($freelancerAstrologers) > 0)
                                                @foreach ($freelancerAstrologers as $freelancer)
                                                <option value="{{ $freelancer['id'] }}">{{ $freelancer['name'] }}
                                                </option>
                                                @endforeach
                                                @else
                                                <option disabled>No Astrologer Found</option>
                                                @endif
                                            </select>
                                        </div>
                                        <form action="{{ route('admin.birth_journal.order.assign-astrologer', [$kundalis['id']]) }}" method="post" id="assign-astrologer-form">
                                            @csrf
                                            <input type="hidden" name="assign_pandit" id="astrologer-id-val">
                                            <input type="hidden" name="astrologer_type" id="astrologer-type-val">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-2">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-body text-capitalize d-flex flex-column gap-4">
                            <div class="d-flex " style="justify-content: space-between;">
                                <h4 class="mb-0 text-center">{{ translate('report') }}</h4>
                                <span class="badge badge-{{ $kundalis['milan_verify'] == 1 ? 'success' : 'warning' }}">
                                    {{ $kundalis['milan_verify'] == 1 ? 'Verfied' : 'Pending' }}</span>
                            </div>
                            @if ($kundalis['milan_verify'] == 0)
                            <div class="">
                                @if (Helpers::modules_permission_check('Birth Journal', 'Kundali Milan All', 'upload') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Pending', 'upload') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Completed', 'upload'))
                                @if($kundalis['assign_pandit'] == 0)
                                <span style="margin-left: 37px;">Please Assign Pandit/astrologer</span>
                                @elseif(($kundalis['reject_status'] == 0 || $kundalis['reject_status'] == 2))
                                <form id="pdfUploadForm" action="{{  route('admin.birth_journal.kundali-milan-uploadPDF',[($kundalis['id'] ??'')]) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <a class="w-100 btn btn-primary" style="position: relative; overflow: hidden;"> {{ translate('Upload_PDF') }}
                                        <input type="file" id="pdfInput" name="kundli_milan_pdf" accept="application/pdf" style="position: absolute; top: 0; right: 0; opacity: 0; cursor: pointer;">
                                    </a>
                                </form>
                                @endif
                                @endif
                            </div>
                            @endif
                            @if (!empty($kundalis['kundali_pdf']))
                            <div class="text-center">
                                @php
                                $type = explode('.', $kundalis['kundali_pdf']);
                                @endphp
                                @if ($type[1] == 'jpg' || $type[1] == 'jpeg' || $type[1] == 'png')
                                <a href="{{ dynamicStorage(path: 'storage/app/public/birthjournal/kundali_milan/'.($kundalis['kundali_pdf']??''))}}" download="">
                                    <img src="{{ asset('public/storage/consultation-order-report/' . $kundalis['kundali_pdf']) }}" alt="" width="100"></a>
                                @elseif ($type[1] == 'doc' || $type[1] == 'docx')
                                <a href="{{ dynamicStorage(path: 'storage/app/public/birthjournal/kundali_milan/'.($kundalis['kundali_pdf']??''))}}" download="">
                                    <img src="{{ asset('public/assets/back-end/img/doc-icon/word.png') }}" alt="" width="100"></a>
                                @elseif ($type[1] == 'Pdf' || $type[1] == 'pdf')
                                <a href="{{ dynamicStorage(path: 'storage/app/public/birthjournal/kundali_milan/'.($kundalis['kundali_pdf']??''))}}" download="">
                                    <img src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png') }}" alt="" width="100"></a>
                                @endif

                                
                                @if(($kundalis['milan_verify'] ?? "") == '0')
                                @if($kundalis['reject_status'] == 1)
                                <a data-href="{{ route('admin.birth_journal.reject-kundali-milan',[($kundalis['id'] ??'')])}}" onclick="kundli_rejectConfirmation(this)" class='btn btn-danger float-end ml-2'>{{ translate('Reject_PDF') }}</a>
                                @endif
                                @if (Helpers::modules_permission_check('Birth Journal', 'Kundali Milan All', 'verify') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Pending', 'verify') || Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Completed', 'verify'))
                                <a data-href="{{ route('admin.birth_journal.verify-kundali-milan',[($kundalis['id'] ??'')])}}" onclick="kundli_verifyConfirmation(this)" class='btn btn-info float-end ml-2'>{{ translate('Verify_PDF') }}</a>
                                @endif

                                @endif
                                @if($kundalis['reject_status'] == 2)
                                        <br>
                                        <span class="text-danger">{{$kundalis['reject_message'] }}</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                                <h4 class="d-flex gap-2">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}"
                                        alt="">
                                    {{ translate('customer_information') }}
                                </h4>
                            </div>
                            <div class="media flex-wrap gap-3">
                                <div class="">
                                    <img class="avatar rounded-circle avatar-70"
                                        src="{{ getValidImage(path: 'storage/app/public/profile/' . $kundalis['userData']['image'], type: 'backend-basic') }}"
                                        alt="{{ translate('Image') }}">
                                </div>
                                <div class="media-body d-flex flex-column align-self-center gap-1">
                                    <span class="title-color">
                                        <strong>{{ $kundalis['userData']['name'] }}</strong>
                                    </span>
                                    <span
                                        class="title-color break-all"><strong>{{ $kundalis['userData']['phone'] }}</strong></span>
                                    @if (str_contains($kundalis['userData']['email'], '.com'))
                                    <span class="title-color break-all"><strong>{{ $kundalis['userData']['email'] }}</strong></span>
                                    @endif
                                </div>
                            </div>
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
<script>
    function kundli_rejectConfirmation(that) {
    let getYesWord = $('#message-yes-word').data('text');
    let getCancelWord = $('#message-cancel-word').data('text');
    let messageYouWillNotAbleRevertThis = $('#message-you-will-not-be-able-to-revert-this').data('text');
    Swal.fire({
        title: "Reject Kundali Milan PDF",
        text: messageYouWillNotAbleRevertThis,
        input: 'textarea',
        inputPlaceholder: 'Enter rejection reason...',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: getYesWord,
        cancelButtonText: getCancelWord,
        reverseButtons: true,
        inputValidator: (value) => {
            if (!value) {
                return 'You must provide a reason!';
            }
        }
    }).then((result) => {
        console.log(result);
        if (result.value) {
            const reason = result.value;
            console.log("Rejection reason:", reason);
            let url = $(that).data('href');
            window.location.href = url+"?message="+reason;
        }
    });
}


    function kundli_verifyConfirmation(that) {
        let getYesWord = $('#message-yes-word').data('text');
        let getCancelWord = $('#message-cancel-word').data('text');
        let messageYouWillNotAbleRevertThis = $('#message-you-will-not-be-able-to-revert-this').data('text');
        Swal.fire({
            title: "Verify Kundali Milan PDF",
            text: messageYouWillNotAbleRevertThis,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: getYesWord,
            cancelButtonText: getCancelWord,
            type: 'info',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                window.location.href = $(that).data('href');
            }
        })
    }

    document.getElementById('pdfInput').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.getElementById('pdfUploadForm').submit();
        }
    });
</script>
<script>
    $('#astrologer-type').change(function(e) {
        e.preventDefault();
        var type = $(this).val();
        if (type == 'in house') {
            $('#in-house').show();
            $('#freelancer').hide();
        } else if (type == 'freelancer') {
            $('#in-house').hide();
            $('#freelancer').show();
        }
        $("#astrologer-type-val").val(type);
    });
    </script>
<script>
    $('.assign-astrologer').on('change', function() {
        var astrologerId = $(this).val();
        $('#astrologer-id-val').val(astrologerId);
        if (astrologerId != "") {
            Swal.fire({
                title: 'Are You Sure To Assign Astrologer',
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#assign-astrologer-form').submit();
                }
            });
        }
    });
</script>

@endpush