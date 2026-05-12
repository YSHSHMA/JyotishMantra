@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('package'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" alt="">
                {{ translate('package - ' . $service->name) }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card my-2 p-2">
                    <h6>User Detail</h6>
                    <div class="row">
                        <input type="hidden" id="user-id">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="number" name="phone" id="phone" class="form-control"
                                    placeholder="Enter mobile no">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter user name">
                            </div>
                        </div>
                        <div class="col-md-4 d-none" id="submit-div">
                            <div class="form-group">
                                <button class="btn btn-primary" id="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card my-2 p-2 d-none" id="package-div">
                    <h6>Packages</h6>
                    <div class="row p-3">

                        <table class="table">
                            <thead>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Person</th>
                                <th>Price</th>
                                <th>Action</th>
                            </thead>

                            <tbody>
                                @forelse (json_decode($service->packages_id, true) as $key => $package)
                                    @php
                                        $packageData = App\Models\Package::find($package['package_id']);
                                    @endphp

                                    @if ($packageData)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $packageData->title }}</td>
                                            <td>{{ $packageData->person }}</td>
                                            <td>{{ $package['package_price'] }}</td>
                                            <td>
                                                <form action="{{route('admin.book.order.place')}}" id="form-{{$key+1}}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="user_id" id="package-user-id-{{$key+1}}">
                                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                                    <input type="hidden" name="package_id" value="{{ $package['package_id'] }}">
                                                    <input type="hidden" name="price" value="{{$package['package_price']}}">
                                                    <input type="hidden" name="sankalp" value="no">

                                                    <button type="button" class="btn btn-primary" data-keyid="{{$key+1}}" data-serviceid="{{ $service->id }}"
                                                        data-packageid="{{ $package['package_id'] }}"
                                                        data-price="{{ $package['package_price'] }}"
                                                        onclick="orderPlace(this)">Select</button>
                                                        </td>
                                                </form>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No Package Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}
@endsection


@push('script')
    <script>
        $('#phone').change(function(e) {
            e.preventDefault();

            var phone = '+91' + $(this).val();

            $.ajax({
                type: "GET",
                url: "{{ route('admin.book.user.check') }}",
                data: {
                    phone: phone
                },
                success: function(response) {
                    if (response.status == true) {
                        $('#user-id').val(response.user.id);
                        $('#phone').attr('disabled', true);
                        $('#name').val(response.user.name).attr('disabled', true);
                        $('#package-div').removeClass('d-none');
                        $('#submit-div').addClass('d-none');
                    } else {
                        $('#submit-div').removeClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        });
    </script>

    <script>
        $('#submit').click(function(e) {
            e.preventDefault();

            var phone = $('#phone').val().trim();
            var name = $('#name').val().trim();

            if (phone === "" || name === "") {
                alert('User details are required');
                return;
            }

            $.ajax({
                type: "POST",
                url: "{{ route('admin.book.user.register') }}",
                data: {
                    _token: '{{ csrf_token() }}',
                    phone: '+91' + phone,
                    name: name
                },
                success: function(response) {
                    if (response.status == true) {
                        $('#user-id').val(response.userStore.id);
                        $('#phone').attr('disabled', true);
                        $('#name').attr('disabled', true);
                        $('#submit-div').addClass('d-none');
                        $('#package-div').removeClass('d-none');
                    } else {
                        console.log(response);
                        toastr.error('an error occured');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        });
    </script>

    <script>
        function orderPlace(that) {
            var keyId = $(that).data('keyid');
            var userId = $('#user-id').val();
            var serviceId = $(that).data('serviceid');
            var packageId = $(that).data('packageid');
            var price = $(that).data('price');

            Swal.fire({
                title: "Do you have sankalp data?",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            }).then((result) => {
                if (result.value) {
                    window.location.href = "{{ url('admin/book/sankalp') }}" + '?service_id=' + serviceId + 
                  '&package_id=' + packageId + 
                  '&user_id=' + userId + 
                  '&price=' + price;
                } else {
                    $('#package-user-id-'+keyId).val(userId);
                    $('#form-'+keyId).submit();
                }
            });
        }
    </script>
@endpush
