@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('VIP_Pooja_Order_List'))
@push('css_or_js')
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
   .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
   top: 14px;
   right: 5px;
   }
   .box {
   width: 200px;
   height: 200px;
   position: absolute;
   top: 50%;
   left: 50%;
   transform: translate(-50%, -50%);
   overflow: hidden;
   }
   .box .b {
   border-radius: 50%;
   border-left: 4px solid;
   border-right: 4px solid;
   border-top: 4px solid transparent !important;
   border-bottom: 4px solid transparent !important;
   position: absolute;
   top: 50%;
   left: 50%;
   transform: translate(-50%, -50%);
   animation: ro 2s infinite;
   }
   .box .b1 {
   border-color: #4A69BD;
   width: 120px;
   height: 120px;
   }
   .box .b2 {
   border-color: #F6B93B;
   width: 100px;
   height: 100px;
   animation-delay: 0.2s;
   }
   .box .b3 {
   border-color: #2ECC71;
   width: 80px;
   height: 80px;
   animation-delay: 0.4s;
   }
   .box .b4 {
   border-color: #34495E;
   width: 60px;
   height: 60px;
   animation-delay: 0.6s;
   }
   @keyframes ro {
   0% {
   transform: translate(-50%, -50%) rotate(0deg);
   }
   50% {
   transform: translate(-50%, -50%) rotate(-180deg);
   }
   100% {
   transform: translate(-50%, -50%) rotate(0deg);
   }
   }
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
   .dateBooking {
   animation: blink 1s infinite;
   color: red;
   }
   .rotate-icon {
   animation: spin 2s linear infinite, moveX 3s ease-in-out infinite;
   }
   @keyframes spin {
   from {
   transform: rotate(0deg);
   }
   to {
   transform: rotate(360deg);
   }
   }
   @keyframes moveX {
   0% {
   transform: translateX(0);
   }
   50% {
   transform: translateX(-20px);
   }
   100% {
   transform: translateX(0);
   }
   }
   #orderCheckboxes {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 10px;
        }

        .form-check {
            display: flex;
            align-items: center;
        }

        #selectAllOrders {
            margin-right: 5px;
        }

        label {
            font-size: 14px;
            font-weight: normal;
        }   
