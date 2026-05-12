@extends('layouts.front-end.app')

@section('title', translate('all_Categories'))

@push('css_or_js')
    <meta property="og:image" content="{{dynamicStorage(path: 'storage/app/public/company')}}/{{$web_config['web_logo']->value}}"/>
    <meta property="og:title" content="Categories of {{$web_config['name']->value}} "/>
    <meta property="og:url" content="{{env('APP_URL')}}">
    <meta property="og:description"
          content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">
    <meta property="twitter:card" content="{{dynamicStorage(path: 'storage/app/public/company')}}/{{$web_config['web_logo']->value}}"/>
    <meta property="twitter:title" content="Categories of {{$web_config['name']->value}}"/>
    <meta property="twitter:url" content="{{env('APP_URL')}}">
    <meta property="twitter:description"
          content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">
@endpush

@section('content')
    <div class="container p-3 rtl __inline-52 text-align-direction">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-9 text-center">
                <h4>{{ translate('category')}}</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-4">
                @foreach(\App\Utils\CategoryManager::parents() as $category)
                    <div class="card-header mb-2 p-2 side-category-bar action-get-categories-function"
                         data-route="{{ route('category-ajax', [$category['id']]) }}">
                        <img alt="" class="__img-18 mr-1"
                             src="{{ getValidImage(path: 'storage/app/public/category/'.$category->icon, type: 'category') }}">
                        {{$category['name']}}
                    </div>
                @endforeach
            </div>
            <div class="col-lg-9 col-md-8">
                <hr>
                <div class="row" id="ajax-categories">
                    <label class="col-md-12 text-center mt-5">
                        {{ translate('select_your_desire_category.')}}
                    </label>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('public/assets/front-end/js/categories.js') }}"></script>
@endpush
