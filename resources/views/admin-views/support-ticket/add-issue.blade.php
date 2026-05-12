@extends('layouts.back-end.app')

@section('title', translate('issue'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-10">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
            {{ translate('Ticket_issue_List') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-start">
                    <form action="{{ route('admin.support-ticket.issue-store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <div class="form-group form-system-language-form">
                                        <div class="form-group">
                                            <label class="title-color">{{ translate('select_type') }}<span class="text-danger">*</span> </label>
                                            <select name="type_id" class="form-control" required>
                                                <option value="">{{ translate('select_type') }} </option>
                                                @if($TypeList)
                                                @foreach($TypeList as $v_data)
                                                <option value="{{ $v_data['id']}}">{{ $v_data['name']}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <div class="form-group form-system-language-form">
                                        <div class="form-group">
                                            <label class="title-color">{{ translate('issue_name') }}<span class="text-danger">*</span> </label>
                                            <input type="text" name="issue_name" class="form-control" placeholder="{{ translate('ticket_issue_name') }}" required>
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
                                {{ translate('ticket_type_list') }}
                                <span class="badge badge-soft-dark radius-50 fz-12">{{ $issueList->count() }}</span>
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
                                <th class="text-center">{{ translate('ticket_type_name') }}</th>
                                <th class="text-center">{{ translate('issue_name') }}</th>
                                <th class="text-center">{{ translate('status') }}</th>
                                <th class="text-center">{{ translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($issueList as $key => $package)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-center">{{ ($package['TicketType']['name']??"") }}</td>
                                <td class="text-center">{{ $package['issue_name'] }}</td>
                                <td class="text-center">
                                    <form action="{{route('admin.support-ticket.status-update-issue') }}" method="post" id="brand-status{{$package['id']}}-form">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$package['id']}}">
                                        <label class="switcher mx-auto">
                                            <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                id="brand-status{{ $package['id'] }}" value="1" {{ $package['status'] == 1 ? 'checked' : '' }}
                                                data-modal-id="toggle-status-modal"
                                                data-toggle-id="brand-status{{ $package['id'] }}"
                                                data-on-title="{{ translate('Want_to_Turn_ON').' '. translate('status') }}"
                                                data-off-title="{{ translate('Want_to_Turn_OFF').' '.translate('status') }}"
                                                data-on-message="<p>{{ translate('if_enabled_this_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                data-off-message="<p>{{ translate('if_disabled_this_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                            <span class="switcher_control"></span>
                                        </label>
                                    </form>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-10">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                title="{{ translate('edit') }}"
                                                href="{{ route('admin.support-ticket.issue-update', [$package['id']]) }}">
                                                <i class="tio-edit"></i>
                                            </a>

                                            <span class="btn btn-outline-danger btn-sm square-btn delete-package"
                                                title="{{ translate('delete') }}"
                                                data-id="package-{{ $package['id'] }}">
                                                <i class="tio-delete"></i>
                                            </span>
                                        </div>
                                        <form action="{{ route('admin.support-ticket.delete-type-issue', [$package['id']]) }}"
                                            method="post" id="package-{{ $package['id'] }}">
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
                        {{ $issueList->links() }}
                    </div>
                </div>
                @if (count($issueList) == 0)
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
@endpush