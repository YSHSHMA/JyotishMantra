@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('booking'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" alt="">
                {{ translate('booking') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="card col-md-3 my-2 py-2 text-center align-content-middle">
                        <a href="{{route('admin.book.pooja',['type'=>'online-pooja'])}}">Online Pooja</a>
                    </div>
                    <div class="card col-md-3 my-2 py-2 text-center align-content-middle">
                        <a href="{{route('admin.book.pooja',['type'=>'vip-pooja'])}}">VIP Pooja</a>
                    </div>
                    <div class="card col-md-3 my-2 py-2 text-center align-content-middle">
                        <a href="{{route('admin.book.pooja',['type'=>'anushthan-pooja'])}}">Anushthan Pooja</a>
                    </div>
                    <div class="card col-md-3 my-2 py-2 text-center align-content-middle">
                        <a href="{{route('admin.book.pooja',['type'=>'chadhava'])}}">Chadhava</a>
                    </div>
                    <div class="card col-md-3 my-2 py-2 text-center align-content-middle">
                        <a href="{{route('admin.book.pooja',['type'=>'counselling'])}}">Counselling</a>
                    </div>
                    <div class="card col-md-3 my-2 py-2 text-center align-content-middle">
                        <a href="{{route('admin.book.pooja',['type'=>'offline-pooja'])}}">Offline Pooja</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
@endpush
