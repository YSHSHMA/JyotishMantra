@extends('layouts.back-end.app')

@section('title', translate('Passenger Company Details'))
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
         {{ translate('Passenger Company Details') }}
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
                  <a class="nav-link" id="transaction-tab" data-toggle="tab" href="#transaction-content-withdrawal">
                     {{ translate('transaction') }}
                  </a>
               </li>
               <li class="nav-item text-capitalize">
                  <a class="nav-link" id="transaction-tab" data-toggle="tab" href="#transaction-content">
                     {{ translate('order_transaction') }}
                  </a>
               </li>
              
            </ul>
            <div class="tab-content">
               <div class="tab-pane fade {{ (($name == 'null')?'show active':'') }}" id="overview-content">
                  <div class="row">
                     @include('admin-views.tour_and_travels.travels.overview')
                  </div>
               </div>
               
               <div class="tab-pane fade" id="transaction-content-withdrawal">
                  <div class="row">
                     <div class="content container-fluid">
                        <div class="mb-3">
                           <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                              <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png')}}" alt="">
                              {{translate('withdraw_Request')}}
                           </h2>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="card">
                                 <div class="px-3 py-4">
                                    <div class="row align-items-center">
                                       <div class="col-lg-4">
                                          <h5>
                                             {{ translate('withdraw_Request_Table')}}
                                             <span class="badge badge-soft-dark radius-50 fz-12 ml-1" id="withdraw-requests-count">{{-- $withdrawRequests->total() --}}</span>
                                          </h5>
                                       </div>
                                       <div class="col-lg-8 mt-3 mt-lg-0 d-flex gap-3 justify-content-lg-end">
                                          
                                          <select name="status" class="custom-select min-w-120 max-w-200 status-filter">
                                             <option value="all" {{ request('approved') == 'all'?'selected':''}}>{{translate('all')}}</option>
                                             <option value="approved" {{ request('approved') == 'approved' ?'selected':''}}>{{translate('approved')}}</option>
                                             <option value="denied" {{ request('approved') == 'denied'?'selected':''}}>{{translate('denied')}}</option>
                                             <option value="pending" {{ request('approved') == 'pending'?'selected':''}}>{{translate('pending')}}</option>
                                          </select>
                                       </div>
                                    </div>
                                 </div>
                                 <div id="status-wise-view">
                                    <div class="table-responsive">
                                       <table id="datatable"
                                          class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                          <thead class="thead-light thead-50 text-capitalize">
                                             <tr>
                                                <th>{{translate('SL')}}</th>
                                                <th>{{translate('amount')}}</th>
                                                <th>{{translate('name') }}</th>
                                                <th>{{translate('request_time')}}</th>
                                                <th class="text-center">{{translate('status')}}</th>
                                                <th class="text-center">{{translate('action')}}</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                             @if(!empty($withdrawRequests) && count($withdrawRequests))
                                             @foreach($withdrawRequests as $key=>$withdraw)
                                             <tr>
                                                <td>{{$withdrawRequests->firstItem()+$key}}</td>
                                                <td>{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $withdraw['amount']), currencyCode: getCurrencyCode())}}</td>

                                                <td>
                                                   @if ($withdraw->deliveryMan)
                                                   <span
                                                      class="title-color hover-c1">{{ $withdraw->deliveryMan->f_name . ' ' . $withdraw->deliveryMan->l_name }}</span>
                                                   @else
                                                   <span>{{translate('not_found')}}</span>
                                                   @endif
                                                </td>
                                                <td>{{ date_format( $withdraw->created_at, 'd-M-Y, h:i:s A') }}</td>
                                                <td class="text-center">
                                                   @if($withdraw->approved==0)
                                                   <label class="badge badge-soft-primary">{{translate('pending')}}</label>
                                                   @elseif($withdraw->approved==1)
                                                   <label class="badge badge-soft-success">{{translate('approved')}}</label>
                                                   @else
                                                   <label class="badge badge-soft-danger">{{translate('denied')}}</label>
                                                   @endif
                                                </td>
                                                @if (Helpers::modules_permission_check('Delivery Men', 'Withdraw', 'detail'))
                                                <td>
                                                   <div class="d-flex justify-content-center">
                                                      @if (isset($withdraw->deliveryMan))
                                                      <button
                                                         class="btn btn-outline-info btn-sm square-btn withdraw-info-show"
                                                         data-action="{{route('admin.delivery-man.withdraw-view',[$withdraw['id']])}}"
                                                         title="{{translate('view')}}">
                                                         <i class="tio-invisible"></i>
                                                      </button>
                                                      @else
                                                      <a class="btn btn-outline-info btn-sm square-btn disabled" href="#">
                                                         <i class="tio-invisible"></i>
                                                      </a>
                                                      @endif
                                                   </div>
                                                </td>
                                                @endif
                                             </tr>
                                             @endforeach
                                             @endif
                                          </tbody>
                                       </table>
                                       @if(count($withdrawRequests)==0)
                                       <div class="text-center p-4">
                                          <img class="mb-3 w-160"
                                             src="{{dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg')}}"
                                             alt="{{translate('image_description')}}">
                                          <p class="mb-0">{{translate('no_data_to_show')}}</p>
                                       </div>
                                       @endif
                                    </div>
                                 </div>
                                 <div class="table-responsive mt-4">
                                    <div class="px-4 d-flex justify-content-center justify-content-md-end">
                                       {{-- $withdrawRequests->links() --}}
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="tab-pane fade" id="transaction-content">
                  <div class="row">
                     <div class="content container-fluid">
                        <div class="mb-3">
                           <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                              <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png')}}" alt="">
                              {{translate('complete_order')}}
                           </h2>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="card">
                                 <div class="px-3 py-4">
                                    <div class="row align-items-center">
                                       <div class="col-lg-4">
                                       </div>
                                       <div class="col-lg-8 mt-3 mt-lg-0 d-flex gap-3 justify-content-lg-end">
                                       </div>
                                    </div>
                                 </div>
                                 <div id="status-wise-view">
                                    <div class="table-responsive">
                                       <table id="datatable"
                                          class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                          <thead class="thead-light thead-50 text-capitalize">
                                             <tr>
                                                <th>{{translate('SL')}}</th>
                                                <th>{{translate('customer_info')}}</th>
                                                <th>{{translate('tour_info') }}</th>
                                                <th>{{translate('TXN_ID')}}</th>
                                                <th>{{translate('amount')}}</th>
                                                <th class="text-center">{{translate('final_amount')}}</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                             @if(!empty($complete_order) && count($complete_order) > 0)
                                             @foreach($complete_order as $orders)
                                             <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                   <span>{{ $orders['userData']['name']}}</span><br>
                                                   <span>{{ $orders['userData']['phone']}}</span><br>
                                                   <span>qty: {{ $orders['qty'] }}</span><br>
                                    <span>package :
                                        @if(!empty($orders['Tour']['package_list']) && json_decode($orders['Tour']['package_list'],true))
                                        @foreach(json_decode($orders['Tour']['package_list'],true) as $val)
                                        @if($val['id'] == $orders['package_id'])
                                        {{ (\App\Models\TourCab::where('id',$val['cab_id'])->first()['name']??"") }}
                                        <a role='tooltip' data-toggle="tooltip" data-html="true" title="
                                        @if(!empty($val['package_id']??''))
                                        @foreach($val['package_id'] as $pn)
                                        <p>Package added : <strong>{{ (\App\Models\TourPackage::where('id',($pn??''))->first()['name']??'') }}</strong></p>
                                        @endforeach 
                                        @endif
                                        ">
                                        <i class="tio-info"></i>
                                    </a>
                                        @endif
                                        @endforeach
                                        @endif
                                    </span><br>
                                                   <span>{{ date('d M,Y H:i:s',strtotime($orders['created_at']))}}</span>
                                                </td>
                                                <td><span>{{ $orders['Tour']['tour_name']}}</span><br>
                                                   <span>{{ date('d M,Y',strtotime($orders['pickup_date']))}} {{ ($orders['pickup_time'])}}</span>
                                                </td>
                                                <td>{{ $orders['transaction_id']}}</td>
                                                <td>
                                                   <div class='row' style="width: 248px;">
                                                      <div class="col-6">{{ translate('amount') }}</div>
                                                      <div class="col-6">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($orders['amount'] + $orders['coupon_amount'])), currencyCode: getCurrencyCode()) }}</div>
                                                      <div class="col-6">{{ translate('coupon_amount') }}</div>
                                                      <div class="col-6"> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($orders['coupon_amount'])), currencyCode: getCurrencyCode()) }}</div>
                                                      <div class="col-6">{{ translate('gst_amount') }}</div>
                                                      <div class="col-6">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($orders['gst_amount'])), currencyCode: getCurrencyCode()) }}</div>
                                                      <div class="col-6">{{ translate('admin_commission') }}</div>
                                                      <div class="col-6">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($orders['admin_commission'])), currencyCode: getCurrencyCode()) }}</div>
                                                   </div>
                                                </td>
                                                <td class="text-center">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($orders['final_amount'])), currencyCode: getCurrencyCode()) }}</td>
                                             </tr>
                                             @endforeach
                                             @endif
                                          </tbody>
                                          <tfoot>
                                             <tr>
                                                <td colspan='5'></td>
                                                <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ( \App\Models\TourOrder::where('status',1)->where('drop_status',1)->where('cab_assign',$getData['id'])->sum('final_amount')) ), currencyCode: getCurrencyCode()) }}</td>
                                             </tr>
                                          </tfoot>
                                       </table>
                                       @if(count($complete_order)==0)
                                       <div class="text-center p-4">
                                          <img class="mb-3 w-160"
                                             src="{{dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg')}}"
                                             alt="{{translate('image_description')}}">
                                          <p class="mb-0">{{translate('no_data_to_show')}}</p>
                                       </div>
                                       @endif
                                    </div>
                                 </div>
                                 <div class="table-responsive mt-4">
                                    <div class="px-4 d-flex justify-content-center justify-content-md-end">
                                      {{  $complete_order->links() }}
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
              
            </div>
         </div>
      </div>
   </div>
</div>

<!--  -->


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

</script>
@endpush