@php use App\Utils\Helpers; @endphp
@php use Illuminate\Support\Str; @endphp
@extends('layouts.back-end.app')

@section('title', translate('app_Downloads'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/customer.png')}}" alt="">
                {{translate('app_Downloads')}}
                <span class="badge badge-soft-dark radius-50">{{App\Models\AppDownload::get()->count()}}</span>
            </h2>
        </div>
        <div class="card">
            <div class="px-3 py-4">
                <div class="row gy-2 align-items-center">
                    <div class="col-sm-3 mb-2 mb-sm-0">
                        <form action="{{ url()->current() }}" method="GET">
                            <select name="state" id="state" class="form-control" onchange="this.form.submit()"> 
                                <option value="all">All</option>
                                @foreach ($states as $state)
                                    <option value="{{$state}}" {{ request('state') == $state ? 'selected' : '' }}>{{$state}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive datatable-custom">
                <table
                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                    <thead class="thead-light thead-50 text-capitalize">
                    <tr>
                        <th>{{translate('SL')}}</th>
                        <th>{{translate('customer_name')}}</th>
                        <th>{{translate('mobile_No')}}</th>
                        <th>{{translate('platform')}} </th>
                        <th>{{translate('country')}} </th>
                        <th>{{translate('state')}} </th>
                        <th>{{translate('city')}} </th>
                        <th>{{translate('address')}} </th>
                        <th>{{translate('latitude')}} </th>
                        <th>{{translate('longitude')}} </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($appDownloads as $key=>$download)
                        <tr>
                            <td>
                                {{$key+1}}
                            </td>
                            <td>
                                {{$download->name}}
                            </td>
                            <td>
                                {{$download->mobile_no}}
                            </td>
                            <td>
                                {{$download->platform}}
                            </td>
                            <td>
                                {{$download->country}}
                            </td>
                            <td>
                                {{$download->state}}
                            </td>
                            <td>
                                {{$download->city}}
                            </td>
                            <td>
                                {{$download->address}}
                            </td>
                            <td>
                                {{$download->latitude}}
                            </td>
                            <td>
                                {{$download->longitude}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-responsive mt-4">
                <div class="px-4 d-flex justify-content-lg-end">
                    {!! $appDownloads->links() !!}
                </div>
            </div>
            @if(count($appDownloads)==0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160" src="{{dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg')}}"
                         alt="Image Description">
                    <p class="mb-0">{{translate('no_data_to_show')}}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
