<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ session()->get('direction') ?? 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="light">
      <title>Shree Mangalnath Mandir Ujjain | मंगलनाथ मंदिर उज्जैन</title>
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="viewport"  content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0, user-scalable=no">

    <link rel="canonical" href="{{ url()->current() }}" />
  
    {{-- Google Tag --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BKWL2WND31"></script>
 

    {{-- Favicon --}}
    <link rel="apple-touch-icon" sizes="180x180"
          href="{{ theme_asset(path: 'storage/app/public/company') }}/{{ $web_config['fav_icon']->value }}">
    <link rel="icon" type="image/png" sizes="32x32"
          href="{{ theme_asset(path: 'storage/app/public/company') }}/{{ $web_config['fav_icon']->value }}">

   

    {{-- Temple Front CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Lightbox CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.9.0/css/lightbox.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/temple-front-end/assets/css/style.css') }}">
   
    {{-- Page Level CSS --}}
    @stack('css_or_js')
</head>

<body>

    {{-- PHP Variables --}}

    {{-- Partials --}}
    @include('layouts.temple-front-end.partials.header1')

    {{-- Page Main Content --}}
    @yield('content')
    {{-- Footer --}}
    @include('layouts.temple-front-end.partials.footer1')

    {{-- JS --}}

    <!-- end footer section -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Lightbox JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.9.0/js/lightbox.min.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/js/slider.js') }}"></script>
      
    @stack('script')
</body>
</html>
