@extends('layouts.front-end.app')

@section('title',translate('darshan'))

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
<div class="inner-page-bg center bg-bla-7 py-4" style="background:url({{ asset('assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
		<div class="container">
			<div class="row all-text-white">
				<div class="col-md-12 align-self-center">
					<h1 class="innerpage-title mb-1">{{translate('darshan_places_in_india')}}</h1>
					<p class="darshan-para">{{translate('Holy places and pilgrimage sites are where the individual with an external quest receives what he comes for, and the individual with an internal quest rises high from within.')}}</p>
					<h5>Travelling from <span class="select-city">Select City <i class="fa fa-angle-down" aria-hidden="true"></i></span></h5>
				</div>
			</div>
		</div>
	</div>
	<div class="container py-5 rtl text-align-direction">
		<div class="row g-4">
			<div class="col-xl-4 col-lg-4 col-sm-6">
				<article class="darshan-card-two">
					<figure class="darshan-banner-two imgEffect">
						<a href="">
							<img src="{{ asset('assets/front-end/img/rameshwaram.jpeg') }}" alt="mahakal">
						</a>
					</figure>
					<div class="darshan-content">
						<div class="heading">
							<span class="heading-pera">Beautiful Pilgrimage Site in Tamil Nadu</span>
						</div>
						<h4 class="title line-clamp-2">
						<a href="">Rameshwaram</a>
						</h4>
						<p>Made famous by the epic Ramayana, Rameshwaram is a quaint beach town and a popular Hindu pilgrimage destination that also attracts nature lovers and adventure...</p>
						<a href="#" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">EXPLORE </a>
					</div>
				</article>
			</div>
			<div class="col-xl-4 col-lg-4 col-sm-6">
				<article class="darshan-card-two">
					<figure class="darshan-banner-two imgEffect">
						<a href="">
							<img src="{{ asset('assets/front-end/img/mathura.jpg') }}" alt="mahakal">
						</a>
					</figure>
					<div class="darshan-content">
						<div class="heading">
							<span class="heading-pera">A Spiritual City in Uttar Pradesh</span>
						</div>
						<h4 class="title line-clamp-2">
						<a href="">Mathura</a>
						</h4>
						<p>Mathura is a popular pilgrimage hub. You will love temple hopping, exploring the ghats and the lively Holi celebrations. Mathura is also popular for its...</p>
						<a href="#" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">EXPLORE </a>
					</div>
				</article>
			</div>
			<div class="col-xl-4 col-lg-4 col-sm-6">
				<article class="darshan-card-two">
					<figure class="darshan-banner-two imgEffect">
						<a href="">
							<img src="{{ asset('assets/front-end/img/maha.jpg') }}" alt="mahakal">
						</a>
					</figure>
					<div class="darshan-content">
						<div class="heading">
							<span class="heading-pera">Home of Kalidasa and Patanjali</span>
						</div>
						<h4 class="title line-clamp-2">
						<a href="">Ujjain</a>
						</h4>
						<p>One of the seven sacred Hindu cities, Ujjain is located on the banks of river Kshipra and is land of the magnificent Kumbh Mela. Iconic religious sites and a web of lively...</p>
						<a href="#" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">EXPLORE </a>
					</div>
				</article>
			</div>
			<div class="col-xl-4 col-lg-4 col-sm-6">
				<article class="darshan-card-two">
					<figure class="darshan-banner-two imgEffect">
						<a href="">
							<img src="{{ asset('assets/front-end/img/vrindavan.jpg') }}" alt="mahakal">
						</a>
					</figure>
					<div class="darshan-content">
						<div class="heading">
							<span class="heading-pera">A sacred site for the Hindu deity Krishna.</span>
						</div>
						<h4 class="title line-clamp-2">
						<a href="">Vrindavan</a>
						</h4>
						<p>Vrindavan, also spelled Vrindaban and Brindaban, is a historic town in Uttar Pradesh's Mathura district. Located in the Braj Bhoomi region, it is a religiously significant place, ....</p>
						<a href="#" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">EXPLORE </a>
					</div>
				</article>
			</div>
			<div class="col-xl-4 col-lg-4 col-sm-6">
				<article class="darshan-card-two">
					<figure class="darshan-banner-two imgEffect">
						<a href="">
							<img src="{{ asset('assets/front-end/img/pushkar.jpg') }}" alt="mahakal">
						</a>
					</figure>
					<div class="darshan-content">
						<div class="heading">
							<span class="heading-pera">Quaint Temple Town in Rajasthan</span>
						</div>
						<h4 class="title line-clamp-2">
						<a href="">Pushkar</a>
						</h4>
						<p>Home to rare temples, shrines and picturesque locations, Pushkar in Rajasthan is where tourists throng to find their peace, and lose themselves in the rare...</p>
						<a href="#" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">EXPLORE </a>
					</div>
				</article>
			</div>
			<div class="col-xl-4 col-lg-4 col-sm-6">
				<article class="darshan-card-two">
					<figure class="darshan-banner-two imgEffect">
						<a href="">
							<img src="{{ asset('assets/front-end/img/dwarkadhish.jpg') }}" alt="mahakal">
						</a>
					</figure>
					<div class="darshan-content">
						<div class="heading">
							<span class="heading-pera">One of the Char Dhams</span>
						</div>
						<h4 class="title line-clamp-2">
						<a href="">Dwarka</a>
						</h4>
						<p>Soak in the mystic hues of Dwarka, a famous pilgrimage spot! You can expect scorching heat which renders venturing out during afternoons, almost impossible...</p>
						<a href="#" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">EXPLORE </a>
					</div>
				</article>
			</div>
		</div>
	</div>
@endsection
