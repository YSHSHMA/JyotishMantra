@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('Chadhava|List'))
@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="row g-2 flex-grow-1">
            <div class="col-sm-8 col-md-6 col-lg-4">
                <div class="mb-3">
                    <h2 class="h1 mb-0 d-flex gap-2">
                        <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/poojas.png') }}"
                            alt="">{{ translate('Chadhava|List') }}<span class="badge badge-soft-dark radius-50 fz-14"></span>
                    </h2>
                </div>
            </div>
            @if (Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'add'))
            <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                <a href="{{ route('admin.chadhava.add-new') }}" class="btn btn--primary">
                    <i class="tio-add"></i><span class="text">{{ translate('Chadhava Add') }}</span>
                </a>
            </div>
            @endif
        </div>
        <div class="mb-3 remove-card-shadow">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24"> {{ \App\Models\Chadhava::count() }}</h3>
                                <div class="text-capitalize mb-0">TOTAL CHADHAVA</div>
                            </div>
                            <div>
                                <img width="40" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/count.png') }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">{{ \App\Models\Chadhava::where('status', 1)->count() }}</h3>
                                <div class="text-capitalize mb-0">ACTIVE CHADHAVA</div>
                            </div>
                            <div>
                                <img width="40" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/special.png') }}"
                                alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24"> {{ \App\Models\Chadhava::where('chadhava_type',0)->where('status', 1)->count() }}</h3>
                                <div class="text-capitalize mb-0">CHADHAVA WEEKLY</div>
                            </div>
                            <div>
                                <img width="40" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/active.png') }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="col-md-3">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24"> {{ \App\Models\Chadhava::where('chadhava_type',1)->where('status', 1)->count() }}</h3>
                                <div class="text-capitalize mb-0">CHADHAVA SPECIAL</div>
                            </div>
                            <div>
                                <img width="40" class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/event.png') }}"
                                alt="">
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>   
         
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                {{-- <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                            placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" value="{{ request('searchValue') }}" required>
                                        <button type="submit" class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                    </div>
                                </form> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="myTable"
                                class="display table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('chadhava_name') }}</th>
                                        <th>{{ translate('chadhava_type') }}</th>
                                        @if (Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'status'))
                                        <th class="text-center">{{ translate('status') }}</th>
                                        @endif
                                        @if (Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'edit') || Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'delete'))
                                        <th class="text-center"> {{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($chadhava as $key => $item)
                                        <tr>
                                            <td>{{ $chadhava->firstItem() + $key }}</td>
                                            <td>
                                                {{-- {{ route('admin.service.view',['addedBy'=>$item['added_by'],'id'=>$item['id']]) }} --}}
                                                <a href="#"
                                                    data-addedby="{{ $item['added_by'] }}" data-id="{{ $item['id'] }}"
                                                    class="media align-items-center gap-2 view-service">
                                                    <img src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $item['thumbnail'], type: 'backend-product') }}"
                                                        class="avatar border" alt="">
                                                    <span class="media-body title-color hover-c1">
                                                        <strong>{{ Str::limit($item['name'], 40) }} </strong>
                                                    </span>
                                                </a>
                                            </td>
                                            <td>
                                                @if ($item['chadhava_type'] == 0)
                                                  Weekly
                                                @elseif ($item['chadhava_type'] == 1)
                                                    Special
                                                @endif
                                            </td>
                                            @if (Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'status'))
                                            <td>
                                                <form action="{{ route('admin.chadhava.status-update') }}" method="post"
                                                    id="chadhava-status{{ $item['id'] }}-form">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                    <label class="switcher mx-auto">
                                                        <input type="checkbox"
                                                            class="switcher_input toggle-switch-message" name="status"
                                                            id="chadhava-status{{ $item['id'] }}" value="1"
                                                            {{ $item['status'] == 1 ? 'checked' : '' }}
                                                            data-modal-id = "toggle-status-modal"
                                                            data-toggle-id = "chadhava-status{{ $item['id'] }}"
                                                            data-on-image = "chadhava-status-on.png"
                                                            data-off-image = "chadhava-status-off.png"
                                                            data-on-title = "{{ translate('Want_to_Turn_ON') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                            data-off-title = "{{ translate('Want_to_Turn_OFF') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                            data-on-message = "<p>{{ translate('if_enabled_this_rashi_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                            data-off-message = "<p>{{ translate('if_disabled_this_rashi_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </form>
                                            </td>
                                            @endif
                                            @if (Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'edit') || Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'delete'))
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if (Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'edit'))
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('edit') }}"
                                                        href="{{ route('admin.chadhava.update', [$item['id']]) }}">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                    @endif
                                                    @if (Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'delete'))
                                                    <a class="btn btn-outline-danger btn-sm delete delete-data"
                                                        href="javascript:" data-id="chadhava-{{ $item['id'] }}"
                                                        title="{{ translate('delete') }}"><i class="tio-delete"></i>
                                                    </a>
                                                    <form action="{{ route('admin.chadhava.delete', [$item['id']]) }}"
                                                        method="post" id="chadhava-{{ $item['id'] }}">
                                                        @csrf @method('delete')
                                                    </form>
                                                    @endif

                                                    <a class="btn btn-outline-primary btn-sm open-whatsapp-modal"
                                                        href="javascript:void(0);"
                                                        data-id="{{ $item['id'] }}"
                                                        data-slug="{{ $item['slug'] }}"
                                                        title="{{ translate('whatsapp') }}">
                                                        <i class="tio-whatsapp"></i>
                                                    </a>
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
                            {{ $chadhava->links() }}
                        </div>
                    </div>

                    {{-- @if (count($services) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif --}}

                </div>
            </div>
        </div>
    </div>
    <!-- <span id="route-admin-rashi-delete" data-url=""></span> -->
    <span id="route-admin-rashi-status-update" data-url="{{ route('admin.chadhava.status-update') }}"></span>
    <!-- Modal Structure -->
    <div class="modal fade" id="chadhavaModal" tabindex="-1" role="dialog" aria-labelledby="chadhavaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chadhavaModalLabel">chadhava Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody id="chadhava-details">
                            <!-- Details will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- whatsapp Model --}}
    <div class="modal fade" id="whatsapp" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
     aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">WhatsApp</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="sendtest" method="post" class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="service-id">
                        <div class="form-group mb-2">
                            <label for="reciver">Mobile Number</label>
                            <input type="number" class="form-control" name="reciver" id="reciver" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="message">Message</label>
                            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-block">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>

    <script>
        $(document).on('click', '.open-whatsapp-modal', function () {
            const id = $(this).data('id');
            const slug = $(this).data('slug');
            const link = `https://mahakal.com/chadhava/details/${slug}`;

            $('#service-id').val(id);
            $('#reciver').val('');
            $('#message').val(`\n\n${link}`);

            $('#whatsapp').modal('show');
        });
    </script>


    <script>
        $('#sendtest').on('submit', function(e) {
            e.preventDefault();
            var formD = $(this).serialize();
            $.ajax({
                url: "{{ url('/admin/whatsapp/send-test-message') }}",
                method: "POST",
                data: formD,
                success: function(res) {
                    $('#sendtest')[0].reset();

                    $('#whatsapp').modal('hide');

                    Swal.fire({
                        position: "top-end",
                        title: 'Message sent Successfully',
                        showConfirmButton: false,
                        timer: 1500,
                        buttonsStyling: false
                    });
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
    </script>
@endpush
