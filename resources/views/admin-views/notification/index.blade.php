@php use App\Utils\Helpers; @endphp
@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.back-end.app')
@section('title', translate('add_new_notification'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/push_notification.png')}}" alt="">
                {{translate('send_notification')}}
            </h2>
        </div>
        <div class="row gx-2 gx-lg-3">
            @if (Helpers::modules_permission_check('Notifications', 'Send Notification', 'add'))
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.notification.index')}}" method="post" class="text-start"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color text-capitalize"
                                               for="exampleFormControlInput1">{{translate('title')}} </label>
                                        <input type="text" name="title" class="form-control"
                                               placeholder="{{translate('new_notification')}}"
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <label class="title-color text-capitalize"
                                               for="exampleFormControlInput1">{{translate('description')}} </label>
                                        <textarea name="description" class="form-control text-area-max-min" required></textarea>
                                    </div>
                                   <div class="form-group">
                                        <label class="title-color text-capitalize">
                                            {{ translate('type') }}
                                        </label>
                                        <select id="typeSelect" class="form-control" name="type">
                                            <option value="">Select Type</option>
                                            <option value="puja">Puja</option>
                                            <option value="vip">VIP</option>
                                            <option value="anushthan">Anushthan</option>
                                            <option value="chadhava">Chadhava</option>
                                            <option value="offlinepuja">Offline Puja</option>
                                            <option value="consultancy">Consultancy</option>
                                            <option value="event">Event</option>
                                            <option value="darshan">Temple</option>
                                            <option value="tour">Tour</option>
                                            <option value="product">Product</option>
                                            <option value="donation">Donation</option>
                                        </select>
                                    </div>
                                    <div class="form-group mt-3" id="dynamicBox" style="display:none;">
                                        <label>Select Item</label>
                                        <select id="itemSelect" name="service_id" class="form-control">
                                            <option value="">Select Item</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-center">
                                            <img class="upload-img-view mb-4" id="viewer"
                                                 src="{{dynamicAsset(path: 'public/assets/back-end/img/900x400/img1.jpg')}}"
                                                 alt="{{translate('image')}}"/>
                                        </div>
                                        <label
                                            class="title-color text-capitalize">{{translate('image')}} </label>
                                        <span class="text-info">({{translate('ratio').'1:1'}})</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" class="custom-file-input image-input"
                                                   data-image-id="viewer"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                   for="customFileEg1">{{translate('choose_File')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" class="btn btn-secondary">{{translate('reset')}} </button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        class="btn btn--primary {{env('APP_MODE')!='demo'?'':'call-demo'}}">{{translate('send_Notification')}}  </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2">
                                    {{ translate('push_notification_table')}}
                                    <span
                                        class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $notifications->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-custom">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="searchValue"
                                               class="form-control"
                                               placeholder="{{translate('search_by_title')}}"
                                               aria-label="Search orders" value="{{ $searchValue }}" required>
                                        <button type="submit"
                                                class="btn btn--primary">{{translate('search')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive datatable-custom">
                        <table id="table" class="table table-bordered">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{translate('SL')}} </th>
                                    <th>{{translate('title')}}/{{translate('description')}} </th>
                                    <th>{{translate('notification_count')}} </th>
                                    @if (Helpers::modules_permission_check('Notifications', 'Send Notification', 'status'))
                                    <th>{{translate('status')}} </th>
                                    @endif
                                    @if (Helpers::modules_permission_check('Notifications', 'Send Notification', 'resend'))
                                    <th>{{translate('resend')}} </th>
                                    @endif
                                    @if (Helpers::modules_permission_check('Notifications', 'Send Notification', 'edit') || Helpers::modules_permission_check('Notifications', 'Send Notification', 'delete'))
                                    <th class="text-center">{{translate('action')}} </th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($notifications as $key=>$notification)
                                <tr>
                                    <td>{{$notifications->firstItem()+ $key}}</td>
                                    <td>
                                        <img class="min-w-50" width="45" height="45"
                                            src="{{ getValidImage(path: 'storage/app/public/notification/' . ($notification['image'] ?? ''), type: 'backend-basic') }}"
                                            alt="">

                                       
                                        <span class="d-block">
                                            {{ Str::limit($notification['title'] ?? '', 30) }}
                                        </span>

                                        {{ Str::limit($notification['description'] ?? '', 40) }}
                                    </td>

                                    <td id="count-{{$notification->id}}">{{ $notification['notification_count'] }}</td>
                                    @if (Helpers::modules_permission_check('Notifications', 'Send Notification', 'status'))
                                    <td>
                                        <form action="{{route('admin.notification.update-status')}}" method="post"
                                              id="notification-status{{$notification['id']}}-form"
                                              class="notification_status_form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$notification['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message"
                                                       id="notification-status{{$notification['id']}}" name="status" value="1"
                                                       {{ $notification['status'] == 1 ? 'checked':'' }}
                                                       data-modal-id = "toggle-status-modal"
                                                       data-toggle-id = "notification-status{{$notification['id']}}"
                                                       data-on-image = "notification-on.png"
                                                       data-off-image = "notification-off.png"
                                                       data-on-title = "{{translate('Want_to_Turn_ON_Notification_Status').'?'}}"
                                                       data-off-title = "{{translate('Want_to_Turn_OFF_Notification_Status').'?'}}"
                                                       data-on-message = "<p>{{translate('if_enabled_customers_will_receive_notifications_on_their_devices')}}</p>"
                                                       data-off-message = "<p>{{translate('if_disabled_customers_will_not_receive_notifications_on_their_devices')}}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endif
                                    @if (Helpers::modules_permission_check('Notifications', 'Send Notification', 'resend'))
                                    <td>
                                        <a href="javascript:" class="btn btn-outline-success square-btn btn-sm resend-notification"
                                           data-id="{{ $notification->id }}">
                                            <i class="tio-refresh"></i>
                                        </a>
                                    </td>
                                    @endif

                                    @if (Helpers::modules_permission_check('Notifications', 'Send Notification', 'edit') || Helpers::modules_permission_check('Notifications', 'Send Notification', 'delete'))
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            @if (Helpers::modules_permission_check('Notifications', 'Send Notification', 'edit'))
                                            <a class="btn btn-outline--primary btn-sm edit square-btn"
                                               title="{{translate('edit')}}"
                                               href="{{route('admin.notification.update',[$notification['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            @endif
                                            @if (Helpers::modules_permission_check('Notifications', 'Send Notification', 'delete'))
                                            <a class="btn btn-outline-danger btn-sm delete-data-without-form"
                                               title="{{translate('delete')}}"
                                               data-action="{{route('admin.notification.delete')}}"
                                               data-id="{{$notification['id']}}')">
                                                <i class="tio-delete"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <table class="mt-4">
                            <tfoot>
                            {!! $notifications->links() !!}
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="get-resend-notification-route-and-text" data-text="{{translate("resend_notification")}}" data-action="{{ route("admin.notification.resend-notification") }}"></span>
@endsection

@push('script')
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/admin/notification.js')}}"></script>
<script>
    // Laravel se data preloaded as JSON arrays
    const puja = @json($Pooja);
    const vip = @json($vipPooja);
    const anushthan = @json($anushthan);
    const chadhava = @json($chadhava);
    const offlinepuja = @json($offlinepuja);
    const consultancy = @json($consultancy);
    const event = @json($event);
    const darshan = @json($darshan);
    const tour = @json($tour);
    const donation = @json($donation);
    const product = @json($product);

    const typeSelect = document.getElementById("typeSelect");
    const itemSelect = document.getElementById("itemSelect");
    const box = document.getElementById("dynamicBox");

    typeSelect.addEventListener("change", function () {
        let list = [];
        itemSelect.innerHTML = '<option value="">Select Item</option>';

        switch (this.value) {
            case "puja": list = puja; break;
            case "vip": list = vip; break;
            case "anushthan": list = anushthan; break;
            case "chadhava": list = chadhava; break;
            case "offlinepuja": list = offlinepuja; break;
            case "consultancy": list = consultancy; break;
            case "event": list = event; break;
            case "darshan": list = darshan; break;
            case "tour": list = tour; break;
            case "donation": list = donation; break;
            case "product": list = product; break;
        }

        if (list && list.length > 0) {
            list.forEach(i => {
                const opt = document.createElement("option");
                opt.value = i.id;
                opt.text = i.name || i.title || i.tour_name || i.event_name || 'Unnamed';
                itemSelect.appendChild(opt);
            });
            box.style.display = "block";
        } else {
            box.style.display = "none";
        }
    });
</script>
<script>
        let table = $('#table').DataTable({
            pageLength: 20,
            searching: true,
            scrollY: '650px',
            scrollCollapse: true,
            paging: true,
            fixedHeader: true,
            fixedFooter: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
        });
    </script>
@endpush