@extends('layouts.back-end.app')
@section('title', translate('video_List'))
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
                      <h3 class="mb-1 fz-24"> {{ $videos->total() }}</h3>
                      <div class="text-capitalize mb-0">TOTAL VIDEOS</div>
                  </div>
                  <div>
                      <img width="70" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/video/counting.png') }}"
                          alt="">
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card card-body h-100 justify-content-center">
              <div class="d-flex gap-2 justify-content-between align-items-center">
                  <div class="d-flex flex-column align-items-start">
                      <h3 class="mb-1 fz-24"> {{ \App\Models\Video::where('status', '1')->count() }}</h3>
                      <div class="text-capitalize mb-0">ACTIVE VIDEOS</div>
                  </div>
                  <div>
                      <img width="40" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/video/activevideo.png') }}"
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
                  <div class="col-sm-8 col-md-6 col-lg-4">
                     
                  </div>
                  <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                     <!-- Add More Button -->
                     <a href="{{ route('admin.video.add-new') }}" class="btn btn-outline--primary ml-2">
                     {{ translate('add_video') }}
                     </a>
                  </div>
                  <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                     <!-- <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                        <i class="tio-download-to"></i>
                        {{ translate('export') }}
                        <i class="tio-chevron-down"></i>
                        </button> -->
                     <ul class="dropdown-menu">
                        <li>
                           <a class="dropdown-item"
                              href="{{ route('admin.video.export', ['searchValue' => request('searchValue')]) }}">
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
                           <th>{{ translate('list_type') }}</th>
                           <th class="text-center">{{ translate('status') }}</th>
                           <th class="text-center"> {{ translate('action') }}</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($videos as $key => $video)
                        <tr>
                           <td><a class="title-color hover-c1 d-flex align-items-center gap-10" href="{{ route('admin.video.list_details', [$video['id']]) }}">{{ $videos->firstItem() + $key }}</td></a>
                           <td><a class="title-color hover-c1 d-flex align-items-center gap-10" href="{{ route('admin.video.list_details', [$video['id']]) }}">{{ $video->category_name }}</td></a>
                           <td><a class="title-color hover-c1 d-flex align-items-center gap-10" href="{{ route('admin.video.list_details', [$video['id']]) }}">{{ $video->subcategory_name }}</td></a>
                           <td><a class="title-color hover-c1 d-flex align-items-center gap-10" href="{{ route('admin.video.list_details', [$video['id']]) }}">
                              {{ Str::limit($video['list_type'],20) }}<br>
                              {{ Str::limit($video['playlist_name'],20) }}
                              </span>
                           </td></a>
                           <td>
                              <form action="{{route('admin.video.status-update') }}" method="post" id="video-status{{$video['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$video['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                    id="video-status{{ $video['id'] }}" value="1" {{ $video['status'] == 1 ? 'checked' : '' }}
                                    data-modal-id = "toggle-status-modal"
                                    data-toggle-id = "video-status{{ $video['id'] }}"
                                    data-on-image = "video-status-on.png"
                                    data-off-image = "video-status-off.png"
                                    data-on-title = "{{ translate('Want_to_Turn_ON').' '.$video['defaultname'].' '. translate('status') }}"
                                    data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$video['defaultname'].' '.translate('status') }}"
                                    data-on-message = "
                                    <p>{{ translate('if_enabled_this_video_will_be_available_on_the_website_and_customer_app') }}</p>
                                    "
                                    data-off-message = "
                                    <p>{{ translate('if_disabled_this_video_will_be_hidden_from_the_website_and_customer_app') }}</p>
                                    ">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                  <a class="btn btn-outline-primary btn-sm square-btn"
                                       title="{{ translate('view') }}"
                                       href="{{ route('admin.video.list_details', [$video['id']]) }}">
                                       <i class="tio-visible"></i>
                                    </a>
                                 <a class="btn btn-outline-info btn-sm square-btn"
                                    title="{{ translate('edit') }}"
                                    href="{{ route('admin.video.update', [$video['id']]) }}">
                                 <i class="tio-edit"></i>
                                 </a>
                                 <span class="btn btn-outline-danger btn-sm square-btn delete-data"
                                    title="{{ translate('delete') }}"
                                    data-id="video-{{ $video['id']}}">
                                 <i class="tio-delete"></i>
                                 </span>
                              </div>
                              <form action="{{ route('admin.video.delete',[$video['id']]) }}"
                                 method="post" id="video-{{ $video['id']}}">
                                 @csrf @method('delete')
                              </form>
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {{ $videos->links() }}
               </div>
            </div>
            @if (count($videos) == 0)
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
{{-- <span id="route-admin-video-delete" data-url="{{ route('admin.video.delete') }}"></span> --}}
<span id="get-videos" data-videos="{{ json_encode($videos) }}"></span>
<div class="modal fade" id="select-video-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
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
               <h5 class="modal-title mb-2 video-title-message"></h5>
            </div>
            {{-- 
            <form action="{{ route('admin.video.delete') }}" method="post"
               class="product-video-update-form-submit">
               @csrf
               <input name="id" hidden="">
               <div class="gap-2 mb-3">
                  <label class="title-color" for="exampleFormControlSelect1">{{ translate('select_Category') }}
                  <span class="text-danger">*</span>
                  </label>
                  <select name="video_id" class="form-control js-select2-custom video-option"
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
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/videos-management.js') }}"></script>
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