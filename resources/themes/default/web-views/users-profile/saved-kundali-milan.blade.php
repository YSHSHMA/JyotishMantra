@extends('layouts.front-end.app')

@section('title', translate('kundali_milan'))

@section('content')

    <div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
        <div class="row">
            @include('web-views.partials._profile-aside')

            <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">
                <div class="card __card d-none d-lg-flex web-direction customer-profile-orders">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-0 mb-md-3">
                            <h5 class="font-bold mb-0 fs-16">{{ translate('saved_Kundali') }}</h5>
                        </div>

                        @if($kundaliMilans->count()>0)
                        <div class="table-responsive">
                            <table class="table __table __table-2 text-center">
                                <thead class="thead-light">
                                    <tr style="background-color: #fe9802; color: white;">
                                        <td class="tdBorder" colspan="5">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('male')}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="tdBorder" colspan="5">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('female')}}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tdBorder">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('#')}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="tdBorder">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('name')}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="tdBorder">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('birth_date')}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="tdBorder">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('birth_time')}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="tdBorder">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('city')}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="tdBorder">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('name')}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="tdBorder">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('birth_date')}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="tdBorder">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('birth_time')}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="tdBorder">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('city')}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="tdBorder">
                                            <div>
                                                <span class="d-block spandHeadO">
                                                    {{translate('action')}}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach($kundaliMilans as $key=>$kundali)
                                    <tr>
                                        <td class="bodytr">
                                            {{ $key+1 }}
                                        </td>
                                        <td class="bodytr">
                                            {{ $kundali['male_name'] }}
                                        </td>
                                        <td class="bodytr">
                                            {{ date('d/m/Y', strtotime($kundali['male_dob'])) }}
                                        </td>
                                        <td class="bodytr">
                                            {{ $kundali['male_time'] }}
                                        </td>
                                        <td class="bodytr">
                                            {{ $kundali['male_city'] }}
                                        </td>
                                        <td class="bodytr">
                                            {{ $kundali['female_name'] }}
                                        </td>
                                        <td class="bodytr">
                                            {{ date('d/m/Y',strtotime($kundali['female_dob'])) }}
                                        </td>
                                        <td class="bodytr">
                                            {{ $kundali['female_time'] }}
                                        </td>
                                        <td class="bodytr">
                                            {{ $kundali['female_city'] }}
                                        </td>
                                        <td class="bodytr">
                                            <div class="__btn-grp-sm flex-nowrap">
                                                <a href="{{ route('saved.kundali.milan.show', ['id'=>$kundali->id]) }}"
                                                class="btn-outline--info text-base __action-btn btn-shadow rounded-full" title="{{translate('view_order_details')}}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                {{-- <a href="{{route('generate-invoice',[$order->id])}}" title="{{translate('download_invoice')}}"
                                                    class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                </a> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{theme_asset(path: 'public/assets/front-end/img/icons/order.svg')}}" alt="" width="70">
                                <h5 class="mt-1 fs-14">{{translate('no_order_found')}}!</h5>
                            </div>
                        @endif
                        <div class="card-footer border-0">
                            {{$kundaliMilans->links()}}
                        </div>
                    </div>
                </div>

            </section>
        </div>

    </div>

@endsection
