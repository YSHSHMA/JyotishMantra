@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('skills'))

@section('content')
    {{--add modal --}}
    <div class="modal fade" id="add-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Skill</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.astrologers.skill.add') }}" method="post">
                    @csrf
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        @foreach($languages as $lang)
                            <li class="nav-item text-capitalize">
                                <span
                                    class="nav-link form-system-language-tab cursor-pointer {{ $lang == $defaultLanguage? 'active':''}}"
                                    id="{{ $lang}}-link">
                                    {{ucfirst(getLanguageName($lang)).'('.strtoupper($lang).')'}}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="modal-body">
                        <div>
                            @foreach($languages as $lang)

                                <div
                                    class="form-group {{ $lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                                    id="{{ $lang}}-form">
                                    <label class="title-color">{{ translate('skills_name') }}<span
                                            class="text-danger">*</span> ({{strtoupper($lang) }})</label>
                                    <input type="text" name="name[]" class="form-control"
                                            placeholder="{{ translate('new_skills') }}" {{ $lang == $defaultLanguage? 'required':''}}>

                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                </div>
                            @endforeach
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Add Skill</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--edit modal --}}
    <div class="modal fade" id="edit-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Skill</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post">
                    @csrf
                    <input type="text" name="id" id="edit-id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="" class="form-label">Name</label>
                            <input type="text" name="name" id="edit-name" class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Update Skill</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}"
                    alt="">
                {{ translate('skill') }}
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
                            @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Skill', 'add'))
                            <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                                <button type="button" class="btn btn-outline--primary" onclick="addModal()">
                                    {{ translate('add_Skill') }}
                                </button>
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
                                        <th>{{ translate('#') }}</th>
                                        <th style="width: 50%">{{ translate('Name') }}</th>
                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Skill', 'status'))
                                        <th>{{ translate('Status') }}</th>
                                        @endif
                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Skill', 'edit'))
                                        <th>{{ translate('Action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($skills as $key => $value)
                                    <!-- {{ $value }} -->
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$value->name}}</td>
                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Skill', 'status'))
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input skill-checkbox" {{$value['status'] == 1 ? "checked" : ""}} id="customSwitch{{$value['id']}}" data-id="{{$value['id']}}">
                                                <label class="custom-control-label" for="customSwitch{{$value['id']}}"></label>
                                            </div>
                                        </td>
                                        @endif
                                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Skill', 'edit'))
                                        <td>
                                            <div class="d-flex justify-content-start gap-2">
                                                <a class="btn btn-outline-info btn-sm square-btn"
                                                    title="{{ translate('edit') }}" href="{{route('admin.astrologers.skill.update',$value['id'])}}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                {{-- <a class="btn btn-outline-danger btn-sm delete delete-data"
                                                    href="javascript:" title="{{ translate('delete') }}">
                                                    <i class="tio-delete"></i>
                                                </a> --}}
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
                            {{ $skills->links() }}
                        </div>
                    </div>
                    @if (count($skills) == 0)
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

    {{-- add modal --}}
    <script>
        function addModal() {
            $('#add-modal').modal('show');
        }
    </script>

    {{-- edit modal --}}
    <script>
        function editModal(that) {
            var id = $(that).attr('data-id');
            var name = $(that).attr('data-name');
            $('#edit-id').val(id);
            $('#edit-name').val(name);
            $('#edit-modal').modal('show');
        }
    </script>

    {{-- status change --}}
    <script>
        $('.skill-checkbox').change(function() {
           var isChecked = $(this).prop('checked');
           var id = $(this).data('id');
           
           $.ajax({
               url: "{{ route('admin.astrologers.skill.status') }}",
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
