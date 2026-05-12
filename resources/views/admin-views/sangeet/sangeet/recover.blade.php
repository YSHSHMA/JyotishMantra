@extends('layouts.back-end.app')
@section('title', translate('Recover Sangeet List'))
@section('content')

@push('css_or_js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
@endpush

<div class="content container-fluid">
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="recoverTable" 
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('Title') }}</th>
                                    <th>{{ translate('Singer Name') }}</th>
                                    <th>{{ translate('Image') }}</th>
                                    <th>{{ translate('Background Image') }}</th>
                                    <th class="text-center">{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sangeetDetails as $key => $detail)
                                    <tr>
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
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="{{ route('admin.sangeet.restore', $detail->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="btn btn-outline-success btn-sm square-btn" title="{{ translate('Restore') }}">
                                                        <i class="tio-restore"></i>
                                                    </button>
                                                </form>
                                            </div>
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
                        {{ $sangeetDetails->links() }}
                    </div>
                </div>
<!--                 @if (count($sangeetDetails) == 0)
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

@endsection

@push('script')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#recoverTable').DataTable({
            searching: true,
            paging: false,
            ordering: true,
            info: true
        });
    });

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
                    document.getElementById(`force-delete-form-${sangeetDetailId}`).submit();
                }
            });
        });
    });
</script>
@endpush
