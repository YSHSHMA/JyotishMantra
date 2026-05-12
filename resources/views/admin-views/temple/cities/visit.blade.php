@extends('layouts.back-end.app')

@section('title', translate('best_time_to_visite'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-10">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('best_time_to_visite') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.visit.store') }}" method="POST" enctype="multipart/form-data">
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
                                        <input type="hidden" name="citie_id" value="{{ $id }}">
                                        @foreach ($languages as $lang)
                                            <div class="form-group {{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                                                id="{{ $lang }}-form">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="title-color">{{ translate('month_name') }}<span
                                                                    class="text-danger">*</span>
                                                                ({{ strtoupper($lang) }})</label>
                                                            <input type="text" name="month_name[]" class="form-control"
                                                                placeholder="{{ translate('month_name') }}"
                                                                {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                                        </div>
                                                    </div>
                                                  
                                              
                                                    
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="title-color">{{ translate('weather') }}<span
                                                                    class="text-danger">*</span>
                                                                ({{ strtoupper($lang) }})</label>
                                                            <input type="text" name="weather[]" class="form-control"
                                                                placeholder="{{ translate('weather') }}"
                                                                {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="title-color">{{ translate('sight') }}<span
                                                                    class="text-danger">*</span>
                                                                ({{ strtoupper($lang) }})</label>
                                                            <input type="text" name="sight[]" class="form-control"
                                                                placeholder="{{ translate('sight') }}"
                                                                {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        @endforeach
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="title-color">{{ translate('season') }}<span
                                                                class="text-danger">*</span>
                                                            </label>
                                                        <select class="form-control" name="season">
                                                            <option value="{{ old('season') }}" selected
                                                            disabled>{{ translate('select_season') }}</option>
                                                            <option value="peak season">Peak Season</option>
                                                            <option value="moderate season">Moderate Season</option>
                                                            <option value="off-season">Off-season</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="title-color">{{ translate('crowd') }}<span
                                                                class="text-danger">*</span>
                                                            </label>
                                                            <select class="form-control" name="crowd">
                                                                <option value="{{ old('crowd') }}" selected
                                                            disabled>{{ translate('select_crowd') }}</option>
                                                                <option value="more crowd">More crowd</option>
                                                                <option value="average crowd">Average Crowd</option>
                                                                <option value="less-crowd">Less Crowd</option>
                                                            </select>
                                                    </div>
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

        <div class="row mt-20" id="cate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
                            <div class="">
                                <h5 class="text-capitalize d-flex gap-1">
                                    {{ translate('visit_list') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{ $visit->count() }}</span>
                                </h5>
                            </div>

                        </div>
                    </div>

                    <div class="table-responsive">
                        <table
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th class="text-center">{{ translate('SL') }}</th>
                                    <th class="text-center">{{ translate('month_name') }}</th>
                                    <th class="text-center">{{ translate('crowd') }}</th>
                                    <th class="text-center">{{ translate('weather') }}</th>
                                    <th class="text-center">{{ translate('visit_view') }}</th>
                                    <th class="text-center">{{ translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($visit as $key => $item)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td class="text-center">{{ $item['month_name'] }}
                                            <br>{{ $item['season'] }}</td>
                                        <td class="text-center">{{ $item['crowd'] }}</td>
                                        <td class="text-center">{{ $item['weather'] }}
                                        <td class="text-center">{{ $item['sight'] }}
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-10">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('edit') }}"
                                                        href="{{ route('admin.visit.update', [$item['id']]) }}">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                    <span class="btn btn-outline-danger btn-sm square-btn delete-visit"
                                                        title="{{ translate('delete') }}" data-id="visit-{{ $item['id'] }}">
                                                        <i class="tio-delete"></i>
                                                    </span>
                                                </div>
                                                <form action="{{ route('admin.visit.delete', [$item['id']]) }}"
                                                    method="post" id="visit-{{ $item['id'] }}">
                                                    @csrf @method('delete')
                                                </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $visit->links() }}
                        </div>
                    </div>
                    @if (count($visit) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="{{ translate('image_description') }}">
                            <p class="mb-0">{{ translate('no_data_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>

<script>
    $('.delete-visit').on('click', function() {
        let visiteID = $(this).attr("data-id");
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
                $('#' + visiteID).submit();
            }
        });
    });
</script>
@endpush
