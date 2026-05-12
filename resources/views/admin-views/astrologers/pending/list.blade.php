@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('astrologer'))
@section('content')
{{-- view modal --}}
<div class="modal fade" id="astrologer-details" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
   <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="modal-title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <table class="table">
               <tbody id="astrologer-details-tbody">
               </tbody>
            </table>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>
{{-- main page --}}
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}"
            alt="">
         {{ translate('pending_astrologer_&_pandit') }}
         {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $festivals->total() }}</span> --}}
      </h2>
   </div>
   <div class="row mt-20">
      <div class="col-md-12">
         <div class="card">
            {{-- 
            <div class="px-3 py-4">
               <div class="row g-2 flex-grow-1">
                  <div class="col-12 d-flex justify-content-end">
                     <a href="{{ route('admin.astrologers.manage.add-new') }}" type="button"
                        class="btn btn-outline--primary">
                     {{ translate('add_astrologer') }}
                     </a>
                  </div>
               </div>
            </div>
            --}}
            <div class="card-body p-0">
               <div class="table-responsive">
                  <table
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('#') }}</th>
                           <th>{{ translate('Image') }}</th>
                           <th>{{ translate('Name') }}</th>
                           <th>{{ translate('Contact Info') }}</th>
                           <th>{{ translate('Type') }}</th>
                           <th>{{ translate('Service Type') }}</th>
                           <th>{{ translate('Total Services') }}</th>
                           @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Pending', 'verify') ||
                           Helpers::modules_permission_check('Astrologer & Pandit', 'Pending', 'block'))
                           <th>{{ translate('Action') }}</th>
                           @endif
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($pending as $key => $value)
                        @php
                        $totalPooja = !empty($value['is_pandit_pooja'])
                        ? count(json_decode($value['is_pandit_pooja'], true))
                        : 0;
                        $totalVipPooja = !empty($value['is_pandit_vippooja'])
                        ? count(json_decode($value['is_pandit_vippooja'], true))
                        : 0;
                        $totalAnushthan = !empty($value['is_pandit_anushthan'])
                        ? count(json_decode($value['is_pandit_anushthan'], true))
                        : 0;
                        $totalChadhava = !empty($value['is_pandit_chadhava'])
                        ? count(json_decode($value['is_pandit_chadhava'], true))
                        : 0;
                        $totalOfflinepooja = !empty($value['is_pandit_offlinepooja'])
                        ? count(json_decode($value['is_pandit_offlinepooja'], true))
                        : 0;
                        $totalConsultation = !empty($value['consultation_charge'])
                        ? count(json_decode($value['consultation_charge'], true))
                        : 0;
                        $totalService =
                        $totalPooja +
                        $totalVipPooja +
                        $totalAnushthan +
                        $totalChadhava +
                        $totalOfflinepooja +
                        $totalConsultation;
                        @endphp
                        <tr>
                           <td>{{ $key + 1 }}</td>
                           <td> <img src="{{ $value['image'] }}" alt="" width="50"></td>
                           <td>{{ @ucwords($value->name) }}</td>
                           <td><b>{{ $value->email }}</b> <br> {{ $value->mobile_no }}</td>
                           <td>{{ @ucwords($value->type) }}</td>
                           <td>{{ $value['primarySkill']['name'] ?? '' }}</td>
                           <td>{{ $totalService }}</td>
                           @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Pending', 'verify') ||
                           Helpers::modules_permission_check('Astrologer & Pandit', 'Pending', 'block'))
                           <td>
                              <div class="d-flex justify-content-start">
                                 <a class="btn btn-outline-info btn-sm square-btn mx-1"
                                    title="{{ translate('view') }}" href="javascript:0" 
                                    data-name="{{@ucwords($value->name)}}" 
                                    data-mobile="{{$value->mobile_no}}"
                                    data-email="{{$value->email}}"
                                    data-gender="{{$value->gender}}"
                                    data-dob="{{$value->dob}}"
                                    data-type="{{$value->type}}"
                                    data-address="{{$value->address}}"
                                    data-aadharcard="{{$value->adharcard}}"
                                    data-aadharcardfrontimage="{{$value->adharcard_front_image}}"
                                    data-aadharcardbackimage="{{$value->adharcard_back_image}}"
                                    data-pancard="{{$value->pancard}}"
                                    data-pancardimage="{{$value->pancard_image}}"
                                    data-primaryskill="{{@ucwords($value['primarySkill']['name'])}}"
                                    data-experience="{{$value->experience}}"
                                    data-qualification="{{$value->highest_qualification}}"
                                    onclick="astrologerDetails(this)">
                                 <i class="tio-invisible"></i>
                                 </a>
                                 @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Pending', 'verify'))
                                 <span
                                    class="btn btn-outline-success btn-sm square-btn mx-1 verify-astrologer"
                                    title="{{ translate('verify_astrologer') }}"
                                    data-id="verify-{{ $value['id'] }}">
                                 <i class="tio-checkmark-circle-outlined"></i>
                                 </span>
                                 @endif
                                 <form action="{{ route('admin.astrologers.manage.status') }}"
                                    method="post" id="verify-{{ $value['id'] }}">
                                    @csrf
                                    <input type="hidden" name="id"
                                       value="{{ $value['id'] }}">
                                    <input type="hidden" name="status" value="1">
                                 </form>
                                 @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Pending', 'block'))
                                 <span
                                    class="btn btn-outline-danger btn-sm square-btn mx-1 block-astrologer"
                                    title="{{ translate('block_astrologer') }}"
                                    data-id="block-{{ $value['id'] }}">
                                 <i class="tio-crossfit"></i>
                                 </span>
                                 @endif
                                 <form action="{{ route('admin.astrologers.manage.status') }}"
                                    method="post" id="block-{{ $value['id'] }}">
                                    @csrf
                                    <input type="hidden" name="id"
                                       value="{{ $value['id'] }}">
                                    <input type="hidden" name="status" value="2">
                                 </form>
                                 {{-- <a class="btn btn-outline-info btn-sm square-btn"
                                    title="{{ translate('edit') }}" href="javascript:0" data-id="{{$value->id}}" data-name="{{$value->name}}"  onclick="editModal(this)">
                                 <i class="tio-edit"></i>
                                 </a> --}}
                                 {{-- <a class="btn btn-outline-danger btn-sm delete delete-data"
                                    href="javascript:" title="{{ translate('delete') }}">
                                 <i class="tio-delete"></i>
                                 </a> --}}
                              </div>
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
                  {{ $pending->links() }}
               </div>
            </div>
            @if (count($pending) == 0)
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
@endsection
@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
{{-- verify astrologer --}}
<script>
   $('.verify-astrologer').on('click', function() {
       let astrologerId = $(this).attr("data-id");
       Swal.fire({
           title: 'Are You Sure To Verify Astrologer',
           type: 'success',
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
{{-- block astrologer --}}
<script>
   $('.block-astrologer').on('click', function() {
       let astrologerId = $(this).attr("data-id");
       Swal.fire({
           title: 'Are You Sure To Block Astrologer',
           type: 'success',
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
{{-- astrologer details modal --}}
<script>
   function astrologerDetails(that){
       var name = $(that).data('name');
       var mobile = $(that).data('mobile');
       var email = $(that).data('email');
       var gender = $(that).data('gender');
       var dob = $(that).data('dob');
       var type = $(that).data('type');
       var address = $(that).data('address');
       var aadharcard = $(that).data('aadharcard');
       var aadharcardFrontImage = $(that).data('aadharcardfrontimage');
       var aadharcardBackImage = $(that).data('aadharcardbackimage');
       var pancard = $(that).data('pancard');
       var pancardImage = $(that).data('pancardimage');
       var primarySkill = $(that).data('primaryskill');
       var experience = $(that).data('experience');
       var qualification = $(that).data('qualification');
   
       $('#modal-title').text(primarySkill+" Details");
   
       $('#astrologer-details-tbody').html('');
       list = `<tr><td>Name: ${name}</td><td>Mobile: ${mobile}</td></tr>
               <tr><td>Email: ${email}</td><td>Gender: ${gender}</td></tr>
               <tr><td>DOB: ${dob}</td><td>Type: ${type}</td></tr>
               <tr><td colspan="2">Address: ${address}</td></tr>
               <tr><td>Experience: ${experience}</td><td>Qualification: ${qualification}</td></tr>
               <tr><td>AadharCard: ${aadharcard}</td><td>Front Image: <img src="${aadharcardFrontImage}" width="200px" height="auto"/></td>${aadharcardBackImage?`<td>Back Image: <img src="${aadharcardBackImage}" width="200px" height="auto"/></td>`:''}</tr>
               <tr><td>Pancard: ${pancard}</td><td>Image: <img src="${pancardImage}" width="200px" height="auto"/></td></tr>`;
       $('#astrologer-details-tbody').append(list)
   
       $('#astrologer-details').modal('show');
   }
</script>
@endpush