</style>
@endpush
@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2 align-items-center">
         <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/vip.png') }}" alt="">
         {{ translate('VIP_Pooja_Order_List') }}
         <span class="badge badge-soft-dark radius-50 fz-14">{{ count($orders) }}</span>
      </h2>
   </div>
   <div class="card mb-3 remove-card-shadow">
      <div class="card-body">
         @php
         $totalPaymentSuccess = \App\Models\Service_order::where('type', 'vip')->where('is_block', '!=', 9)
         ->where('status', 0)
         ->where('payment_status', 1)
         ->sum('pay_amount');
         $totalPaymentPending = \App\Models\Service_order::where('type', 'vip')->where('is_block', '!=', 9)
         ->where('status', 0)
         ->where('payment_status', 0)
         ->sum('pay_amount');
         $totalPaymentFaild = \App\Models\Service_order::where('type', 'vip')->where('is_block', '!=', 9)
         ->where('status', 0)
         ->where('payment_status', 2)
         ->sum('pay_amount');
         @endphp
         <div class="row g-3" id="order_stats">
            <div class="col-lg-12">
               <div class="row g-2">
                  <div class="col-md-4">
                     <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                           <div class="d-flex flex-column align-items-start">
                              <h3 class="mb-1 fz-24 text-success">
                                 {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentSuccess), currencyCode: getCurrencyCode()) }}
                              </h3>
                              <div class="text-capitalize mb-0 text-success">VIP puja Success Earning</div>
                           </div>
                           <div>
                              <img width="40" class="mb-2 rotate-icon"
                                 src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                 alt="">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <a href="{{ route('admin.vippooja.order.list', 'all') }}" class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                           <div class="d-flex gap-2 justify-content-between align-items-center">
                              <div class="d-flex flex-column align-items-start">
                                 <h3 class="mb-1 fz-24 text-warning">
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentPending), currencyCode: getCurrencyCode()) }}
                                 </h3>
                                 <div class="text-capitalize mb-0 text-warning">VIP puja Pending Earning</div>
                              </div>
                              <div>
                                 <img width="40" class="mb-2 rotate-icon"
                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                    alt="">
                              </div>
                           </div>
                        </div>
                     </a>
                  </div>
                  <div class="col-md-4">
                     <a href="{{ route('admin.vippooja.order.list', 'all') }}" class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                           <div class="d-flex gap-2 justify-content-between align-items-center">
                              <div class="d-flex flex-column align-items-start">
                                 <h3 class="mb-1 fz-24 text-danger">
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentFaild), currencyCode: getCurrencyCode()) }}
                                 </h3>
                                 <div class="text-capitalize mb-0 text-danger">VIP puja Faild Earning</div>
                              </div>
                              <div>
                                 <img width="40" class="mb-2 rotate-icon"
                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                    alt="">
                              </div>
                           </div>
                        </div>
                     </a>
                  </div>
                  <div class="col-md-3">
                     <a href="{{ route('admin.vippooja.order.list', 'all') }}" class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                           <div class="d-flex gap-2 justify-content-between align-items-center">
                              <div class="d-flex flex-column align-items-start">
                                 <h3 class="mb-1 fz-24 text-info">
                                    {{ \App\Models\Service_order::where('type', 'vip')->where('is_block', '!=', 9)->count() }}
                                 </h3>
                                 <div class="text-capitalize mb-0">TOTAL ORDER</div>
                              </div>
                              <div>
                                 <img width="40" class="mb-2"
                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/order.png') }}"
                                    alt="">
                              </div>
                           </div>
                        </div>
                     </a>
                  </div>
                  <div class="col-md-3">
                     <a href="{{ route('admin.vippooja.order.list', 1) }}" class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                           <div class="d-flex gap-2 justify-content-between align-items-center">
                              <div class="d-flex flex-column align-items-start">
                                 <h3 class="mb-1 fz-24 text-success">
                                    {{ \App\Models\Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('is_completed', 1)->where('status', 1)->where('payment_status', 1)->count() }}
                                 </h3>
                                 <div class="text-capitalize mb-0">COMPLETED ORDER</div>
                              </div>
                              <div>
                                 <img width="40"
                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/ordercom.png') }}"
                                    alt="">
                              </div>
                           </div>
                        </div>
                     </a>
                  </div>
                  <div class="col-md-3">
                     <a href="{{ route('admin.vippooja.order.list', 0) }}" class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                           <div class="d-flex gap-2 justify-content-between align-items-center">
                              <div class="d-flex flex-column align-items-start">
                                 <h3 class="mb-1 fz-24 text-primary">
                                    {{ \App\Models\Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('status', 0)->where('is_completed', 0)->where('payment_status', 1)->count() }}
                                 </h3>
                                 <div class="text-capitalize mb-0">PENDING ORDER</div>
                              </div>
                              <div>
                                 <img width="40"
                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/panding.png') }}"
                                    alt="">
                              </div>
                           </div>
                        </div>
                     </a>
                  </div>
                  <div class="col-md-3">
                     <a href="{{ route('admin.vippooja.order.list', 6) }}" class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                           <div class="d-flex gap-2 justify-content-between align-items-center">
                              <div class="d-flex flex-column align-items-start">
                                 <h3 class="mb-1 fz-24 text-danger">
                                    {{ \App\Models\Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('status', 6)->where('order_status', 6)->count() }}
                                 </h3>
                                 <div class="text-capitalize mb-0">REJECTED ORDER</div>
                              </div>
                              <div>
                                 <img src="https://cdn-icons-png.flaticon.com/512/1828/1828665.png"
                                    alt="Rejected Icon" width="40">
                              </div>
                           </div>
                        </div>
                     </a>
                  </div>
               </div>
            </div>
         </div>
         <div class="d-flex gap-3 mt-3">
            <!-- Schedule Count Card -->
            <div class="card card-body h-100 justify-content-center">
               <div class="d-flex gap-2 justify-content-between align-items-center">
                  <div class="d-flex flex-column align-items-start">
                     <h3 class="mb-1 fz-24 text-primary">
                        {{ \App\Models\Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('order_status', 3)->where('package_id', 5)->count() }}
                     </h3>
                     <div class="text-capitalize mb-0">Schedule</div>
                  </div>
                  <div>
                     <img src="https://cdn-icons-png.flaticon.com/512/7474/7474976.png"
                        alt="Schedule Icon" width="40">
                  </div>
               </div>
            </div>
            <!-- Live Stream Count Card -->
            <div class="card card-body h-100 justify-content-center">
               <div class="d-flex gap-2 justify-content-between align-items-center">
                  <div class="d-flex flex-column align-items-start">
                     <h3 class="mb-1 fz-24 text-primary">
                        {{ \App\Models\Service_order::where('order_status', 4)->where('is_block', '!=', 9)->where('type', 'vip')->where('package_id', 5)->count() }}
                     </h3>
                     <div class="text-capitalize mb-0">Live Stream</div>
                  </div>
                  <div>
                     <img src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" alt="Video Icon" width="40">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="row mt-20">
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <div class="row g-2 flex-grow-1">
                  <div class="col-lg-12 d-flex justify-content-end">
                     @if (Helpers::modules_permission_check('Vip Order', 'All', 'block') ||
                           Helpers::modules_permission_check('Vip Order', 'Pending', 'block') ||
                           Helpers::modules_permission_check('Vip Order', 'Completed', 'block') ||
                           Helpers::modules_permission_check('Vip Order', 'Canceled', 'block') ||
                           Helpers::modules_permission_check('Vip Order', 'Rejected', 'block'))
                     <a href="javascript:void(0);" onclick="block_order(this)" class="btn btn-danger">
                        Block User Orders
                     </a>
                     @endif
                  </div>
               </div>
            </div>
            @include('admin-views.pooja.order.partial.payment')
            <div class="px-3 py-4">
               <div class="row g-2 flex-grow-1">
                  <div class="col-md-12">
                     <table id="vipOrderTable" class="table table-bordered">
                        <thead>
                           <tr>
                            <th>{{ translate('SL') }}</th>
                                <th>{{ translate('Service Name & Performing Date') }}</th>
                                <th>{{ translate('order_Id') }}</th>
                                <th>{{ translate('create_order_Date') }}</th>
                                <th>{{ translate('customer') }}</th>
                                <th>{{ translate('is_prashad') }}</th>
                                <th>{{ translate('purohit') }}</th>
                                <th>{{ translate('amount') }}</th>
                                <th>{{ translate('status') }}</th>
                                <th class="text-center">{{ translate('action') }}</th>
                           </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>{{ translate('SL') }}</th>
                                <th>{{ translate('Service Name & Performing Date') }}</th>
                                <th>{{ translate('order_Id') }}</th>
                                <th>{{ translate('create_order_Date') }}</th>
                                <th>{{ translate('customer') }}</th>
                                <th>{{ translate('is_prashad') }}</th>
                                <th>{{ translate('purohit') }}</th>
                                <th>{{ translate('amount') }}</th>
                                <th>{{ translate('status') }}</th>
                                <th class="text-center">{{ translate('action') }}</th>
                            </tr>
                        </tfoot>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="box" style="display: none;">
      <div class="b b1"></div>
      <div class="b b2"></div>
      <div class="b b3"></div>
      <div class="b b4"></div>
   </div>
