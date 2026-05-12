@extends('layouts.back-end.app-trustees')

@section('title', translate('Darshan_details'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/refund_transaction.png') }}"
                alt="">
            {{ translate('Darshan_details') }}
        </h2>
    </div>
    <div class="refund-details-card--2 p-4">
        <div class="row gy-2">
            <div class="col-lg-12">
                <div class="card h-100 refund-details-card">
                    <div class="card-body">
                        <div class="gap-3 mb-4 d-flex justify-content-between flex-wrap align-items-center">
                            <h4 class="">{{ translate('Booking_information') }}</h4>
                        </div>
                        <div class="refund-details">
                            <div class="img">
                                <div class="onerror-image border rounded">
                                    <img src="{{ getValidImage(path: 'storage/app/public/temple/thumbnail/' . ($getData['Temple']['thumbnail'] ?? ''), type: 'backend-product') }}"
                                        alt="">
                                </div>
                            </div>
                            <div class="--content flex-grow-1">
                                <h4>
                                    <a href="{{ route('admin.donate_management.trust.trust-detail', [($getData['Temple']->matchingTrust()['id'] ?? '')]) }}">
                                        {{($getData['Temple']->matchingTrust()['trust_name']??"")}}
                                    </a>
                                    <br>
                                    <a href="{{ route('temple-details', [($getData['Temple']['slug'] ?? '')]) }}">
                                        {{ $getData['Temple']['name']??'' }}
                                    </a><br>
                                    <span class="left text-capitalize">{{ translate('booking_date') }}</span>
                                    <span>:</span>
                                    <span class="right">{{ date('d M Y, h:s:A', strtotime($getData['created_at'])) }}</span><br>
                                    <span class="h6">Date : {{ date('d M,Y', strtotime($getData['date'])) }}</span><br>
                                    <span class="h6">Time Slot : {{ $getData['time'] }}</span><br>
                                </h4>
                            </div>
                            <ul class="dm-info p-0 m-0 w-l-115">

                                <li>
                                    <span class="left">{{ translate('total_price') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['price']??0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>

                                <li>
                                    <span class="left">{{ translate('total_tax') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['gst_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{ translate('admin_commission') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['admin_commission'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{ translate('final_price') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['final_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>

                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                            <h4 class="d-flex gap-2">
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}" alt=""> Customer information
                            </h4>
                        </div>
                        <div class="media flex-wrap gap-3">
                            <div class="">
                                <img class="avatar rounded-circle avatar-70"
                                    src="{{ getValidImage(path: 'storage/app/public/profile/' . $getData['userData']['image'], type: 'backend-product') }}"
                                    alt="Image">
                            </div>
                            <div class="media-body d-flex flex-column gap-1">
                                <span class="title-color"><strong>{{ $getData['userData']['name'] }}</strong></span>
                                <span
                                    class="title-color break-all"><strong>{{ $getData['userData']['phone'] }}</strong></span>
                                @if ($getData['userData']['phone'] != $getData['userData']['email'])
                                <span class="title-color break-all">{{ $getData['userData']['email'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card h-100 refund-details-card--2">
                    <div class="card-body">
                        <h4 class="mb-3 text-capitalize">{{ translate('user_info') }}</h4>
                        <div class="row">
                            <div class="col-12 table-responsive datatable-custom">
                                <table
                                    class="table table-hover text-center table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('Sno') }}.</th>
                                            <th>{{ translate('name') }}</th>
                                            <th>{{ translate('phone') }}</th>
                                            <th>{{ translate('aadhaar') }}</th>
                                            <th>{{ translate('status') }}</th>
                                            <th>{{ translate('kyc_status') }}</th>
                                        </tr>
                                    </thead>
                                    @if($getData['Members'] && count($getData['Members']) > 0)
                                    <tbody>
                                        @foreach($getData['Members'] as $key=>$val)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $val['name']??"" }}</td>
                                            <td>{{ $val['phone']??"-//-" }}</td>
                                            <td>{{ $val['aadhar']??'' }}</td>
                                            <td>{{ ((($val['verify']??0) == 0)?'Not Available':'Available') }}</td>
                                            <td>
                                                @if(($val['aadhar_verify_status']??0)==1)
                                                <a class="btn btn-info btn-sm" onclick="getAaddharInfo('{{ $val['aadhar']??'' }}')"><i class='tio-invisible'></i> Done</a>
                                                @else
                                                <a class="btn btn-danger btn-sm"><i class='tio-clear'></i> Not</a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    @endif
                                </table>
                                @if(!$getData['Members'] || count($getData['Members']) < 0)
                                    <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="{{ translate('image_description') }}">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">

        </div>
    </div>
</div>
</div>

<div class="modal fade" id="AadharDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    User Infomation
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">Name</div>
                    <div class="col-6"><span class="user_full_name"></span></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6"></div>
                    <div class="col-6"><img src="" class="user_image"></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6">phone</div>
                    <div class="col-6"><span class="user_phone_number"></span></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6">Aadhar Number</div>
                    <div class="col-6"><span class="user_aadhar_number"></span></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6">Date Of Brith</div>
                    <div class="col-6"><span class="user_bod"></span></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6">Gender</div>
                    <div class="col-6"><span class="user_gender"></span></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6">Country</div>
                    <div class="col-6"><span class="user_country"></span></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6">State</div>
                    <div class="col-6"><span class="user_state"></span></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6">City</div>
                    <div class="col-6"><span class="user_city"></span></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6">village</div>
                    <div class="col-6"><span class="user_village"></span></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6">landmark</div>
                    <div class="col-6"><span class="user_landmark"></span></div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-6">zip code</div>
                    <div class="col-6"><span class="user_zip_code"></span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>


@endsection
@push('script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    function getAaddharInfo(aadhar) {
        $.ajax({
            url: "{{ url('api/v1/darshan/aadhar-details') }}",
            data: {
                "aadhar": aadhar,
                "_token": "{{ csrf_token() }}"
            },
            dataType: "json",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.status == 1) {
                    toastr.success(data.message);
                    $('#AadharDetailsModal').modal('show');
                    $('.user_full_name').text(data.data.full_name);
                    $('.user_image').attr('src',data.data.image);
                    $('.user_phone_number').text(data.data.phone_no);
                    $('.user_aadhar_number').text(data.data.aadhaar_number);
                    $('.user_bod').text(data.data.dob);
                    $('.user_gender').text(data.data.gender);
                    let address = JSON.parse(data.data.address);
                    $('.user_country').text(address.country);
                    $('.user_state').text(address.state);
                    $('.user_city').text(address.dist);
                    $('.user_village').text(address.house);
                    $('.user_landmark').text(address.landmark);
                    $('.user_zip_code').text(data.data.zip);
                } else {
                    toastr.error(data.message);
                }
            }
        });
    }
</script>
@endpush