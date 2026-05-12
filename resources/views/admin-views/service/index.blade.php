@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('pooja_List'))

@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="row g-2 flex-grow-1">
            <div class="col-sm-8 col-md-6 col-lg-4">
                <div class="mb-3">
                    <h2 class="h1 mb-0 d-flex gap-2">
                        <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}"
                            alt="">
                        {{ translate('pooja_List') }} <span class="badge badge-soft-info badge-pill ml-1">

                        </span>
                        <span class="badge badge-soft-dark radius-50 fz-14"></span>
                    </h2>
                </div>
            </div>
            <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'add'))
                    <a href="{{ route('admin.service.add-new') }}" class="btn btn--primary">
                        <i class="tio-add"></i>
                        <span class="text">{{ translate('add_new_puja') }}</span>
                    </a>
                @endif
            </div>
        </div>
        <div class="mb-3 remove-card-shadow">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24"> {{ \App\Models\Service::where('product_type', 'pooja')->count() }}
                                </h3>
                                <div class="text-capitalize mb-0">TOTAL POOJA</div>
                            </div>
                            <div>
                                <img width="40"
                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/count.png') }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    {{ \App\Models\Service::where('product_type', 'pooja')->where('status', 1)->count() }}
                                </h3>
                                <div class="text-capitalize mb-0">ACTIVE POOJA</div>
                            </div>
                            <div>
                                <img width="40"
                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/active.png') }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    {{ \App\Models\Service::where('product_type', 'pooja')->where('status', 1)->where(['pooja_type' => '0'])->count() }}
                                </h3>
                                <div class="text-capitalize mb-0">Total Weekly Pooja</div>
                            </div>
                            <div>
                                <img width="40"
                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/event.png') }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">{{ \App\Models\Service::where(['pooja_type' => '1'])->count() }}
                                </h3>
                                <div class="text-capitalize mb-0">Total Special Pooja</div>
                            </div>
                            <div>
                                <img width="40"
                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/special.png') }}"
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
                                        <th>{{ translate('pooja_name') }}</th>
                                        <th class="max-width-100px">{{ translate('pooja_category') }}</th>
                                        <th>{{ translate('prashad_assign') }}</th>
                                        <th class="max-width-100px">{{ translate('pooja_type') }}</th>
                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'status'))
                                            <th class="text-center">{{ translate('status') }}</th>
                                        @endif
                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'schedule') ||
                                                Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'edit') ||
                                                Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'delete') ||
                                                Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'whatsapp') ||
                                                Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'prashad'))
                                            <th class="text-center"> {{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $key => $item)
                                        <tr>
                                            <td>{{ $services->firstItem() + $key }}</td>
                                            <td>
                                                {{-- <a href="{{ route('admin.service.views',[$item['added_by'],$item['id']]) }}" --}}
                                                <a href="{{ route('admin.service.views', [$item['added_by'], $item['id']]) }}"
                                                    data-addedby="{{ $item['added_by'] }}" data-id="{{ $item['id'] }}"
                                                    class="media align-items-center gap-2 view-service">
                                                    <img src="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/' . $item['thumbnail'], type: 'backend-product') }}"
                                                        class="avatar border" alt="">
                                                    <span class="media-body title-color hover-c1">
                                                        <strong>{{ Str::limit($item['name'], 40) }} </strong>
                                                    </span>
                                                </a>
                                            </td>

                                            <td class="text-center">
                                                <strong>
                                                    {{ isset($item['category']) && $item['category'] !== null ? translate(str_replace('_', ' ', $item['category']['name'])) : 'No Category' }}
                                                </strong>
                                            </td>
                                            <td>
                                                @php
                                                    $Pname = \App\Models\Product::where('id', $item->prashadam_id)->first();
                                                @endphp
                                                
                                                @if($Pname)
                                                    {{ $Pname->name }} 
                                                @else
                                                    <p>Product not found</p>
                                                @endif
    
    
                                                </td>
                                            <td class="text-center">
                                                @if ($item['pooja_type'] == 1)
                                                    Special
                                                @elseif ($item['pooja_type'] == 0)
                                                    Weekly
                                                @else
                                                    {{ $item['pooja_type'] }}
                                                @endif
                                            </td>

                                            @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'status'))
                                                <td>
                                                    <form action="{{ route('admin.service.status-update') }}"
                                                        method="post" id="service-status{{ $item['id'] }}-form">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                        <label class="switcher mx-auto">
                                                            <input type="checkbox"
                                                                class="switcher_input toggle-switch-message" name="status"
                                                                id="service-status{{ $item['id'] }}" value="1"
                                                                {{ $item['status'] == 1 ? 'checked' : '' }}
                                                                data-modal-id = "toggle-status-modal"
                                                                data-toggle-id = "service-status{{ $item['id'] }}"
                                                                data-on-image = "service-status-on.png"
                                                                data-off-image = "service-status-off.png"
                                                                data-on-title = "{{ translate('Want_to_Turn_ON') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                                data-off-title = "{{ translate('Want_to_Turn_OFF') . ' ' . $item['defaultname'] . ' ' . translate('status') }}"
                                                                data-on-message = "<p>{{ translate('if_enabled_this_rashi_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                                data-off-message = "<p>{{ translate('if_disabled_this_rashi_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </form>
                                                </td>
                                            @endif

                                            @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'schedule') ||
                                                    Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'edit') ||
                                                    Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'delete') ||
                                                    Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'prashad') ||
                                                    Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'whatsapp'))
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'schedule'))
                                                            @if ($item['pooja_type'] == 1)
                                                                <a class="btn btn-outline-info btn-sm square-btn"
                                                                    title="{{ translate('Schedule_Pooja') }}"
                                                                    href="{{ route('admin.service.schedule', [$item['id']]) }}">
                                                                    <i class="tio-date-range nav-icon"></i>
                                                                </a>
                                                            @endif
                                                        @endif
                                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'prashad'))
                                                            <a class="btn btn-outline-warning btn-sm square-btn"
                                                                title="{{ translate('Prashadam') }}" href="javascript:(0);" data-id="{{ $item->id }}" onclick="prashadam_model(this)">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/prsd.png') }}"
                                                                    alt="" width="20px" height="20px">
                                                            </a>
                                                        @endif

                                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'edit'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                title="{{ translate('edit') }}"
                                                                href="{{ route('admin.service.update', [$item['id']]) }}">
                                                                <i class="tio-edit"></i>
                                                            </a>
                                                        @endif

                                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'delete'))
                                                            <a class="btn btn-outline-danger btn-sm delete delete-data"
                                                                href="javascript:" data-id="service-{{ $item['id'] }}"
                                                                title="{{ translate('delete') }}"><i
                                                                    class="tio-delete"></i>
                                                            </a>

                                                            <form action="{{ route('admin.service.delete', [$item['id']]) }}"
                                                                method="post" id="service-{{ $item['id'] }}">
                                                                @csrf @method('delete')
                                                            </form>
                                                        @endif
                                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'whatsapp'))
                                                    <a class="btn btn-outline-primary btn-sm open-whatsapp-modal"
                                                        href="javascript:void(0);"
                                                        data-id="{{ $item['id'] }}"
                                                        data-slug="{{ $item['slug'] }}"
                                                        title="{{ translate('whatsapp') }}">
                                                        <i class="tio-whatsapp"></i>
                                                    </a>
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
                            {{ $services->links() }}
                        </div>
                    </div>

                    @if (count($services) == 0)
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
    <!-- <span id="route-admin-rashi-delete" data-url=""></span> -->
    <span id="route-admin-rashi-status-update" data-url="{{ route('admin.service.status-update') }}"></span>
    <!-- Modal Structure -->
    <div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">Service Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody id="service-details">
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
    {{-- Prashadam Model --}}
    <div class="modal fade" id="prashadam-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Add_The_Prasadam_For_Pooja')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="">
                        
                        <label class="font-weight-bold title-color fz-14">{{ translate('Select_Prasad') }}</label>
                        <select class="js-select2-custom form-control  add-prashadam"  id="add-prashadam" name="prashadam_id" required>
                        <option value="" disabled>{{ translate('Select_Prasad') }}</option>
                            @if (count($prashadamList) > 0)
                                @foreach ($prashadamList as $PraItem)
                                        <option value="{{ $PraItem['id'] }}"  {{ $PraItem['prashadam_id'] == $PraItem['id'] ? 'selected' : '' }}> {{ $PraItem['name'] }}</option>

                                @endforeach
                            @else
                                <option disabled>No {{ translate('Add_The_Prasadam_For_Pooja')}}</option>
                            @endif
                        </select>                       
                        <form action="{{ route('admin.service.pooja_prashad') }}" method="post" id="add-prashad-form">
                            @csrf
                            <input type="hidden" name="pooja_id" id="pooja-id">
                            <input type="hidden" name="prashadam_id" id="add-prashad-id-val">
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    {{-- <button type="submit" class="btn btn-primary">Change</button> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- whatsapp Model --}}
    <div class="modal fade" id="whatsapp" tabindex="-1" role="dialog" aria-labelledby="whatsappTitleId"
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
        const link = `https://mahakal.com/epooja/${slug}`;

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


    <script>
        $('.add-prashadam').on('change', function() {
            var prashadam = $(this).val();
            $('#add-prashad-id-val').val(prashadam);
            Swal.fire({
                title: 'Are you sure you want to add the Prashad to this pooja?',
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#add-prashad-form').submit();
                }
            });
        });
        function prashadam_model(that){
            var poojaId=$(that).data('id');
            $('#pooja-id').val(poojaId);
            $('#prashadam-modal').modal('show');
        }
    </script>

@endpush
