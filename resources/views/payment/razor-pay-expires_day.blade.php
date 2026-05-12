@extends('layouts.front-end.app')

@section('title', translate('payment'))


@section('content')
<div class="container rtl pt-4 pb-5 text-align-direction tracking-page">
    <div class="card border-0 box-shadow-lg">
        <div class="card-body py-5">
            <h6 class="text-end small font-bold fs-14">
                <a>
                    <span class="text-primary"><i class="tio-refresh"></i></span>
                    {{ translate('clear') }}
                </a>
            </h6>
            <div class="mw-1000 mx-auto">
                <h3 class="text-center text-capitalize font-bold fs-25">
                    @if(isset($status) && $status == 1)
                    {{translate('Transaction_Successfully')}}
                    @elseif(isset($status) && $status == 2)
                    {{translate('Transaction_Failed')}}
                    @else
                    {{translate('Link_Expires')}}
                    @endif
                </h3>
                <div class="pt-md-5 mx-auto text-center max-width-350px">
                    <img class="mb-2" src="{{theme_asset(path: 'public/assets/front-end/img/icons/money.png')}}" alt="">
                    <div class="opacity-50">
                        @if(isset($status) && $status == 1)
                        {{$message}}
                        @elseif(isset($status) && $status == 2)
                        {{$message}}
                        @else
                        {{translate('amount_transaction_link_has_Expired')}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection