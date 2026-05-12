@extends('layouts.back-end.app')
@section('title', translate('Recover data'))
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
                                    <th>{{ translate('Chapter') }}</th>
                                    <th>{{ translate('Verse') }}</th>
                                    <th>{{ translate('Image') }}</th>
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
                                                <img src="{{ getValidImage(path: 'storage/app/public/sahitya/bhagavad-gita/' . $detail->image, type: 'backend-bhagavadgita') }}" alt="Image" style="width: 100px; height: 50;">
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="{{ route('admin.bhagavadgita.restore', $detail->id) }}" method="POST" style="display:inline-block;">
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
                        {{ $bhagavadgitaDetails->links() }}
                    </div>
                </div>
<!--                 @if (count($bhagavadgitaDetails) == 0)
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
                    document.getElementById(`force-delete-form-${bhagavadgitaDetailId}`).submit();
                }
            });
        });
    });
</script>
@endpush
