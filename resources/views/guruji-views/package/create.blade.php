@extends('layouts.back-end.app-guruji')
@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Utils\Helpers;
if (auth('guruji')->check()) {
    $vendorId = auth('guruji')->user()->id;
} 
@endphp
@push('css_or_js')

@endpush

@section('title', translate('package'))

@section('content')
<div id="pageLoader" style=" display:none; position:fixed;   top:0; left:0;  width:100%; height:100%;
  background:rgba(255,255,255,0.8);   z-index:9999; align-items:center;justify-content:center;">
  <div class="text-center">
    <div class="spinner-border text-primary" role="status"></div>
    <div class="mt-2 fw-bold">Please wait...</div>
  </div>
</div>
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-10">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('package_List') }}
            </h2>
        </div>
        @if($packages->count())                                          
        <div class="row mt-20" id="cate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
                            <div class="">
                                <h5 class="text-capitalize d-flex gap-1">
                                    {{ translate('package_list') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12">
                                        0
                                    </span>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="packageTable"
                            class="table table-hover table-borderless  table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('title') }}</th>
                                    <th>{{ translate('type') }}</th>
                                    <th>{{ translate('person') }}</th>
                                    <th>{{ translate('color') }}</th>
                                    <th>{{ translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @foreach($packages as $key => $package) 
                                    <tr> 
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $package->title }}</td>          
                                        <td>{{ $package->type }}</td>                   
                                        <td>{{ $package->person }}</td>                   
                                        <td class="text-center">
                                            <div style="width: 20px; height: 20px; background-color: {{ isset($package['color']) ? $package['color'] : '#ffffff' }}"></div>                                          
                                        </td> 
                                        <td>
                                            <div class="d-flex justify-content-center gap-10">
                                                @if(!is_null($package->pandit_id))
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                    title="{{ translate('edit') }}"
                                                    href="{{ route('admin.package.update', [$package->id]) }}">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>             
                                    @endforeach                     
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center">{{ translate('SL') }}</th>
                                    <th class="text-center">{{ translate('title') }}</th>
                                    <th class="text-center">{{ translate('type') }}</th>
                                    <th class="text-center">{{ translate('person') }}</th>
                                    <th class="text-center">{{ translate('color') }}</th>
                                   
                                    <th class="text-center">{{ translate('action') }}</th>
                                  
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('form').on('submit', function () {
                $('#saveId').prop('disabled', true);
                $('#pageLoader').css('display', 'flex');
            });
        
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>

    <script>
        $('.delete-package').on('click', function() {
            let packageId = $(this).attr("data-id");
            Swal.fire({
                title: messageAreYouSureDeleteThis,
                text: messageYouWillNotAbleRevertThis,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + packageId).submit();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#packageTable').DataTable({
                pageLength: 10,
                scrollY: '500px',
                scrollCollapse: true,
                paging: true,
                fixedHeader: true,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
            });
        });
    </script>
@endpush