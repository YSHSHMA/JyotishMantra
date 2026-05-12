@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('package'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-10">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('package_List') }}
            </h2>
        </div>

        
        @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'add'))
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.package.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach ($languages as $lang)
                                    <li class="nav-item text-capitalize">
                                        <span
                                            class="nav-link form-system-language-tab cursor-pointer {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                            id="{{ $lang }}-link">
                                            {{ ucfirst(getLanguageName($lang)) . '(' . strtoupper($lang) . ')' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        {{-- <input type="hidden" name="service_id" value="{{ $id }}"> --}}
                                        @foreach ($languages as $lang)
                                            <div class="form-group {{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                                                id="{{ $lang }}-form">
                                                <div class="form-group">
                                                    <label class="title-color">{{ translate('package_Title') }}<span
                                                            class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                                    <input type="text" name="title[]" class="form-control"
                                                        placeholder="{{ translate('new_Package') }}"
                                                        {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                                </div>

                                                <div class="form-group">
                                                    <label class="title-color">{{ translate('description') }}<span
                                                            class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                                    <textarea name="description[]" class="form-control ckeditor" id="description" value=""
                                                        placeholder="{{ translate('ex') }} : {{ translate('description') }}"
                                                        {{ $lang == $defaultLanguage ? 'required' : '' }}></textarea>
                                                </div>
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        @endforeach
                                        <input name="position" value="0" class="d-none">
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="title-color" for="person">{{ translate('person') }}</label>
                                                <input type="number" name="person" id="" class="form-control"
                                                placeholder="{{ translate('person') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="title-color" for="type">{{ translate('package_type') }}</label>
                                                <select name="type"  id="package_type" class="form-control" required>
                                                    <option value="">Select Package</option>
                                                    <option value="pooja">Pooja Package</option>
                                                    <option value="vippooja">VIP Pooja Package</option>
                                                    <option value="anushthan">Anushthan Package</option>
                                                    <option value="offlinepooja">Offline Pooja Package</option>
                                                    <option value="panditpooja">Pandit Pooja Package</option>
                                                </select>
                                                <span>{{ translate('Note: If the package type is Pandit Pooja, then selecting an Astrologer is mandatory.') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 d-none" id="panditDiv">
                                            <div class="form-group">
                                                <label class="title-color">{{ translate('Astrologer_list') }}</label>
                                                <select name="pandit_id" class="form-control">
                                                    <option value="">Select Astrologer</option>
                                                    @foreach($pandit as $nameList)
                                                        <option value="{{ $nameList->id }}">{{ $nameList->name }} ({{ $nameList->type }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="title-color" for="price">{{ translate('color') }}</label>
                                                <input type="color" name="color" id="" class="form-control"
                                                    placeholder="{{ translate('color') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <button type="reset" id="reset"
                                    class="btn btn-secondary">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row mt-20" id="cate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
                            <div class="">
                                <h5 class="text-capitalize d-flex gap-1">
                                    {{ translate('package_list') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{ $packages->count() }}</span>
                                </h5>
                            </div>

                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="packageTable"
                            class="table table-hover table-borderless  table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th class="text-center">{{ translate('SL') }}</th>
                                    <th class="text-center">{{ translate('title') }}</th>
                                    <th class="text-center">{{ translate('type') }}</th>
                                    <th class="text-center">{{ translate('person') }}</th>
                                    <th class="text-center">{{ translate('color') }}</th>
                                    @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'edit') || Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'delete'))
                                    <th class="text-center">{{ translate('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packages as $key => $package)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td class="text-center">{{ $package['title'] }}</td>
                                        <td class="text-center">{{ $package['type'] }}</td>
                                        <td class="text-center"> {{ $package['person'] }}
                                        <td class="text-center">
                                            <center>
                                                <div style="width: 20px; height: 20px; background-color: {{ isset($package['color']) ? $package['color'] : '#ffffff' }}"></div>
                                            </center>                                            
                                        </td>
                                        @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'edit') || Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'delete'))
                                        <td>
                                            <div class="d-flex justify-content-center gap-10">
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'edit'))
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('edit') }}"
                                                        href="{{ route('admin.package.update', [$package['id']]) }}">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                    @endif

                                                    @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'delete'))
                                                    <span class="btn btn-outline-danger btn-sm square-btn delete-package"
                                                        title="{{ translate('delete') }}"
                                                        data-id="package-{{ $package['id'] }}">
                                                        <i class="tio-delete"></i>
                                                    </span>
                                                    @endif
                                                </div>
                                                <form action="{{ route('admin.package.delete', [$package['id']]) }}"
                                                    method="post" id="package-{{ $package['id'] }}">
                                                    @csrf @method('delete')
                                                </form>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center">{{ translate('SL') }}</th>
                                    <th class="text-center">{{ translate('title') }}</th>
                                    <th class="text-center">{{ translate('type') }}</th>
                                    <th class="text-center">{{ translate('person') }}</th>
                                    <th class="text-center">{{ translate('color') }}</th>
                                    @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'edit') || Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'delete'))
                                    <th class="text-center">{{ translate('action') }}</th>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $packages->links() }}
                        </div>
                    </div>
                    @if (count($packages) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="{{ translate('image_description') }}">
                            <p class="mb-0">{{ translate('no_data_found') }}</p>
                        </div>
                    @endif -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>

    <script>
        $('.delete-package').on('click', function() {
            let packageId = $(this).attr("data-id");
            Swal.fire({
                title: messageAreYouSureDeleteThis,
                text: messageYouWillNotAbleRevertThis,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + packageId).submit();
                }
            });
        });
    </script>
     <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#packageTable').DataTable({
                pageLength: 10,
                scrollY: '500px',
                scrollCollapse: true,
                paging: true,
                fixedHeader: true,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
            });

            // Filter auto-submit (no need for ajax reload)
            $('#payment-status, #purohit-id, #booking-status').on('change', function() {
                $(this).closest('form').submit();
            });
        });
    </script>
    <script>
    $(document).ready(function () {

        $('#package_type').on('change', function () {
            let type = $(this).val();

            if (type === 'panditpooja') {
                $('#panditDiv').removeClass('d-none');
                $('#panditDiv select').prop('required', true);
            } else {
                $('#panditDiv').addClass('d-none');
                $('#panditDiv select').prop('required', false).val('');
            }
        });

    });
</script>

@endpush
