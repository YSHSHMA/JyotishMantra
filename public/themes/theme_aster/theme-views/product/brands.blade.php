@extends('theme-views.layouts.app')

@section('title', translate('all_Brands_Page').' | '.$web_config['name']->value.' '.translate('ecommerce'))

@section('content')
    <main class="main-content d-flex flex-column gap-3 py-3 mb-30">
        <div class="container">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row gy-2 align-items-center">
                        <div class="col-md-6">
                            <h3 class="mb-1 text-capitalize">{{ translate('all_brands') }}</h3>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb fs-12 mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ translate('home') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ translate('brands') }}</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-md-end">
                                <div class="border rounded custom-ps-3 py-2">
                                    <div class="d-flex gap-2">
                                        <div class="flex-middle gap-2">
                                            <i class="bi bi-sort-up-alt"></i>
                                            {{ translate('show_brand :') }}
                                        </div>
                                        <div class="dropdown">
                                            <button type="button" class="border-0 bg-transparent dropdown-toggle p-0 custom-pe-3" data-bs-toggle="dropdown" aria-expanded="false">
                                                @if(isset($order_by) && $order_by=='desc')
                                                    {{ translate('Z-A') }}
                                                @elseif(isset($order_by) && $order_by=='asc')
                                                    {{ translate('A-Z') }}
                                                @else
                                                    {{ translate('new_arrival') }}
                                                @endif
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="d-flex" href="{{ route('brands') }}">
                                                        {{ translate('new_arrival') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="d-flex" href="{{ route('brands') }}/?order_by=asc">
                                                        {{ translate('A-Z') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="d-flex" href="{{ route('brands') }}/?order_by=desc">
                                                        {{ translate('Z-A') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="auto-col xxl-items-6 justify-content-center gap-3">
                        @foreach($brands as $brand)
                        <div class="brand-item grid-center">
                            <div class="hover__action">
                                <a href="{{route('products',['id'=> $brand['id'],'data_from'=>'brand','page'=>1])}}" class="eye-btn mx-auto mb-3">
                                    <i class="bi bi-eye fs-12"></i>
                                </a>
                                <div class="d-flex flex-column flex-wrap gap-1 text-white">
                                    <h6 class="text-white">{{$brand->brand_products_count}}</h6>
                                    <p>{{translate('Products')}}</p>
                                </div>
                            </div>
                            <img width="130" loading="lazy" class="dark-support rounded text-center"
                                 src="{{ getValidImage(path: 'storage/app/public/brand/'.$brand->image, type:'brand') }}" alt="{{$brand->name}}">
                        </div>
                        @endforeach
                            @if($brands->count()==0)
                                <div class="mb-2 mt-3"><h5 class="text-center">{{ translate('not_found_anything') }}</h5></div>
                            @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer border-0">
            {{$brands->links() }}
        </div>
    </main>
@endsection
