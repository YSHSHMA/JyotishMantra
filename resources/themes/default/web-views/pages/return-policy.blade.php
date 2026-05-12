@extends('layouts.front-end.app')

@section('title',translate('return_policy'))

@push('css_or_js')
    <meta property="og:image" content="{{dynamicStorage(path: 'storage/app/public/company')}}/{{$web_config['web_logo']->value}}"/>
    <meta property="og:title" content="Terms & conditions of {{$web_config['name']->value}} "/>
    <meta property="og:url" content="{{env('APP_URL')}}">
    <meta property="og:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">
    <meta property="twitter:card" content="{{dynamicStorage(path: 'storage/app/public/company')}}/{{$web_config['web_logo']->value}}"/>
    <meta property="twitter:title" content="Terms & conditions of {{$web_config['name']->value}}"/>
    <meta property="twitter:url" content="{{env('APP_URL')}}">
    <meta property="twitter:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">
@endpush

@section('content')
<div class="inner-page-bg center bg-bla-7 py-4" style="background:url(public/assets/front-end/img/bg.jpg) no-repeat;background-size:cover;background-position:center center">
		<div class="container">
			<div class="row all-text-white">
				<div class="col-md-12 align-self-center">
					<h1 class="innerpage-title">{{translate('return_policy')}}</h1>
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item active"><a href="index.html" class="text-white"><i class="fa fa-home"></i> Home</a></li>
							<li class="breadcrumb-item">{{translate('return_policy')}}</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
    <div class="container py-5 rtl text-align-direction">
        <!--<h2 class="text-center mb-3 headerTitle">{{translate('return_policy')}}</h2>-->
        <div class="card __card">
            <div class="card-body text-justify">
                {!! $returnPolicy['content'] !!}
            </div>
        </div>
    </div>
@endsection
