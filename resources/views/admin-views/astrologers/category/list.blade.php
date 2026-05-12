@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('astrologer_Categories'))

@section('content')

<style>
    .imagePreview {
        max-width: 100%;
        max-height: 100px;
    }
</style>

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}"
                    alt="">
                {{ translate('astrologer_Categories') }}
                {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $festivals->total() }}</span> --}}
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="{{ url()->current() }}" method="GET">
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
                                </form>
                            </div>
                            @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Category', 'add'))
                            <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                                <a href="{{route('admin.astrologers.category.add-new')}}" type="button" class="btn btn-outline--primary">
                                    {{ translate('add_Astrologer_Category') }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('Image') }}</th>
                                        <th>{{ translate('Name') }}</th>
                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Category', 'status'))
                                        <th>{{ translate('Status') }}</th>
                                        @endif
                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Category', 'edit'))
                                        <th>{{ translate('Action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $key=>$category)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td> <img src="{{ asset('storage/app/public/astrologer-category-img/' . $category['image']) }}"
                                            alt="" width="50"></td>
                                        <td>{{$category['name']}}</td>
                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Category', 'status'))
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input category-checkbox" {{$category['status'] == 1 ? "checked" : ""}} id="customSwitch{{$category['id']}}" data-id="{{$category['id']}}">
                                                <label class="custom-control-label" for="customSwitch{{$category['id']}}"></label>
                                            </div>
                                        </td>
                                        @endif
                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Category', 'edit'))
                                        <td>
                                            <div class="d-flex justify-content-start gap-2">
                                                <a class="btn btn-outline-info btn-sm square-btn"
                                                    title="{{ translate('edit') }}" href="{{ route('admin.astrologers.category.update', [$category['id']]) }}">
                                                    <i class="tio-edit"></i>
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
                            {{ $categories->links() }}
                        </div>
                    </div>
                    @if (count($categories) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

    {{-- status change --}}
    <script>
        $('.category-checkbox').change(function() {
           var isChecked = $(this).prop('checked');
           var id = $(this).data('id');
           
           $.ajax({
               url: "{{ route('admin.astrologers.category.status') }}",
               type: "POST",
               data: {
                   _token: '{{ csrf_token() }}',
                   id: id,
                   status: isChecked ? 1 : 0
               },
               success: function(response) {
                   if(response.status == 200){
                       toastr.success('status updated successfully');
                   }
                   else{
                       toastr.error('an error occured');
                   }
               },
               error: function(xhr, status, error) {
                   toastr.error('an error occured');
               }
           });
       });
   </script>
@endpush
