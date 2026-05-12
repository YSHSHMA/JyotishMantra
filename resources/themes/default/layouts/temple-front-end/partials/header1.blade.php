@php
    $logo = is_object($temple) ? $temple->logo : $temple;
@endphp
<!-- start header -->
<nav class="navbar sticky-top navbar-expand-lg header-bg">
    <div class="container">
        <a class="navbar-brand" href="{{ url('mandir/'.$temple->slug) }}">
            <img class="logo-img"  src="{{ theme_asset(path: 'storage/app/public/temple/logo/'.$logo) }}"
            alt="">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
            aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarScroll">
            <ul class="navbar-nav m-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#history">History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#poojas">Pujas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#livedarshan">Live Darshan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
            </ul>
            <span class="me-3 header-phone"><i class="fa-solid fa-phone me-1"></i> +91 96858 56131</span>
            <a href="#poojas" class="header-btn" target="">
                Registered Login Only
            </a>

        </div>
    </div>
</nav>
<!-- end header -->