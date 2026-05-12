@extends('layouts.back-end.app')

@section('title', translate('update_Notification'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/push_notification.png')}}" alt="">
                {{translate('push_notification_update')}}
            </h2>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.notification.update',[$notification['id']])}}" method="post" class="text-start"
                        enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('title')}}</label>
                                <input type="text" value="{{$notification['title']}}" name="title" class="form-control"
                                        placeholder="{{translate('new_notification')}}" required>
                            </div>
                            <div class="form-group mb-0">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('description')}}</label>
                                <textarea name="description" class="form-control"
                                            required>{{$notification['description']}}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="title-color text-capitalize">
                                    {{ translate('type') }}
                                </label>
                                <select id="typeSelect" class="form-control" name="type" value="{{$notification}}">
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
                                <select id="itemSelect" name="service_id" class="form-control" required>
                                    <option value="">Select Item</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-center">
                                <img class="upload-img-view mt-4" id="viewer"
                                     src="{{ getValidImage(path: 'storage/app/public/notification/'.$notification['image']?? '', type: 'backend-basic') }}"
                                        alt="{{translate('image')}}"/>
                            </div>
                            <label class="title-color">{{translate('image')}}</label>
                            <span class="text-info"> ( {{translate('ratio').'1:1'}})</span>
                            <div class="custom-file">
                                <input type="file" name="image"  class="custom-file-input image-input" data-image-id="viewer"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="customFileEg1">{{translate('choose_File')}}</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
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
@endpush