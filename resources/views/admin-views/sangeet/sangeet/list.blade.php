@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('sangeet_List'))
@section('content')
@push('css_or_js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
@endpush
<div class="content container-fluid">
<div class="row g-2">
      <div class="col-md-3">
          <div class="card card-body h-100 justify-content-center">
              <div class="d-flex gap-2 justify-content-between align-items-center">
                  <div class="d-flex flex-column align-items-start">
                      <h3 class="mb-1 fz-24"> {{ \App\Models\Sangeet::where('status', '1')->count() }}</h3>
                      <div class="text-capitalize mb-0">Total Active Category</div>
                  </div>
                  <div>
                      <img width="50" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/sangeet/cate.png') }}"
                          alt="">
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card card-body h-100 justify-content-center">
              <div class="d-flex gap-2 justify-content-between align-items-center">
                  <div class="d-flex flex-column align-items-start">
                      <h3 class="mb-1 fz-24"> {{ $totalLanguages }}</h3>
                      <div class="text-capitalize mb-0">Total Language</div>
                  </div>
                  <div>
                      <img width="90" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/sangeet/language.png') }}"
                          alt="">
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
         <div class="card card-body h-100 justify-content-center">
             <div class="d-flex gap-2 justify-content-between align-items-center">
                 <div class="d-flex flex-column align-items-start">
                     <h3 class="mb-1 fz-24">{{ \App\Models\SangeetDetails::count() }}</h3>
                     <div class="text-capitalize mb-0">Total Songs</div>
                 </div>
                 <div>
                     <img width="50" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/sangeet/cate.png') }}"
                         alt="">
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

                  <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                     <!-- <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                        <i class="tio-download-to"></i>
                        {{ translate('export') }}
                        <i class="tio-chevron-down"></i>
                        </button> -->
                     <ul class="dropdown-menu">
                        <li>
                           <a class="dropdown-item"
                              href="{{ route('admin.sangeet.export', ['searchValue' => request('searchValue')]) }}">
                           <img width="14"
                              src="{{ dynamicAsset(path: 'public/assets/back-end/img/excel.png') }}"
                              alt="">
                           {{ translate('excel') }}
                           </a>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
            <div class="card-body p-0">
               <div class="table-responsive">
                  <table id="example" 
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('category') }}</th>
                           <th>{{ translate('subcategory') }}</th>
                           <th>{{ translate('language') }}</th>
                           @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'status'))
                           <th class="text-center">{{ translate('status') }}</th>
                           @endif
                           @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail') || Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-add') || Helpers::modules_permission_check('Sangeet', 'Sangeet', 'edit'))
                           <th class="text-center"> {{ translate('action') }}</th>
                           @endif
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($sangeets as $key => $sangeet)
                        <tr>
                           <td>{{ $sangeets->firstItem() + $key }}</td>
                           <td><a class="title-color hover-c1 d-flex align-items-center gap-10" href="{{ route('admin.sangeet.details', [$sangeet->id]) }}"> 
                            @php
                             $hindiName = $sangeet->category->translations->isNotEmpty()
                             ? $sangeet->category->translations->first()->value
                             : $sangeet->category->name;
                              @endphp
                          <span data-toggle="tooltip" data-placement="right" title="{{ $hindiName }}">
                            {{ $hindiName }}
                          </span>
                          </a></td>
                           <td><a class="title-color hover-c1 d-flex align-items-center gap-10" href="{{ route('admin.sangeet.details', [$sangeet->id]) }}">
                             @php
                             $hindiName = $sangeet->subcategory->translations->isNotEmpty()
                             ? $sangeet->subcategory->translations->first()->value
                             : $sangeet->subcategory->name;
                              @endphp
                          <span data-toggle="tooltip" data-placement="right" title="{{ $hindiName }}">
                            {{ $hindiName }}
                          </span>
                           </a></td>
                           <td><a class="title-color hover-c1 d-flex align-items-center gap-10" href="{{ route('admin.sangeet.details', [$sangeet->id]) }}">{{ $sangeet->language }}</a></td>
                           @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'status'))
                           <td>
                              <form action="{{route('admin.sangeet.status-update') }}" method="post" id="sangeet-status{{$sangeet['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$sangeet['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                    id="sangeet-status{{ $sangeet['id'] }}" value="1" {{ $sangeet['status'] == 1 ? 'checked' : '' }}
                                    data-modal-id = "toggle-status-modal"
                                    data-toggle-id = "sangeet-status{{ $sangeet['id'] }}"
                                    data-on-image = "sangeet-status-on.png"
                                    data-off-image = "sangeet-status-off.png"
                                    data-on-title = "{{ translate('Want_to_Turn_ON').' '.$sangeet['defaultname'].' '. translate('status') }}"
                                    data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$sangeet['defaultname'].' '.translate('status') }}"
                                    data-on-message = "
                                    <p>{{ translate('if_enabled_this_sangeet_will_be_available_on_the_website_and_customer_app') }}</p>
                                    "
                                    data-off-message = "
                                    <p>{{ translate('if_disabled_this_sangeet_will_be_hidden_from_the_website_and_customer_app') }}</p>
                                    ">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           @endif

                           @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail') || Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-add') || Helpers::modules_permission_check('Sangeet', 'Sangeet', 'edit'))
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail'))
                                 <a class="btn btn-outline-primary btn-sm square-btn"
                                    title="{{ translate('view') }}"
                                    href="{{ route('admin.sangeet.details', [$sangeet->id]) }}">
                                    <i class="tio-visible"></i>
                                 </a>
                                 @endif
                                 @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-add'))
                                  <a class="btn btn-outline-primary btn-sm square-btn"
                                       title="{{ translate('add') }}"
                                       href="{{ route('admin.sangeet.add_details', [$sangeet['id']]) }}">
                                       <i class="tio-add"></i>
                                    </a>
                                    @endif
                                    @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'edit'))
                                 <a class="btn btn-outline-info btn-sm square-btn"
                                    title="{{ translate('edit') }}"
                                    href="{{ route('admin.sangeet.update', [$sangeet['id']]) }}">
                                 <i class="tio-edit"></i>
                                 </a>
                                 @endif
                           <!--       <span class="btn btn-outline-danger btn-sm square-btn delete-data"
                                    title="{{ translate('delete') }}"
                                    data-id="sangeet-{{ $sangeet['id']}}">
                                 <i class="tio-delete"></i>
                                 </span>
                              </div>
                              <form action="{{ route('admin.sangeet.delete',[$sangeet['id']]) }}"
                                 method="post" id="sangeet-{{ $sangeet['id']}}">
                                 @csrf @method('delete')
                              </form> -->
                           </td>
                           @endif
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {{ $sangeets->links() }}
               </div>
            </div>
