@extends('layouts.back-end.app')

@section('title', translate('bhagwan_logs'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" alt="">
         {{ translate('bhagwan_logs') }}
      </h2>
   </div>
   <div class="row">
      <!-- Section for displaying  logs -->
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $logs->total() }}</span>
                     </h5>
                  </div>
                  <div class="col-sm-8 col-md-6 col-lg-4">
                     <form action="{{ url()->current() }}" method="GET">
                        <div class="input-group input-group-custom input-group-merge">
                           <div class="input-group-prepend">
                              <div class="input-group-text">
                                 <i class="tio-search"></i>
                              </div>
                           </div>
                           <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                             placeholder="{{ translate('search_by_name') }}"
                              aria-label="{{ translate('search_by_name') }}" required>
                           <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
            <!-- Table displaying user-->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="datatable"
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('User Information') }}</th>
                            <th>{{ translate('Location') }}</th>
                            <th>{{ translate('Duration') }}</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($logs as $key => $data)
                            <tr>
                                <td>{{ $logs->firstItem() + $key }}</td> 
                                <td>
                                    <div>
                                       @if(isset($data['customer']) && $data['customer'])
                                          <b><p>{{ ucwords($data['customer']['f_name']) }} {{ $data['customer']['l_name'] }}</p></b>
                                          <p>{{ $data['customer']['phone'] }}
                                            <br> {{ $data['customer']['email'] }}</p>
                                       @else
                                          <p>{{ translate('user_not_found') }}</p>
                                       @endif
                                    </div>
                                 </td>
                                <td>{{ $data->location }}</td>
                                <td>{{ $data->duration }}</td>
                            </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <!-- Pagination for list -->
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {{ $logs->links() }}
               </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($logs) == 0)
            <div class="text-center p-4">
               <img class="mb-3 w-160"
                  src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                  alt="{{ translate('image') }}">
               <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>

@endsection