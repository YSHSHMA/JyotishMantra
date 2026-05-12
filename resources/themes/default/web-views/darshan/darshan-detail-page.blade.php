@extends('layouts.front-end.app')

@section('title',translate('darshan-detail'))

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
<!-- Promotion S t a r t -->
<section class="platform-area platform-area-bg" style="background-image: url({{ asset('assets/front-end/img/darshan-banner.png') }});">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="app-section-padding">
                            <div class="hero-caption-one position-relative">
                                <h4 class="title">
                                Ujjain
                                </h4>
                                <p class="pera">
                                One of the seven sacred Hindu cities, Ujjain is located on the banks of river Kshipra and is land of the magnificent Kumbh Mela.Iconic religious sites and a web of lively lanes are a vibrant display of Ujjain’s art and heritage.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="hero-banner d-none d-lg-block wow fadeInUp" data-wow-delay="0.2s">
                        <div id="page">
	<div class="row">
		<div class="column small-11 small-centered">
			<h2>Slick Slider Syncing</h2>
			<div class="slider slider-single">
				<div><h3>1</h3></div>
				<div><h3>2</h3></div>
				<div><h3>3</h3></div>
				<div><h3>4</h3></div>
				<div><h3>5</h3></div>
				<div><h3>6</h3></div>
				<div><h3>7</h3></div>
				<div><h3>8</h3></div>
				<div><h3>9</h3></div>
				<div><h3>10</h3></div>
			</div>
			<div class="slider slider-nav">
				<div><h3><span>1</span></h3></div>
				<div><h3><span>2</span></h3></div>
				<div><h3><span>3</span></h3></div>
				<div><h3><span>4</span></h3></div>
				<div><h3><span>5</span></h3></div>
				<div><h3><span>6</span></h3></div>
				<div><h3><span>7</span></h3></div>
				<div><h3><span>8</span></h3></div>
				<div><h3><span>9</span></h3></div>
				<div><h3><span>10</span></h3></div>
			</div>
		</div>
	</div>
</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--/ End of Promotion -->
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
@push('script')
<script>
     $('.slider-single').slick({
 	slidesToShow: 1,
 	slidesToScroll: 1,
 	arrows: true,
 	fade: false,
 	adaptiveHeight: true,
 	infinite: false,
	useTransform: true,
 	speed: 400,
 	cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
 });

 $('.slider-nav')
 	.on('init', function(event, slick) {
 		$('.slider-nav .slick-slide.slick-current').addClass('is-active');
 	})
 	.slick({
 		slidesToShow: 7,
 		slidesToScroll: 7,
 		dots: false,
 		focusOnSelect: false,
 		infinite: false,
 		responsive: [{
 			breakpoint: 1024,
 			settings: {
 				slidesToShow: 5,
 				slidesToScroll: 5,
 			}
 		}, {
 			breakpoint: 640,
 			settings: {
 				slidesToShow: 4,
 				slidesToScroll: 4,
			}
 		}, {
 			breakpoint: 420,
 			settings: {
 				slidesToShow: 3,
 				slidesToScroll: 3,
		}
 		}]
 	});

 $('.slider-single').on('afterChange', function(event, slick, currentSlide) {
 	$('.slider-nav').slick('slickGoTo', currentSlide);
 	var currrentNavSlideElem = '.slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
 	$('.slider-nav .slick-slide.is-active').removeClass('is-active');
 	$(currrentNavSlideElem).addClass('is-active');
 });

 $('.slider-nav').on('click', '.slick-slide', function(event) {
 	event.preventDefault();
 	var goToSingleSlide = $(this).data('slick-index');

 	$('.slider-single').slick('slickGoTo', goToSingleSlide);
 });
</script>
@endpush


