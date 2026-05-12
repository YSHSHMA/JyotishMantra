@extends('layouts.back-end.app-tour')
@section('title', translate('overview'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')

<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('traveller_details') }}
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
                  <a class="nav-link {{ (($name == 'review')?'active':'') }}" id="review-tab" data-toggle="tab" href="#review-content">
                     {{ translate('review') }}
                  </a>
               </li>
            </ul>
            <div class="tab-content">
               <div class="tab-pane fade {{ (($name == 'null')?'show active':'') }}" id="overview-content">
                  <div class="row">
                      @include('all-views.tour.overview.partial.overview') 
                  </div>
               </div>
              
               <div class="tab-pane fade {{ (($name == 'order')?'show active':'') }}" id="order-content">
                  <div class="row">
                     @include('all-views.tour.overview.partial.order')
                  </div>
               </div>
               <div class="tab-pane fade {{ (($name == 'refund')?'show active':'') }}" id="refund-content">
                  <div class="row">
                     @include('all-views.tour.overview.partial.refund')
                  </div>
               </div>
               
               
               <div class="tab-pane fade {{ (($name == 'review')?'show active':'') }}" id="review-content">
                  <div class="row">
                  @include('all-views.tour.overview.partial.review')
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

@endpush