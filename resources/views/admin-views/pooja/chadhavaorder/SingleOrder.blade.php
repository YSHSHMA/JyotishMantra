@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Chadhava|Order List'))
@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/social-icon.css') }}">
    <style>
        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .pending {
            animation: blink 1s infinite;
            color: red;
        }
    </style>
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/anushthan.png') }}"
                    alt="">{{ translate('Chadhava|Order List') }}
            </h2>
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button"
                    role="tab" aria-controls="home" aria-selected="true">ORDER VIEW</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button"
                    role="tab" aria-controls="profile" aria-selected="false">LIST VIEW</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                
                <div class="row mt-20">
                    <div class="col-md-12">
                        <div class="row align-items-center py-2">
                            <!-- Left Section: Image & Details -->
                            <div class="col-lg-10 col-sm-10">
                                <div class="d-flex align-items-center gap-3 p-3 bg-white shadow-sm rounded">
                                    <!-- Service Image -->
                                    <img src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $chadhava['thumbnail'], type: 'backend-product') }}"
                                        class="avatar avatar-lg rounded border" alt="Gauri Shankar Rudraksha Image">
                                    @php
                                        $statuses = [
                                            0 => 'Pending',
                                            1 => 'Completed',
                                            2 => 'Cancel',
                                            3 => 'Schedule Time',
                                            4 => 'Live Pooja',
                                            5 => 'Share Soon',
                                            6 => 'Rejected',
                                        ];
                                    @endphp
                                    <!-- Service Details -->
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $chadhava->name }}</span>
                                        <span class="fw-bold text-dark">{{ $chadhava->chadhava_venue ?? 'no pooja_venue' }}</span>
                                        <span class="text-muted">{{ $chadhavaOrders->first()->booking_date ?? 'No Booking Date' }}</span>
                                       <span class="pending">{{ $pending ? $statuses[$pending->order_status] ?? 'Unknown Status' : 'No Order' }}</span>
                                    </div>
                                </div>
                                 <!-- Right Section: Print Button -->
                        
                            </div>
                        </div>  
                       
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table id="myTable"
                                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start printTable">
                                        <thead class="thead-light thead-50 text-capitalize">
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('order_Id') }}</th>
                                                <th>{{ translate('Pooja_Date_Time') }}</th>
                                                <th>{{ translate('Customer Details') }}</th>
                                                <th>{{ translate('Members') }}</th>
                                                <th>{{ translate('Total_amount') }}</th>
                                                @if (Helpers::modules_permission_check('Chadhava Order', 'Single Order List', 'download-invoice') || Helpers::modules_permission_check('Chadhava Order', 'Single Order List', 'detail'))
                                                <th class="text-center"> {{ translate('Action (Invoice)') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($chadhavaOrders as $key => $order)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td><a href="#" data-id="{{ $order['id'] }}"
                                                            class="ChadhavaBookingDetails">{{ $order->order_id }}</a></td>
                                                    <td>{{ date('d M Y H:i:s', strtotime($order->created_at)) }} </td>
                                                    <td><b>{{ @ucwords($order['customers']['f_name']) }}
                                                            {{ $order['customers']['l_name'] }}</b>
                                                        <p>{{ $order['customers']['phone'] }}</p>
                                                    </td>
                                                    <td>{{ $order->members ?? 'No Member' }}</td>
                                                    <td>{{ webCurrencyConverter(amount: $order->pay_amount - $order->coupon_amount) }}</td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-2">
                                                            @if (Helpers::modules_permission_check('Chadhava Order', 'Single Order List', 'download-invoice'))
                                                            <a class="btn btn-outline-primary btn-sm square-btn"
                                                                title="{{ translate('view') }}"
                                                                href="{{ route('admin.chadhava.order.details', [$order['id']]) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                    height="12" viewBox="0 0 14 12" fill="none"
                                                                    class="svg replaceds-svg">
                                                                    <path
                                                                        d="M6.79584 3.75937C6.86389 3.75234 6.93195 3.75 7 3.75C8.2882 3.75 9.33333 4.73672 9.33333 6C9.33333 7.24219 8.2882 8.25 7 8.25C5.68993 8.25 4.66667 7.24219 4.66667 6C4.66667 5.93437 4.6691 5.86875 4.67639 5.80313C4.90243 5.90859 5.16493 6 5.44445 6C6.30243 6 7 5.32734 7 4.5C7 4.23047 6.90521 3.97734 6.79584 3.75937ZM11.6813 2.63906C12.8188 3.65625 13.5795 4.85391 13.9392 5.71172C14.0194 5.89687 14.0194 6.10312 13.9392 6.28828C13.5795 7.125 12.8188 8.32266 11.6813 9.36094C10.5365 10.3875 8.96389 11.25 7 11.25C5.03611 11.25 3.46354 10.3875 2.31924 9.36094C1.18174 8.32266 0.42146 7.125 0.059818 6.28828C0.0203307 6.19694 0 6.09896 0 6C0 5.90104 0.0203307 5.80306 0.059818 5.71172C0.42146 4.85391 1.18174 3.65625 2.31924 2.63906C3.46354 1.61344 5.03611 0.75 7 0.75C8.96389 0.75 10.5365 1.61344 11.6813 2.63906ZM7 2.625C5.06771 2.625 3.5 4.13672 3.5 6C3.5 7.86328 5.06771 9.375 7 9.375C8.93229 9.375 10.5 7.86328 10.5 6C10.5 4.13672 8.93229 2.625 7 2.625Z"
                                                                        fill="#0177CD"></path>
                                                                </svg>
                                                            </a>
                                                            @endif
                                                            @if (Helpers::modules_permission_check('Chadhava Order', 'Single Order List', 'detail'))
                                                            <a href="{{ route('admin.chadhava.order.generate.invoice', [$order['id']]) }}"
                                                                title="{{ translate('download_invoice') }}" class="btn btn-outline-primary btn-sm square-btn">
                                                                <i class="tio-arrow-downward"></i>

                                                            </a>
                                                            @endif

                                                        </div>

                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if (count($chadhavaOrders) == 0)
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- Second --}}
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="row mt-20">
                    <div class="col-md-12">
                        <div class="row align-items-center py-2">
                            <!-- Left Section: Image & Details -->
                            <div class="col-lg-10 col-sm-10">
                                <div class="d-flex align-items-center gap-3 p-3 bg-white shadow-sm rounded">
                                    <!-- Service Image -->
                                    <img src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $chadhava['thumbnail'], type: 'backend-product') }}"
                                        class="avatar avatar-lg rounded border" alt="Gauri Shankar Rudraksha Image">

                                    <!-- Service Details -->
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $chadhava->name }}</span>
                                        <span
                                            class="fw-bold text-dark">{{ $chadhava->chadhava_venue ?? 'no pooja_venue' }}</span>
                                        <span
                                            class="text-muted">{{ $chadhavaOrders->first()->booking_date ?? 'No Booking Date' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Section: Print Button -->
                            @if (Helpers::modules_permission_check('Chadhava Order', 'Single Order List', 'download-pdf'))
                            <a href="{{ route('admin.chadhava.order.memberspdf', [
                                'service_id' => $chadhava->id,
                                'booking_date' => optional($chadhavaOrders->first())->booking_date ?? '',
                                'status' => optional($chadhavaOrders->first())->order_status ?? '',
                            ]) }}"
                                class="btn btn-success btn-sm">
                                Download PDF
                            </a>
                            @endif
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">

                                    <table id="example"
                                        class="display table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                        <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th class="text-center" style="width: 5%;">{{ translate('no.') }}</th>
                                            <th>{{ translate('member_name') }}</th>
                                            <th>{{ translate('gotra') }}</th>
                                            <th>{{ translate('reason') }}</th>
                                            <th>{{ translate('Product') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($chadhavaOrders as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $order->members ?? 'No Member' }}</td>
                                            <td>{{ $order->gotra }}</td>
                                            <td>{{ $order->reason ?? '-' }}</td>
                                            <td>
                                                @if($order->leads && $order->leads->addProducts()->count())
                                                @foreach($order->leads->addProducts()->get() as $p)
                                                {{ $p->name }} <br>
                                                @endforeach
                                                @else
                                                No Product
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    </table>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Model --}}
    <!-- Modal -->
    <div class="modal fade" id="shareButton" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalLabel">Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- The invoice content will be loaded here -->
                    <iframe id="invoiceIframe" src="" width="100%" height="400px"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Structure -->
    <div id="orderModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Order details will be populated here -->
                    <table class="table table-bordered">
                        <tbody id="order-details">
                            <!-- Table rows will be populated by jQuery -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
   
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    <script>
         $(document).ready(function() {
            // Capture click event on order links
            $('.ChadhavaBookingDetails').click(function(e) {
                e.preventDefault();
                var orderId = $(this).data('id');
                $.ajax({
                    url: "{{url('admin/chadhava/order/get-order-details')}}",
                    type: 'GET',
                    data: { id: orderId },
                    success: function(data) {
                    function formatDate(dateString) {
                        const date = new Date(dateString);
                        const options = { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: true };
                        return date.toLocaleString('en-US', options).replace(',', '');
                    }
                        console.log(data);
                        var baseUrl = "{{ url('/') }}";
                        // let membersArray = JSON.parse(data.members);
                    
                        $('#order-details').html(`
                        <tr><th><b>Booking Id</b></th><td>${data.order_id}</td><th><b>Booking Date</b></th><td>${formatDate(data.created_at)}</td><th><b>TXN ID </b></th><td>${data.payment_id}</td></tr>
                        <tr><th><b>Pooja Details</b></th><td colspan=3><b>Pooja Name:</b>${data.chadhava.name},<br><b>Pooja Venue:</b>${data.chadhava.chadhava_venue}</td><th><b>Prashad(YES/NO)</b></th><td><span class="badge badge-soft-${data.prashad_status == 0 ? 'primary' : (data.prashad_status == 1 ? 'success' : 'danger')}">
                                    ${data.prashad_status == 0 ? 'No' : (data.prashad_status == 1 ? 'Yes' : 'Canceled')}
                                </span></td></tr>
                        <tr>
                            <th><b>Pandit Name/Email</b></th>
                            <td colspan=2>${data.pandit_assign ? `${data.astrologer.name}<br>${data.astrologer.email}` : 'Not Assigned'}</td>
                            <th colspan=2><b>Pandit Ji Mobile Number</b></th>
                            <td>${data.pandit_assign ? data.astrologer.mobile_no : 'Not Assigned'}</td>
                        </tr>
                        <tr><th><b>Customer Name/Email</b></th><td colspan=2>${data.customers.name}<br>${data.customers.email}</td><th   colspan=2><b>Mobile Number</b></th><td>${data.customers.phone}</td></tr>
                        <tr>
                            <th><b>Order Status</b></th>
                            <td  colspan=3>
                                <span class="badge badge-soft-${data.status == 0 ? 'primary' : (data.status == 1 ? 'success' : 'danger')}">
                                    ${data.status == 0 ? 'Pending' : (data.status == 1 ? 'Completed' : 'Canceled')}
                                </span>
                            </td>
                            
                        </tr> 
                        <tr>
                            <th><b>Number of Members Name</b></th>
                                <td>${data.members}</td>
                            <th><b>Pooja Video</b></th>
                                <td>
                                    ${data.pooja_video ? `<a href="${data.pooja_video}" target="_blank">View Video</a>` : 'No video available'}
                                </td>
                            <th><b>Pooja Certificate</b></th>
                                <td>
                                    ${data.pooja_certificate ? `<img src="${baseUrl}/public/assets/back-end/img/certificate/pooja/${data.pooja_certificate}" alt="Pooja Certificate" style="max-width:100px;">` : 'Certificate pending'}
                                </td>
                        </tr>
                       
                        <tr>
                            <th colspan="3"><b>Charity</b></th>
                            <th colspan="3"><b>Charity Product Price</b></th>
                        </tr>
                        ${data.product_leads.map(lead => `
                        <tr>
                            <td colspan=3>${lead.product_name}</td>
                            <td colspan=3>₹ ${lead.product_price}</td>
                             `).join('')}
                        </tr>
                        <th colspan=3><b>Total Amount Pay</b></th><td  colspan=3>₹  ${data.pay_amount}</td>
                        `);   
                        // Show the modal
                        $('#orderModal').modal('show');
                    },
                    error: function() {
                        alert('Failed to fetch order details.');
                    }
                });
            });
        });
    </script>
    <script>
        $('#date_type').change(function(e) {
            e.preventDefault();
            var value = $(this).val();
            if (value == 'custom_date') {
                $('#from-to-div').show();
            } else {
                $('#from-to-div').hide();
            }
        });
    </script>
    <script>
        let table = new DataTable('#myTable');
    </script>
    <script>
        // $(document).ready(function() {
        //     $('#printButton').click(function() {
        //         const { jsPDF } = window.jspdf;
        //         const doc = new jsPDF();

        //         // Get table data
        //         let tableData = [];
        //         $('#example tbody tr').each(function(index, row) {
        //             let rowData = [];
        //             $(row).find('td').each(function(index, cell) {
        //                 rowData.push($(cell).text().trim());
        //             });
        //             tableData.push(rowData);
        //         });

        //         // Add table headers
        //         let headers = [];
        //         $('#example thead th').each(function(index, th) {
        //             headers.push($(th).text().trim());
        //         });

        //         // Create table in the PDF
        //         doc.autoTable({
        //             head: [headers],
        //             body: tableData,
        //         });

        //         // Save the PDF
        //         doc.save('table.pdf');
        //     });
        // });
    </script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
            $('#printButton').click(function() {
                // Create a new window
                var printWindow = window.open('', '', 'height=800,width=1200');

                // Write the content to the new window
                printWindow.document.write('<html><head><title>Pooja Member List</title>');
                printWindow.document.write(
                    '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">'
                );
                printWindow.document.write('</head><body >');
                printWindow.document.write('<h1 align="center">Member List</h1>');
                printWindow.document.write(
                    '<p align="center">SR-{{ $chadhava->id }}  {{ $chadhava->name }}</p>');
                printWindow.document.write(document.getElementById('example').outerHTML);
                printWindow.document.write('</body></html>');

                // Close the document to trigger printing
                printWindow.document.close();
                printWindow.print();
            });
        });
    </script>
@endpush
