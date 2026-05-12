@extends('layouts.back-end.app')

@section('title', translate('traveler_details'))
@push('css_or_js')
<style>
   .rainbow {
      background-color: #343A40;
      border-radius: 4px;
      color: #000;
      cursor: pointer;
      padding: 8px 16px;
   }

   .rainbow-1 {
      background-image: linear-gradient(359deg, #90e979d9 13%, #f8f8f8 54%, #ebd859 103%);
      animation: slidebg 5s linear infinite;
   }

   @keyframes slidebg {
      to {
         background-position: 20vw;
      }
   }
</style>
@endpush

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('traveler_details') }}
      </h2>
   </div>
   <div class="row">
      <div class="card w-100">
         <div class="card-body">
            <ul class="nav nav-tabs w-fit-content mb-4">
               <li class="nav-item text-capitalize">
                  <a class="nav-link {{ (($name == 'null')?'active':'') }}" id="overview-tab" data-toggle="tab" href="#overview-content">
                     {{ translate('overview') }}
                  </a>
               </li>
               <li class="nav-item text-capitalize">
                  <a class="nav-link {{ (($name == 'order')?'active':'') }}" id="order-tab" data-toggle="tab" href="#order-content">
                     {{ translate('order') }}
                  </a>
               </li>
               <li class="nav-item text-capitalize">
                  <a class="nav-link {{ (($name == 'refund')?'active':'') }}" id="refund-tab" data-toggle="tab" href="#refund-content">
                     {{ translate('refund') }}
                  </a>
               </li>
               <li class="nav-item text-capitalize">
                  <a class="nav-link" id="setting-tab" data-toggle="tab" href="#setting-content">
                     {{ translate('setting') }}
                  </a>
               </li>
               <li class="nav-item text-capitalize">
                  <a class="nav-link {{ (($name == 'review')?'active':'') }}" id="review-tab" data-toggle="tab" href="#review-content">
                     {{ translate('review') }}
                  </a>
               </li>
            </ul>
            <div class="tab-content">
               <div class="tab-pane fade {{ (($name == 'null')?'show active':'') }}" id="overview-content">
                  <div class="row">
                     @include('admin-views.tour_and_travels.tour_visit.partial.overview')
                  </div>
               </div>
              
               <div class="tab-pane fade {{ (($name == 'order')?'show active':'') }}" id="order-content">
                  <div class="row">
                     @include('admin-views.tour_and_travels.tour_visit.partial.order') 
                  </div>
               </div>
               <div class="tab-pane fade {{ (($name == 'refund')?'show active':'') }}" id="refund-content">
                  <div class="row">
                     @include('admin-views.tour_and_travels.tour_visit.partial.refund')
                  </div>
               </div>
               
               <div class="tab-pane fade" id="setting-content">
                  <div class="row">
                        @include('admin-views.tour_and_travels.tour_visit.partial.setting')
                  </div>
               </div>
               
               <div class="tab-pane fade {{ (($name == 'review')?'show active':'') }}" id="review-content">
                  <div class="row">
                  @include('admin-views.tour_and_travels.tour_visit.partial.review')
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div id="modal_order_view" class="modal fade modal-center modal-order" role="dialog" aria-label="modal order">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="close"><i class="icon-close" aria-hidden="true"></i></button>
            <h4 class="modal-title">Order view</h4>
            <div class="form-group view_orders_items">

            </div>

         </div>
      </div>
   </div>
</div>

<!--  -->

<!-- Modal -->
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
   let getYesWord = $('#message-yes-word').data('text');
   let getCancelWord = $('#message-cancel-word').data('text');
   $('.reject-artist_data').on('click', function() {
      let astrologerId = $(this).attr("data-id");
      Swal.fire({
         title: 'Are You Sure To '+ $(this).data('title'),
         type: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: getYesWord,
         cancelButtonText: getCancelWord,
         reverseButtons: true
      }).then((result) => {
         if (result.value) {
            $('#' + astrologerId).submit();
         }
      });
   });

   $(document).ready(function() {
      var totalOrderAmount = 0;
      var totalFinalAmount = 0;
      var totalcommission = 0;
      var totalgovttax = 0;

      // Iterate over each row in the tbody
      $('#datatable_transaction tbody tr').each(function() {
         // Get the order amount and final amount from each row
         var orderAmount = parseFloat($(this).find('.order_amount').text()) || 0;
         var finalAmount = parseFloat($(this).find('.final_amount').text()) || 0;
         var commission = parseFloat($(this).find('.order_commission').text()) || 0;
         var govttax = parseFloat($(this).find('.order_govt_tax').text()) || 0;

         // Add the amounts to the total
         totalOrderAmount += orderAmount;
         totalFinalAmount += finalAmount;
         totalcommission += commission;
         totalgovttax += govttax;
      });

      // Display the totals in the footer
      $('#totalOrderAmount').text(totalOrderAmount.toFixed(2));
      $('#totalFinalAmount').text(totalFinalAmount.toFixed(2));
      $('#totalOrdercommission').text(totalcommission.toFixed(2));
      $('#totalOrdergovttax').text(totalgovttax.toFixed(2));
   });

   function orderitemviews(order_id) {
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
         }
      });
      $.ajax({
         url: "{{ route('admin.event-managment.event.event-order-view') }}",
         data: {
            order_id
         },
         dataType: "json",
         type: "post",
         success: function(data) {
            if (data.success == 1) {
               $('#modal_order_view').modal('show');
               $(".view_orders_items").html(data.data);

            } else {
               toastr.error('Data Not Found');
            }

         }
      });
   }

   function getAllPackage(that) {
      var htmlContent = $(that).data('html');
      $('#modalBodyContent').html(htmlContent);
      $('#dataModal').modal('show');
   }

   function showDetailsButton(that){
      var point = $(that).data('point');
      var availableSoldRows = $(`.available-sold-row${point}`);

      if (availableSoldRows.hasClass('d-none')) {
         availableSoldRows.removeClass('d-none').addClass('visible');
      } else {
         availableSoldRows.removeClass('visible').addClass('d-none');
      }
   }

   $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>
@endpush