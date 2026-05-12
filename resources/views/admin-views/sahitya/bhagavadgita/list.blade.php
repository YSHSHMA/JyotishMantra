@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('bhagavad_gita_List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/bhagavadgita.png') }}" alt="">
                {{ translate('bhagavad_gita_List') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ $bhagavadgitas->total() }}</span>
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
                                            placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" value="{{ request('searchValue') }}" required>
                                        <button type="submit" class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('Image') }}</th>
                                    <th>{{ translate('chapter_name') }}</th>
                                    <!-- <th class="text-center">{{ translate('status') }}</th> -->
                                    @if (Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'add') || Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'edit') || Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'detail'))
                                    <th class="text-center"> {{ translate('action') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bhagavadgitas as $key => $bhagavadgita)
                                    <tr>
                                        <td>{{ $bhagavadgitas->firstItem()+$key }}</td>
                                        <td>
                                            <div class="avatar-60 d-flex align-items-center rounded">
                                                <img class="img-fluid" alt=""
                                                     src="{{ getValidImage(path: 'storage/app/public/sahitya/bhagavad-gita/'.$bhagavadgita['image'], type: 'backend-bhagavadgita') }}">
                                            </div>
                                        </td>
                                        <td class="overflow-hidden max-width-100px">
                                            <span data-toggle="tooltip" data-placement="right" title="{{$bhagavadgita['defaultname']}}">
                                                 {{ Str::limit($bhagavadgita['defaultname'],20) }}
                                            </span>
                                        </td>
                                        <!-- <td>
                                            <form action="{{route('admin.bhagavadgita.status-update') }}" method="post" id="bhagavadgita-status{{$bhagavadgita['id']}}-form">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$bhagavadgita['id']}}">
                                                <label class="switcher mx-auto">
                                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                           id="bhagavadgita-status{{ $bhagavadgita['id'] }}" value="1" {{ $bhagavadgita['status'] == 1 ? 'checked' : '' }}
                                                           data-modal-id = "toggle-status-modal"
                                                           data-toggle-id = "bhagavadgita-status{{ $bhagavadgita['id'] }}"
                                                           data-on-image = "bhagavadgita-status-on.png"
                                                           data-off-image = "bhagavadgita-status-off.png"
                                                           data-on-title = "{{ translate('Want_to_Turn_ON').' '.$bhagavadgita['defaultname'].' '. translate('status') }}"
                                                           data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$bhagavadgita['defaultname'].' '.translate('status') }}"
                                                           data-on-message = "<p>{{ translate('if_enabled_this_bhagavadgita_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                           data-off-message = "<p>{{ translate('if_disabled_this_bhagavadgita_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </form>
                                        </td> -->

                                        @if (Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'add') || Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'edit') || Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'detail'))
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @if (Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'detail'))
                                                <a class="btn btn-outline-primary btn-sm square-btn"
                                                    title="{{ translate('view') }}"
                                                    href="{{ route('admin.bhagavadgita.details', [$bhagavadgita->id]) }}">
                                                    <i class="tio-visible"></i>
                                                 </a>
                                                 @endif

                                                 @if (Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'add'))
                                                  <a class="btn btn-outline-primary btn-sm square-btn"
                                                       title="{{ translate('add') }}"
                                                       href="{{ route('admin.bhagavadgita.add_verse', [$bhagavadgita['id']]) }}">
                                                       <i class="tio-add"></i>
                                                    </a>
                                                    @endif

                                                    @if (Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'detail'))
                                                <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                    href="{{ route('admin.bhagavadgita.update', [$bhagavadgita['id']]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                @endif
                                                <!-- <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="bhagavadgita-{{$bhagavadgita['id']}}"
                                                title="{{ translate('delete')}}">
                                                <i class="tio-delete"></i>
                                            </a> -->
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
                            {{ $bhagavadgitas->links() }}
                        </div>
                    </div>
                    @if(count($bhagavadgitas)==0)
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

