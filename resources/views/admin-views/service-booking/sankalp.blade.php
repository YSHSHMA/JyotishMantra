@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('sankalp'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" alt="">
                {{ translate('sankalp - ' . $service->name) }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card my-2 p-2">
                    <h6>User Detail</h6>
                    <form action="{{ route('admin.book.order.place') }}" method="POST">
                        @csrf
                        <input type="hidden" name="sankalp" value="yes">
                        <input type="hidden" name="service_id" value={{$service->id}}>
                        <input type="hidden" name="package_id" value={{$packageId}}>
                        <input type="hidden" name="user_id" value={{$userId}}>
                        <input type="hidden" name="price" value={{$price}}>
                        <div class="row">
                            @for ($i = 1; $i <= $persons; $i++)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="member">Member</label>
                                        <input type="text" name="member[]" class="form-control"
                                            placeholder="Enter member name" required>
                                    </div>
                                </div>
                            @endfor
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gotra">Gotra</label>
                                    <input type="text" name="gotra" value="Kashyapa" class="form-control"
                                        placeholder="Enter gotra" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prashad">Prashad</label>
                                    <select name="prashad" id="prashad" class="form-control">
                                        <option value="1">Yes</option>
                                        <option value="0" selected>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="prashad-div" class="row d-none">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="house_no">House No.</label>
                                    <input type="number" name="house_no" class="form-control" placeholder="Enter house no">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="landmark">Landmark</label>
                                    <input type="text" name="landmark" class="form-control" placeholder="Enter landmark">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="area">Address</label>
                                    <input type="text" name="area" id="google-search" class="form-control"
                                        placeholder="Enter address">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text" name="state" id="state" class="form-control"
                                        placeholder="Enter state">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">city</label>
                                    <input type="text" name="city" id="city" class="form-control"
                                        placeholder="Enter city">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pincode">pincode</label>
                                    <input type="number" name="pincode" id="pincode" class="form-control"
                                        placeholder="Enter pincode">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="text" name="latitude" id="latitude" class="form-control"
                                        placeholder="Enter latitude">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">Longitude</label>
                                    <input type="text" name="longitude" id="longitude" class="form-control"
                                        placeholder="Enter longitude">
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <div class="form-group">
                                <button class="btn btn-primary" id="submit">Submit</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete"
        async></script>

    <script>
        $('#prashad').change(function(e) {
            e.preventDefault();
            var option = $(this).val();
            if (option == 1) {
                $('#prashad-div').removeClass('d-none');
            } else {
                $('#prashad-div').addClass('d-none');
            }
        });
    </script>

    <script>
        let autocomplete;

        function initAutocomplete() {
            const input = document.getElementById("google-search");
            const options = {
                componentRestrictions: {
                    country: "IN"
                }
            }
            autocomplete = new google.maps.places.Autocomplete(input, options);
            autocomplete.addListener("place_changed", onPlaceChange)
        }

        function onPlaceChange() {
            const place = autocomplete.getPlace();
            const addressComponents = place.address_components;

            let latitude = place.geometry.location.lat();
            let longitude = place.geometry.location.lng();
            let address = place.formatted_address;
            let state = '';
            let city = '';
            let postalCode = '';

            addressComponents.forEach(component => {
                const componentType = component.types[0];

                switch (componentType) {
                    case 'administrative_area_level_1':
                        state = component.long_name;
                        break;
                    case 'locality':
                        city = component.long_name;
                        break;
                    case 'postal_code':
                        postalCode = component.long_name;
                        break;
                }
            });

            $('#state').val(state);
            $('#city').val(city);
            $('#pincode').val(postalCode);
            $('#latitude').val(latitude);
            $('#longitude').val(longitude);
        }
    </script>
@endpush