</div>
{{-- Model --}}
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
{{-- Rejected Model --}}
<div class="modal fade" id="rejectedModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
   aria-hidden="true">
   <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Order Rejected ::<span id="OrderIdVAl"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-8">
                  <form action="{{ route('admin.vippooja.order.updatedOrder') }}" method="post"
                     id="Rejcted_model">
                     @csrf
                     <input type="hidden" name="order_id" id="OrderId">
                     <input type="hidden" name="service_id" id="ServiceId">
                     <input type="text" name="booking_date" class="form-control mb-2" required
                        id="BookingDateSelected" placeholder="Select Booking Date">
                     <textarea class="ckeditor" id="editor" placeholder="Enter Rejected Reason" name="reject_reason"
                        style="height:300px"></textarea>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                     </div>
                  </form>
               </div>
               <div class="col-md-4">
                  <table class="table table-bordered">
                     <tbody>
                        <tr>
                           <th><strong>Service Name</strong></th>
                           <td colspan="2"><span id="NameofService"></span></td>
                        </tr>
                        <tr>
                           <th><strong>Customer Name / Mobile</strong></th>
                           <td colspan="2"><span id="customerName"></span><br>
                              <span id="MobileCustomer"></span>
                           </td>
                        </tr>
                     </tbody>
                  </table>
                  <h3>Note:</h3>
                  <p>Please select a date and type it in the text area below in the format
                     <strong>yyyy-mm-dd</strong>:
                  </p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
{{-- bLOCK uSER dETAILS--}}
    <div class="modal fade" id="BlockUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="blockUserOrdersLabel">Block User Service Orders</h5>
                        <p class="mb-0 text-muted" style="font-size: 14px;">
                            Select the customer ID below. All service orders for this customer will be blocked based on your
                            selection.
                        </p>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="customer_id">Select Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" style="width:100%;">
                            <option value="">Search by name or mobile...</option>
                            @foreach ($users as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->f_name }} {{ $customer->l_name }} ({{ $customer->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Orders List -->
                    <div id="ordersList" class="mt-3" style="display:none;">
                        <div class="form-check mb-2">
                            <input type="checkbox" id="selectAllOrders" class="form-check-input">
                            <label for="selectAllOrders" class="form-check-label">Select All Orders</label>
                        </div>
                        <div id="orderCheckboxes"></div>
                    </div>

                    <button id="blockOrdersBtn" class="btn btn-danger mt-3 w-100" disabled>Block Selected
                        Orders</button>


                </div>

            </div>
        </div>
    </div>
{{-- VIP Model --}}
<div class="modal fade" id="Assgine-the-pandit" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
   aria-hidden="true" data-keyboard="false" data-backdrop="static">
   <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <div>
               <h5 class="modal-title">Change Pandit</h5>
               <p class="text-muted small mt-1">
                  आप इस VIP पूजा के लिए नियुक्त पंडित (पुरोहित) को बदलने जा रहे हैं। कृपया ध्यान दें कि नया चयन  ग्राहक की अपेक्षाओं और निर्धारित समय के अनुसार होना चाहिए।
               </p>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="">
               <label class="font-weight-bold title-color fz-14">{{ translate('type_of_pandit_ji') }}</label>
               <select name="astrologer_type" id="astrologer-type" class="astrologer-type form-control">
                  <option value="in house">In house</option>
                  <option value="freelancer">Freelancer</option>
               </select>
               <br>
               <div class="" id="in-house">
                  <label
                     class="font-weight-bold title-color fz-14">{{ translate('Inhouse_Astrologer') }}</label>
                  <select name="assign_pandit" id="assign-inhouse-pandit" class="assign-pandit form-control">
                  </select>
               </div>
               <div class="" id="freelancer" style="display: none;">
                  <label
                     class="font-weight-bold title-color fz-14">{{ translate('freelancer_Astrologer') }}</label>
                  <select name="assign_pandit" id="assign-freelancer-pandit"
                     class="assign-pandit form-control">
                  </select>
               </div>
               <form action="{{ route('admin.vippooja.order.pandit') }}" method="post"
                  id="assign-pandit-form">
                  @csrf
                  <input type="hidden" name="id" id="table-id">
                  <input type="hidden" name="booking_date" id="booking-date">
                  <input type="hidden" name="service_id" id="service-id">
                  <input type="hidden" name="package_id" id="package-id">
                  <input type="hidden" name="pandit_id" id="pandit-id-val">
               </form>
            </div>
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
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
   $(document).ready(function () {
       $('#vipOrderTable').DataTable({
            pageLength: 20,
            scrollY: '500px',
            scrollCollapse: true,
            paging: true,
            fixedHeader: true,
            fixedFooter: true,
            lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
            ],
   
           ajax: "{{ route('admin.vippooja.order.data') }}",
           order: [[0, 'asc']],
           columns: [
               {
                   data: 'payment_status',
                   name: 'payment_status',
                   render: function (data, type, row, meta) {
                       let color, label;
   
                       if (row.payment_status == 1) {
                           color = '#28a745';
                           label = 'SUCCESS';
                       } else if (row.payment_status == 2) {
                           color = '#dc3545';
                           label = 'FAILED';
                       } else if (row.payment_status == 0) {
                           color = '#ffc107';
                           label = 'PENDING';
                       }
   
                       return `
                            <div style="display: flex; align-items: stretch; min-height: 80px;">
                               <div style=" width: 28px; background-color: ${color}; color: white;
                                 font-weight: bold;  font-size: 11px; writing-mode: vertical-rl;
                                  text-orientation: mixed; text-transform: uppercase;
                                   display: flex;  justify-content: center;
                                    align-items: center; border-radius: 0 4px 4px 0;
                               ">
                                   ${label}
                               </div>
                               <div style="flex: 1; padding-left: 10px; display: flex; align-items: center;">
                               ${meta.row + 1}
                           </div>
                           </div>`;
                   },
                   createdCell: function(td, cellData, rowData, row, col) {
                           td.style.paddingLeft = '0px';
                       }
               },
               {
                   data: 'pooja_name',
                   name: 'pooja_name',
                   render: (data, type, row) => {
                       return data
                           ? `<div style="font-weight: 600;">${data}</div>
                           <div style="color:red; font-weight:bold;">
                               ${row.booking_date || ''}
                           </div>`
                           : 'N/A';
                   }
               },
               {
                   data: 'order_id',
                   name: 'order_id',
                   render: (data) => `<a href="#" class="text-primary" style="font-weight: 600;">${data}</a><br>
                                   <span class="badge badge-danger">W</span>`
               },
               {
                   data: 'created_at',
                   name: 'created_at',
                   render: (data) => data || 'N/A'
               },
               {
                   data: 'customer_name',
                   name: 'customer_name',
                   render: (data, type, row) => data
                       ? `<div>${data}</div>
                       <div style="font-size: 13px; color: gray;">${row.customer_phone || ''}</div>`
                       : 'N/A'
               },
               {
                   data: 'is_prashad',
                   name: 'is_prashad',
                   render: (data) => data == 1
                       ? `<span class="badge badge-success">Yes</span>`
                       : `<span class="badge badge-danger">No</span>`
               },
               {
                   data: 'pandit_name',
                   name: 'pandit_name',
                   render: (data) => data
                       ? `<span style="font-weight: 500;">${data}</span>`
                       : `<span class="badge badge-soft-danger">Not Assigned</span>`
               },
               {
                   data: null,
                   name: 'amount',
                   render: (data) => {
                       let amount = (parseFloat(data.pay_amount) || 0) - (parseFloat(data.coupon_amount) || 0);
                       return `₹${amount.toFixed(2)}`;
                   }
               },
               {
                   data: 'order_status',
                   name: 'order_status',
                   render: (data) => {
                       const statuses = {
                           0: 'Pending',
                           1: 'Completed',
                           2: 'Cancel',
                           3: 'Schedule Time',
                           4: 'Live Pooja',
                           5: 'Share Soon',
                           6: 'Rejected'
                       };
                       const badgeClass = {
                           0: 'primary',
                           1: 'success',
                           2: 'danger',
                           3: 'warning',
                           4: 'secondary',
                           5: 'info',
                           6: 'warning'
                       };
                       return `<span class="badge badge-soft-${badgeClass[data] || 'light'}">${statuses[data] || 'Unknown'}</span>`;
                   }
               },
               {
                   data: 'id',
                   orderable: false,
                   searchable: false,
                   className: "text-center",
                   render: function (data, type, row) {
                       let detailUrl = `${baseDetailUrl}/${data}`;
                       let invoiceUrl = `${baseInvoiceUrl}/${data}`;
                       let buttons = '';
   
                       @if (Helpers::modules_permission_check('Vip Order', 'All', 'schedule') ||
                           Helpers::modules_permission_check('Vip Order', 'Pending', 'schedule') ||
                           Helpers::modules_permission_check('Vip Order', 'Completed', 'schedule') ||
                           Helpers::modules_permission_check('Vip Order', 'Canceled', 'schedule') ||
                           Helpers::modules_permission_check('Vip Order', 'Rejected', 'schedule'))
                           if (row.status == 6 || row.order_status == 6) {
                               buttons += `
                                   <button class="btn btn-outline-primary btn-sm square-btn"
                                       data-toggle="modal"
                                       data-target="#rejected-modal"
                                       data-servicename="${row.pooja_name || ''}"
                                       data-orderid="${row.order_id}"
                                       data-id="${row.id}"
                                       data-customer="${row.customer_name || ''}"
                                       data-customerMobile="${row.customer_phone || ''}"
                                       data-poojaType="${row.pooja_type || ''}"
                                       onclick="RejctedModel(this)">
                                       <i class="tio-message"></i>
                                   </button>`;
                           }
                       @endif
   
                       @if (Helpers::modules_permission_check('Vip Order', 'All', 'detail') ||
                           Helpers::modules_permission_check('Vip Order', 'Pending', 'detail') ||
                           Helpers::modules_permission_check('Vip Order', 'Completed', 'detail') ||
                           Helpers::modules_permission_check('Vip Order', 'Canceled', 'detail') ||
                           Helpers::modules_permission_check('Vip Order', 'Rejected', 'detail'))
                           if (!(row.status == 6 || row.order_status == 6)) {
                               buttons += `
                                   <a class="btn btn-outline-primary btn-sm square-btn"
                                       target="_blank"
                                       href="${detailUrl}"
                                       title="View">
                                       <i class="tio-visible"></i>
                                   </a>`;
                           }
                       @endif
   
                       @if (Helpers::modules_permission_check('Vip Order', 'All', 'download') ||
                           Helpers::modules_permission_check('Vip Order', 'Pending', 'download') ||
                           Helpers::modules_permission_check('Vip Order', 'Completed', 'download') ||
                           Helpers::modules_permission_check('Vip Order', 'Canceled', 'download') ||
                           Helpers::modules_permission_check('Vip Order', 'Rejected', 'download'))
                           buttons += `
                               <a class="btn btn-outline-info btn-sm square-btn" href="${invoiceUrl}" title="Download Invoice">
                                   <i class="tio-download-to"></i>
                               </a>`;
                       @endif

                       @if (Helpers::modules_permission_check('Vip Order', 'All', 'assign-pandit') ||
                           Helpers::modules_permission_check('Vip Order', 'Pending', 'assign-pandit') ||
                           Helpers::modules_permission_check('Vip Order', 'Completed', 'assign-pandit') ||
                           Helpers::modules_permission_check('Vip Order', 'Canceled', 'assign-pandit') ||
                           Helpers::modules_permission_check('Vip Order', 'Rejected', 'assign-pandit'))
                       if (![1, 6].includes(row.order_status)) {
                           buttons += `
                               <a class="btn btn-outline-warning btn-sm square-btn"
                                   title="Assign Pandit"
                                   href="javascript:void(0);"
                                   data-id="${row.id}"
                                   data-tableid="${row.id}"
                                   data-serviceid="${row.service_id}"
                                   data-bookingdate="${row.booking_date}"
                                   data-packageid="${row.package_id}"
                                   onclick="pandit_model(this)">
                                   <img src="{{ asset('public/assets/back-end/img/pooja/pandit.png') }}" alt="" width="20px" height="20px">
                               </a>`;
                       }
                       @endif

                       @if (Helpers::modules_permission_check('Vip Order', 'All', 'pay') ||
                           Helpers::modules_permission_check('Vip Order', 'Pending', 'pay') ||
                           Helpers::modules_permission_check('Vip Order', 'Completed', 'pay') ||
                           Helpers::modules_permission_check('Vip Order', 'Canceled', 'pay') ||
                           Helpers::modules_permission_check('Vip Order', 'Rejected', 'pay'))
                       if (row.payment_status == 0){
                            buttons += `<button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 shadow-sm fw-bold mt-2" onclick="pendingOrder('${row.order_id}')">Pay</button>`;
                           }
                           @endif
                           
                       return `<div class="d-flex gap-2 justify-content-center">${buttons}</div>`;
                   }
               }
           ]
   
       });
   });
</script>
<script>
   const baseDetailUrl = "{{ url('admin/vippooja/order/details') }}";
   const baseInvoiceUrl = "{{ url('admin/vippooja/order/generate/invoice') }}";
</script>
<script>
   // datepicker
   var today = new Date();
   var tomorrow = new Date(today);
   tomorrow.setDate(today.getDate() + 1);
   //Start Date
   $('#BookingDateSelected').datepicker({
       uiLibrary: 'bootstrap4',
       format: 'yyyy/mm/dd',
       modal: true,
       footer: true,
       minDate: today,
       todayHighlight: true
   });
</script>
<script type="text/javascript">
   $(document).ready(function() {
       $('.ckeditor').ckeditor();
   });
</script>
<script>
   $(document).ready(function() {
       // Capture click event on order links
       $('.order-link').click(function(e) {
           e.preventDefault();
           var orderId = $(this).data('id');
   
           // Make Ajax call to fetch order details
           $.ajax({
               url: "{{ url('admin/vippooja/order/get-order-details') }}",
               type: 'GET',
               data: {
                   id: orderId
               },
               success: function(data) {
                   function formatDate(dateString) {
                       const date = new Date(dateString);
                       const options = {
                           year: 'numeric',
                           month: 'short',
                           day: '2-digit',
                           hour: '2-digit',
                           minute: '2-digit',
                           hour12: true
                       };
                       return date.toLocaleString('en-US', options).replace(',', '');
                   }
                   console.log(data);
                   var baseUrl = "{{ url('/') }}";
                   let membersArray;
                   let membersArray = JSON.parse(data.members);
                   let name = membersArray[0];
   
   
                   $('#order-details').html(`
              <tr><th><b>Booking Id</b></th><td>${data.order_id}</td><th><b>Booking Date</b></th><td>${formatDate(data.created_at)}</td><th><b>TXN ID </b></th><td>${data.payment_id ? data.payment_id : data.wallet_translation_id}</td></tr>
              <tr><th><b>Pooja Details</b></th><td colspan=3><b>Pooja Name:</b>${data.vippoojas.name},<br><b>Pooja Venue:</b>{data.astrologer.is_pandit_primary_mandir_location}</td><th><b>Prashad(YES/NO)</b></th><td><span class="badge badge-soft-${data.is_prashad == 0 ? 'primary' : (data.is_prashad == 1 ? 'success' : 'danger')}">
                          ${data.is_prashad == 0 ? 'No' : (data.is_prashad == 1 ? 'Yes' : 'Canceled')}
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
                  <td  colspan=2>
                      <span class="badge badge-soft-${data.status == 0 ? 'primary' : (data.status == 1 ? 'success' : 'danger')}">
                          ${data.status == 0 ? 'Pending' : (data.status == 1 ? 'Completed' : 'Canceled')}
                      </span>
                  </td>
                  
              </tr> 
              <tr>
                  <th><b>Number of Members Name</b></th>
                      <td>${name}</td>
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
                  <th><b>Package</b></th>
                  <td  colspan=2>
                      ${data.packages.title}
                  </td>
                  <th><b>Package Price Pay</b></th><td  colspan=2>₹  ${data.package_price}</td>
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
   function RejctedModel(that) {
       var orderid = $(that).data('orderid');
       var id = $(that).data('id');
       var servicename = $(that).data('servicename');
       var customer = $(that).data('customer');
       var bookingDate = $(that).data('bookingdate');
       var poojaType = $(that).data('poojaType');
       var customerMobile = $(that).data('customerMobile');
   
       // Populate the modal fields
       $('#OrderId').val(orderid);
       $('#ServiceId').val(id);
       $('#ServiceName').val(servicename);
       $('#NextBookingDate').val(bookingDate);
       // Populate the table with dynamic content
       $('#NameofService').text(servicename);
       $('#OrderIdVAl').text(orderid);
       $('#customerName').text(customer);
       $('#NameCustomer').text(customer);
       $('#MobileCustomer').text(customerMobile);
       $('#rejectedModal').modal('show');
   
       var message = `<p>Dear ${customer},</p>
              <p>We regret to inform you that your booking for the pooja <strong>${servicename}</strong> has been rejected.</p>
              <p>The next available date for this pooja is on <strong>yyyy-mm-dd</strong>.</p>
              <p>We apologize for the inconvenience. Please feel free to contact us if you have any questions.</p>
              <p>Best regards,</p>
              <p>Mahakal.com</p>`;
       CKEDITOR.instances.editor.setData(message);
   
   }
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
   new DataTable('#example', {
       layout: {
           topStart: {
               buttons: ['print']
           }
       }
   });
</script>
<script>
   $('.assign-pandit').on('change', function() {
       var panditId = $(this).val();
       $('#pandit-id-val').val(panditId);
       Swal.fire({
           title: 'Are you sure you want to assign this VIP? This action will change the current VIP assignment',
           type: 'success',
           showCancelButton: true,
           confirmButtonColor: '#3085d6',
           cancelButtonColor: '#d33',
           confirmButtonText: 'Yes',
           cancelButtonText: 'Cancel',
           reverseButtons: true
       }).then((result) => {
           if (result.value) {
               $('#assign-pandit-form').submit();
           }
       });
   });
   
   function pandit_model(that) {
       $('.box').css('display', 'block');
       var panditId = $(that).data('id');
       var bookdate = $(that).data('bookingdate');
       var serviceId = $(that).data('serviceid');
       var packageId = $(that).data('packageid');
       var tableId = $(that).data('tableid');
   
       $('#service-id').val(serviceId);
       $('#package-id').val(packageId);
       $('#booking-date').val(bookdate);
       $('#table-id').val(tableId);
   
       var inhouseList = "";
       var freelancerList = "";
   
       $.ajax({
           type: "get",
           url: "{{ url('admin/vippooja/order/getpandit') }}" + '/' + serviceId + '/' + bookdate,
           success: function(response) {
               if (response.status == 200) {
                   $('#assign-inhouse-pandit').html('');
                   $('#assign-freelancer-pandit').html('');
   
                   if (response.inhouse.length > 0) {
                       let inhouseList = `<option value="" selected disabled>Select Pandit Ji</option>`;
                       let addedPanditIds = new Set(); // Track added Pandit IDs
   
                       $.each(response.inhouse, function(key, value) {
                           if (
                               value.is_pandit_pooja_per_day > value.checkastro &&
                               !addedPanditIds.has(value.id)
                           ) {
                               inhouseList += `<option value="${value.id}">${value.name}</option>`;
                               addedPanditIds.add(value.id);
                           }
                       });
   
                       // Update the dropdown just once, after loop
                       $('#assign-inhouse-pandit').html(inhouseList);
                   } else {
                       $('#assign-inhouse-pandit').html(
                           '<option value="" selected disabled>No Pandit Found</option>'
                       );
                   }
   
   
                   if (response.freelancer.length > 0) {
                       let freelancerList = `<option value="" selected disabled>Select Pandit Ji</option>`;
                       let addedPanditIds = new Set(); // Track added IDs
   
                       $.each(response.freelancer, function(key, value) {
                           if (
                               value.is_pandit_pooja_per_day > value.checkastro &&
                               !addedPanditIds.has(value.id)
                           ) {
                               let priceText = value.price ?
                                   `₹${parseInt(value.price).toLocaleString('en-IN')}` : 'N/A';
                               freelancerList +=
                                   `<option value="${value.id}">${value.name} - Price: ${value.price}</option>`;
                               addedPanditIds.add(value.id); // Mark this ID as added
                           }
                       });
   
                       $('#assign-freelancer-pandit').empty().append(freelancerList);
                   } else {
                       $('#assign-freelancer-pandit').empty().append(
                           '<option value="" selected disabled>No Pandit Found</option>'
                       );
                   }
                   
                   $('.box').css('display', 'none');
                   $('#Assgine-the-pandit').modal('show');
               } else {
                   alert('An error occurred');
               }
           }
       });
   }
   
   function orderCount(panditId, bookdate, callback) {
       $.ajax({
           type: "get",
           url: "{{ url('admin/pooja/orders/get-pandit-order-count') }}" + '/' + panditId + '/' + bookdate,
           success: function(response) {
               callback(response.ordercount); // Pass the order count to the callback
           },
           error: function() {
               callback(0); // In case of error, pass 0 as the count
           }
       });
   }
</script>
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
   });
   
   $('#astrologer-type-change').change(function(e) {
       e.preventDefault();
       var type = $(this).val();
       if (type == 'in house') {
           $('#in-house-change').show();
           $('#freelancer-change').hide();
       } else if (type == 'freelancer') {
           $('#in-house-change').hide();
           $('#freelancer-change').show();
       }
   });
</script>
<script>
   $('.assign-astrologer-change').on('change', function() {
       var panditId = $(this).val();
       $('#change-pandit-id-val').val(panditId);
       Swal.fire({
           title: 'Are You Sure To Change Pandit',
           type: 'success',
           showCancelButton: true,
           confirmButtonColor: '#3085d6',
           cancelButtonColor: '#d33',
           confirmButtonText: 'Yes',
           cancelButtonText: 'Cancel',
           reverseButtons: true
       }).then((result) => {
           if (result.value) {
               $('#change-pandit-form').submit();
           }
       });
   });
</script>


{{-- pending order pay --}}
   <script>
      function pendingOrder(orderId) {
         if (!orderId) {
               alert('Order ID not found');
         } else {
               $('#pending-order-id').val(orderId);
               $('.pooja-pending-form').submit();
         }
      }
      function block_order(el) {
         $('#BlockUserModal').modal('show'); // Show modal
      }
      $(document).ready(function() {

         //  Enable search in select
         $('#customer_id').select2({
               placeholder: 'Search by name or mobile...',
               allowClear: true,
               matcher: function(params, data) {
                  if ($.trim(params.term) === '') return data;
                  let term = params.term.toLowerCase();
                  let text = data.text.toLowerCase();
                  if (text.includes(term)) return data;
                  return null;
               }
         });

         //  Fetch orders when customer selected
         $('#customer_id').on('change', function() {
               let customerId = $(this).val();
               if (!customerId) {
                  $('#ordersList').hide();
                  $('#orderCheckboxes').html('');
                  $('#blockOrdersBtn').prop('disabled', true);
                  return;
               }

               $.ajax({
                  url: "{{ route('admin.vippooja.order.get-customer-orders') }}",
                  type: "GET",
                  data: {
                     customer_id: customerId
                  },
                  success: function(response) {
                     if (response.orders.length > 0) {
                           let html = '';
                           response.orders.forEach(order => {
                              html += `
                           <div class="form-check">
                              <input type="checkbox" name="order_ids[]" value="${order.order_id}" class="form-check-input orderCheckbox">
                              <label class="form-check-label">Order ID: ${order.order_id}</label>
                           </div>
                     `;
                           });
                           $('#orderCheckboxes').html(html);
                           $('#ordersList').show();
                     } else {
                           $('#orderCheckboxes').html(
                              '<p class="text-muted">No orders found for this customer.</p>'
                           );
                           $('#ordersList').show();
                     }
                  }
               });
         });

         //  Select all orders
         $(document).on('change', '#selectAllOrders', function() {
               $('.orderCheckbox').prop('checked', $(this).prop('checked'));
               toggleBlockButton();
         });

         //  Enable/Disable Block button
         $(document).on('change', '.orderCheckbox', function() {
               toggleBlockButton();
         });

         function toggleBlockButton() {
               let checked = $('.orderCheckbox:checked').length > 0;
               $('#blockOrdersBtn').prop('disabled', !checked);
         }

         //  Block selected orders
         $('#blockOrdersBtn').click(function() {
               let selectedOrders = $('.orderCheckbox:checked').map(function() {
                  return $(this).val();
               }).get();

               $.ajax({
                  url: "{{ route('admin.vippooja.order.block-orders') }}",
                  type: "POST",
                  data: {
                     _token: "{{ csrf_token() }}",
                     order_ids: selectedOrders
                  },
                  success: function(response) {
                     toastr.success(response.message);

                     //  Modal hide
                     $('#BlockUserModal').modal('hide');
                     $('#ordersList').hide();
                     $('#orderCheckboxes').html('');
                     $('#blockOrdersBtn').prop('disabled', true);
                     $('#customer_id').val('').trigger('change');
                  }
               });
         });

      });
   </script>
@endpush