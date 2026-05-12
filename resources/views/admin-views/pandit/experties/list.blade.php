@extends('layouts.back-end.app')

@section('title', translate('experties'))

@section('content')
    {{--add modal --}}
    <div class="modal fade" id="add-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Experties</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.pandit.experties.add') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                          <label for="name">Name</label>
                          <input type="text" name="name" id="" class="form-control" placeholder="Experties Name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Add Experties</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Edit Experties</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.pandit.experties.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" id="edit-id">
                    <div class="modal-body">
                        <div class="form-group">
                          <label for="name">Name</label>
                          <input type="text" name="name" id="edit-name" class="form-control" placeholder="Experties Name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Edit Experties</button>
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
                {{ translate('experties') }}
                {{-- <span class="badge badge-soft-dark radius-50 fz-14">{{ $festivals->total() }}</span> --}}
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" class="btn btn-outline--primary" onclick="addModal()">
                                    {{ translate('add_Experties') }}
                                </button>
                            </div>
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
                                        <th>{{ translate('Status') }}</th>
                                        <th>{{ translate('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($experties as $key => $value)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$value->name}}</td>
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input experties-checkbox" {{$value->status == 1 ? "checked" : ""}} id="customSwitch{{$value->id}}" data-id="{{$value->id}}">
                                                <label class="custom-control-label" for="customSwitch{{$value->id}}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-start gap-2">
                                                <a class="btn btn-outline-info btn-sm square-btn"
                                                    title="{{ translate('edit') }}" href="javascript:0" data-id="{{$value->id}}" data-name="{{$value->name}}"  onclick="editModal(this)">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                {{-- <a class="btn btn-outline-danger btn-sm delete delete-data"
                                                    href="{{route('admin.pandit.experties.delete',$value->id)}}" title="{{ translate('delete') }}">
                                                    <i class="tio-delete"></i>
                                                </a> --}}
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $experties->links() }}
                        </div>
                    </div>
                    @if (count($experties) == 0)
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
            let id = $(that).data('id');
            let name = $(that).data('name');
            $('#edit-id').val(id);
            $('#edit-name').val(name);
            $('#edit-modal').modal('show');
        }
    </script>

    {{-- status change --}}
    <script>
         $('.experties-checkbox').change(function() {
            var isChecked = $(this).prop('checked');
            var id = $(this).data('id');
            
            $.ajax({
                url: "{{ route('admin.pandit.experties.status') }}",
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
