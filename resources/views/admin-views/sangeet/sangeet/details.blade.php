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
                      <h3 class="mb-1 fz-24">{{ $activeStatusCount }}</h3>
                      <div class="text-capitalize mb-0">Total Active Songs</div>
                  </div>
                  <div>
                      <img width="70" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/sangeet/count.png') }}"
                          alt="">
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card card-body h-100 justify-content-center">
              <div class="d-flex gap-2 justify-content-between align-items-center">
                  <div class="d-flex flex-column align-items-start">
                      <h3 class="mb-1 fz-24">{{ $sangeetDetails->total() }}</h3>
                      <div class="text-capitalize mb-0">TOTAL SONGS</div>
                  </div>
                  <div>
                      <img width="70" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/sangeet/song.png') }}"
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
                <div class="row g-2 flex-grow-1 justify-content-between align-items-center">
                      <a href="{{ route('admin.sangeet.recover') }}" class="btn btn-warning">{{ translate('View Delete Data') }}</a>
                      @if(count($sangeetDetails) > 0)
                            <a class="btn btn--primary text-nowrap text-capitalize"
                               title="{{ translate('add') }}"
                               href="{{ route('admin.sangeet.add_details', [$sangeetDetails->first()->sangeet_id]) }}">
                                <i class="tio-add"></i>
                               {{ translate('add') }}
                            </a>
                        @endif
                  </div>
            </div>
            <div class="card-body p-0">
               <div class="table-responsive">
                  <table  id="example" 
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                     <thead class="thead-light thead-50 text-capitalize">
                       <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('Title') }}</th>
                           <th>{{ translate('Singer Name') }}</th>
                           <th>{{ translate('Image') }}</th>
                           <th>{{ translate('Background Image') }}</th>
                           @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-status'))
                           <th>{{ translate('status') }}</th>
                           @endif
                           @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-edit') || Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-status'))
                           <th>{{ translate('Action') }}</th>
                           @endif
                       </tr>
                     </thead>
                     <tbody>
                       @forelse ($sangeetDetails as $key => $detail)
                           <tr>
                               <!-- Calculate serial number -->
                               <td>{{ $sangeetDetails->firstItem() + $key }}</td>
                               <td>{{ Str::limit($detail->title, 30) }}</td>
                               <td>{{ Str::limit($detail->singer_name, 30) }}</td>
                               <td>
                                   @if($detail->image)
                                      <img src="{{ getValidImage(path: 'storage/app/public/sangeet-img/' . $detail->image, type: 'backend-sangeet') }}" alt="Image" style="width: 100px; height: auto;">
                                   @endif
                               </td>
                               <td>
                                   @if($detail->background_image)
                                          <img src="{{ getValidImage(path: 'storage/app/public/sangeet-background-img/' . $detail->background_image, type: 'backend-sangeet') }}" alt="Background Image" style="width: 100px; height: auto;">
                                   @endif
                               </td>
                               @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-status'))
                               <td>
                                  <form action="{{ route('admin.sangeet.updateDetailStatus', $detail['id']) }}" method="post" id="sangeet-status{{$detail['id']}}-form">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$detail['id']}}">
                                      <label class="switcher mx-auto">
                                          <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                 id="sangeet-status{{ $detail['id'] }}" value="1" {{ $detail['status'] == 1? 'checked' : '' }}
                                                 data-modal-id="toggle-status-modal"
                                                 data-toggle-id="sangeet-status{{ $detail['id'] }}"
                                                 data-on-image="sangeet-status-on.png"
                                                 data-off-image="sangeet-status-off.png"
                                                 data-on-title="{{ translate('Want_to_Turn_ON').' '.$detail['defaultname'].' '. translate('status') }}"
                                                 data-off-title="{{ translate('Want_to_Turn_OFF').' '.$detail['defaultname'].' '.translate('status') }}"
                                                 data-on-message="
                                                 <p>{{ translate('if_enabled_this_sangeet_will_be_available_on_the_website_and_customer_app') }}</p>
                                                 "
                                                 data-off-message="
                                                 <p>{{ translate('if_disabled_this_sangeet_will_be_hidden_from_the_website_and_customer_app') }}</p>
                                                 ">
                                          <span class="switcher_control"></span>
                                      </label>
                                  </form>
                              </td>
                              @endif
                              @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-edit') || Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-delete'))
                               <td>
                               <div class="d-flex justify-content-center gap-2">
                                @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-edit'))
                                   <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{ route('admin.sangeet.editDetails', $detail->id) }}">
                                       <i class="tio-edit"></i>
                                     </a>
                                     @endif
                                     @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'detail-delete'))
                                 <button class="btn btn-outline-danger btn-sm square-btn delete-data"
                                        title="{{ translate('delete') }}"
                                        data-id="{{ $detail->id }}">
                                    <i class="tio-delete"></i>
                                </button>
                                @endif
                                <form action="{{ route('admin.sangeet.soft-delete', $detail->id) }}" method="POST" id="delete-form-{{ $detail->id }}" style="display: none;">
                                    @csrf
                                    @method('PATCH')
                                </form>
                            </td>
                            @endif
                           </tr>
                       @empty

                       @endforelse
                     </tbody>
                  </table>
               </div>
            </div>
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {{ $sangeetDetails->links() }}
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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

<script>
  // Delete confirmation
document.querySelectorAll('.delete-data').forEach(deleteButton => {
    deleteButton.addEventListener('click', function () {
        const sangeetDetailId = this.dataset.id;
        Swal.fire({
            title: '{{ translate('Are you sure?') }}',
            text: '{{ translate('This action cannot be undone!') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ translate('Yes, delete it!') }}',
            cancelButtonText: '{{ translate('Cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${sangeetDetailId}`).submit();
            }
        });
    });
});
</script>
@endpush
