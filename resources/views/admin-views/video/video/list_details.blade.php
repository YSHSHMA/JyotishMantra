@extends('layouts.back-end.app')
@section('title', translate('Video Details'))
@section('content')
@push('css_or_js')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.js"></script>
@endpush
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset('public/assets/back-end/img/video.png') }}" alt="">
            {{ translate('Video Details') }}
        </h2>
    </div>
      <div class="row g-2">
              <div class="col-md-6">
          <div class="card card-body h-100 justify-content-center">
              <div class="d-flex gap-2 justify-content-between align-items-center">
                  <div class="d-flex flex-column align-items-start">
                        <h3>{{ translate('Category') }}: {{ optional($video->category)->name }}</h3>
                        <h3>{{ translate('Subcategory') }}: {{ optional($video->subcategory)->name }}</h3>
                        <h3>{{ translate('List Type') }}: {{ $video->list_type }}</h3>
                        <h3>{{ translate('Playlist Name') }}: {{ $video->playlist_name }}</h3>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card card-body h-100 justify-content-center">
              <div class="d-flex gap-2 justify-content-between align-items-center">
                  <div class="d-flex flex-column align-items-start">
                      <h3 class="mb-1 fz-24">{{ count(json_decode($video->title, true)) }} </h3>
                      <div class="text-capitalize mb-0">TOTAL VIDEO</div>
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
                        @php
                            $urlStatusArray = json_decode($video->url_status, true);
                            $activeCount = is_array($urlStatusArray) ? count(array_filter($urlStatusArray, function($status) {
                                return $status == 1;
                            })) : 0;
                        @endphp
                        <h3 class="mb-1 fz-24">{{ $activeCount }} </h3>
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
                <div class="card-body">
                      <table id="example" 
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('Title') }}</th>
                                <th>{{ translate('URL') }}</th>
                                <th>{{ translate('Thumbnail') }}</th>
                                <th>{{ translate('status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (json_decode($video->title, true) as $index => $title)
                                <tr>
                                    <td>{{ Str::limit($title, 40) }}</td>
                                    <td>
                                        @if (isset(json_decode($video->url, true)[$index]))
                                            <a href="{{ json_decode($video->url, true)[$index] }}" target="_blank">
                                                {{ Str::limit(json_decode($video->url, true)[$index], 20) }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                         @if (isset(json_decode($video->image, true)[$index]))
                                            <img class="img-fluid avatar-60 d-flex align-items-center rounded" alt="" src="{{ asset('storage/app/public/video-img/' . json_decode($video->image, true)[$index]) }}" width="50" alt="">
                                        @endif
                                    </td>
                                     <td>
                                        @if (isset(json_decode($video->url_status, true)[$index]))
                                            <input type="checkbox" class="url-status-toggle" data-id="{{ $index }}" data-toggle="toggle" data-on="Active" data-off="Inactive" {{ json_decode($video->url_status, true)[$index] ? 'checked' : '' }}>
                                        @else
                                            <input type="checkbox" class="url-status-toggle" data-id="{{ $index }}" data-toggle="toggle" data-on="Active" data-off="Inactive">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <a href="{{ route('admin.video.list') }}" class="btn btn-primary mt-3">{{ translate('Back to List') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(function() {
  $('.url-status-toggle').change(function() {
    const status = $(this).prop('checked') ? 1 : 0;
    const index = $(this).data('id');
    const confirmMessage = `Are you sure you want to ${status === 1 ? 'enable' : 'disable'} this URL?`;
    
    Swal.fire({
      title: 'Confirm',
      text: confirmMessage,
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
      type: 'warning'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: '{{ route('admin.video.update-url-status') }}',
          method: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            id: {{ $video->id }},
            index: index,
            status: status
          },
          success: function(response) {
            if(response.success) {
              toastr.success(response.message);
            } else {
              toastr.error(response.message);
            }
          }
        });
      } else {

        $(this).prop('checked', !status);
      }
    });
  });
});
</script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
   $(document).ready(function() {
       $('#example').DataTable({
           searching: true, 
           paging: false, 
           ordering: true,  
           info: false      
       });
   });
</script>
@endpush