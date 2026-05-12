@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('temple_list'))

@section('content')
 <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
                {{ translate('temple_list') }}
                <span class="badge badge-soft-dark radius-50 fz-14"></span>
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
                                @if (Helpers::modules_permission_check('Temple', 'Temple', 'add'))</div>
                                <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                                    <a href="{{route('admin.temple.add')}}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ translate('add_new_temple') }}</span>
                                    </a>
                                </div>
                                @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('temple_name') }}</th>
                                    <th class="max-width-100px">{{ translate('state_name') }}</th>
                                    <th class="max-width-100px">{{ translate('cities_name') }}</th>
                                    <th class="max-width-100px">{{ translate('trust_Name') }}</th>
                                    @if (Helpers::modules_permission_check('Temple', 'Temple', 'status'))
                                    <th class="text-center">{{ translate('status') }}</th>
                                    @endif
                                    @if (Helpers::modules_permission_check('Temple', 'Temple', 'gallery') || Helpers::modules_permission_check('Temple', 'Temple', 'edit') || Helpers::modules_permission_check('Temple', 'Temple', 'delete'))
                                    <th class="text-center"> {{ translate('action') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($temple as $key => $item)
                                <tr>
                                    <td>{{ $temple->firstItem()+$key }}</td>
                                   
                                    <td>
                                        <a href="{{ route('admin.temple.list',['addedBy'=>$item['added_by'],'id'=>$item['id']]) }}"
                                           class="media align-items-center gap-2">
                                            <img src="{{ getValidImage(path: 'storage/app/public/temple/thumbnail/'.$item['thumbnail'], type: 'backend-product') }}"
                                                 class="avatar border" alt="">
                                            <span class="media-body title-color hover-c1">
                                            {{ Str::limit($item['name'], 20) }}
                                        </span>
                                        </a>
                                    </td>
                                    <td>
                                    {{ $item['states']['name']??"" }}
                                    </td>
                                    <td>
                                    {{ $item['cities']['city']??"" }}
                                    </td>
                                    <td>
                                        <?php
                                        $matchingTrust = \App\Models\DonateTrust::all()
                                            ->first(function ($items) use ($item) {
                                                $ids = json_decode($items->trust_temple_id, true);
                                                return in_array($item['id'], (array) $ids);
                                            });
                                        ?>
                                        @if($matchingTrust && $matchingTrust['id'])
                                        <span role="tooltip"  data-toggle='tooltip' data-placement="left" data-title="{{ $matchingTrust['trust_name']??''}}">{{ Str::limit(($matchingTrust['trust_name']??''),20)}}</span>
                                        @endif
                                    </td>
                                    @if (Helpers::modules_permission_check('Temple', 'Temple', 'status'))
                                    <td>
                                        <form action="{{route('admin.temple.status-update') }}" method="post" id="temple-status{{$item['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$item['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                       id="temple-status{{ $item['id'] }}" value="1" {{ $item['status'] == 1 ? 'checked' : '' }}
                                                       data-modal-id = "toggle-status-modal"
                                                       data-toggle-id = "temple-status{{ $item['id'] }}"
                                                       data-on-image = "temple-status-on.png"
                                                       data-off-image = "temple-status-off.png"
                                                       data-on-title = "{{ translate('Want_to_Turn_ON').' '.$item['defaultname'].' '. translate('status') }}"
                                                       data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$item['defaultname'].' '.translate('status') }}"
                                                       data-on-message = "<p>{{ translate('if_enabled_this_temple_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                       data-off-message = "<p>{{ translate('if_disabled_this_temple_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endif

                                    @if (Helpers::modules_permission_check('Temple', 'Temple', 'gallery') || Helpers::modules_permission_check('Temple', 'Temple', 'edit') || Helpers::modules_permission_check('Temple', 'Temple', 'delete')) 
                                    <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                
                                            {{-- <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('gallery') }}"
                                                    href="{{ route('admin.temple.gallery.list', [$item['id']]) }}">
                                                    <i class="tio-photo-square-outlined nav-icon"></i>
                                                     <span class="badge badge-soft-info badge-pill ml-1">
                                                        {{ \App\Models\Gallery::where('id', '$item['id']]')->count() }}
                                                    </span> 
                                                </a>--}}
                                                
                                                @if (Helpers::modules_permission_check('Temple', 'Temple', 'gallery'))
                                                <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('gallery') }}"
                                                    href="{{ route('admin.temple.gallery.add_new', [$item['id']]) }}">
                                                    <i class="tio-photo-square-outlined nav-icon"></i>                                                
                                                </a>
                                                @endif

                                                @if (Helpers::modules_permission_check('Temple', 'Temple', 'edit'))
                                                <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                    href="{{ route('admin.temple.update', [$item['id']]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                @endif
                                                <a href="{{ route('admin.temple.addpackage', $item['id']) }}"class="btn btn-outline-info btn-sm square-btn"
                                                    title="{{ translate('Package Create') }}" data-toggle="tooltip" data-placement="left"><i class="tio-book"></i>
                                                </a>

                                                @if (Helpers::modules_permission_check('Temple', 'Temple', 'delete'))
                                                <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                                data-id="temple-{{$item['id']}}"  title="{{ translate('delete')}}"><i class="tio-delete"></i>
                                                </a>
                                                <form action="{{ route('admin.temple.delete',[$item['id']]) }}"
                                                    method="post" id="temple-{{ $item['id']}}">
                                                    @csrf @method('delete')
                                                </form>
                                                @endif
                                                
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
                            {{ $temple->links() }}
                        </div>
                    </div>
                   
                    @if(count($temple)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                 
                </div>
            </div>
        </div>
    </div>
    <!-- <span id="route-admin-rashi-delete" data-url=""></span> -->
    <span id="route-admin-rashi-status-update" data-url="{{ route('admin.temple.status-update') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush
