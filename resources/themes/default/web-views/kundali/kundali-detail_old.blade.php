@extends('layouts.front-end.app')

@section('title',translate('kundali'))

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
<div class="inner-page-bg center bg-bla-7 py-4" style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
		<div class="container">
			<div class="row all-text-white">
				<div class="col-md-12 align-self-center">
					<h1 class="innerpage-title">{{translate('kundali')}}</h1>
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item active"><a href="{{url('/')}}" class="text-white"><i class="fa fa-home"></i> Home</a></li>
							<li class="breadcrumb-item">{{translate('kundali')}}</li>
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
                <div class="row">
                    <div class="col-md-6">
                        <table class="table kundli-basic-details">
                          <thead class="thead-dark">
                            <tr>
                              <th scope="col" colspan="2"><i class="fa fa-user fa-lg"></i>&nbsp; Basic Detail</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <th scope="row"><b>Name</b></th>
                              <td>Gaurav Sharma</td>
                            </tr>
                            <tr>
                              <th scope="row"><b>Birth Date & Time</b></th>
                              <td>02/06/2025 | 01:48 PM</td>
                            </tr>
                            <tr>
                              <th scope="row"><b>Birth Place</b></th>
                              <td>Ujjain</td>
                            </tr>
                          </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table kundli-basic-details">
                          <thead class="thead-dark">
                            <tr>
                              <th scope="col" colspan="2"><i class="fa fa-file-text fa-lg"></i>&nbsp; Your Detail</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <th scope="row"><b>Nakshatra</b></th>
                              <td>Magha</td>
                            </tr>
                            <tr>
                              <th scope="row"><b>Ascendant</b></th>
                              <td>Virgo</td>
                            </tr>
                            <tr>
                              <th scope="row"><b>Sign</b></th>
                              <td>Leo</td>
                            </tr>
                          </tbody>
                        </table>
                    </div>
                </div>
               <!-- start tabs -->
                <div class="tabbable-responsive my-3">
                    <div class="tabbable">
                        <ul class="nav nav-pills nav-justified" id="linxea-avenir" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="daily-tab" data-toggle="tab" href="#daily" role="tab"
                                    aria-controls="first" aria-selected="true"
                                style="color: #222 !important; font-weight: 600;">{{translate('सामान्य')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="month-tab" data-toggle="tab" href="#month" role="tab"
                                    aria-controls="second" aria-selected="false" style="color: #222 !important; font-weight: 600;">
                                {{translate('चार्ट')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="varshik-tab" data-toggle="tab" href="#varshik" role="tab"
                                    aria-controls="second" aria-selected="false" style="color: #222 !important; font-weight: 600;">
                                {{translate('दशा')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="varshik-tab" data-toggle="tab" href="#varshik" role="tab"
                                    aria-controls="second" aria-selected="false" style="color: #222 !important; font-weight: 600;">
                                {{translate('फल')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="varshik-tab" data-toggle="tab" href="#varshik" role="tab"
                                    aria-controls="second" aria-selected="false" style="color: #222 !important; font-weight: 600;">
                                {{translate('सुझाव')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="varshik-tab" data-toggle="tab" href="#varshik" role="tab"
                                    aria-controls="second" aria-selected="false" style="color: #222 !important; font-weight: 600;">
                                {{translate('दोष')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="varshik-tab" data-toggle="tab" href="#varshik" role="tab"
                                    aria-controls="second" aria-selected="false" style="color: #222 !important; font-weight: 600;">
                                {{translate('लाल किताब')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- end tabs -->
                <div class="tab-content">
                    {{-- daily rashifal --}}
                    <div class="tab-pane fade show active" id="daily" role="tabpanel" aria-labelledby="daily-tab">
                        <div class="row mt-3">
                            <div class="col-md-12 card border-0 box-shadow mygap-bottom">
                                <div class="card-body mybgcolor">
                                    <span id="monthlyHeadingAkshar" class="h5"> </span>
                                    <span id="monthlyAkshar" class="h5"></span>
                                    <p id="monthlyDetail"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- monthly rashifal --}}
                    <div class="tab-pane fade" id="month" role="tabpanel" aria-labelledby="month-tab">
                        <div class="row mt-3">
                            <div class="col-md-12 card border-0 box-shadow mygap-bottom">
                                <div class="card-body mybgcolor">
                                    <span id="monthlyHeadingAkshar" class="h5"> </span>
                                    <span id="monthlyAkshar" class="h5"></span>
                                    <p id="monthlyDetail"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- yearly rashifal --}}
                    <div class="tab-pane fade" id="varshik" role="tabpanel" aria-labelledby="varshik-tab">
                        <div class="row mt-3">
                            <div class="col-md-12 card border-0 box-shadow mygap-bottom">
                                <div class="card-body mybgcolor">
                                    <span id="varshikHeadingAkshar" class="h5"> </span>
                                    <span id="varshikAkshar" class="h5"></span>
                                    <p id="varshikDetail"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
