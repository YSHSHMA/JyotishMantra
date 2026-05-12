@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('remote_access'))

@section('content')

    {{-- edit modal --}}
    <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remote Access Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.remote.access.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="host_address">Host (% wildcard is allowed)</label>
                                <input type="text" name="host_address" id="host-address" class="form-control"
                                    placeholder="Enter host address" required>
                            </div>
                            <div class="form-group col-12">
                                <label for="comment">Comment (optional)</label>
                                <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="comment"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/panchangmoonimage.png') }}" alt="">
                {{ translate('remote_access') }}
            </h2>
        </div>
        <div class="row">
            <!-- Form for adding new panchangmoonimage -->
            @if (Helpers::modules_permission_check('Remote Access', 'Remote Access', 'add'))
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.remote.access.add') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="host_address">Host (% wildcard is allowed)</label>
                                        <input type="text" name="host_address" id="" class="form-control"
                                            placeholder="Enter host address" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="comment">Comment (optional)</label>
                                        <textarea name="comment" id="" rows="3" class="form-control" placeholder="comment"></textarea>
                                    </div>
                                </div>
                                <!-- Buttons for form actions -->
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                                    <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Section for displaying panchangmoonimage list -->
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('remote_access_list') }}
                                    {{-- <span
                                        class="badge badge-soft-dark radius-50 fz-12">{{ $panchangmoonimages->total() }}</span> --}}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <!-- Table displaying panchangmoonimage -->
                    <div class="text-start">
                        <div class="table-responsive">
                            <table id="datatable"
                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('host_Address') }}</th>
                                        <th>{{ translate('comment') }}</th>
                                        @if (Helpers::modules_permission_check('Remote Access', 'Remote Access', 'edit') ||
                                                Helpers::modules_permission_check('Remote Access', 'Remote Access', 'delete'))
                                            <th>{{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($remoteAccess as $key => $access)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $access['host_address'] }}</td>
                                            <td>{{ $access['comment'] }}</td>
                                            @if (Helpers::modules_permission_check('Remote Access', 'Remote Access', 'edit') ||
                                                    Helpers::modules_permission_check('Remote Access', 'Remote Access', 'delete'))
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @if (Helpers::modules_permission_check('Remote Access', 'Remote Access', 'edit'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                title="{{ translate('edit') }}" href="javascript:0"
                                                                data-id="{{ $access['id'] }}"
                                                                data-host="{{ $access['host_address'] }}"
                                                                data-comment="{{ $access['comment'] }}"
                                                                onclick="edit(this)">
                                                                <i class="tio-edit"></i>
                                                            </a>
                                                        @endif

                                                        @if (Helpers::modules_permission_check('Remote Access', 'Remote Access', 'delete'))
                                                            {{-- <a href="{{route('admin.remote.access.delete',$access['id'])}}" class="btn btn-outline-danger btn-sm square-btn">
                                                                <i class="tio-delete"></i>
                                                            </a> --}}

                                                            <span
                                                                class="btn btn-outline-danger btn-sm square-btn delete-host ml-2"
                                                                title="{{ translate('delete_host') }}"
                                                                data-id="delete-{{ $access['id'] }}">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <form action="{{ route('admin.remote.access.delete') }}"
                                                                method="post" id="delete-{{ $access['id'] }}">
                                                                @csrf
                                                                <input type="hidden" name="id"
                                                                    value="{{ $access['id'] }}">
                                                            </form>
                                                        @endif
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
                            {!! $remoteAccess->links() !!}
                        </div>
                    </div>
                    <!-- Message for no data to show -->
                    @if (count($remoteAccess) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="{{ translate('image') }}">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Hidden HTML element for delete route -->
    <span id="route-admin-panchangmoonimage-delete" data-url="{{ route('admin.panchangmoonimage.delete') }}"></span>
    <!-- Toast message for panchangmoonimage deleted -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="toast-body">
                {{ translate('panchangmoonimage deleted') }}
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Include SweetAlert2 for confirmation dialogs -->
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    {{-- edit --}}
    <script>
        function edit(that) {
            var id = $(that).data('id');
            var host = $(that).data('host');
            var comment = $(that).data('comment');

            $('#id').val(id);
            $('#host-address').val(host);
            $('#comment').val(comment);

            $('#edit-modal').modal('show');
        }
    </script>

    {{-- delete astrologer --}}
    <script>
        $('.delete-host').on('click', function() {
            let hostId = $(this).attr("data-id");
            Swal.fire({
                title: 'Are You Sure To Delete Host',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + hostId).submit();
                }
            });
        });
    </script>
@endpush
