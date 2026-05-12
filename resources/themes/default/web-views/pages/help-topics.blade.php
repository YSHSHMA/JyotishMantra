@extends('layouts.front-end.app')

@section('title',translate('FAQ'))

@push('css_or_js')
<meta property="og:image" content="{{dynamicStorage(path: 'storage/app/public/company')}}/{{$web_config['web_logo']->value}}" />
<meta property="og:title" content="FAQ of {{$web_config['name']->value}} " />
<meta property="og:url" content="{{env('APP_URL')}}">
<meta property="og:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">

<meta property="twitter:card" content="{{dynamicStorage(path: 'storage/app/public/company')}}/{{$web_config['web_logo']->value}}" />
<meta property="twitter:title" content="FAQ of {{$web_config['name']->value}}" />
<meta property="twitter:url" content="{{env('APP_URL')}}">
<meta property="twitter:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">
@endpush

@section('content')
<div class="__inline-60">
    <div class="container rtl">
        <div class="row">
            <div class="col-md-12 sidebar_heading text-center mb-2">
                <h1 class="h3  mb-0 text-center headerTitle">{{translate('frequently_asked_question')}}</h1>
            </div>
        </div>
        <hr>
    </div>
    <div class="container rtl my-4">
        <div class="row">
            <div class="col-12">
                @if($helps)
                @foreach($helps as $faq)
                <div class="row pt-2 specification">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="accordion" id="accordionExample">
                            <div class="cards">
                                <div class="card-header" id="heading{{$faq->id}}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block  text-left btnClr" type="button" data-toggle="collapse" data-target="#collapse{{$faq->id}}" aria-expanded="true" aria-controls="collapseOne" style="white-space: normal;">
                                        <i class="czi-book text-muted mr-2"></i>  {{ $faq->question }}
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse{{$faq->id}}" class="collapse" aria-labelledby="heading{{$faq->id}}" data-parent="#accordionExample">
                                    <div class="card-body">
                                        {!! $faq->detail??$faq->answer !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
    
    </section>
    </section>
</div>
</div>
</div>
@endsection