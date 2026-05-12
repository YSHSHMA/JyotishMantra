@extends('layouts.back-end.app')

@section('title', translate('review_List'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('Review') }}
        </h2>
    </div>
    <div class="row">
        <div class="card w-100">
            <div class="card-body">
                <ul class="nav nav-tabs w-fit-content mb-4">
                    <li class="nav-item text-capitalize">
                        <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#puja-review-list">
                            {{ translate('review') }} 1
                        </a>
                    </li>
                    <li class="nav-item text-capitalize">
                        <a class="nav-link" id="tour-event-reviews-tab" data-toggle="tab" href="#tour-event-reviews">
                            {{ translate('review') }} 2
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="puja-review-list">
                        <div class="mb-3">
                            <h2 class="h1 mb-0 d-flex gap-2">
                                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/bhagwan.jpg') }}"
                                    alt="">
                                {{ translate('List') }}
                                <span class="badge badge-soft-dark radius-50 fz-14">{{ $reviews->total() }}</span>
                            </h2>
                        </div>
                        <div class="row mt-20">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="px-3 py-4">
                                        <div class="row g-2 flex-grow-1">
                                            <div class="col-md-12">
                                                {{-- <form action="{{ url()->current() }}" method="GET">
                                                <div class="input-group input-group-custom input-group-merge">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="tio-search"></i>
                                                        </div>
                                                    </div>
                                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                                        placeholder="{{ translate('search_by_name') }}"
                                                        aria-label="{{ translate('search_by_name') }}"
                                                        value="{{ request('searchValue') }}" required>
                                                    <button type="submit"
                                                        class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                                </div>
                                                </form> --}}
                                                <form action="{{ url()->current() }}" method="GET">
                                                    <div class="row">
                                                        <div class="form-group col-md-4">
                                                            <label for="type" class="title-color">
                                                                {{ translate('type') }}
                                                            </label>
                                                            <select name="type" id="" class="form-control">
                                                                <option value="all" {{empty($type)?'selected':''}}>All</option>
                                                                <option value="pooja" {{$type=='pooja'?'selected':''}}>Pooja</option>
                                                                <option value="chadhava" {{$type=='chadhava'?'selected':''}}>Chadhava</option>
                                                                <option value="darshan" {{$type=='darshan'?'selected':''}}>Darshan</option>
                                                                <option value="product" {{$type=='product'?'selected':''}}>Product</option>
                                                                <option value="tour" {{$type=='tour'?'selected':''}}>Tour</option>
                                                                <option value="general" {{$type=='general'?'selected':''}}>General</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label for="status" class="title-color">
                                                                {{ translate('status') }}
                                                            </label>
                                                            <select name="status" id="" class="form-control">
                                                                <option value="all" {{empty($status)?'selected':''}}>All</option>
                                                                <option value="0" {{$status=='0'?'selected':''}}>Pending</option>
                                                                <option value="1" {{$status=='1'?'selected':''}}>Approved</option>
                                                                <option value="2" {{$status=='2'?'selected':''}}>Blocked</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 align-self-center">
                                                            <button type="submit"
                                                                class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table
                                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                                <thead class="thead-light thead-50 text-capitalize">
                                                    <tr>
                                                        <th>{{ translate('SL') }}</th>
                                                        <th>{{ translate('image') }}</th>
                                                        <th>{{ translate('user_name') }}</th>
                                                        <th>{{ translate('type') }}</th>
                                                        <th>{{ translate('anonymous') }}</th>
                                                        <th>{{ translate('rating') }}</th>
                                                        <th>{{ translate('status') }}</th>
                                                        {{-- <th>{{ translate('video_url') }}</th> --}}
                                                        <th>{{ translate('date') }}</th>
                                                        <th class="text-center"> {{ translate('action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($reviews as $key => $review)
                                                    <tr>
                                                        <td>{{ $reviews->firstItem() + $key }}</td>
                                                        <td>
                                                            <div class="avatar-60 d-flex align-items-center rounded">
                                                                <img class="img-fluid" alt=""
                                                                    src="{{ getValidImage(path: 'storage/app/public/general-review/'.$review['profile_image'], type: 'backend-festival') }}">
                                                            </div>
                                                        </td>
                                                        <td class="">
                                                            {{ $review->user_name ?? 'NA' }}
                                                        </td>
                                                        <td class="">
                                                            {{ $review->review_type }}
                                                        </td>
                                                        <td class="">
                                                            {{ $review->is_anonymous }}
                                                        </td>
                                                        <td class="">
                                                            {{ $review->star_rating }}
                                                        </td>
                                                        <td class="">
                                                            {{ $review->status == 0 ? 'pending' : ($review->status == 1 ? 'approved' : 'blocked') }}
                                                        </td>
                                                        {{-- <td class="">
                                                           {{ $review->video_url ?? 'NA' }}
                                                        </td> --}}
                                                        <td class="">
                                                            {{ date('d M Y', strtotime($review->created_at)) }}
                                                        </td>
                                                        <td>
                                                            <div class="d-flex justify-content-center gap-2">
                                                                @if ($review->status != 1)
                                                                <a class="btn btn-outline-info btn-sm square-btn"
                                                                    title="{{ translate('approve') }}"
                                                                    href="{{ route('admin.general.review.status', ['id' => $review['id'], 'status' => '1']) }}">
                                                                    <i class="tio-checkmark-circle-outlined"></i>
                                                                </a>
                                                                @endif

                                                                @if ($review->status != 2)
                                                                <a class="btn btn-outline-warning btn-sm square-btn"
                                                                    title="{{ translate('block') }}"
                                                                    href="{{ route('admin.general.review.status', ['id' => $review['id'], 'status' => '2']) }}">
                                                                    <i class="tio-blocked"></i>
                                                                </a>
                                                                @endif
                                                                <a href="{{route('admin.general.review.edit',$review['id'])}}" class="btn btn-outline-primary btn-sm square-btn" title="{{ translate('edit') }}">
                                                                    <i class="tio-edit"></i>
                                                                </a>
                                                                <span class="btn btn-outline-danger btn-sm square-btn delete-data"
                                                                    title="{{ translate('delete') }}"
                                                                    data-id="review-{{ $review['id'] }}">
                                                                    <i class="tio-delete"></i>
                                                                </span>
                                                            </div>
                                                            <form action="{{ route('admin.general.review.delete', [$review['id']]) }}"
                                                                method="post" id="review-{{ $review['id'] }}">
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
                                            {{ $reviews->links() }}
                                        </div>
                                    </div>
                                    @if (count($reviews) == 0)
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
                    <div class="tab-pane fade" id="tour-event-reviews">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table id="policy_table" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                                <thead class="thead-light thead-50 text-capitalize">
                                                    <tr>
                                                        <th>{{ translate('SL') }}</th>
                                                        <th>{{ translate('type') }}</th>
                                                        <th>{{ translate('User_info') }}</th>
                                                        <th>{{ translate('details') }}</th>
                                                        <th>{{ translate('create_by') }}</th>
                                                        <th>{{ translate('action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit review Modal -->
{{-- <div class="modal fade" id="edit-review-modal" tabindex="-1" role="dialog" aria-labelledby="edit-review-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-review-modal-label">{{ translate('edit_review') }}</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
</div>
<form id="edit-review-form" method="POST" action="{{ route('admin.general.review.update') }}"
    enctype="multipart/form-data">
    @csrf

    <div class="modal-body">
        <input type="hidden" name="id" id="review-id">
        <div class="form-group">
            <label for="review_text" class="title-color">
                {{ translate('review_text') }}
                <span class="text-danger">*</span>
            </label>
            <textarea name="review_text" id="edit-review-text" maxlength="400" class="form-control" rows="3"
                placeholder="{{ translate('enter review text') }}" required></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary"
            data-dismiss="modal">{{ translate('close') }}</button>
        <button type="submit" class="btn btn-primary">{{ translate('update') }}</button>
    </div>
</form>
</div>
</div>
</div> --}}


@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    // $('#edit-review-modal').on('show.bs.modal', function(event) {
    //     var button = $(event.relatedTarget);
    //     var reviewId = button.data('id');
    //     var reviewDate = button.data('review-text');

    //     var modal = $(this);
    //     modal.find('#review-id').val(reviewId);
    //     modal.find('#edit-review-text').val(reviewDate);
    // });
</script>
<script>
    $(document).ready(function() {
        initDataTable({
            tableId: '#policy_table',
            ajaxUrl: "{{ route('admin.general.review.tour-event-temple-all-review-filter') }}",
            exportTitle: "policy list",
            pageLength: 25,
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'type',
                    name: 'type',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'user_info',
                    name: 'user_info',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'details',
                    name: 'details',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'create_by',
                    name: 'create_by',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'option',
                    name: 'option',
                    orderable: false,
                    searchable: false
                },
            ],
            extraOptions: {
                serverSide: true,
                ajax: {
                    data: function(d) {
                        d.search_by_type = $('.search_by_type').val();
                        d.search_by_name = $('.search_by_name').val();
                    }
                }
            }
        });
    });
    $('.search_by_type').on('change', function() {
        $('#policy_table').DataTable().draw();
    });
    let searchDelay;
    $('.search_by_name').on('keyup', function() {
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function() {
            $('#policy_table').DataTable().draw();
        }, 500);
    });
    $('#policy_table').on('draw.dt', function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $(document).ready(function() {
        $(document).on('change', '.toggle-switch-message', function(e) {
            e.preventDefault();

            const $checkbox = $(this);
            const form = $checkbox.closest('form');
            const isChecked = $checkbox.is(':checked');

            // Extract data
            const title = isChecked ? $checkbox.data('on-title') : $checkbox.data('off-title');
            const html = isChecked ? $checkbox.data('on-message') : $checkbox.data('off-message');

            $checkbox.prop('checked', !isChecked);

            Swal.fire({
                title: title,
                html: html,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.value) {
                    $checkbox.prop('checked', isChecked);
                    form.submit();
                }
            });
        });
    });
</script>
@endpush