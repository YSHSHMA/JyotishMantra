@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', 'puja_review_list')
@push('css_or_js')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        .comment-box {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ asset('public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('Puja Review List') }}
            </h2>
        </div>

        <div class="card p-4">
            <table id="table" class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 60px;">SL</th>
                        {{-- <th style="width: 100px;">OrderID</th> --}}
                        <th style="width: 70px;">AstroId</th>
                        <th style="width: 70px;">UserID</th>
                        <th style="width: 80px;">ServiceId</th>
                        <th style="max-width: 250px;">Comment</th>
                        {{-- <th style="100px;">ServiceType</th> --}}
                        <th style="80px;">Rating</th>
                        <th style="150px;">YouTube</th>
                        <th style="120px;">is_edited</th>
                        <th style="100px;">Status</th>
                        @if (Helpers::modules_permission_check('Pooja Review', 'Pooja Review', 'status') || Helpers::modules_permission_check('Pooja Review', 'Pooja Review', 'edit') || Helpers::modules_permission_check('Pooja Review', 'Pooja Review', 'delete'))
                        <th style="100px;">Action</th>
                        @endif

                    </tr>
                </thead>
                <tbody>
                    @foreach ($review as $index => $row)
                        <tr @if ($loop->first) style="background-color: #e6ffe6;" @endif>
                            <td>{{ $index + 1 }}</td>
                            {{-- <td>{{ $row['order_id'] }}</td> --}}
                            <td>{{ $row->pandit->name ?? 'Not Found' }}</td>
                            <td>{{ $row->users->name ?? 'Not Found' }}</td>
                            <td>
                                @if ($row['service_type'] == 'pooja' || $row['service_type'] == 'counselling')
                                    <h4>{{ Str::limit($row->services?->name,20) }}</h4>
                                @elseif ($row['service_type'] == 'offlinepooja')
                                    <h4>{{ Str::limit($row->offlinePooja?->name,20) }}</h4>
                                @elseif ($row['service_type'] == 'anushthan' || $row['service_type'] == 'vip')
                                    <h4>{{ Str::limit($row->vippoojas?->name,20) }}</h4>
                                @elseif ($row['service_type'] == 'chadhava')
                                    <h4>{{ Str::limit($row->chadhava?->name,20) }}</h4>
                                @else
                                    {{ 'Not Found' }}
                                @endif
                                <p class="mb-0"><b>{{ $row['order_id'] . ' | ' . $row['service_type'] }}</b></p>
                            </td>

                            {{-- Comment Box --}}
                            <td style="word-break: break-word;">
                                <div class="comment-box" style="max-height: 4.5em; overflow: hidden; position: relative;">
                                    <span
                                        class="short-comment">{{ Str::limit(strip_tags($row['comment']), 150, '...') }}</span>
                                    <span class="full-comment d-none">{{ $row['comment'] }}</span>
                                </div>
                                @if (strlen($row['comment']) > 150)
                                    <a href="javascript:void(0)" class="read-more">Read more</a>
                                @endif
                            </td>

                            {{-- <td>{{ $row['service_type'] }}</td> --}}

                            {{-- Rating --}}
                            <td>
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $row['rating'])
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-muted"></i>
                                    @endif
                                @endfor
                            </td>

                            {{-- Youtube Link --}}
                            <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                <a href="{{ $row['youtube_link'] }}" target="_blank">
                                    {{ Str::limit($row['youtube_link'], 25, '...') }}
                                </a>
                            </td>

                            {{-- Edited Status --}}
                            <td>
                                @if ($row['is_edited'] == 1)
                                    <span class="badge badge-info">Edited by Customer</span>
                                @else
                                    <span class="badge badge-secondary">Pending Edited</span>
                                @endif
                            </td>

                            {{-- Active/Inactive --}}
                            <td>
                                @if ($row['status'] == 1)
                                    <span class="badge badge-success">Active</span>
                                @elseif($row['status'] == 0 || $row['status'] == 10)
                                    <span class="badge badge-danger">Inactive</span>
                                @else
                                    <span class="badge badge-warning">{{ $row['status'] }}</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            @if (Helpers::modules_permission_check('Pooja Review', 'Pooja Review', 'status') || Helpers::modules_permission_check('Pooja Review', 'Pooja Review', 'edit') || Helpers::modules_permission_check('Pooja Review', 'Pooja Review', 'delete'))
                            <td>
                                @if (Helpers::modules_permission_check('Pooja Review', 'Pooja Review', 'status'))
                                <div class="text-center my-2">
                                    <label class="switcher mx-auto">
                                        <input type="checkbox" class="switcher_input status-toggle" name="status" data-id="{{ $row['order_id'] }}" {{ $row['status'] == 1 ? 'checked' : '' }}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                                @endif
                                @if (Helpers::modules_permission_check('Pooja Review', 'Pooja Review', 'edit'))
                                <div class="text-center my-1 d-flex justify-content-center gap-2">
                                <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                    data-toggle="modal" data-target="#change-video-model" data-id="{{ $row['order_id'] }}"
                                    data-comment="{{ $row['comment'] }}" data-youtube="{{ $row['youtube_link'] }}">
                                    <i class="tio-edit"></i>
                                </a>
                                @endif
                                @if (Helpers::modules_permission_check('Pooja Review', 'Pooja Review', 'delete'))
                                <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                    data-id="pujarecords-{{ $row['order_id'] }}" title="{{ translate('delete') }}">
                                    <i class="tio-delete"></i>
                                </a>
                                @endif
                                <form action="{{ route('admin.pujarecords.delete-comment', [$row['order_id']]) }}"
                                    method="POST" id="pujarecords-{{ $row['order_id'] }}">
                                    @csrf
                                </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <!-- Reusable Modal -->
    <div class="modal fade" id="change-video-model" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.pujarecords.chanage-comment') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" id="modalId">

                    <div class="modal-body">
                        <h5 class="mb-2">Update Puja Record</h5>
                        <p class="text-muted small">
                            यहाँ आप अपना comment और YouTube link update कर सकते हैं।
                        </p>
                        <div class="form-group">
                            <label>Comment</label>
                            <textarea class="form-control" name="comment" id="modalComment"></textarea>
                        </div>
                        <div class="form-group">
                            <label>YouTube Link</label>
                            <input type="text" class="form-control" name="youtube_link" id="modalYoutubeInput">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@push('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $('#table').DataTable({
            pageLength: 10,
            scrollX: true,
            autoWidth: false,
            columnDefs: [{
                    targets: [5],
                    width: "250px"
                }, // Comment column
                {
                    targets: [6],
                    width: "150px"
                }, // Youtube
                {
                    targets: [8, 9],
                    orderable: false
                } // status + action non-sortable
            ]
        });
        $(document).ready(function() {
            $(document).on('click', '[data-target="#change-video-model"]', function() {
                const id = $(this).data('id') || '';
                const comment = $(this).data('comment') || '';
                const youtubeLink = $(this).data('youtube') || '';

                // Fill modal inputs
                $('#modalId').val(id);
                $('#modalComment').val(comment);
                $('#modalYoutubeInput').val(youtubeLink);
            });
        });

        $(document).on('click', '.read-more', function() {
            var $this = $(this);
            var $cell = $this.closest('td');

            $cell.find('.short-comment').toggleClass('d-none');
            $cell.find('.full-comment').toggleClass('d-none');

            if ($this.text() === "Read more") {
                $this.text("Read less");
            } else {
                $this.text("Read more");
            }
        });

        $(document).on('change', '.status-toggle', function() {
            const orderId = $(this).data('id');
            const status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('admin.pujarecords.status-update') }}",
                method: "GET",
                data: {
                    order_id: orderId,
                    status: status
                },
                success: function(response) {
                    toastr.success(response.message || "Status updated successfully");
                },
                error: function(xhr) {
                    toastr.error("Failed to update status");
                }
            });
        });
    </script>
@endpush
