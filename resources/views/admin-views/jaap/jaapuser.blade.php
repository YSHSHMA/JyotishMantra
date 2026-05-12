@extends('layouts.back-end.app')

@section('title', translate('jaap_user'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" alt="">
         {{ translate('jaap_user') }}
      </h2>
   </div>
   <div class="row">
      <!-- Section for displaying jaap use list -->
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('jaap_user_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $jaapCounts->total() }}</span>
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
            <!-- Table displaying jaap  user-->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="datatable"
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('User Information') }}</th>
                            <th>{{ translate('Type') }}</th>
                            <th>{{ translate('Mantra') }}</th>
                            <th>{{ translate('Location') }}</th>
                            <th>{{ translate('Count') }}</th>
                            <th>{{ translate('Duration') }}</th>
                            <th>{{ translate('Date') }}</th>
                            <th>{{ translate('Time') }}</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($jaapCounts as $key => $jaapCount)
                            <tr>
                                <td>{{ $jaapCounts->firstItem() + $key }}</td> 
                                <td>
                                    <div>
                                       @if(isset($jaapCount['customer']) && $jaapCount['customer'])
                                          <b><p>{{ ucwords($jaapCount['customer']['f_name']) }} {{ $jaapCount['customer']['l_name'] }}</p></b>
                                          <p>{{ $jaapCount['customer']['phone'] }} {{ $jaapCount['customer']['email'] }}</p>
                                       @else
                                          <p>{{ translate('user_not_found') }}</p>
                                       @endif
                                    </div>
                                 </td>
                                <td>{{ $jaapCount->type }}</td>
                                <td>{{ $jaapCount->name }}</td>
                                <td>{{ $jaapCount->location }}</td>
                                <td>{{ $jaapCount->count }}</td>
                                <td>{{ $jaapCount->duration }}</td>
                                <td>{{ $jaapCount->date }}</td>
                                <td>{{ $jaapCount->time }}</td>
                            </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <!-- Pagination for jaaplist -->
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {{ $jaapCounts->links() }}
               </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($jaapCounts) == 0)
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