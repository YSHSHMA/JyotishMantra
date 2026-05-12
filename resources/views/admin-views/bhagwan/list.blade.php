
@extends('layouts.back-end.app')

@section('title', translate('bhagwan_List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/bhagwan.jpg') }}" alt="">
                {{ translate('List') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ $bhagwans->total() }}</span>
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                            placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" value="{{ request('searchValue') }}" required>
                                        <button type="submit" class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('Thumbnail') }}</th>
                                    <th>{{ translate('name') }}</th>
                                    <th>{{ translate('week') }}</th>
                                    <th>{{ translate('date') }}</th>
                                    <th>{{ translate('event_image') }}</th>
                                    <th class="text-center">{{ translate('status') }}</th>
                                    <th class="text-center"> {{ translate('action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bhagwans as $key => $bhagwan)
                                    <tr>
                                        <td>{{ $bhagwans->firstItem()+$key }}</td>
                                        <td>
                                            <div class="avatar-60 d-flex align-items-center rounded">
                                                <img class="img-fluid" alt=""
                                                     src="{{ getValidImage(path: 'storage/app/public/bhagwan/thumbnail/'.$bhagwan['thumbnail'], type: 'backend-bhagwan') }}">
                                            </div>
                                        </td>
                                        <td class="overflow-hidden max-width-100px">
                                            <span data-toggle="tooltip" data-placement="right" title="{{$bhagwan['defaultname']}}">
                                                 {{ Str::limit($bhagwan['defaultname'],20) }}
                                            </span>
                                        </td>
                                        <td class="overflow-hidden max-width-100px">
                                            <span data-toggle="tooltip" data-placement="right">
                                                 {{ $bhagwan->week }}
                                            </span>
                                        </td>
                                        <td class="overflow-hidden max-width-100px">
                                            <span data-toggle="tooltip" data-placement="right">
                                                 {{ $bhagwan->date }}
                                            </span>
                                        </td>
                                        <td class="overflow-hidden max-width-100px">
                                            <div class="avatar-60 d-flex align-items-center rounded">
                                                <img class="img-fluid" alt=""
                                                     src="{{ getValidImage(path: 'storage/app/public/bhagwan/event-img/'.$bhagwan['event_image'], type: 'backend-bhagwan') }}">
                                            </div>
                                        </td>
                                        <td>
                                            <form action="{{route('admin.bhagwan.status-update') }}" method="post" id="bhagwan-status{{$bhagwan['id']}}-form">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$bhagwan['id']}}">
                                                <label class="switcher mx-auto">
                                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                           id="bhagwan-status{{ $bhagwan['id'] }}" value="1" {{ $bhagwan['status'] == 1 ? 'checked' : '' }}
                                                           data-modal-id = "toggle-status-modal"
                                                           data-toggle-id = "bhagwan-status{{ $bhagwan['id'] }}"
                                                           data-on-image = "bhagwan-status-on.png"
                                                           data-off-image = "bhagwan-status-off.png"
                                                           data-on-title = "{{ translate('Want_to_Turn_ON').' '.$bhagwan['defaultname'].' '. translate('status') }}"
                                                           data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$bhagwan['defaultname'].' '.translate('status') }}"
                                                           data-on-message = "<p>{{ translate('if_enabled_this_bhagwan_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                           data-off-message = "<p>{{ translate('if_disabled_this_bhagwan_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                               @if ($bhagwan->event_image && $bhagwan->date)
                                                    <a class="btn btn-outline-warning btn-sm square-btn" 
                                                       data-toggle="modal" 
                                                       data-target="#edit-bhagwan-modal" 
                                                       title="{{ translate('edit') }}"
                                                       data-id="{{ $bhagwan->id }}"
                                                       data-date="{{ $bhagwan->date }}"
                                                       data-event-image="{{ getValidImage(path: 'storage/app/public/bhagwan/event-img/'.$bhagwan->event_image, type: 'backend-bhagwan') }}"
                                                       data-defaultname="{{ $bhagwan->defaultname }}">
                                                       <i class="tio-edit"></i> {{ translate('edit') }}
                                                    </a>
                                                @else
                                                    <a class="btn btn-outline-info btn-sm square-btn" data-toggle="modal" data-target="#add-bhagwan-modal" title="{{ translate('add') }}"
                                                       data-id="{{ $bhagwan->id }}">
                                                       <i class="tio-add"></i> {{ translate('add') }}
                                                    </a>
                                                @endif

                                                <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                    href="{{ route('admin.bhagwan.update', [$bhagwan['id']]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                 <span class="btn btn-outline-danger btn-sm square-btn delete-data"
                                                    title="{{ translate('delete') }}"
                                                    data-id="bhagwan-{{ $bhagwan['id']}}">
                                                 <i class="tio-delete"></i>
                                                 </span>
                                            </div>
                                             <form action="{{     route('admin.bhagwan.delete',[$bhagwan['id']]) }}"
                                                 method="post" id="bhagwan-{{ $bhagwan['id']}}">
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
                            {{ $bhagwans->links() }}
                        </div>
                    </div>
                    @if(count($bhagwans)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- <span id="route-admin-bhagwan-delete" data-url="{{ route('admin.bhagwan.delete') }}"></span> --}}
<span id="get-bhagwans" data-bhagwans="{{ json_encode($bhagwans) }}"></span>
<div class="modal fade" id="select-bhagwan-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
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
               <h5 class="modal-title mb-2 bhagwan-title-message"></h5>
            </div>
            {{-- 
            <form action="{{ route('admin.bhagwan.delete') }}" method="post"
               class="product-bhagwan-update-form-submit">
               @csrf
               <input name="id" hidden="">
               <div class="gap-2 mb-3">
                  <label class="title-color" for="exampleFormControlSelect1">{{ translate('select_Category') }}
                  <span class="text-danger">*</span>
                  </label>
                  <select name="bhagwan_id" class="form-control js-select2-custom bhagwan-option"
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


<!--add special event model-->
<div class="modal fade" id="add-bhagwan-modal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add-bhagwan-modal">{{ translate('add_event') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add-bhagwan-form" action="{{ route('admin.bhagwan.store-event-image') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <input type="hidden" id="bhagwan-id" name="id">
                    <div class="form-group">
                        <label for="special" class="title-color">{{ translate('Date') }}</label>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <div class="text-center">
                            <img class="upload-img-view" id="detail-viewer"
                                 src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}"
                                 alt="">
                        </div>
                        <div class="form-group">
                            <label for="detail_image" class="title-color">{{ translate('thumbnail') }}<span class="text-danger">*</span></label>
                            <span class="ml-1 text-info">{{ THEME_RATIO[theme_root_path()]['Brand Image'] }}</span>
                            <div class="custom-file text-left">
                                <input type="file" name="event_image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#detail-viewer" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="detail-image">{{ translate('choose_file') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('add') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit special event Modal -->
<div class="modal fade" id="edit-bhagwan-modal" tabindex="-1" role="dialog" aria-labelledby="edit-bhagwan-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-bhagwan-modal-label">{{ translate('edit_event') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-bhagwan-form" method="POST" action="{{ route('admin.bhagwan.update_event') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="id" id="bhagwan-id">
                    <div class="form-group">
                        <label for="edit-date">{{ translate('date') }}</label>
                        <input type="date" class="form-control" id="edit-date" name="date" required>
                    </div>
                    <div class="form-group">
                        <div class="text-center">
                            <img class="upload-img-view" id="viewer" src="" alt="">
                        </div>
                        <div class="form-group">
                            <label for="image" class="title-color">{{ translate('Thumbnail') }}</label>
                            <div class="custom-file text-left">
                                <input type="file" name="event_image" id="image" class="custom-file-input image-preview-before-upload" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="image">{{ translate('choose_file') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
$('#add-bhagwan-modal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); 
    var bhagwanId = button.data('id'); 
    var modal = $(this);

    modal.find('#bhagwan-id').val(bhagwanId);
});
</script>
<script>
  $('#add-bhagwan-form').on('submit', function(e) {
    let selectedDate = $('#date').val();
    let currentDate = new Date().toISOString().split('T')[0];

    if (selectedDate < currentDate) {
        alert('The selected date has already passed. The event status will be set to inactive.');
        $('#event_status').val(0); 
    } else {
        $('#event_status').val(1); 
    }
});
</script>
<script>
    $('#edit-bhagwan-modal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var bhagwanId = button.data('id'); // Extract info from data-* attributes
    var bhagwanDate = button.data('date');
    var bhagwanImage = button.data('event-image');

    var modal = $(this);
    modal.find('#bhagwan-id').val(bhagwanId); // Set the ID
    modal.find('#edit-date').val(bhagwanDate); // Set the date
    modal.find('#viewer').attr('src', bhagwanImage); // Set the image source
});
</script>

@endpush

