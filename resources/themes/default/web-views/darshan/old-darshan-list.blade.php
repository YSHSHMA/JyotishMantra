@extends('layouts.front-end.app')

@section('title',translate('darshan'))

@push('css_or_js')
<meta property="og:image" content="{{dynamicStorage(path: 'storage/app/public/company')}}/{{$web_config['web_logo']->value}}" />
<meta property="og:title" content="Terms & conditions of {{$web_config['name']->value}} " />
<meta property="og:url" content="{{env('APP_URL')}}">
<meta property="og:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">
<meta property="twitter:card" content="{{dynamicStorage(path: 'storage/app/public/company')}}/{{$web_config['web_logo']->value}}" />
<meta property="twitter:title" content="Terms & conditions of {{$web_config['name']->value}}" />
<meta property="twitter:url" content="{{env('APP_URL')}}">
<meta property="twitter:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">
<style>
	.two-lines-only {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.5em;
            min-height: 3em;
        }
        .newpadding{
            padding: 5px 1.25rem 1.25rem;
        }

    .one-lines-only {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush


@section('content')
<div class="inner-page-bg center bg-bla-7 py-4" style="background:url({{ asset('public/assets/front-end/img/page-bg/darshan.png') }}) no-repeat;background-size:cover;background-position:center center">
	<div class="container">
		<div class="row all-text-white">
			<div class="col-md-12 align-self-center text-center">
				<h1 class="innerpage-title mb-1">{{ translate('darshan_places') }}</h1>
				<h5>
					<form action="{{ url()->current() }}" method="GET">
						<div class="input-group w-50" style="margin-left: 28%; opacity: 0.6;">
							<input type="text" name="search" class="form-control border-0 fw-bold" placeholder="{{ translate('Search_Temple_Name_&_Address') }}">
							<button class="btn btn-primary" type="submit">Search</button>
						</div>
					</form>
				</h5>
			</div>
		</div>
	</div>
</div>
<!-- start to temple section -->
<section class="temple-section">
	<div class="container-fluid p-0 rtl">
		<div class="__inline-62 pt-3">

			<style>
				.btn.active-category {
					background-color: #fe9802;
					color: #fff;
				}
			</style>
			<div class='ml-2'>
				<button class="temple-category-filter btn active-category" data-category="all">All</button>
				@if (isset($categoryList) && !empty($categoryList) && count($categoryList) > 0)
				@foreach ($categoryList as $cat_name)
				<button class="temple-category-filter btn" data-category="{{ Str::slug($cat_name['name'])}}">{{ ucwords($cat_name['name'])}}</button>
				@endforeach
				@endif
			</div>
			<div class="row">
				<div class="col-12">
					<a href="{{ route('darshan') }}" class="float-end btn btn--primary btn-sm me-4"><i class="fa fa-refresh"></i> {{ translate('Clear')}}</a>
				</div>
			</div>
			<div class="container feature-product p-0">
				<div class="portfoliolist_event p-1">
					<div class="EventFilter row ">
						@if (isset($templeList) && !empty($templeList) && count($templeList) > 0)
						@foreach ($templeList as $product)
						<div class="col-xl-3 col-lg-3 col-sm-6 my-2 portfolioEvents {{ Str::slug($product['category']['name'])}}" data-cat="{{ Str::slug($product['category']['name'])}}" style="display: inline-block;" data-bound="">
							<div class="portfolio-wrapper">
								<div class="card">
									{{--<span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
										 <span class="direction-ltr blink d-block">{{ Str::slug($product['category']['name'])}}</span> 
									</span>--}}
									<a href="{{ route('temple-details',[ $product['slug'] ])}}"><img src="{{ getValidImage(path: 'storage/app/public/temple/thumbnail/' . $product['thumbnail'], type: 'product') }}" class="card-img-top puja-image" alt="..."></a>
									<div class="card-body newpadding">
										<p class="pooja-heading underborder one-lines-only mb-0">
											@isset($product['states'])
											{{ translate("Beautiful_Pilgrimage_Site_in")}} {{ $product['states']['name'] }}
											@else
											&nbsp;
											@endisset
										</p>
										<!-- <div class="w-bar h-bar bg-gradient mt-2"></div> -->
										<h5 class="font-weight-bolder two-lines-only">{{ $product['name']}}</h5>
										<p class="card-text mt-2 mb-2 two-lines-only">{!! (strip_tags($product['short_description'] ?? "")) !!}</p>
										<a href="{{ route('temple-details',[ $product['slug'] ])}}" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold"> {{ translate("EXPLORE")}}</a>
									</div>
								</div>
							</div>
						</div>



						{{-- <div class="col-xl-4 col-lg-4 col-sm-6 portfolioEvents {{ Str::slug($product['category']['name'])}}" data-cat="{{ Str::slug($product['category']['name'])}}">
						<article class="darshan-card-two mt-4">
							<figure class="darshan-banner-two imgEffect">
								@php($temple_image='')
								@if(isset($product['galleries'][0]['images']) && json_decode($product['galleries'][0]['images']))
								@php($temple_image = json_decode($product['galleries'][0]['images'])[0]??"")
								@endif
								<a>
									<img src="{{ getValidImage(path: 'storage/app/public/temple/thumbnail/' . $product['thumbnail'], type: 'product') }}" alt="mahakal">
								</a>
							</figure>
							<div class="darshan-content">
								<div class="heading">
									<span class="heading-pera">@isset($product['states'])
										{{ translate("Beautiful_Pilgrimage_Site_in")}} {{ $product['states']['name'] }}
										@endisset
									</span>
								</div>
								<h4 class="title line-clamp-2">
									<a>{{ $product['name']}}</a>
								</h4>
								{!! Str::limit(strip_tags($product['short_description'] ?? ""), 30) !!}
								<a href="{{ route('temple-details',[ $product['slug'] ])}}" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold"> </a>
							</div>
						</article>
					</div> --}}
					@endforeach
					@endif
				</div>
			</div>
			<div class="text-center pt-2 d-md-none">
				<a class="text-capitalize view-all-text web-text-primary" href="{{ route('event') }}">
					{{ translate('view_all') }}
					<i class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
				</a>
			</div>
		</div>
	</div>
	</div>
</section>
@endsection

@push('script')
<script>
	$('.temple-category-filter').on('click', function() {
		var category = $(this).data('category');
		$('.temple-category-filter').removeClass('active-category');
		$(this).addClass('active-category');
		filterEventItems(category);
	});

	function filterEventItems(category) {
		if (category === 'all') {
			$('.portfolioEvents').stop(true, true).fadeIn(340);
		} else {
			$('.portfolioEvents').each(function() {
				if ($(this).data('cat') === category) {
					$(this).stop(true, true).fadeIn(340);
				} else {
					$(this).stop(true, true).fadeOut(340);
				}
			});
		}
	}
</script>

@endpush