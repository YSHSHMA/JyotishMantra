@extends('layouts.back-end.app')
@section('title', translate('bhagavad_gita_List'))
@section('content')
@push('css_or_js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
@endpush
<div class="content container-fluid">
   <div class="px-3 py-4">
    <div class="row g-2 flex-grow-1 justify-content-between align-items-center">
      <h2 class="">
      </h2>
        @if(count($bhagavadgitaDetails) > 0)
            <a class="btn btn--primary text-nowrap text-capitalize"
               title="{{ translate('add') }}"
               href="{{ route('admin.bhagavadgita.add_verse', [$bhagavadgitaDetails->first()->chapter_id]) }}">
                <i class="tio-add"></i>
               {{ translate('add') }}
            </a>
        @else
            <p>{{ translate('No details available.') }}</p>
        @endif
    </div>
   </div>
   <div class="row mt-20">
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
                <div class="row g-2 flex-grow-1 justify-content-between align-items-center">
                      <h2 class="">
                       {{ $currentChapter ? $currentChapter->name : 'Chapter not found' }}
                      </h2>
                      <a href="{{ route('admin.bhagavadgita.recover') }}" class="btn btn-warning">{{ translate('View Delete Data') }}</a>
                  </div>
            </div>
            <div class="card-body p-0">
               <div class="table-responsive">
                  <table  id="example" 
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                     <thead class="thead-light thead-50 text-capitalize">
                       <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('Chapter') }}</th>
                           <th>{{ translate('Verse') }}</th>
                           <th>{{ translate('Image') }}</th>
                           <th class="text-center">{{ translate('status') }}</th>
                           <th class="text-center">{{ translate('Action') }}</th>
                       </tr>
                     </thead>
                     <tbody>
                       @forelse ($bhagavadgitaDetails as $key => $detail)
                           <tr>
                               <td>{{ $bhagavadgitaDetails->firstItem() + $key }}</td>
                               <td>{{ Str::limit($detail->chapter_id, 30) }}</td>
                               <td>{{ Str::limit($detail->verse, 30) }}</td>
                               <td>
                                   @if($detail->image)
                                      <img  src="{{ getValidImage(path: 'storage/app/public/sahitya/bhagavad-gita/' . $detail->image, type: 'backend-bhagavadgita') }}" alt="Image" style="width: 100px; height: 50px;">
                                   @endif
                               </td>
                               <td>
                                  <form action="{{ route('admin.bhagavadgita.updateDetailStatus', $detail['id']) }}" method="post" id="bhagavadgita-status{{$detail['id']}}-form">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$detail['id']}}">
                                      <label class="switcher mx-auto">
                                          <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                 id="bhagavadgita-status{{ $detail['id'] }}" value="1" {{ $detail['status'] == 1? 'checked' : '' }}
                                                 data-modal-id="toggle-status-modal"
                                                 data-toggle-id="bhagavadgita-status{{ $detail['id'] }}"
                                                 data-on-image="bhagavadgita-status-on.png"
                                                 data-off-image="bhagavadgita-status-off.png"
                                                 data-on-title="{{ translate('Want_to_Turn_ON').' '.$detail['chapter'].' '. translate('status') }}"
                                                 data-off-title="{{ translate('Want_to_Turn_OFF').' '.$detail['chapter'].' '.translate('status') }}"
                                                 data-on-message="
                                                 <p>{{ translate('if_enabled_this_bhagavadgita_will_be_available_on_the_website_and_customer_app') }}</p>
                                                 "
                                                 data-off-message="
                                                 <p>{{ translate('if_disabled_this_bhagavadgita_will_be_hidden_from_the_website_and_customer_app') }}</p>
                                                 ">
                                          <span class="switcher_control"></span>
                                      </label>
                                  </form>
                              </td>
                               <td>
                               <div class="d-flex justify-content-center gap-2">
                                <a class="btn btn-outline-primary btn-sm square-btn" title="{{ translate('view') }}" href="{{ route('admin.bhagavadgita.all-details', [$detail->id]) }}">
                                                <i class="tio-visible"></i>
                                            </a>
                                   <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{ route('admin.bhagavadgita.editVerse', $detail->id) }}">
                                       <i class="tio-edit"></i>
                                     </a>
                                 <button class="btn btn-outline-danger btn-sm square-btn delete-data"
                                        title="{{ translate('delete') }}"
                                        data-id="{{ $detail->id }}">
                                    <i class="tio-delete"></i>
                                </button>
                                <form action="{{ route('admin.bhagavadgita.soft-delete', $detail->id) }}" method="POST" id="delete-form-{{ $detail->id }}" style="display: none;">
                                    @csrf
                                    @method('PATCH')
                                </form>
                            </td>
                           </tr>
                       @empty

                       @endforelse
                     </tbody>
                  </table>
               </div>
            </div>
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {{ $bhagavadgitaDetails->links() }}
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
          const bhagavadgitaDetailId = this.dataset.id;
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
                  document.getElementById(`delete-form-${bhagavadgitaDetailId}`).submit();
              }
          });
      });
  });
</script>
@endpush
