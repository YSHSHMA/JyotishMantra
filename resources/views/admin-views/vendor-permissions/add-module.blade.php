@extends('layouts.back-end.app')

@section('title', translate('add_module'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-10">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
            {{ translate('add_Module') }}
        </h2>
    </div>
    @if(!request('type'))
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-start">
                    <form action="{{ url()->current() }}" method="GET" class="form-submit-add-module">
                        <div class="col-md-4 col-sm-6 col-12">
                            <select name="type" class="form-control" onchange="return $('.form-submit-add-module').submit()">
                                <option value="">Select Option</option>
                                <option value="tour">Tour</option>
                                <option value="event">Event Org.</option>
                                <option value="trust">Trustees</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.permission-module.update-module') }}" method="post">
                @csrf
                <input type="hidden" name="module_name" value="{{ request('type')}}">
                <div class="container mt-4">
                    <div id="permissions-wrapper">
                        
                    </div>
                    <button type="button" id="add-parent" class="btn btn-success mb-3">+ Add Parent Permission</button>
                </div>
                <button type="submit" class="btn btn-success my-3">Update Module</button>
            </form>


        </div>
    </div>


    @endif
</div>
@endsection

@push('script')

<script>
    let parentIndex = 0;

    // // Add Parent
    $('#add-parent').click(function() {
        $('#permissions-wrapper').append(`
        <div class="parent-permission border p-3 mb-3 bg-light rounded">
            <div class="form-group row align-items-center">
                <label class="col-sm-2 col-form-label">Module Name</label>
                <div class="col-sm-7">
                    <input type="text" name="permissions[${parentIndex}][name]" class="form-control parent-input" placeholder="Enter Module Name">
                </div>
                <div class="col-sm-3 d-flex">
                    <button type="button" class="btn btn-primary mr-2 add-child">+ Child Module</button>
                    <button type="button" class="btn btn-danger remove-parent">−</button>
                </div>
            </div>
            <div class="child-wrapper pl-3"></div>
        </div>
    `);
        parentIndex++;
    });

    // // Remove Parent
    $(document).on('click', '.remove-parent', function() {
        $(this).closest('.parent-permission').remove();
    });

    // Add Child
    $(document).on('click', '.add-child', function() {
        const parent = $(this).closest('.parent-permission');
        const parentIdx = parent.index();
        const childWrapper = parent.find('.child-wrapper');
        const childCount = childWrapper.children().length;

        childWrapper.append(`
        <div class="child-permission border p-2 mb-2 bg-white rounded">
            <div class="form-group row align-items-center">
                <label class="col-sm-3 col-form-label">Child Module</label>
                <div class="col-sm-6">
                    <input type="text" name="permissions[${parentIdx}][children][${childCount}][name]" class="form-control child-input" placeholder="Enter Module Name">
                </div>
                <div class="col-sm-3 d-flex">
                    <button type="button" class="btn btn-warning mr-2 add-sub-child text-white">+ Sub-Module</button>
                    <button type="button" class="btn btn-danger remove-child">−</button>
                </div>
            </div>
            <div class="sub-child-wrapper pl-4"></div>
        </div>
    `);
    });

    // // Remove Child
    $(document).on('click', '.remove-child', function() {
        $(this).closest('.child-permission').remove();
    });

    // // Add Sub-Child
    $(document).on('click', '.add-sub-child', function() {
        const child = $(this).closest('.child-permission');
        const childWrapper = child.find('.sub-child-wrapper');
        const childIdx = child.index();
        const parentIdx = child.closest('.parent-permission').index();
        const subChildCount = childWrapper.children().length;

        childWrapper.append(`
        <div class="sub-child-permission form-group row align-items-center mb-2">
            <label class="col-sm-4 col-form-label">Sub Module</label>
            <div class="col-sm-6">
                <input type="text" name="permissions[${parentIdx}][children][${childIdx}][subchildren][${subChildCount}][name]" class="form-control" placeholder="Enter sub-module Name">
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-danger remove-sub-child">−</button>
            </div>
        </div>
    `);
    });

    // // Remove Sub-Child
    $(document).on('click', '.remove-sub-child', function() {
        $(this).closest('.sub-child-permission').remove();
    });
     let preloadedPermissions = @json($groupedPermissions);
    $(document).ready(function() {
    if (preloadedPermissions && preloadedPermissions.length > 0) {
        preloadedPermissions.forEach((parent, parentIdx) => {
            $('#permissions-wrapper').append(`
                <div class="parent-permission border p-3 mb-3 bg-light rounded">
                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">Module Name</label>
                        <div class="col-sm-7">
                            <input type="text" name="permissions[${parentIdx}][name]" class="form-control parent-input" value="${parent.name}" placeholder="Enter Module Name">
                        </div>
                        <div class="col-sm-3 d-flex">
                            <button type="button" class="btn btn-primary mr-2 add-child">+ Child Module</button>
                        </div>
                    </div>
                    <div class="child-wrapper pl-3"></div>
                </div>
            `);

            const parentDiv = $('#permissions-wrapper .parent-permission').last();

            parent.children.forEach((child, childIdx) => {
                const childWrapper = parentDiv.find('.child-wrapper');

                childWrapper.append(`
                    <div class="child-permission border p-2 mb-2 bg-white rounded">
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label">Child Module</label>
                            <div class="col-sm-6">
                                <input type="text" name="permissions[${parentIdx}][children][${childIdx}][name]" class="form-control child-input" value="${child.name}" placeholder="Enter Module Name">
                            </div>
                            <div class="col-sm-3 d-flex">
                                <button type="button" class="btn btn-warning mr-2 add-sub-child text-white">+ Sub-Module</button>
                            </div>
                        </div>
                        <div class="sub-child-wrapper pl-4"></div>
                    </div>
                `);

                const subChildWrapper = childWrapper.find('.child-permission').last().find('.sub-child-wrapper');

                child.subchildren.forEach((subChild, subIdx) => {
                    subChildWrapper.append(`
                        <div class="sub-child-permission form-group row align-items-center mb-2">
                            <label class="col-sm-4 col-form-label">Sub Module</label>
                            <div class="col-sm-6">
                                <input type="text" name="permissions[${parentIdx}][children][${childIdx}][subchildren][${subIdx}][name]" class="form-control" value="${subChild.name}" placeholder="Enter sub-module Name">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger remove-sub-child">−</button>
                            </div>
                        </div>
                    `);
                });
            });
        });

        parentIndex = preloadedPermissions.length;
    }
});

</script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.ckeditor').ckeditor();
    });
</script>

@endpush