<!--             @if (count($sangeets) == 0)
            <div class="text-center p-4">
               <img class="mb-3 w-160"
                  src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                  alt="">
               <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif -->
         </div>
      </div>
   </div>
</div>
{{-- <span id="route-admin-sangeet-delete" data-url="{{ route('admin.sangeet.delete') }}"></span> --}}
<span id="get-sangeets" data-sangeets="{{ json_encode($sangeets) }}"></span>
<div class="modal fade" id="select-sangeet-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg">
         <div class="modal-header border-0 pb-0 d-flex justify-content-end">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i
               class="tio-clear"></i></button>
         </div>
         <div class="modal-body px-4 px-sm-5 pt-0 pb-sm-5">
            <div class="d-flex flex-column align-items-center text-center gap-2 mb-2">
               <div
                  class="toggle-modal-img-box d-flex flex-column justify-content-center align-items-center mb-3 position-relative">
                  <img src="{{ dynamicAsset('public/assets/back-end/img/icons/info.svg') }}" alt=""
                     width="90" />
               </div>
               <h5 class="modal-title mb-2 sangeet-title-message"></h5>
            </div>
            {{-- 
            <form action="{{ route('admin.sangeet.delete') }}" method="post"
               class="product-sangeet-update-form-submit">
               @csrf
               <input name="id" hidden="">
               <div class="gap-2 mb-3">
                  <label class="title-color" for="exampleFormControlSelect1">{{ translate('select_Category') }}
                  <span class="text-danger">*</span>
                  </label>
                  <select name="sangeet_id" class="form-control js-select2-custom sangeet-option"
                     required>
                  </select>
               </div>
               <div class="d-flex justify-content-center gap-3">
                  <button type="submit" class="btn btn--primary min-w-120">{{ translate('update') }}</button>
                  <button type="button" class="btn btn-danger-light min-w-120"
                     data-dismiss="modal">{{ translate('cancel') }}</button>
               </div>
            </form>
            --}}
         </div>
      </div>
   </div>
</div>
@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
 <script>
     $(document).ready(function() {
         $('#example').DataTable({
             searching: true,
             paging: false,
             ordering: true,
             info: true
         });
     });
 </script>
@endpush