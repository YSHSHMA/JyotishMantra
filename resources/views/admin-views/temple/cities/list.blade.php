@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('cities_list'))
@section('content')
 <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
                {{ translate('cities_list') }}
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
                            </div>
                              <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                                @if (Helpers::modules_permission_check('Temple', 'City', 'add'))
                                    <a href="{{route('admin.temple.cities.add-new')}}" class="btn btn--primary">
                                        <i class="tio-add"></i>
                                        <span class="text">{{ translate('add_new_cities') }}</span>
                                    </a>
                                    @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('citie_name') }}</th>
                                    <th class="max-width-100px">{{ translate('short_descescription') }}</th>
                                    <th class="max-width-100px">{{ translate('famous_for') }}</th>
                                    <th class="max-width-100px">{{ translate('create_date') }}</th>
                                    @if (Helpers::modules_permission_check('Temple', 'City', 'edit') || Helpers::modules_permission_check('Temple', 'City', 'gallery') || Helpers::modules_permission_check('Temple', 'City', 'visit'))
                                    <th class="text-center"> {{ translate('action') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cities as $key => $item)
                                <tr>
                                    <td>{{ $cities->firstItem()+$key }}</td>
                                    <td>{{ Str::limit($item['city'], 20) }}</td>
                                    <td>{{ $item['short_desc'] }}</td>
                                    <td>{{ $item['famous_for'] }}</td>
                                    <td>{{ date('d M Y',strtotime($item->created_at)) }}</td>
                              
                                    @if (Helpers::modules_permission_check('Temple', 'City', 'edit') || Helpers::modules_permission_check('Temple', 'City', 'gallery') || Helpers::modules_permission_check('Temple', 'City', 'visit'))
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            @if ( Helpers::modules_permission_check('Temple', 'City', 'visit'))
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('best_time_to_visite') }}"
                                             href="{{ route('admin.visit.list', [$item['id']]) }}">
                                                <i class="tio-star nav-icon"></i>
                                            </a>
                                            @endif
                                            @if (Helpers::modules_permission_check('Temple', 'City', 'edit'))
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                href="{{ route('admin.temple.cities.update', [$item['id']]) }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            @endif
                                            @if (Helpers::modules_permission_check('Temple', 'City', 'gallery'))
                                            <a class="btn btn-outline-danger btn-sm delete delete-data" href="javascript:"
                                            data-id="cities-{{$item['id']}}" title="{{ translate('delete')}}"><i class="tio-delete"></i>
                                            </a>
                                            @endif
                                            <form action="{{ route('admin.temple.cities.delete',[$item['id']]) }}"
                                                method="post" id="cities-{{$item['id']}}">
                                                @csrf @method('delete')
                                            </form>
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
                            {{ $cities->links() }}
                        </div>
                    </div>
                   
                    @if(count($cities)==0)
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
   
@endsection

@push('script')

@endpush