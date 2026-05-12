<!-- Start Slider -->
<div class="slider p-relative">
    <div class="main-banner arrow-layout-1 ">
        @foreach ($images as $photo)
        <div class="slide-item">
            <img src="{{ getValidImage('storage/app/public/temple/gallery/' . $photo, type: 'product') }}" class="image-fit" title="Logo" alt="slider">
            <div class="transform-center">
                <div class="container">
                    <div class="slider-content">
                        <h1 class="text-custom-white">मंगल नाथ </h1>
                        <p class="text-white">मंदिर उज्जैन में आपका स्वागत है</p>
                        <a href="#" class="btn-submit btn_text_effect"><span class="button_title">BOOK NOW</span></a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<!-- End Slider -->